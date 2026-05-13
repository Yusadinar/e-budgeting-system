<?php
// app/Http/Controllers/SuperAdmin/DashboardController.php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AnnualBudget;
use App\Models\Department;
use App\Models\InternalAgreement;
use App\Models\ProposalHarga;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $year = now()->year;

        // ── Statistik Global ─────────────────────────────────
        $totalUsers       = User::where('role', '!=', 'superadmin')->count();
        $totalDepartments = Department::count();
        $totalBudgetPlan  = AnnualBudget::where('fiscal_year', $year)->sum('total_plan');
        $totalBudgetUsed  = AnnualBudget::where('fiscal_year', $year)->sum('total_used');
        $totalPengajuanAktif = ProposalHarga::whereIn('status', ['Draft', 'In_Review'])->count();

        // ── Budget per Departemen (untuk bar chart) ──────────
        $deptBudgets = Department::with(['currentBudget'])
            ->get()
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
                    'plan'       => (float) ($dept->currentBudget?->total_plan ?? 0),
                    'used'       => (float) ($dept->currentBudget?->total_used ?? 0),
                    'reserved'   => (float) ($dept->currentBudget?->total_reserved ?? 0),
                ];
            });

        $chartDeptLabels   = $deptBudgets->pluck('short_name')->values();
        $chartDeptPlan     = $deptBudgets->pluck('plan')->values();
        $chartDeptUsed     = $deptBudgets->pluck('used')->values();

        // ── Tren Realisasi Bulanan (line chart) ──────────────
        $realisasiBulanan = InternalAgreement::where('status_ia', 'Approved')
            ->whereYear('updated_at', $year)
            ->selectRaw('MONTH(updated_at) as bulan, SUM(final_nominal) as total')
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $labels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $dataRealisasi = [];
        for ($m = 1; $m <= 12; $m++) {
            $dataRealisasi[] = (float) ($realisasiBulanan[$m] ?? 0);
        }

        // ── Recent Activities ────────────────────────────────
        $recentActivities = ProposalHarga::with(['ppbj.user.department'])
            ->orderByDesc('updated_at')
            ->take(10)
            ->get();

        // ── Departemen dengan utilisasi tinggi (>80%) ────────
        $highUtilDepts = $deptBudgets->filter(fn ($d) => $d['plan'] > 0 && (($d['used'] / $d['plan']) * 100) >= 80);

        return view('superadmin.dashboard', compact(
            'year',
            'totalUsers',
            'totalDepartments',
            'totalBudgetPlan',
            'totalBudgetUsed',
            'totalPengajuanAktif',
            'chartDeptLabels',
            'chartDeptPlan',
            'chartDeptUsed',
            'labels',
            'dataRealisasi',
            'recentActivities',
            'highUtilDepts',
        ));
    }
}
