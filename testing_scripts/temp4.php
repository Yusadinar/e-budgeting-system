<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ppbjAktif = App\Models\Ppbj::where(function ($q) {
    $q->whereIn('status', ['Draft', 'In_Review'])
      ->orWhere(function ($q2) {
          $q2->where('status', 'Approved')
             ->whereDoesntHave('proposalHarga', function ($q3) {
                 $q3->whereHas('internalAgreement', function ($q4) {
                     $q4->whereIn('status_ia', ['Approved', 'Rejected']);
                 })->orWhere('status', 'Rejected');
             });
      });
})->count();

$ppbjApproved = App\Models\Ppbj::whereHas('proposalHarga.internalAgreement', function ($q) {
    $q->where('status_ia', 'Approved');
})->count();

$ppbjRejected = App\Models\Ppbj::where(function ($q) {
    $q->where('status', 'Rejected')
      ->orWhereHas('proposalHarga', function ($q2) {
          $q2->where('status', 'Rejected')
             ->orWhereHas('internalAgreement', function ($q3) {
                 $q3->where('status_ia', 'Rejected');
             });
      });
})->count();

echo "PPBJ Aktif: $ppbjAktif, Approved: $ppbjApproved, Rejected: $ppbjRejected\n";
