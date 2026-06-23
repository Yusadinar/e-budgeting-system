<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\AnnualBudget;
use App\Models\Department;

$year = now()->year;

echo "=== METODE BARU (GROUP BY per dept) ===\n\n";

// Dept-level aggregation
$deptTotals = AnnualBudget::where('fiscal_year', $year)
    ->whereNotNull('dept_id')
    ->whereNull('cost_center_id')
    ->selectRaw('dept_id, SUM(total_plan) as plan, SUM(total_used) as used, SUM(total_reserved) as reserved')
    ->groupBy('dept_id')
    ->get();

// CC-level aggregation grouped by dept
$ccTotals = AnnualBudget::where('fiscal_year', $year)
    ->whereNotNull('cost_center_id')
    ->join('cost_centers', 'cost_centers.id', '=', 'annual_budgets.cost_center_id')
    ->selectRaw('cost_centers.dept_id, SUM(annual_budgets.total_plan) as plan, SUM(annual_budgets.total_used) as used, SUM(annual_budgets.total_reserved) as reserved')
    ->groupBy('cost_centers.dept_id')
    ->get();

$totalPlan     = $deptTotals->sum('plan')     + $ccTotals->sum('plan');
$totalUsed     = $deptTotals->sum('used')     + $ccTotals->sum('used');
$totalReserved = $deptTotals->sum('reserved') + $ccTotals->sum('reserved');

echo "Total Plan     : Rp " . number_format($totalPlan, 0, ',', '.') . "\n";
echo "Total Used     : Rp " . number_format($totalUsed, 0, ',', '.') . "\n";
echo "Total Reserved : Rp " . number_format($totalReserved, 0, ',', '.') . " (" . number_format($totalReserved/1_000_000_000, 4, ',', '.') . " M)\n";

echo "\n=== Per Departemen (reserved > 0) ===\n";

// Gabungkan dept aggregates dengan nama dept
$deptMap = Department::pluck('dept_name', 'id');

foreach ($deptTotals as $r) {
    if ($r->reserved > 0) {
        $deptName = $deptMap[$r->dept_id] ?? '(dept_id=' . $r->dept_id . ')';
        echo "  DEPT: " . $deptName . " -> reserved = Rp " . number_format($r->reserved, 0, ',', '.') . "\n";
    }
}
foreach ($ccTotals as $r) {
    if ($r->reserved > 0) {
        $deptName = $deptMap[$r->dept_id] ?? '(dept_id=' . $r->dept_id . ')';
        echo "  CC:   " . $deptName . " (via CC) -> reserved = Rp " . number_format($r->reserved, 0, ',', '.') . "\n";
    }
}

echo "\nExpected: Finance Accounting = Rp 40.000.000, Manufacturing = Rp 49.000.000\n";
echo "Grand Total Reserved Expected: Rp 89.000.000 = 0,09 M\n";
echo "\nNote: 49 juta dari Manufacturing kemungkinan adalah DUPLIKAT/SALAH INPUT.\n";
echo "Verifikasi: apakah ada pengajuan aktif dari Manufacturing yang menyebabkan reserve 49 juta?\n";

// Cek ppbj/proposal harga dari Manufacturing yang masih aktif
$mfcDept = Department::where('dept_name', 'Manufacturing')->first();
if ($mfcDept) {
    $count = \App\Models\ProposalHarga::whereHas('ppbj.user', fn($q) => $q->where('dept_id', $mfcDept->id))
        ->whereIn('status', ['Draft', 'In_Review'])
        ->count();
    echo "\nManufacturing pengajuan aktif: " . $count . " pengajuan\n";
    
    // Cek IA yang masih In_Review
    $iaCount = \App\Models\InternalAgreement::whereHas('proposalHarga.ppbj.user', fn($q) => $q->where('dept_id', $mfcDept->id))
        ->where('status_ia', 'In_Review')
        ->count();
    echo "Manufacturing IA In_Review: " . $iaCount . "\n";
}
