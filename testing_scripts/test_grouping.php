<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sectionsByDept = App\Models\User::where('role', 'ka_sie')
    ->whereNotNull('section')
    ->whereNotNull('dept_id')
    ->get()
    ->groupBy('dept_id')
    ->map(function($users) {
        return $users->pluck('section')->unique()->values()->all();
    });

echo json_encode($sectionsByDept);
