<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ccs = \App\Models\CostCenter::where('dept_id', 6)->pluck('id');
$budgets = \App\Models\AnnualBudget::whereIn('cost_center_id', $ccs)->get();
echo json_encode($budgets, JSON_PRETTY_PRINT);
