<?php
// app/Http/Controllers/SuperAdmin/CostCenterController.php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AnnualBudget;
use App\Models\CostCenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CostCenterController extends Controller
{
    public function index(Request $request): View
    {
        $query = CostCenter::query();

        if ($request->filled('plant')) {
            $query->where('plant', $request->plant);
        }
        if ($request->filled('expense_type')) {
            $query->where('expense_type', $request->expense_type);
        }
        if ($request->filled('department_group')) {
            $query->where('department_group', $request->department_group);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cost_center_name', 'like', "%{$search}%")
                  ->orWhere('cost_center_code', 'like', "%{$search}%");
            });
        }

        $costCenters = $query->orderBy('department_group')
            ->orderBy('plant')
            ->orderBy('cost_center_name')
            ->paginate(25)
            ->withQueryString();

        // Stats
        $totalCostCenters = CostCenter::count();
        $deptGroups = CostCenter::select('department_group')->distinct()->pluck('department_group');
        $plants = ['IBEK', 'IKAR', 'HO'];

        return view('superadmin.cost-centers.index', compact(
            'costCenters', 'totalCostCenters', 'deptGroups', 'plants'
        ));
    }

    public function create(): View
    {
        $deptGroups = CostCenter::select('department_group')->distinct()->orderBy('department_group')->pluck('department_group');
        return view('superadmin.cost-centers.create', compact('deptGroups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'department_group'  => ['required', 'string', 'max:255'],
            'plant'             => ['required', 'in:IBEK,IKAR,HO'],
            'expense_type'      => ['required', 'in:FOH,OPEX'],
            'cost_center_code'  => ['nullable', 'string', 'max:50', 'unique:cost_centers,cost_center_code'],
            'cost_center_name'  => ['required', 'string', 'max:255'],
            'initial_budget'    => ['nullable', 'numeric', 'min:0'],
        ]);

        $cc = CostCenter::create([
            'department_group'  => $validated['department_group'],
            'plant'             => $validated['plant'],
            'expense_type'      => $validated['expense_type'],
            'cost_center_code'  => $validated['cost_center_code'],
            'cost_center_name'  => $validated['cost_center_name'],
        ]);

        // Buat annual budget jika diminta
        if (!empty($validated['initial_budget']) && $validated['initial_budget'] > 0) {
            AnnualBudget::create([
                'cost_center_id' => $cc->id,
                'fiscal_year'    => now()->year,
                'total_plan'     => $validated['initial_budget'],
                'total_used'     => 0,
                'total_reserved' => 0,
            ]);
        }

        return redirect()->route('superadmin.cost-centers.index')
            ->with('success', "Cost Center '{$cc->cost_center_name}' berhasil ditambahkan.");
    }

    public function edit(CostCenter $costCenter): View
    {
        $deptGroups = CostCenter::select('department_group')->distinct()->orderBy('department_group')->pluck('department_group');
        $budget = $costCenter->annualBudgets()->where('fiscal_year', now()->year)->first();
        return view('superadmin.cost-centers.edit', compact('costCenter', 'deptGroups', 'budget'));
    }

    public function update(Request $request, CostCenter $costCenter): RedirectResponse
    {
        $validated = $request->validate([
            'department_group'  => ['required', 'string', 'max:255'],
            'plant'             => ['required', 'in:IBEK,IKAR,HO'],
            'expense_type'      => ['required', 'in:FOH,OPEX'],
            'cost_center_code'  => ['nullable', 'string', 'max:50', 'unique:cost_centers,cost_center_code,'.$costCenter->id],
            'cost_center_name'  => ['required', 'string', 'max:255'],
            'budget_plan'       => ['nullable', 'numeric', 'min:0'],
        ]);

        $costCenter->update([
            'department_group'  => $validated['department_group'],
            'plant'             => $validated['plant'],
            'expense_type'      => $validated['expense_type'],
            'cost_center_code'  => $validated['cost_center_code'],
            'cost_center_name'  => $validated['cost_center_name'],
        ]);

        // Update/create budget jika diminta
        if (isset($validated['budget_plan'])) {
            AnnualBudget::updateOrCreate(
                ['cost_center_id' => $costCenter->id, 'fiscal_year' => now()->year],
                ['total_plan' => $validated['budget_plan']]
            );
        }

        return redirect()->route('superadmin.cost-centers.index')
            ->with('success', "Cost Center '{$costCenter->cost_center_name}' berhasil diperbarui.");
    }

    public function destroy(CostCenter $costCenter): RedirectResponse
    {
        $name = $costCenter->cost_center_name;
        $costCenter->delete();

        return redirect()->route('superadmin.cost-centers.index')
            ->with('success', "Cost Center '{$name}' berhasil dihapus.");
    }
}
