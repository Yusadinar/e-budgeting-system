<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ph = App\Models\ProposalHarga::find(12);
if ($ph) {
    $budget = App\Models\AnnualBudget::where('cost_center_id', $ph->cost_center_id)->first();
    if ($budget) {
        $budget->decrement('total_reserved', $ph->nominal_request);
        echo "Decremented budget by " . $ph->nominal_request . "\n";
    }
    $ph->delete();
    echo "Deleted PH 12\n";
} else {
    echo "PH 12 not found\n";
}
