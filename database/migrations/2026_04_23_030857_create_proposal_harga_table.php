<?php
// database/migrations/2024_01_01_000005_create_proposal_harga_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_harga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppbj_id')
                  ->constrained('ppbj')
                  ->cascadeOnDelete()
                  ->comment('Jika PPBJ dihapus, semua PH ikut terhapus');
            $table->string('ph_number', 50)->unique()
                  ->comment('Format: 001/PURCH/IPPI/Jan/2024');
            $table->string('subject', 255)
                  ->comment('Deskripsi singkat pengajuan PH');
            $table->decimal('nominal_request', 20, 2)
                  ->comment('Nominal yang diminta dalam Rupiah');
            $table->string('qr_token', 100)->unique()
                  ->comment('Token unik untuk URL QR Code hybrid approval');
            $table->tinyInteger('approval_step')->default(0)->unsigned()
                  ->comment('0=Draft, 1=Menunggu Ka.Dept, 2=Menunggu Ka.Div');
            $table->enum('status', ['Draft', 'In_Review', 'Approved', 'Rejected'])
                  ->default('Draft');
            $table->timestamps();
            $table->softDeletes()->comment('SoftDelete: data tidak benar-benar hilang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_harga');
    }
};