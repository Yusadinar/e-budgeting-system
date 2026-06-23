<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$logs = \App\Models\BudgetLog::where('dept_id', 6)->whereNull('cost_center_id')->get();
echo json_encode($logs, JSON_PRETTY_PRINT);
