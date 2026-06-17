<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('role', 'man_dir')->first();
\Illuminate\Support\Facades\Auth::login($user);

$c = app(\App\Http\Controllers\Director\DashboardController::class);
$view = $c->index();
$data = $view->getData()['chartDeptPlan'];
print_r($data->toArray());
