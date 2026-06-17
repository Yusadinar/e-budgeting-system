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
        Schema::table('proposal_harga', function (Blueprint $table) {
            $table->string('type')->nullable();
            $table->string('department_name')->nullable();
            $table->string('section_name')->nullable();
            $table->string('cost_center')->nullable();
            $table->string('no_io_asset')->nullable();
            
            $table->json('items_data')->nullable();
            
            $table->string('delivery_time')->nullable();
            $table->string('quality')->nullable();
            $table->string('payment_terms')->nullable();
            $table->string('experience_non_ippi')->nullable();
            
            $table->string('preparer_name')->nullable();
            $table->string('negotiator_name')->nullable();
            $table->string('approver_name')->nullable();
            $table->string('selected_vendor_name')->nullable();
            
            $table->text('signature_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposal_harga', function (Blueprint $table) {
            $table->dropColumn([
                'type', 'department_name', 'section_name', 'cost_center', 'no_io_asset',
                'items_data', 'delivery_time', 'quality', 'payment_terms', 'experience_non_ippi',
                'preparer_name', 'negotiator_name', 'approver_name', 'selected_vendor_name', 'signature_data'
            ]);
        });
    }
};
