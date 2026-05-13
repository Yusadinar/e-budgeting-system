<?php
// database/migrations/2024_01_01_000006_create_internal_agreements_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internal_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ph_id')
                  ->constrained('proposal_harga')
                  ->cascadeOnDelete()
                  ->comment('Jika PH dihapus (hard), IA ikut terhapus');
            $table->string('ia_number', 50)->unique()
                  ->comment('Format: 001/IA/IPPI/Jan/2024');
            $table->string('sap_doc_no', 50)->nullable()
                  ->comment('Nomor dokumen dari SAP setelah IA terbit dan posting');
            $table->decimal('final_nominal', 20, 2)
                  ->comment('Nominal final yang disepakati dalam IA');
            $table->tinyInteger('approval_step')->default(0)->unsigned()
                  ->comment('0=Draft, 1=Menunggu Ka.Dept, 2=Menunggu Ka.Div');
            $table->enum('status_ia', ['Draft', 'In_Review', 'Approved'])
                  ->default('Draft');
            $table->timestamps();
            $table->softDeletes()->comment('SoftDelete: data tidak benar-benar hilang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_agreements');
    }
};