<?php
use App\Models\User;
use App\Models\ProposalHarga;
use App\Models\InternalAgreement;

$user = User::where('email', 'staff.it@establish.dev')->first();

$phBelumApproved = ProposalHarga::whereHas('ppbj', function ($q) use ($user) {
        $q->where('user_id', $user->id);
    })
    ->whereIn('status', ['Draft', 'In_Review'])
    ->sum('nominal_request');

$iaBelumApproved = InternalAgreement::whereHas('proposalHarga.ppbj', function ($q) use ($user) {
        $q->where('user_id', $user->id);
    })
    ->whereIn('status_ia', ['Draft', 'In_Review'])
    ->sum('final_nominal');

$phTanpaIa = ProposalHarga::whereHas('ppbj', function ($q) use ($user) {
        $q->where('user_id', $user->id);
    })
    ->where('status', 'Approved')
    ->whereDoesntHave('internalAgreement')
    ->sum('nominal_request');

$totalPengajuanBerjalan = $phBelumApproved + $iaBelumApproved + $phTanpaIa;

echo "PH Belum Approved: $phBelumApproved\n";
echo "IA Belum Approved: $iaBelumApproved\n";
echo "PH Tanpa IA: $phTanpaIa\n";
echo "Total Berjalan: $totalPengajuanBerjalan\n";
