<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ppbj', function (Blueprint $table) {
            // (2) Kolom kiri atas
            $table->string('department_section')->nullable()->after('ppbj_number')
                  ->comment('Departemen/Section pengaju');
            $table->string('subject')->nullable()->after('department_section');
            $table->string('nama_barang_jasa')->nullable()->after('subject')
                  ->comment('Nama barang/jasa yang diajukan');
            $table->text('spesifikasi')->nullable()->after('nama_barang_jasa');

            // (3) Kolom kanan atas
            $table->string('ia_no', 100)->nullable()->after('spesifikasi')
                  ->comment('IA number, diisi oleh accounting');
            $table->string('io_fr_no', 100)->nullable()->after('ia_no')
                  ->comment('IO/FR number, diisi oleh accounting');
            $table->integer('qty')->nullable()->after('io_fr_no');
            $table->string('uom', 50)->nullable()->after('qty')
                  ->comment('Unit of Measure');
            $table->enum('pernah_order', ['sudah', 'belum'])->nullable()->after('uom');
            $table->string('pernah_order_bulan', 50)->nullable()->after('pernah_order')
                  ->comment('Jika sudah pernah order, bulan & tahun');

            // (4) Background / Problem 5W+1H
            $table->text('bg_what')->nullable()->after('pernah_order_bulan');
            $table->text('bg_why')->nullable()->after('bg_what');
            $table->text('bg_when')->nullable()->after('bg_why');
            $table->text('bg_where')->nullable()->after('bg_when');
            $table->text('bg_who')->nullable()->after('bg_where');
            $table->text('bg_how')->nullable()->after('bg_who');

            // (5) Risk Analysis
            $table->text('risk_analysis')->nullable()->after('bg_how');

            // (6) Condition photo
            $table->string('condition_photo')->nullable()->after('risk_analysis')
                  ->comment('Path to condition sketch/photo');

            // (7) Detail Specification
            $table->string('spec_brand')->nullable()->after('condition_photo');
            $table->string('spec_maker')->nullable()->after('spec_brand');
            $table->string('spec_negara_asal')->nullable()->after('spec_maker');
            $table->text('spec_lain_lain')->nullable()->after('spec_negara_asal');

            // (8) Urgency
            $table->enum('urgency_level', ['low', 'medium', 'high'])->nullable()->after('spec_lain_lain');
            $table->string('potensi_line_stop', 100)->nullable()->after('urgency_level')
                  ->comment('Potensi line stop: XX jam/hari');
            $table->json('urgency_options')->nullable()->after('potensi_line_stop')
                  ->comment('JSON array of selected: tidak_ada_backup, pengadaan_baru, penggantian_rusak, schedule_general_check');
            $table->string('pengadaan_baru_untuk')->nullable()->after('urgency_options');
            $table->date('schedule_general_check')->nullable()->after('pengadaan_baru_untuk');

            // (9) Budget/Estimasi
            $table->enum('budget_type', ['capex', 'foh', 'opex', 'project'])->nullable()->after('schedule_general_check');
            $table->string('budget_amount_range')->nullable()->after('budget_type')
                  ->comment('Range estimasi budget');
            $table->string('capex_attachment')->nullable()->after('budget_amount_range')
                  ->comment('Lampiran capex jika dipilih');

            // (10) Layout Area
            $table->string('layout_photo')->nullable()->after('capex_attachment')
                  ->comment('Path to layout area photo');
            $table->string('lokasi_pressline')->nullable()->after('layout_photo');
            $table->string('lokasi_sub_assy')->nullable()->after('lokasi_pressline');
            $table->string('lokasi_metal_finish')->nullable()->after('lokasi_sub_assy');
            $table->string('lokasi_lain_lain')->nullable()->after('lokasi_metal_finish');
        });
    }

    public function down(): void
    {
        Schema::table('ppbj', function (Blueprint $table) {
            $table->dropColumn([
                'department_section', 'subject', 'nama_barang_jasa', 'spesifikasi',
                'ia_no', 'io_fr_no', 'qty', 'uom', 'pernah_order', 'pernah_order_bulan',
                'bg_what', 'bg_why', 'bg_when', 'bg_where', 'bg_who', 'bg_how',
                'risk_analysis', 'condition_photo',
                'spec_brand', 'spec_maker', 'spec_negara_asal', 'spec_lain_lain',
                'urgency_level', 'potensi_line_stop', 'urgency_options',
                'pengadaan_baru_untuk', 'schedule_general_check',
                'budget_type', 'budget_amount_range', 'capex_attachment',
                'layout_photo', 'lokasi_pressline', 'lokasi_sub_assy',
                'lokasi_metal_finish', 'lokasi_lain_lain',
            ]);
        });
    }
};
