<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$aktif = App\Models\ProposalHarga::where(function ($q) {
    $q->whereIn('status', ['Draft', 'In_Review'])
      ->orWhere(function ($q2) {
          $q2->where('status', 'Approved')
             ->whereDoesntHave('internalAgreement', function ($q3) {
                 $q3->whereIn('status_ia', ['Approved', 'Rejected']);
             });
      });
})->count();

$approved = App\Models\ProposalHarga::whereHas('internalAgreement', function ($q) {
    $q->where('status_ia', 'Approved');
})->count();

$rejected = App\Models\ProposalHarga::where(function ($q) {
    $q->where('status', 'Rejected')
      ->orWhereHas('internalAgreement', function ($q2) {
          $q2->where('status_ia', 'Rejected');
      });
})->count();

echo "Aktif: $aktif, Approved: $approved, Rejected: $rejected\n";
