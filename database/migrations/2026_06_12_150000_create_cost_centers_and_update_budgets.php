<?php
// database/migrations/2026_06_12_150000_create_cost_centers_and_update_budgets.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Buat tabel cost_centers
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->string('department_group')
                  ->comment('Grup departemen, misal: Manufacturing, Quality, Maintenance, PPLC, FIN & ACC');
            $table->enum('plant', ['IBEK', 'IKAR', 'HO'])
                  ->comment('IBEK=Bekasi, IKAR=Karawang, HO=Head Office');
            $table->enum('expense_type', ['FOH', 'OPEX'])
                  ->comment('FOH=Factory Overhead, OPEX=Operational Expenditure');
            $table->string('cost_center_code', 50)->nullable()->unique()
                  ->comment('Kode TM dari SAP, misal P-902-3711. NULL jika di master data tertulis NA');
            $table->string('cost_center_name')
                  ->comment('Nama deskripsi detail, misal: IBEK Line A');
            $table->timestamps();

            // Index untuk query cascading dropdown
            $table->index('department_group', 'idx_cc_dept_group');
            $table->index('plant', 'idx_cc_plant');
            $table->index('expense_type', 'idx_cc_expense_type');
        });

        // 2. Tambah cost_center_id ke annual_budgets (nullable dulu untuk backward compat)
        Schema::table('annual_budgets', function (Blueprint $table) {
            $table->foreignId('cost_center_id')->nullable()->after('dept_id')
                  ->constrained('cost_centers')
                  ->cascadeOnDelete()
                  ->comment('FK ke cost_centers. Budget kini di level cost center');
        });

        // 3. Tambah cost_center_id ke users (optional assignment)
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('cost_center_id')->nullable()->after('dept_id')
                  ->constrained('cost_centers')
                  ->nullOnDelete()
                  ->comment('Cost Center yang di-assign ke user');
        });

        // 4. Tambah cost_center_id ke budget_logs
        Schema::table('budget_logs', function (Blueprint $table) {
            $table->foreignId('cost_center_id')->nullable()->after('dept_id')
                  ->constrained('cost_centers')
                  ->nullOnDelete()
                  ->comment('Cost Center terkait log');
        });

        // 5. Tambah cost_center_id ke proposal_harga (relasi langsung agar tidak perlu chain ke user)
        Schema::table('proposal_harga', function (Blueprint $table) {
            $table->foreignId('cost_center_id')->nullable()->after('ppbj_id')
                  ->constrained('cost_centers')
                  ->nullOnDelete()
                  ->comment('Cost Center yang dipilih saat membuat PH');
        });
    }

    public function down(): void
    {
        Schema::table('proposal_harga', function (Blueprint $table) {
            $table->dropForeign(['cost_center_id']);
            $table->dropColumn('cost_center_id');
        });

        Schema::table('budget_logs', function (Blueprint $table) {
            $table->dropForeign(['cost_center_id']);
            $table->dropColumn('cost_center_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cost_center_id']);
            $table->dropColumn('cost_center_id');
        });

        Schema::table('annual_budgets', function (Blueprint $table) {
            $table->dropForeign(['cost_center_id']);
            $table->dropColumn('cost_center_id');
        });

        Schema::dropIfExists('cost_centers');
    }
};
