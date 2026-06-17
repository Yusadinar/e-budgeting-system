<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CostCenter;
use App\Models\Department;
use App\Models\AnnualBudget;
use Illuminate\Support\Facades\Artisan;

echo "Running seeders to reset budgets...\n";
Artisan::call('db:seed', ['--class' => 'AnnualBudgetSeeder']);
Artisan::call('db:seed', ['--class' => 'CostCenterSeeder']);

echo "Mapping Cost Centers...\n";
$mapping = [
    'Quality' => 'Quality Management System',
    'Quality Assurance' => 'Quality Management System',
    'PPLC' => 'Production Planning & Logistic Control',
    'GA' => 'Human Capital & General Services',
    'Human Resource' => 'Human Capital & General Services',
    'HRGA' => 'Human Capital & General Services',
    'Purchasing' => 'Procurement',
    'FIN & ACC' => 'Finance Accounting',
];

$ccs = CostCenter::all();
foreach ($ccs as $cc) {
    $deptName = $mapping[$cc->department_group] ?? $cc->department_group;
    
    // Khusus untuk IKAR
    if ($cc->plant === 'IKAR') {
        if ($deptName === 'Manufacturing') {
            $deptName = 'Manufacturing IKAR';
        } elseif ($deptName === 'Maintenance') {
            $deptName = 'Maintenance IKAR';
        }
    }

    $dept = Department::where('dept_name', $deptName)->first();
    if ($dept) {
        $cc->dept_id = $dept->id;
        $cc->save();
    } else {
        echo "Could not find department for: {$deptName}\n";
    }
}
echo "Cost Centers mapped.\n";

echo "Distributing Budgets...\n";
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
            if ($index === 0) {
                $share += $remainder;
            }

            $ccBudget = AnnualBudget::where('cost_center_id', $cc->id)
                ->where('fiscal_year', $deptBudget->fiscal_year)
                ->first();

            if ($ccBudget) {
                $ccBudget->total_plan += $share;
                $ccBudget->save();
            }
        }

        // Zero out the department's direct budget
        $deptBudget->total_plan = 0;
        $deptBudget->save();
    } elseif ($deptBudget && $deptBudget->total_plan > 0 && $dept->costCenters->count() === 0) {
        echo "Dept: {$dept->dept_name} | HAS NO COST CENTERS! Budget Rp " . number_format($deptBudget->total_plan, 0, ',', '.') . " remains on department.\n";
    }
}
echo "All done.\n";
