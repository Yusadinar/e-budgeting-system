<?php
// app/Http/Controllers/Director/DashboardController.php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\AnnualBudget;
use App\Models\Department;
use App\Models\InternalAgreement;
use App\Models\ProposalHarga;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $year = now()->year;

        // ── Statistik Global Perusahaan ──────────────────────
        // Gunakan GROUP BY per dept_id untuk handle duplikat rows di annual_budgets
        $totalDepartments   = Department::count();
        $totalKaryawan      = User::whereNotIn('role', ['superadmin'])->count();

        // SUM per dept (dept-level rows, bukan cost-center rows)
        $deptTotals = AnnualBudget::where('fiscal_year', $year)
            ->whereNotNull('dept_id')
            ->whereNull('cost_center_id')
            ->selectRaw('dept_id, SUM(total_plan) as plan, SUM(total_used) as used, SUM(total_reserved) as reserved')
            ->groupBy('dept_id')
            ->get();

        // SUM cost-center rows digroup per dept
        $ccTotals = AnnualBudget::where('fiscal_year', $year)
            ->whereNotNull('cost_center_id')
            ->join('cost_centers', 'cost_centers.id', '=', 'annual_budgets.cost_center_id')
            ->selectRaw('cost_centers.dept_id, SUM(annual_budgets.total_plan) as plan, SUM(annual_budgets.total_used) as used, SUM(annual_budgets.total_reserved) as reserved')
            ->groupBy('cost_centers.dept_id')
            ->get();

        $totalBudgetPlan     = $deptTotals->sum('plan')     + $ccTotals->sum('plan');
        $totalBudgetUsed     = $deptTotals->sum('used')     + $ccTotals->sum('used');
        $totalBudgetReserved = $deptTotals->sum('reserved') + $ccTotals->sum('reserved');
        $totalBudgetSisa     = $totalBudgetPlan - $totalBudgetUsed - $totalBudgetReserved;

        $pctUsed = $totalBudgetPlan > 0
            ? round(($totalBudgetUsed / $totalBudgetPlan) * 100, 1)
            : 0;

        $totalPengajuanAktif = \App\Models\Ppbj::where(function ($q) {
            $q->whereIn('status', ['Draft', 'In_Review'])
              ->orWhere(function ($q2) {
                  $q2->where('status', 'Approved')
                     ->whereDoesntHave('proposalHarga', function ($q3) {
                         $q3->whereHas('internalAgreement', function ($q4) {
                             $q4->whereIn('status_ia', ['Approved', 'Rejected']);
                         })->orWhere('status', 'Rejected');
                     });
              });
        })->count();

        $totalPengajuanApproved = \App\Models\Ppbj::whereHas('proposalHarga.internalAgreement', function ($q) {
            $q->where('status_ia', 'Approved');
        })->count();

        $totalPengajuanRejected = \App\Models\Ppbj::where(function ($q) {
            $q->where('status', 'Rejected')
              ->orWhereHas('proposalHarga', function ($q2) {
                  $q2->where('status', 'Rejected')
                     ->orWhereHas('internalAgreement', function ($q3) {
                         $q3->where('status_ia', 'Rejected');
                     });
              });
        })->count();

        // ── Tugas yang menunggu approval direktur (PPBJ, PH, IA) ──────
        $pendingApprovalDir = $user->getPendingActionPpbjIds()->count();

        // ── Budget per Departemen (bar chart) ────────────────
        $deptBudgets = Department::with(['currentBudget', 'costCenters.currentBudget'])->get()
            ->map(function ($dept) {
                // Shorten name mapping
                $shortName = match($dept->dept_name) {
                    'Marketing'                               => 'MKT',
                    'Manufacturing'                           => 'MFC',
                    'Manufacturing IKAR'                      => 'MFC-IK',
                    'Engineering'                             => 'ENG',
                    'Maintenance'                             => 'MTC',
                    'Maintenance IKAR'                        => 'MTC-IK',
                    'Production Planning & Logistic Control' => 'PPLC',
                    'Finance Accounting'                      => 'ACC',
                    'Procurement'                             => 'PRC',
                    'Quality Management System'               => 'QMS',
                    'Human Capital & General Services'        => 'HCGS',
                    default                                   => substr($dept->dept_name, 0, 4)
                };

                return [
                    'name'       => $dept->dept_name,
                    'short_name' => $shortName,
                    'code'       => $dept->budget_code,
                    'plan'       => (float)($dept->total_plan),
                    'used'       => (float)($dept->total_used),
                    'reserved'   => (float)($dept->total_reserved),
                ];
            })
            ->filter(fn($d) => $d['plan'] > 0)
            ->values();

        $chartDeptLabels = $deptBudgets->pluck('short_name');
        $chartDeptPlan   = $deptBudgets->pluck('plan');
        $chartDeptUsed   = $deptBudgets->pluck('used');

        // ── Tren Realisasi Bulanan (line chart) ──────────────
        $realisasiBulanan = InternalAgreement::where('status_ia', 'Approved')
            ->whereYear('updated_at', $year)
            ->selectRaw('MONTH(updated_at) as bulan, SUM(final_nominal) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $labels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $dataRealisasi = [];
        $currentMonth = now()->month;
        for ($m = 1; $m <= 12; $m++) {
            if ($m <= $currentMonth) {
                $dataRealisasi[] = (float)($realisasiBulanan[$m] ?? 0);
            } else {
                $dataRealisasi[] = null;
            }
        }

        // ── Departemen utilisasi tinggi (>= 75%) ─────────────
        $highUtilDepts = $deptBudgets->filter(
            fn($d) => $d['plan'] > 0 && (($d['used'] / $d['plan']) * 100) >= 75
        )->sortByDesc(fn($d) => $d['used'] / $d['plan'])->values();

        // ── Aktivitas IA terbaru yang perlu di-approve ────────
        // ── Budget per Cost Center ───────────────────────────
        $costCenterBudgets = \App\Models\CostCenter::with(['currentBudget', 'department'])
            ->get()
            ->map(function ($cc) {
                $plan = (float)($cc->currentBudget?->total_plan ?? 0);
                $used = (float)($cc->currentBudget?->total_used ?? 0);
                $reserved = (float)($cc->currentBudget?->total_reserved ?? 0);
                $sisa = $plan - $used - $reserved;
                $utilization = $plan > 0 ? round(($used / $plan) * 100, 1) : 0;

                return [
                    'id' => $cc->id,
                    'code' => $cc->cost_center_code,
                    'name' => $cc->cost_center_name,
                    'dept_name' => $cc->department?->dept_name ?? '-',
                    'plan' => $plan,
                    'used' => $used,
                    'reserved' => $reserved,
                    'sisa' => $sisa,
                    'utilization' => $utilization,
                ];
            })
            ->filter(fn($d) => $d['plan'] > 0)
            ->sortByDesc('utilization')
            ->values();

        $pendingList = \App\Models\Ppbj::with(['user.department', 'latestProposalHarga.internalAgreement'])
            ->whereIn('id', $user->getPendingActionPpbjIds())
            ->latest()
            ->take(8)
            ->get();

        return view('director.dashboard', compact(
            'user',
            'year',
            'totalDepartments',
            'totalKaryawan',
            'totalBudgetPlan',
            'totalBudgetUsed',
            'totalBudgetReserved',
            'totalBudgetSisa',
            'pctUsed',
            'totalPengajuanAktif',
            'totalPengajuanApproved',
            'totalPengajuanRejected',
            'pendingApprovalDir',
            'chartDeptLabels',
            'chartDeptPlan',
            'chartDeptUsed',
            'labels',
            'dataRealisasi',
            'highUtilDepts',
            'pendingList',
            'costCenterBudgets',
        ));
    }
}
