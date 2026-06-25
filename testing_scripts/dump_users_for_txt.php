<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$users = User::with('department')->get();

$roles = [
    'superadmin' => 'Super Admin',
    'pres_dir' => 'Presiden Direktur',
    'man_dir' => 'Managing Direktur',
    'fin_dir' => 'Finance & HC Direktur',
    'prod_dir' => 'Production Direktur',
    'ka_div' => 'Kepala Divisi (Plant Head)',
    'ka_dept' => 'Kepala Departemen',
    'ka_sie' => 'Kepala Seksi',
    'staff' => 'Staff'
];

$output = "======================================================\n";
$output .= "DAFTAR LENGKAP SEMUA AKUN TESTING E-BUDGETING SYSTEM\n";
$output .= "======================================================\n";
$output .= "* Catatan: Password default seluruh akun di bawah ini adalah: password\n\n";

// Group by role
$groupedUsers = $users->groupBy('role');

foreach ($roles as $roleKey => $roleLabel) {
    if (!isset($groupedUsers[$roleKey]) || $groupedUsers[$roleKey]->isEmpty()) {
        continue;
    }
    
    $output .= "\n[ ". strtoupper($roleLabel) ." ]\n";
    $output .= "------------------------------------------------------\n";
    
    foreach ($groupedUsers[$roleKey] as $user) {
        $deptName = $user->department ? $user->department->dept_name : 'Tidak ada Dept / Pusat';
        $sectionName = $user->section ? $user->section : '-';
        
        $output .= "- Nama   : " . $user->name . "\n";
        $output .= "  Email  : " . $user->email . "\n";
        if (in_array($roleKey, ['ka_dept', 'ka_sie', 'staff', 'ka_div'])) {
            $output .= "  Dept   : " . $deptName . "\n";
        }
        if ($roleKey == 'ka_sie') {
            $output .= "  Seksi  : " . $sectionName . "\n";
        }
        $output .= "\n";
    }
}

file_put_contents(__DIR__ . '/../daftar_akun_testing.txt', $output);

echo "Done.";
