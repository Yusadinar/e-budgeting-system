<?php
// database/migrations/2024_01_01_000007_create_budget_logs_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dept_id')
                  ->constrained('departments')
                  ->restrictOnDelete()
                  ->comment('Department yang terkena dampak log, tidak bisa dihapus jika ada log');
            $table->string('reference_no', 100)
                  ->comment('Nomor dokumen trigger: PH number atau IA number');
            $table->decimal('amount', 20, 2)
                  ->comment('Nominal perubahan anggaran (bisa positif/negatif)');
            $table->enum('log_type', ['reserve', 'actual_deduction', 'increase', 'reclass'])
                  ->comment('reserve=hold PH, actual_deduction=IA approved, increase=tambah pagu, reclass=reklasifikasi');
            $table->text('description')
                  ->comment('Keterangan detail perubahan anggaran');
            $table->timestamps();

            // Index untuk performa query laporan per departemen
            $table->index(['dept_id', 'created_at'], 'idx_dept_log_date');
            $table->index('log_type', 'idx_log_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_logs');
    }
};