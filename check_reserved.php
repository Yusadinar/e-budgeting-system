<?php
use App\Models\AnnualBudget;
use App\Models\ProposalHarga;

$budget = AnnualBudget::where('dept_id', 1)->where('fiscal_year', 2026)->first();
$pendingPh = ProposalHarga::whereHas('ppbj', fn($q) => $q->whereHas('user', fn($u) => $u->where('dept_id', 1)))
    ->whereIn('status', ['Draft', 'In_Review'])
    ->get();

echo "RESERVED_IN_TABLE: " . $budget->total_reserved . "\n";
echo "SUM_OF_PH: " . $pendingPh->sum('nominal_request') . "\n";
foreach ($pendingPh as $ph) {
    echo "PH_ID: " . $ph->id . " | Subject: " . $ph->subject . " | Nominal: " . $ph->nominal_request . " | Status: " . $ph->status . " | User: " . $ph->ppbj->user->name . "\n";
}
