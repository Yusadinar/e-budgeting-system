<?php
// app/Http/Controllers/BudgetController.php

namespace App\Http\Controllers;

use App\Models\AnnualBudget;
use App\Models\BudgetLog;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BudgetController extends Controller
{
    /**
     * Halaman input budget (khusus Ka.Dept)
     */
    public function create(): View
    {
        abort_if(! Auth::user()->isKaDept(), 403, 'Hanya Kepala Departemen yang bisa mengakses.');

        $user       = Auth::user();
        $department = $user->department;
        $year       = now()->year;

        $budget = AnnualBudget::where('dept_id', $department->id)
            ->where('fiscal_year', $year)
            ->first();

        return view('budget.create', compact('department', 'budget', 'year'));
    }

    /**
     * Simpan/Update budget department
     */
    public function store(Request $request): RedirectResponse
    {
        abort_if(! Auth::user()->isKaDept(), 403);

        $validated = $request->validate([
            'fiscal_year'     => ['required', 'integer', 'min:2020', 'max:2099'],
            'total_plan'      => ['required', 'numeric', 'min:0'],
            'adjustment_type' => ['required', 'in:new,increase'],
            'notes'           => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();
        $dept = $user->department;

        DB::transaction(function () use ($validated, $dept) {
            $budget = AnnualBudget::firstOrCreate(
                [
                    'dept_id'     => $dept->id,
                    'fiscal_year' => $validated['fiscal_year'],
                ],
                [
                    'total_plan'     => 0,
                    'total_used'     => 0,
                    'total_reserved' => 0,
                ]
            );

            $oldPlan = $budget->total_plan;
            $newPlan = (float) $validated['total_plan'];

            if ($validated['adjustment_type'] === 'increase') {
                // Tambah pagu
                $budget->increment('total_plan', $newPlan);
                $finalPlan = $budget->fresh()->total_plan;

                BudgetLog::record(
                    deptId:      $dept->id,
                    referenceNo: 'BUDGET-ADJ-' . now()->format('YmdHis'),
                    amount:      $newPlan,
                    logType:     'increase',
                    description: "Penambahan pagu anggaran: " . ($validated['notes'] ?? 'Adjustment by Ka.Dept'),
                );
            } else {
                // Set ulang pagu
                $budget->update(['total_plan' => $newPlan]);
                $finalPlan = $newPlan;

                BudgetLog::record(
                    deptId:      $dept->id,
                    referenceNo: 'BUDGET-NEW-' . now()->format('YmdHis'),
                    amount:      $newPlan,
                    logType:     'increase',
                    description: "Penetapan pagu anggaran baru FY{$validated['fiscal_year']}: " . ($validated['notes'] ?? 'Set by Ka.Dept'),
                );
            }
        });

        return redirect()->route('dashboard')
            ->with('success', 'Budget berhasil diperbarui.');
    }
}