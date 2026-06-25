<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Tambahkan 'ka_sie' ke enum role di users table
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('staff', 'ka_sie', 'ka_dept', 'ka_div', 'accounting', 'superadmin', 'ka_dept_acc', 'ka_div_acc', 'fin_dir', 'man_dir', 'pres_dir') DEFAULT 'staff'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Rollback: kembalikan user ka_sie ke staff dulu, lalu hapus enum
            DB::statement("UPDATE users SET role = 'staff' WHERE role = 'ka_sie'");
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('staff', 'ka_dept', 'ka_div', 'accounting', 'superadmin', 'ka_dept_acc', 'ka_div_acc', 'fin_dir', 'man_dir', 'pres_dir') DEFAULT 'staff'");
        }
    }
};
