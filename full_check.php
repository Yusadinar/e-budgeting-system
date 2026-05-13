<?php
use App\Models\InternalAgreement;
use App\Models\ProposalHarga;

$ias = InternalAgreement::whereHas('proposalHarga.ppbj.user', fn($u) => $u->where('dept_id', 1))->get();
echo "--- IA DEPT 1 ---\n";
foreach ($ias as $ia) {
    echo "ID: {$ia->id} | Status: {$ia->status_ia} | Nominal: {$ia->final_nominal}\n";
}

$phs = ProposalHarga::whereHas('ppbj.user', fn($u) => $u->where('dept_id', 1))
    ->whereDoesntHave('internalAgreement')
    ->get();
echo "\n--- PH DEPT 1 (NO IA) ---\n";
foreach ($phs as $ph) {
    echo "ID: {$ph->id} | Status: {$ph->status} | Nominal: {$ph->nominal_request}\n";
}
