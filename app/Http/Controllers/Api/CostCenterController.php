<?php
// app/Http/Controllers/Api/CostCenterController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CostCenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    /**
     * Return daftar Cost Center berdasarkan filter:
     * - department_group (opsional)
     * - plant (opsional)
     * - expense_type (opsional)
     * 
     * Digunakan untuk mengisi cascading dropdown di form pengajuan.
     */
    public function index(Request $request): JsonResponse
    {
        $query = CostCenter::query();

        if ($request->filled('department_group')) {
            $query->where('department_group', $request->department_group);
        }

        if ($request->filled('plant')) {
            $query->where('plant', $request->plant);
        }

        if ($request->filled('expense_type')) {
            $query->where('expense_type', $request->expense_type);
        }

        $costCenters = $query->orderBy('cost_center_name')
            ->get()
            ->map(fn($cc) => [
                'id'    => $cc->id,
                'code'  => $cc->cost_center_code,
                'name'  => $cc->cost_center_name,
                'label' => $cc->dropdown_label,
                'plant' => $cc->plant,
                'type'  => $cc->expense_type,
                'dept'  => $cc->department_group,
            ]);

        return response()->json($costCenters);
    }

    /**
     * Return daftar department_group yang unik (untuk dropdown pertama).
     */
    public function departments(): JsonResponse
    {
        $groups = CostCenter::select('department_group')
            ->distinct()
            ->orderBy('department_group')
            ->pluck('department_group');

        return response()->json($groups);
    }

    /**
     * Return budget info untuk satu cost center (by ID).
     */
    public function budgetInfo(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|exists:cost_centers,id']);

        $cc = CostCenter::find($request->id);
        $budget = \App\Models\AnnualBudget::where('cost_center_id', $cc->id)
            ->where('fiscal_year', now()->year)
            ->first();

        if (!$budget) {
            return response()->json([
                'plan'      => 0,
                'used'      => 0,
                'reserved'  => 0,
                'remaining' => 0,
            ]);
        }

        return response()->json([
            'plan'      => $budget->total_plan,
            'used'      => $budget->total_used,
            'reserved'  => $budget->total_reserved,
            'remaining' => $budget->remaining,
            'plan_formatted'      => 'Rp ' . number_format($budget->total_plan, 0, ',', '.'),
            'used_formatted'      => 'Rp ' . number_format($budget->total_used, 0, ',', '.'),
            'remaining_formatted' => 'Rp ' . number_format($budget->remaining, 0, ',', '.'),
        ]);
    }
}
