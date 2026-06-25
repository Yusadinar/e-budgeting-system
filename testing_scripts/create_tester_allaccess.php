<?php
/**
 * Script untuk membuat akun tester all-access.
 * Jalankan: php artisan tinker < testing_scripts/create_tester_allaccess.php
 * Atau: php testing_scripts/create_tester_allaccess.php (dari root project)
 */

// Bootstrap Laravel
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

echo "\n=== CREATE TESTER ALL-ACCESS ACCOUNT ===\n\n";

// Gunakan departemen Finance Accounting agar bisa akses budget
$accDept = Department::where('budget_code', 'IKAR-ACC-006')->first();

if (!$accDept) {
    echo "⚠ Department Finance Accounting tidak ditemukan!\n";
    echo "  Mencari departemen pertama yang tersedia...\n";
    $accDept = Department::first();
}

echo "📁 Department: {$accDept->dept_name} (ID: {$accDept->id})\n";

// Cek apakah akun sudah ada
$existing = User::where('email', 'tester.allaccess@establish.dev')->first();

if ($existing) {
    echo "ℹ Akun tester sudah ada (ID: {$existing->id}). Memperbarui...\n";
    $existing->update([
        'name'     => 'Tester All-Access',
        'dept_id'  => $accDept->id,
        'role'     => 'ka_sie',  // Ka. Sie agar canApprove() true secara normal
        'section'  => 'All Access Testing',
        'password' => Hash::make('password'),
    ]);
    $user = $existing;
} else {
    $user = User::create([
        'name'              => 'Tester All-Access',
        'email'             => 'tester.allaccess@establish.dev',
        'password'          => Hash::make('password'),
        'role'              => 'ka_sie',  // Ka. Sie agar canApprove() true secara normal
        'dept_id'           => $accDept->id,
        'section'           => 'All Access Testing',
        'email_verified_at' => now(),
    ]);
    echo "✅ Akun baru berhasil dibuat (ID: {$user->id})\n";
}

echo "\n";
echo "╔═══════════════════════════════════════════════╗\n";
echo "║       AKUN TESTER ALL-ACCESS                  ║\n";
echo "╠═══════════════════════════════════════════════╣\n";
echo "║  Email    : tester.allaccess@establish.dev    ║\n";
echo "║  Password : password                          ║\n";
echo "║  Username : tester.allaccess                  ║\n";
echo "║  Role     : ka_sie (All Access Testing)       ║\n";
echo "║  Dept     : {$accDept->dept_name}             \n";
echo "╠═══════════════════════════════════════════════╣\n";
echo "║  AKSES:                                       ║\n";
echo "║  ✓ Buat PPBJ (sebagai staff/ka_sie)           ║\n";
echo "║  ✓ Approve PPBJ (semua step 1-5)              ║\n";
echo "║  ✓ Buat Proposal Harga (PH)                   ║\n";
echo "║  ✓ Approve PH (semua step 1-6)                ║\n";
echo "║  ✓ Buat Internal Agreement (IA)               ║\n";
echo "║  ✓ Approve IA (semua step 1-6)                ║\n";
echo "╚═══════════════════════════════════════════════╝\n";
echo "\n";
