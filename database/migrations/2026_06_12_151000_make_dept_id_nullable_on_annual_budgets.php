<?php
// database/migrations/2026_06_12_151000_make_dept_id_nullable_on_annual_budgets.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Hapus foreign key dulu
        Schema::table('annual_budgets', function (Blueprint $table) {
            $table->dropForeign(['dept_id']);
        });

        // Step 2: Hapus unique constraint
        Schema::table('annual_budgets', function (Blueprint $table) {
            $table->dropUnique('unique_dept_fiscal_year');
        });

        // Step 3: Ubah dept_id menjadi nullable
        DB::statement('ALTER TABLE `annual_budgets` MODIFY `dept_id` BIGINT UNSIGNED NULL');

        // Step 4: Pasang kembali foreign key
        Schema::table('annual_budgets', function (Blueprint $table) {
            $table->foreign('dept_id')
                  ->references('id')->on('departments')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('annual_budgets', function (Blueprint $table) {
            $table->dropForeign(['dept_id']);
        });

        DB::statement('ALTER TABLE `annual_budgets` MODIFY `dept_id` BIGINT UNSIGNED NOT NULL');

        Schema::table('annual_budgets', function (Blueprint $table) {
            $table->foreign('dept_id')
                  ->references('id')->on('departments')
                  ->cascadeOnDelete();

            $table->unique(['dept_id', 'fiscal_year'], 'unique_dept_fiscal_year');
        });
    }
};
