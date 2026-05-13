<?php
// database/migrations/2024_01_01_000003_create_annual_budgets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annual_budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dept_id')
                  ->constrained('departments')
                  ->cascadeOnDelete()
                  ->comment('Jika department dihapus, budget ikut dihapus');
            $table->year('fiscal_year')->comment('Tahun fiskal anggaran, misal: 2024');
            $table->decimal('total_plan', 20, 2)->default(0)
                  ->comment('Plafon anggaran awal yang disetujui');
            $table->decimal('total_used', 20, 2)->default(0)
                  ->comment('Total realisasi dari IA yang sudah Approved & closed');
            $table->decimal('total_reserved', 20, 2)->default(0)
                  ->comment('Total yang sedang di-hold (PH/IA In Review)');
            $table->timestamps();

            // Satu departemen hanya boleh punya 1 budget per tahun fiskal
            $table->unique(['dept_id', 'fiscal_year'], 'unique_dept_fiscal_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annual_budgets');
    }
};