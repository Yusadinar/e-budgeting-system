<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('name', 'like', '%yusa%')->first();
echo 'user_id: ' . $user->id . ', dept_id: ' . var_export($user->dept_id, true) . ', role: ' . $user->role . "\n";
