<?php
use App\Models\Department;
use App\Models\User;
use App\Models\AnnualBudget;
use App\Models\ProposalHarga;
use App\Models\InternalAgreement;

$itDept = Department::where('dept_name', 'LIKE', '%IT%')->first();
$staffIt = User::where('email', 'staff.it@establish.dev')->first();
$year = now()->year;

$budget = AnnualBudget::where('dept_id', $itDept->id)->where('fiscal_year', $year)->first();

// Personal Staff IT
$personalBerjalan = ProposalHarga::whereHas('ppbj', fn($q) => $q->where('user_id', $staffIt->id))
    ->whereIn('status', ['Draft', 'In_Review'])
    ->sum('nominal_request');

$personalSelesai = InternalAgreement::whereHas('proposalHarga.ppbj', fn($q) => $q->where('user_id', $staffIt->id))
    ->where('status_ia', 'Approved')
    ->sum('final_nominal');

echo "IT_DEPT_ID: " . $itDept->id . "\n";
echo "STAFF_IT_ID: " . $staffIt->id . "\n";
echo "PAGU: " . $budget->total_plan . "\n";
echo "USED: " . $budget->total_used . "\n";
echo "RESERVED: " . $budget->total_reserved . "\n";
echo "REMAINING: " . $budget->remaining . "\n";
echo "PERSONAL_BERJALAN: " . $personalBerjalan . "\n";
echo "PERSONAL_SELESAI: " . $personalSelesai . "\n";
