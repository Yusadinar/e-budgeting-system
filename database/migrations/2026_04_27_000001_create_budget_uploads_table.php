<?php
// database/migrations/2026_04_27_000001_create_budget_uploads_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_uploads', function (Blueprint $table) {
            $table->id();

            // Relasi ke department
            $table->foreignId('dept_id')
                  ->constrained('departments')
                  ->cascadeOnDelete();

            // Siapa yang upload
            $table->foreignId('uploaded_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Nama file asli Excel
            $table->string('file_name')->nullable();

            // Nomor Outlook (OL1, OL2, OL3, OL2 ADJ, dst)
            $table->string('outlook_number', 20)->comment('Contoh: OL1, OL2, OL3, OL2 ADJ');

            // Periode Outlook (Mei 2026 - Jul 2026)
            $table->string('outlook_period', 100)->nullable();

            // Kategori anggaran
            $table->enum('category', ['CAPEX', 'FOH', 'OPEX'])->comment('Kategori: CAPEX / FOH / OPEX');

            // Tahun fiskal
            $table->year('fiscal_year');

            // Data baris item dari Excel (JSON)
            $table->json('items')->comment('Array item budget dari baris Excel');

            // Total keseluruhan
            $table->decimal('total_amount', 20, 2)->default(0);

            // Catatan / keterangan tambahan
            $table->text('notes')->nullable();

            // Status upload (draft = belum dikonfirmasi, confirmed = sudah disimpan)
            $table->enum('status', ['draft', 'confirmed'])->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_uploads');
    }
};
