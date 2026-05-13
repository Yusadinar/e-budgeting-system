<?php

use App\Models\InternalAgreement;
use App\Models\ProposalHarga;

$phList = ProposalHarga::with('ppbj.user')->where('status', 'In_Review')->get();
echo "--- PROPOSAL HARGA (In_Review) ---\n";
foreach ($phList as $ph) {
    echo "ID: {$ph->id}, Step: {$ph->approval_step}, Dept: {$ph->ppbj->user->dept_id}\n";
}

$iaList = InternalAgreement::with('proposalHarga.ppbj.user')->where('status_ia', 'In_Review')->get();
echo "\n--- INTERNAL AGREEMENT (In_Review) ---\n";
foreach ($iaList as $ia) {
    echo "ID: {$ia->id}, Step: {$ia->approval_step}, Dept: {$ia->proposalHarga->ppbj->user->dept_id}\n";
}

$user = App\Models\User::where('email', 'kadept.acc@establish.dev')->first();
echo "\nKa Dept Acc Dept ID: " . $user->dept_id . "\n";
