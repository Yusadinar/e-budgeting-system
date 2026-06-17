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
        Schema::create('ph_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ph_id')->constrained('proposal_harga')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('step');
            $table->enum('action', ['approved', 'rejected']);
            $table->text('signature_data')->nullable(); // Base64 signature
            $table->text('reject_reason')->nullable();
            $table->timestamps();
            
            // User shouldn't approve the same step twice
            $table->unique(['ph_id', 'step', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ph_approvals');
    }
};
