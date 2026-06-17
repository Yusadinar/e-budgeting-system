<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Department;
use App\Models\AnnualBudget;

$departments = Department::with('costCenters')->get();

foreach ($departments as $dept) {
    // Cari budget level departemen
    $deptBudget = AnnualBudget::where('dept_id', $dept->id)
        ->whereNull('cost_center_id')
        ->first();

    if ($deptBudget && $deptBudget->total_plan > 0 && $dept->costCenters->count() > 0) {
        $amountToDistribute = $deptBudget->total_plan;
        $ccCount = $dept->costCenters->count();
        
        $baseShare = floor($amountToDistribute / $ccCount);
        $remainder = $amountToDistribute - ($baseShare * $ccCount);

        echo "Dept: {$dept->dept_name} | Distributing Rp " . number_format($amountToDistribute, 0, ',', '.') . " to {$ccCount} cost centers.\n";

        foreach ($dept->costCenters as $index => $cc) {
            $share = $baseShare;
            // Add remainder to the first cost center to ensure exact total
            if ($index === 0) {
                $share += $remainder;
            }

            // Find or create budget for this cost center
            $ccBudget = AnnualBudget::where('cost_center_id', $cc->id)
                ->where('fiscal_year', $deptBudget->fiscal_year)
                ->first();

            if ($ccBudget) {
                $ccBudget->total_plan += $share;
                $ccBudget->save();
            } else {
                AnnualBudget::create([
                    'dept_id' => $dept->id,
                    'cost_center_id' => $cc->id,
                    'fiscal_year' => $deptBudget->fiscal_year,
                    'total_plan' => $share,
                    'total_used' => 0,
                    'total_reserved' => 0,
                ]);
            }
        }

        // Zero out the department's direct budget
        $deptBudget->total_plan = 0;
        $deptBudget->save();
    } elseif ($deptBudget && $deptBudget->total_plan > 0 && $dept->costCenters->count() === 0) {
        echo "Dept: {$dept->dept_name} | HAS NO COST CENTERS! Budget Rp " . number_format($deptBudget->total_plan, 0, ',', '.') . " remains on department.\n";
    }
}

echo "Done distributing budgets.\n";
