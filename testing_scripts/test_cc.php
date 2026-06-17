<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$costCenterBudgets = \App\Models\CostCenter::with(['currentBudget', 'department'])
    ->get()
    ->map(function ($cc) {
        $plan = (float)($cc->currentBudget?->total_plan ?? 0);
        $used = (float)($cc->currentBudget?->total_used ?? 0);
        $reserved = (float)($cc->currentBudget?->total_reserved ?? 0);
        $sisa = $plan - $used - $reserved;
        $utilization = $plan > 0 ? round(($used / $plan) * 100, 1) : 0;

        return [
            'id' => $cc->id,
            'name' => $cc->cost_center_name,
            'dept_name' => $cc->department?->dept_name ?? '-',
            'plan' => $plan,
            'used' => $used,
            'reserved' => $reserved,
            'sisa' => $sisa,
            'utilization' => $utilization,
        ];
    })
    ->filter(fn($d) => $d['plan'] > 0)
    ->sortByDesc('utilization')
    ->values();

echo "Count: " . $costCenterBudgets->count() . "\n";
print_r($costCenterBudgets->first());
