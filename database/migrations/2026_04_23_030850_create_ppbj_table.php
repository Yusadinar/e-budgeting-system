<?php
// database/migrations/2024_01_01_000004_create_ppbj_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppbj', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete()
                  ->comment('User yang membuat PPBJ, tidak bisa dihapus jika masih ada PPBJ');
            $table->enum('jenis_pengeluaran', ['FR', 'IR', 'IO'])
                  ->comment('FR=Fund Reservation, IR=Internal Rate, IO=Internal Order');
            $table->string('ppbj_number', 50)->unique()
                  ->comment('Nomor unik PPBJ, bisa di-generate otomatis');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppbj');
    }
};