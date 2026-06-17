<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ccs = \App\Models\CostCenter::whereNull('dept_id')->pluck('department_group')->unique();
print_r($ccs->toArray());
