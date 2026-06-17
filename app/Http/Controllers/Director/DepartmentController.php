<?php
// app/Http/Controllers/Director/DepartmentController.php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\AnnualBudget;
use App\Models\BudgetLog;
use App\Models\Department;
use App\Models\InternalAgreement;
use App\Models\ProposalHarga;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * List semua departemen + ringkasan budget — untuk monitoring direktur.
     */
    public function index(Request $request): View
    {
        $year = $request->input('year', now()->year);

        $departments = Department::with([
                'annualBudgets' => fn ($q) => $q->where('fiscal_year', $year),
                'costCenters.annualBudgets' => fn ($q) => $q->where('fiscal_year', $year)
            ])
            ->withCount('users')
            ->orderBy('dept_name')
            ->get()
            ->map(function ($dept) use ($year) {
                // Jumlah pengajuan aktif untuk dept ini
                $pengajuanAktif = ProposalHarga::whereHas('ppbj.user', fn($q) => $q->where('dept_id', $dept->id))
                    ->whereIn('status', ['Draft', 'In_Review'])
                    ->count();

                $budget = $dept->annualBudgets->first();
                $deptPlan = $budget ? $budget->total_plan : 0;
                $deptUsed = $budget ? $budget->total_used : 0;
                $deptReserved = $budget ? $budget->total_reserved : 0;

                $ccPlan = $dept->costCenters->reduce(fn($c, $cc) => $c + ($cc->annualBudgets->first() ? $cc->annualBudgets->first()->total_plan : 0), 0);
                $ccUsed = $dept->costCenters->reduce(fn($c, $cc) => $c + ($cc->annualBudgets->first() ? $cc->annualBudgets->first()->total_used : 0), 0);
                $ccReserved = $dept->costCenters->reduce(fn($c, $cc) => $c + ($cc->annualBudgets->first() ? $cc->annualBudgets->first()->total_reserved : 0), 0);

                $plan = $deptPlan + $ccPlan;
                $used = $deptUsed + $ccUsed;
                $rsv = $deptReserved + $ccReserved;
                $sisa   = $plan - $used - $rsv;
                $pct    = $plan > 0 ? round(($used / $plan) * 100, 1) : 0;

                return [
                    'id'             => $dept->id,
                    'name'           => $dept->dept_name,
                    'budget_code'    => $dept->budget_code,
                    'users_count'    => $dept->users_count,
                    'plan'           => $plan,
                    'used'           => $used,
                    'reserved'       => $rsv,
                    'sisa'           => $sisa,
                    'utilization'    => $pct,
                    'pengajuan_aktif'=> $pengajuanAktif,
                ];
            });

        $totalPlan     = $departments->sum('plan');
        $totalUsed     = $departments->sum('used');
        $totalReserved = $departments->sum('reserved');

        return view('director.departments.index', compact(
            'departments', 'year', 'totalPlan', 'totalUsed', 'totalReserved'
        ));
    }

    /**
     * Detail satu departemen: statistik, grafik realisasi, daftar pengajuan, anggota.
     */
    public function show(Department $department, Request $request): View
    {
        $year = $request->input('year', now()->year);

        $deptBudget = $department->annualBudgets()->where('fiscal_year', $year)->first();
        
        $costCenterBudgets = \App\Models\AnnualBudget::whereIn('cost_center_id', $department->costCenters()->pluck('id'))
            ->where('fiscal_year', $year)
            ->get();

        $totalPlan = ($deptBudget ? $deptBudget->total_plan : 0) + $costCenterBudgets->sum('total_plan');
        $totalUsed = ($deptBudget ? $deptBudget->total_used : 0) + $costCenterBudgets->sum('total_used');
        $totalReserved = ($deptBudget ? $deptBudget->total_reserved : 0) + $costCenterBudgets->sum('total_reserved');

        $budget = null;
        if ($totalPlan > 0 || $deptBudget || $costCenterBudgets->count() > 0) {
            $budget = (object) [
                'total_plan' => $totalPlan,
                'total_used' => $totalUsed,
                'total_reserved' => $totalReserved,
                'remaining' => $totalPlan - $totalUsed - $totalReserved,
            ];
        }

        // Realisasi per bulan (IA Approved)
        $realisasiBulanan = InternalAgreement::whereHas('proposalHarga.ppbj.user', function ($q) use ($department) {
                $q->where('dept_id', $department->id);
            })
            ->where('status_ia', 'Approved')
            ->whereYear('updated_at', $year)
            ->selectRaw('MONTH(updated_at) as bulan, SUM(final_nominal) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $labels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $dataRealisasi = [];
        for ($m = 1; $m <= 12; $m++) {
            $dataRealisasi[] = (float) ($realisasiBulanan[$m] ?? 0);
        }

        // Pengajuan aktif departemen ini
        $pengajuanList = ProposalHarga::with(['ppbj.user', 'internalAgreement'])
            ->whereHas('ppbj.user', fn($q) => $q->where('dept_id', $department->id))
            ->whereIn('status', ['Draft', 'In_Review', 'Approved', 'Rejected'])
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        // Statistik pengajuan
        $statAktif = ProposalHarga::whereHas('ppbj.user', fn($q) => $q->where('dept_id', $department->id))
            ->where(function ($query) {
                $query->whereIn('status', ['Draft', 'In_Review'])
                    ->orWhere(function ($q2) {
                        $q2->where('status', 'Approved')
                            ->whereDoesntHave('internalAgreement', function ($q3) {
                                $q3->whereIn('status_ia', ['Approved', 'Rejected']);
                            });
                    });
            })->count();

        $statApproved = ProposalHarga::whereHas('ppbj.user', fn($q) => $q->where('dept_id', $department->id))
            ->whereHas('internalAgreement', function ($query) {
                $query->where('status_ia', 'Approved');
            })->count();

        $statRejected = ProposalHarga::whereHas('ppbj.user', fn($q) => $q->where('dept_id', $department->id))
            ->where(function ($query) {
                $query->where('status', 'Rejected')
                    ->orWhereHas('internalAgreement', function ($q2) {
                        $q2->where('status_ia', 'Rejected');
                    });
            })->count();

        // Anggota departemen
        $members = User::where('dept_id', $department->id)
            ->orderBy('name')
            ->get();

        // Budget logs (audit trail)
        $logType = $request->input('log_type');
        $search  = $request->input('search');
        $logsQuery = BudgetLog::where('dept_id', $department->id);
        if ($logType) $logsQuery->where('log_type', $logType);
        if ($search) {
            $logsQuery->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        $logs = $logsQuery->orderByDesc('created_at')->paginate(10)->withQueryString();

        $costCenterBudgets = \App\Models\CostCenter::where('dept_id', $department->id)
            ->with(['currentBudget'])
            ->get()
            ->map(function ($cc) {
                $plan = (float)($cc->currentBudget?->total_plan ?? 0);
                $used = (float)($cc->currentBudget?->total_used ?? 0);
                $reserved = (float)($cc->currentBudget?->total_reserved ?? 0);
                $sisa = $plan - $used - $reserved;
                $utilization = $plan > 0 ? round(($used / $plan) * 100, 1) : 0;

                return [
                    'code' => $cc->cost_center_code,
                    'name' => $cc->cost_center_name,
                    'plan' => $plan,
                    'used' => $used,
                    'sisa' => $sisa,
                    'utilization' => $utilization,
                ];
            })
            ->sortByDesc('utilization');

        return view('director.departments.show', compact(
            'department', 'budget', 'year',
            'labels', 'dataRealisasi',
            'pengajuanList',
            'statAktif', 'statApproved', 'statRejected',
            'members', 'logs', 'costCenterBudgets'
        ));
    }
}
