<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AnnualBudget;
use App\Models\ProposalHarga;
use App\Models\InternalAgreement;

$budgets = AnnualBudget::all();
foreach ($budgets as $budget) {
    $deptId = $budget->dept_id;
    
    // Sum of all pending PH that do NOT have an IA yet
    $pendingPhSum = ProposalHarga::whereHas('ppbj.user', function($q) use ($deptId) {
            $q->where('dept_id', $deptId);
        })
        ->where('status', 'In_Review')
        ->whereDoesntHave('internalAgreement') // Hanya PH yang belum berlanjut ke IA
        ->sum('nominal_request');
        
    // Sum of all pending IA
    $pendingIaSum = InternalAgreement::whereHas('proposalHarga.ppbj.user', function($q) use ($deptId) {
            $q->where('dept_id', $deptId);
        })
        ->where('status_ia', 'In_Review')
        ->sum('final_nominal');
        
    $actualReserved = $pendingPhSum + $pendingIaSum;
    
    if ($budget->total_reserved != $actualReserved) {
        echo "Dept ID {$deptId} - Memperbaiki hold budget dari " . $budget->total_reserved . " menjadi " . $actualReserved . "\n";
        $budget->total_reserved = $actualReserved;
        $budget->save();
    }
}
echo "Koreksi data berhasil.\n";
