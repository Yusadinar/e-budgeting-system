<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BudgetLog;
use App\Models\InternalAgreement;

// Cari IA yang baru dibuat
$ia = InternalAgreement::with('proposalHarga')->latest()->first();

if ($ia) {
    // Cek apakah log release (increase) untuk IA ini sudah ada
    $exists = BudgetLog::where('reference_no', $ia->ia_number)
                       ->where('log_type', 'increase')
                       ->exists();
                       
    if (!$exists) {
        $ph = $ia->proposalHarga;
        $deptId = $ph->ppbj->user->dept_id;
        
        BudgetLog::create([
            'dept_id' => $deptId,
            'reference_no' => $ia->ia_number,
            'amount' => $ph->nominal_request,
            'log_type' => 'increase',
            'description' => "Release hold PH ({$ph->ph_number}) untuk penyesuaian IA",
        ]);
        
        echo "Log release berhasil ditambahkan untuk IA: {$ia->ia_number}\n";
    } else {
        echo "Log release sudah ada.\n";
    }
} else {
    echo "IA tidak ditemukan.\n";
}
