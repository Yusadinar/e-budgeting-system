<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$budgets = \App\Models\AnnualBudget::whereNull('cost_center_id')
    ->where(function($q) {
        $q->where('total_used', '>', 0)
          ->orWhere('total_reserved', '>', 0)
          ->orWhere('total_plan', '>', 0);
    })
    ->with('department')
    ->get();

echo json_encode($budgets, JSON_PRETTY_PRINT);
