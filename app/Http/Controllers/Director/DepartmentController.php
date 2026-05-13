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

        $departments = Department::with(['annualBudgets' => function ($q) use ($year) {
                $q->where('fiscal_year', $year);
            }])
            ->withCount('users')
            ->orderBy('dept_name')
            ->get()
            ->map(function ($dept) use ($year) {
                $budget = $dept->annualBudgets->first();
                $plan   = (float) ($budget?->total_plan   ?? 0);
                $used   = (float) ($budget?->total_used   ?? 0);
                $rsv    = (float) ($budget?->total_reserved ?? 0);
                $sisa   = $plan - $used - $rsv;
                $pct    = $plan > 0 ? round(($used / $plan) * 100, 1) : 0;

                // Jumlah pengajuan aktif untuk dept ini
                $pengajuanAktif = ProposalHarga::whereHas('ppbj.user', fn($q) => $q->where('dept_id', $dept->id))
                    ->whereIn('status', ['Draft', 'In_Review'])
                    ->count();

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

        $budget = $department->annualBudgets()
            ->where('fiscal_year', $year)
            ->first();

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
        $statAktif    = ProposalHarga::whereHas('ppbj.user', fn($q) => $q->where('dept_id', $department->id))
            ->whereIn('status', ['Draft', 'In_Review'])->count();
        $statApproved = ProposalHarga::whereHas('ppbj.user', fn($q) => $q->where('dept_id', $department->id))
            ->where('status', 'Approved')->count();
        $statRejected = ProposalHarga::whereHas('ppbj.user', fn($q) => $q->where('dept_id', $department->id))
            ->where('status', 'Rejected')->count();

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

        return view('director.departments.show', compact(
            'department', 'budget', 'year',
            'labels', 'dataRealisasi',
            'pengajuanList',
            'statAktif', 'statApproved', 'statRejected',
            'members', 'logs'
        ));
    }
}
