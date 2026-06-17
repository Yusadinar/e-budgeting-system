<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'like', '%bram%')->first();
$pendingPhPpbjIds = \App\Models\ProposalHarga::where('status', 'In_Review')
    ->where(function($q) use ($user) {
        $hasCondition = false;
        if ($user->username === 'bramansyah.badar') {
            $q->orWhere('approval_step', 1);
            $hasCondition = true;
        }
        if (!$hasCondition) {
            $q->where('id', 0);
        }
    })
    ->pluck('ppbj_id');

echo "PH Pending PPBJ IDs:\n";
print_r($pendingPhPpbjIds->toArray());

$isKaSiePurchasing = $user->isKaSie() && $user->section === 'Purchasing & Import';
echo "isKaSiePurchasing: " . ($isKaSiePurchasing ? 'true' : 'false') . "\n";
