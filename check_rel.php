<?php
use App\Models\ProposalHarga;
use App\Models\InternalAgreement;

$phs = ProposalHarga::whereHas('ppbj.user', fn($u) => $u->where('dept_id', 1))->get();
echo "--- PH RELATIONSHIPS ---\n";
foreach ($phs as $ph) {
    $ia = InternalAgreement::where('ph_id', $ph->id)->first();
    echo "PH ID: {$ph->id} | Status: {$ph->status} | Nominal: {$ph->nominal_request} | Has IA: " . ($ia ? "YES (ID: {$ia->id}, Status: {$ia->status_ia}, Nominal: {$ia->final_nominal})" : "NO") . "\n";
}
