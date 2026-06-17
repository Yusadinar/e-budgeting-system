<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// SuperAdmin BudgetOverview
$c1 = app(\App\Http\Controllers\SuperAdmin\BudgetOverviewController::class);
$v1 = $c1->index(request());
$data1 = $v1->getData()['departments'];
echo "--- Super Admin ---\n";
foreach($data1 as $d) echo $d['name'] . ': ' . $d['total_plan'] . "\n";

// Director Dashboard
$user = \App\Models\User::where('role', 'man_dir')->first();
\Illuminate\Support\Facades\Auth::login($user);
$c2 = app(\App\Http\Controllers\Director\DashboardController::class);
$v2 = $c2->index();
$data2 = $v2->getData()['chartDeptPlan'];
$labels = $v2->getData()['chartDeptLabels'];
echo "--- Director Dashboard ---\n";
foreach($data2 as $i => $plan) echo $labels[$i] . ': ' . $plan . "\n";

// Director DepartmentController
$c3 = app(\App\Http\Controllers\Director\DepartmentController::class);
$v3 = $c3->index(request());
$data3 = $v3->getData()['departments'];
echo "--- Director Department ---\n";
foreach($data3 as $d) echo $d['name'] . ': ' . $d['plan'] . "\n";
