<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AnnualBudget;

// Zero out all budgets
$budgets = AnnualBudget::all();
foreach($budgets as $b) {
    $b->total_used = 0;
    $b->total_reserved = 0;
    $b->save();
}

// Restore Finance Accounting user transactions
$financeBudget = AnnualBudget::where('dept_id', 6)->whereNull('cost_center_id')->first();
if ($financeBudget) {
    $financeBudget->total_used = 100000000;
    $financeBudget->total_reserved = 40000000;
    $financeBudget->save();
}

echo "Cleaned dummy data and restored Finance actual transactions.\n";
