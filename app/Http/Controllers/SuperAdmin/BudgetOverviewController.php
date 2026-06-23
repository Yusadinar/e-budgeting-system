<?php
// app/Http/Controllers/SuperAdmin/BudgetOverviewController.php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BudgetLog;
use App\Models\Department;
use App\Models\InternalAgreement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BudgetOverviewController extends Controller
{
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
            ->map(function ($dept) {
                $budget = $dept->annualBudgets->where('cost_center_id', null)->first();
                $deptPlan = $budget ? $budget->total_plan : 0;
                $deptUsed = $budget ? $budget->total_used : 0;
                $deptReserved = $budget ? $budget->total_reserved : 0;

                $ccPlan = $dept->costCenters->reduce(fn($c, $cc) => $c + ($cc->annualBudgets->first() ? $cc->annualBudgets->first()->total_plan : 0), 0);
                $ccUsed = $dept->costCenters->reduce(fn($c, $cc) => $c + ($cc->annualBudgets->first() ? $cc->annualBudgets->first()->total_used : 0), 0);
                $ccReserved = $dept->costCenters->reduce(fn($c, $cc) => $c + ($cc->annualBudgets->first() ? $cc->annualBudgets->first()->total_reserved : 0), 0);

                $totalPlan = $deptPlan + $ccPlan;
                $totalUsed = $deptUsed + $ccUsed;
                $totalReserved = $deptReserved + $ccReserved;
                $remaining = $totalPlan - $totalUsed - $totalReserved;
                $utilization = $totalPlan > 0 ? round(($totalUsed / $totalPlan) * 100, 1) : 0;

                return [
                    'id'           => $dept->id,
                    'name'         => $dept->dept_name,
                    'budget_code'  => $dept->budget_code,
                    'users_count'  => $dept->users_count,
                    'total_plan'   => (float) $totalPlan,
                    'total_used'   => (float) $totalUsed,
                    'total_reserved' => (float) $totalReserved,
                    'remaining'    => (float) $remaining,
                    'utilization'  => (float) $utilization,
                ];
            });

        $totalPlan = $departments->sum('total_plan');
        $totalUsed = $departments->sum('total_used');
        $totalReserved = $departments->sum('total_reserved');

        return view('superadmin.budget.index', compact(
            'departments', 'year', 'totalPlan', 'totalUsed', 'totalReserved'
        ));
    }

    public function show(Department $department, Request $request): View
    {
        $year = $request->input('year', now()->year);

        $deptBudget = $department->annualBudgets()->where('cost_center_id', null)->where('fiscal_year', $year)->first();
        
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

        // Realisasi per bulan
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

        // Audit logs with filtering
        $logType = $request->input('log_type');
        $search = $request->input('search');

        $logsQuery = BudgetLog::where('dept_id', $department->id);

        if ($logType) {
            $logsQuery->where('log_type', $logType);
        }

        if ($search) {
            $logsQuery->where(function($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $logs = $logsQuery->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('superadmin.budget.show', compact(
            'department', 'budget', 'year', 'labels', 'dataRealisasi', 'logs'
        ));
    }

    public function exportMaster(Request $request)
    {
        $year = $request->input('year', now()->year);
        
        $departments = Department::orderBy('dept_name')->get();
        
        $costCenters = \App\Models\CostCenter::with(['department'])
            ->orderBy('plant')
            ->orderBy('expense_type')
            ->orderBy('cost_center_code')
            ->get();
            
        $budgets = \App\Models\AnnualBudget::where('fiscal_year', $year)->get();
        
        $writer = new \App\Services\XlsxWriter();
        $writer->setSheetName("Master Budget $year");
        
        $writer->setColumnWidths([30, 15, 40, 10, 15, 20, 20, 20, 20]);
        
        $writer->addRow(['Master Data Budget - Tahun Fiskal ' . $year], 'header');
        $writer->addMerge("A1:I1");
        
        $writer->addRow(array_fill(0, 9, ''));
        
        $headers = [
            'Departemen',
            'CC Code',
            'Cost Center Name',
            'Plant',
            'Kategori',
            'Total Pagu',
            'Realisasi (Terpakai)',
            'Reserved (Hold)',
            'Sisa Budget'
        ];
        $writer->addRow($headers, 'subheader');
        
        $grandTotalPlan = 0;
        $grandTotalUsed = 0;
        $grandTotalReserved = 0;
        $grandTotalRem = 0;
        
        foreach ($departments as $dept) {
            $deptCCs = $costCenters->where('dept_id', $dept->id);
            $deptBudget = $budgets->where('dept_id', $dept->id)->where('cost_center_id', null)->first();
            
            if ($deptBudget && ($deptBudget->total_plan > 0 || $deptBudget->total_used > 0 || $deptBudget->total_reserved > 0)) {
                $rem = $deptBudget->total_plan - $deptBudget->total_used - $deptBudget->total_reserved;
                $writer->addRow([
                    $dept->dept_name,
                    '-',
                    'GLOBAL DEPARTEMEN',
                    '-',
                    '-',
                    (float)$deptBudget->total_plan,
                    (float)$deptBudget->total_used,
                    (float)$deptBudget->total_reserved,
                    (float)$rem
                ], 'data');
                
                $grandTotalPlan += $deptBudget->total_plan;
                $grandTotalUsed += $deptBudget->total_used;
                $grandTotalReserved += $deptBudget->total_reserved;
                $grandTotalRem += $rem;
            }
            
            foreach ($deptCCs as $cc) {
                $ccBudget = $budgets->where('cost_center_id', $cc->id)->first();
                $plan = $ccBudget ? (float)$ccBudget->total_plan : 0;
                $used = $ccBudget ? (float)$ccBudget->total_used : 0;
                $res = $ccBudget ? (float)$ccBudget->total_reserved : 0;
                
                $rem = $plan - $used - $res;
                $writer->addRow([
                    $dept->dept_name,
                    $cc->cost_center_code ?? 'N/A',
                    $cc->cost_center_name ?? 'N/A',
                    $cc->plant ?? 'N/A',
                    $cc->expense_type ?? 'N/A',
                    $plan,
                    $used,
                    $res,
                    $rem
                ], 'data');
                
                $grandTotalPlan += $plan;
                $grandTotalUsed += $used;
                $grandTotalReserved += $res;
                $grandTotalRem += $rem;
            }
        }
        
        $writer->addRow([
            'GRAND TOTAL',
            '',
            '',
            '',
            '',
            $grandTotalPlan,
            $grandTotalUsed,
            $grandTotalReserved,
            $grandTotalRem
        ], 'highlight');
        
        return $writer->download("Master_Budget_{$year}.xlsx");
    }
}
