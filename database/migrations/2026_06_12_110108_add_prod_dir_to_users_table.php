<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('staff', 'ka_sie', 'ka_dept', 'ka_div', 'accounting', 'superadmin', 'fin_dir', 'man_dir', 'prod_dir', 'pres_dir') DEFAULT 'staff'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert by moving prod_dir back to staff, then altering enum
        DB::statement("UPDATE users SET role = 'staff' WHERE role = 'prod_dir'");
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('staff', 'ka_sie', 'ka_dept', 'ka_div', 'accounting', 'superadmin', 'fin_dir', 'man_dir', 'pres_dir') DEFAULT 'staff'");
    }
};
