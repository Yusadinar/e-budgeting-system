<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppbj_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppbj_id')
                  ->constrained('ppbj')
                  ->cascadeOnDelete();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->tinyInteger('step')->unsigned()
                  ->comment('Approval step: 1=KaDept, 2=KaDiv, 3=KaDept FinAcc, 4=KaDiv FinAcc');
            $table->enum('action', ['approved', 'rejected'])->default('approved');
            $table->longText('signature_data')->nullable()
                  ->comment('Base64 encoded PNG signature image');
            $table->text('reject_reason')->nullable();
            $table->timestamps();

            $table->unique(['ppbj_id', 'step'], 'ppbj_step_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppbj_approvals');
    }
};
