<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan status dan approval_step ke ppbj
        Schema::table('ppbj', function (Blueprint $table) {
            $table->tinyInteger('approval_step')->default(0)->unsigned()
                  ->comment('0=Draft, 1=Menunggu Ka.Dept, 2=Menunggu Ka.Div, 3=Menunggu Accounting')
                  ->after('ppbj_number');
            $table->enum('status', ['Draft', 'In_Review', 'Approved', 'Rejected'])
                  ->default('Draft')
                  ->after('approval_step');
        });

        // 2. Ubah enum role pada users table
        // Harus menggunakan raw SQL karena ENUM sulit diubah dengan Blueprint standard di MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('staff', 'ka_sie', 'ka_dept', 'ka_div', 'accounting', 'superadmin', 'ka_dept_acc', 'ka_div_acc', 'fin_dir', 'man_dir', 'pres_dir') DEFAULT 'staff'");
        }
    }

    public function down(): void
    {
        Schema::table('ppbj', function (Blueprint $table) {
            $table->dropColumn(['approval_step', 'status']);
        });

        // Rollback ENUM
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('staff', 'ka_dept', 'ka_div', 'accounting', 'superadmin') DEFAULT 'staff'");
        }
    }
};
