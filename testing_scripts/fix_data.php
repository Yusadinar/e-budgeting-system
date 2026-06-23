<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AnnualBudget;
use App\Models\BudgetLog;
use App\Models\ProposalHarga;
use App\Models\CostCenter;
use Illuminate\Support\Facades\DB;

DB::transaction(function () {
    $budgets = AnnualBudget::whereNull('cost_center_id')
        ->where(function($q) {
            $q->where('total_used', '>', 0)
              ->orWhere('total_reserved', '>', 0);
        })
        ->get();

    foreach ($budgets as $budget) {
        $firstCC = CostCenter::where('dept_id', $budget->dept_id)->first();
        if ($firstCC) {
            // Update ProposalHargas
            $phs = ProposalHarga::whereNull('cost_center_id')
                ->whereHas('ppbj.user', function($q) use ($budget) {
                    $q->where('dept_id', $budget->dept_id);
                })->get();
            foreach ($phs as $ph) {
                $ph->cost_center_id = $firstCC->id;
                $ph->save();
            }

            // Update BudgetLogs
            $logs = BudgetLog::where('dept_id', $budget->dept_id)
                ->whereNull('cost_center_id')
                ->get();
            foreach ($logs as $log) {
                $log->cost_center_id = $firstCC->id;
                $log->save();
            }

            // Move budget values to the first CC
            $ccBudget = AnnualBudget::firstOrCreate([
                'dept_id' => $budget->dept_id,
                'cost_center_id' => $firstCC->id,
                'fiscal_year' => $budget->fiscal_year,
            ]);

            $ccBudget->total_used += $budget->total_used;
            $ccBudget->total_reserved += $budget->total_reserved;
            $ccBudget->save();

            // Zero out the old one
            $budget->total_used = 0;
            $budget->total_reserved = 0;
            
            if ($budget->total_plan == 0) {
                $budget->delete();
            } else {
                $budget->save();
            }
            
            echo "Migrated Dept ID: {$budget->dept_id} to CC ID: {$firstCC->id}\n";
        }
    }
});
