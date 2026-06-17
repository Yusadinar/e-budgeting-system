<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing users to fallback roles
        DB::statement("UPDATE users SET role = 'ka_dept' WHERE role = 'ka_dept_acc'");
        DB::statement("UPDATE users SET role = 'ka_div' WHERE role = 'ka_div_acc'");

        // Alter enum to remove 'ka_dept_acc' and 'ka_div_acc'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('staff', 'ka_sie', 'ka_dept', 'ka_div', 'accounting', 'superadmin', 'fin_dir', 'man_dir', 'pres_dir') DEFAULT 'staff'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('staff', 'ka_sie', 'ka_dept', 'ka_div', 'accounting', 'superadmin', 'ka_dept_acc', 'ka_div_acc', 'fin_dir', 'man_dir', 'pres_dir') DEFAULT 'staff'");
    }
};
