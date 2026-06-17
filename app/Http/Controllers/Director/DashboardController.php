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
        $totalDepartments   = Department::count();
        $totalKaryawan      = User::whereNotIn('role', ['superadmin'])->count();
        $totalBudgetPlan    = AnnualBudget::where('fiscal_year', $year)->sum('total_plan');
        $totalBudgetUsed    = AnnualBudget::where('fiscal_year', $year)->sum('total_used');
        $totalBudgetReserved = AnnualBudget::where('fiscal_year', $year)->sum('total_reserved');
        $totalBudgetSisa    = $totalBudgetPlan - $totalBudgetUsed - $totalBudgetReserved;

        $pctUsed = $totalBudgetPlan > 0
            ? round(($totalBudgetUsed / $totalBudgetPlan) * 100, 1)
            : 0;

        // ── Status Pengajuan Seluruh Perusahaan ──────────────
        $totalPengajuanAktif    = ProposalHarga::whereIn('status', ['Draft', 'In_Review'])->count();
        $totalPengajuanApproved = ProposalHarga::where('status', 'Approved')->count();
        $totalPengajuanRejected = ProposalHarga::where('status', 'Rejected')->count();

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
