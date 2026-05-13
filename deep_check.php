<?php
use App\Models\Ppbj;
use App\Models\ProposalHarga;
use App\Models\InternalAgreement;

$ppbjList = Ppbj::whereHas('user', fn($u) => $u->where('dept_id', 1))->get();
echo "--- PPBJ DEPT 1 ---\n";
foreach ($ppbjList as $ppbj) {
    echo "ID: {$ppbj->id} | Status: {$ppbj->status} | Nominal: {$ppbj->nominal_estimate}\n";
}

$phList = ProposalHarga::whereHas('ppbj.user', fn($u) => $u->where('dept_id', 1))->get();
echo "\n--- PH DEPT 1 ---\n";
foreach ($phList as $ph) {
    echo "ID: {$ph->id} | Status: {$ph->status} | Nominal: {$ph->nominal_request}\n";
}

$iaList = InternalAgreement::whereHas('proposalHarga.ppbj.user', fn($u) => $u->where('dept_id', 1))->get();
echo "\n--- IA DEPT 1 ---\n";
foreach ($iaList as $ia) {
    echo "ID: {$ia->id} | Status: {$ia->status_ia} | Nominal: {$ia->final_nominal}\n";
}
