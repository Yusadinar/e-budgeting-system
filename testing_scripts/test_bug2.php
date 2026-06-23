<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ccs = \App\Models\CostCenter::where('dept_id', 6)->get();
echo json_encode($ccs, JSON_PRETTY_PRINT);
