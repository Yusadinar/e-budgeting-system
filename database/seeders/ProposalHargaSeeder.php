<?php
// database/seeders/ProposalHargaSeeder.php

namespace Database\Seeders;

use App\Models\Ppbj;
use App\Models\ProposalHarga;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProposalHargaSeeder extends Seeder
{
    public function run(): void
    {
        $year = now()->year;

        // Ambil PPBJ berdasarkan nomor
        $ppbj = fn(string $no) => Ppbj::where('ppbj_number', $no)->first();

        $phData = [
            // ── APPROVED (sudah selesai, ada IA) ──
            [
                'ppbj_id'         => $ppbj('PPBJ-' . $year . '-0001')?->id,
                'ph_number'       => '001/PURCH/IPPI/Jan/' . $year,
                'subject'         => 'Pengadaan Laptop Dell XPS untuk Tim Developer',
                'nominal_request' => 125_000_000,
                'qr_token'        => Str::uuid(),
                'approval_step'   => 4,
                'status'          => 'Approved',
                'created_at'      => now()->startOfYear()->addDays(12),
                'updated_at'      => now()->startOfYear()->addDays(15),
            ],
            [
                'ppbj_id'         => $ppbj('PPBJ-' . $year . '-0002')?->id,
                'ph_number'       => '002/PURCH/IPPI/Mar/' . $year,
                'subject'         => 'Lisensi Software Adobe Creative Suite Tahunan',
                'nominal_request' => 85_000_000,
                'qr_token'        => Str::uuid(),
                'approval_step'   => 4,
                'status'          => 'Approved',
                'created_at'      => now()->startOfYear()->addMonths(2)->addDays(12),
                'updated_at'      => now()->startOfYear()->addMonths(2)->addDays(15),
            ],
            [
                'ppbj_id'         => $ppbj('PPBJ-' . $year . '-0006')?->id,
                'ph_number'       => '003/PURCH/IPPI/Feb/' . $year,
                'subject'         => 'Pelatihan & Sertifikasi HR Management System',
                'nominal_request' => 45_000_000,
                'qr_token'        => Str::uuid(),
                'approval_step'   => 4,
                'status'          => 'Approved',
                'created_at'      => now()->startOfYear()->addMonths(1)->addDays(22),
                'updated_at'      => now()->startOfYear()->addMonths(1)->addDays(25),
            ],
            [
                'ppbj_id'         => $ppbj('PPBJ-' . $year . '-0008')?->id,
                'ph_number'       => '004/PURCH/IPPI/Mar/' . $year,
                'subject'         => 'Pembelian Forklift Gudang Unit 2',
                'nominal_request' => 350_000_000,
                'qr_token'        => Str::uuid(),
                'approval_step'   => 4,
                'status'          => 'Approved',
                'created_at'      => now()->startOfYear()->addMonths(2)->addDays(22),
                'updated_at'      => now()->startOfYear()->addMonths(2)->addDays(25),
            ],

            // ── IN REVIEW (sedang berjalan) ──
            [
                'ppbj_id'         => $ppbj('PPBJ-' . $year . '-0003')?->id,
                'ph_number'       => '005/PURCH/IPPI/Apr/' . $year,
                'subject'         => 'Upgrade Server Infrastruktur Data Center',
                'nominal_request' => 280_000_000,
                'qr_token'        => Str::uuid(),
                'approval_step'   => 1,
                'status'          => 'In_Review',
                'created_at'      => now()->startOfYear()->addMonths(3)->addDays(15),
                'updated_at'      => now()->startOfYear()->addMonths(3)->addDays(15),
            ],
            [
                'ppbj_id'         => $ppbj('PPBJ-' . $year . '-0004')?->id,
                'ph_number'       => '006/PURCH/IPPI/Mei/' . $year,
                'subject'         => 'Pengadaan Mesin Absensi Fingerprint 10 Unit',
                'nominal_request' => 65_000_000,
                'qr_token'        => Str::uuid(),
                'approval_step'   => 2,
                'status'          => 'In_Review',
                'created_at'      => now()->startOfYear()->addMonths(4)->addDays(1),
                'updated_at'      => now()->startOfYear()->addMonths(4)->addDays(5),
            ],
            [
                'ppbj_id'         => $ppbj('PPBJ-' . $year . '-0007')?->id,
                'ph_number'       => '007/PURCH/IPPI/Mei/' . $year,
                'subject'         => 'Percetakan Seragam Karyawan Baru',
                'nominal_request' => 38_500_000,
                'qr_token'        => Str::uuid(),
                'approval_step'   => 1,
                'status'          => 'In_Review',
                'created_at'      => now()->startOfYear()->addMonths(4)->addDays(10),
                'updated_at'      => now()->startOfYear()->addMonths(4)->addDays(10),
            ],
        ];

        foreach ($phData as $data) {
            if (! $data['ppbj_id']) continue;

            ProposalHarga::firstOrCreate(
                ['ph_number' => $data['ph_number']],
                $data
            );
        }

        $this->command->info('  → ProposalHargaSeeder: ' . count($phData) . ' PH seeded.');
    }
}