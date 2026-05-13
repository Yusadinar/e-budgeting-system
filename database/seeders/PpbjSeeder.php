<?php
// database/seeders/PpbjSeeder.php

namespace Database\Seeders;

use App\Models\Ppbj;
use App\Models\User;
use Illuminate\Database\Seeder;

class PpbjSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user staff dari berbagai departemen sebagai pengaju
        $fadli    = User::where('email', 'fadli.arif@establish.dev')->first();
        $deddy    = User::where('email', 'deddy.supriyadi@establish.dev')->first();
        $bagas    = User::where('email', 'bagas.rangga@establish.dev')->first();
        $alberta  = User::where('email', 'alberta.purnita@establish.dev')->first();
        $fauzan   = User::where('email', 'fauzan.nurdinsyah@establish.dev')->first();
        $fitri    = User::where('email', 'fitri.nurmala@establish.dev')->first();

        $ppbjData = [
            // Engineering Department
            [
                'user_id'           => $fadli?->id,
                'jenis_pengeluaran' => 'FR',
                'ppbj_number'       => 'PPBJ-' . now()->year . '-0001',
                'approval_step'     => 4,
                'status'            => 'Approved',
                'created_at'        => now()->startOfYear()->addDays(5),
                'updated_at'        => now()->startOfYear()->addDays(10),
            ],
            [
                'user_id'           => $fadli?->id,
                'jenis_pengeluaran' => 'IR',
                'ppbj_number'       => 'PPBJ-' . now()->year . '-0002',
                'approval_step'     => 4,
                'status'            => 'Approved',
                'created_at'        => now()->startOfYear()->addMonths(2)->addDays(5),
                'updated_at'        => now()->startOfYear()->addMonths(2)->addDays(10),
            ],

            // Maintenance Department (IBEK)
            [
                'user_id'           => $deddy?->id,
                'jenis_pengeluaran' => 'IO',
                'ppbj_number'       => 'PPBJ-' . now()->year . '-0003',
                'approval_step'     => 4,
                'status'            => 'Approved',
                'created_at'        => now()->startOfYear()->addMonths(1)->addDays(5),
                'updated_at'        => now()->startOfYear()->addMonths(1)->addDays(10),
            ],
            [
                'user_id'           => $deddy?->id,
                'jenis_pengeluaran' => 'FR',
                'ppbj_number'       => 'PPBJ-' . now()->year . '-0004',
                'approval_step'     => 4,
                'status'            => 'Approved',
                'created_at'        => now()->startOfYear()->addMonths(3)->addDays(5),
                'updated_at'        => now()->startOfYear()->addMonths(3)->addDays(10),
            ],

            // Manufacturing Department (IBEK)
            [
                'user_id'           => $bagas?->id,
                'jenis_pengeluaran' => 'IR',
                'ppbj_number'       => 'PPBJ-' . now()->year . '-0005',
                'approval_step'     => 4,
                'status'            => 'Approved',
                'created_at'        => now()->startOfYear()->addMonths(4)->addDays(5),
                'updated_at'        => now()->startOfYear()->addMonths(4)->addDays(10),
            ],

            // PPLC Department
            [
                'user_id'           => $alberta?->id,
                'jenis_pengeluaran' => 'FR',
                'ppbj_number'       => 'PPBJ-' . now()->year . '-0006',
                'approval_step'     => 4,
                'status'            => 'Approved',
                'created_at'        => now()->startOfYear()->addMonths(1)->addDays(15),
                'updated_at'        => now()->startOfYear()->addMonths(1)->addDays(20),
            ],

            // Procurement (IKAR)
            [
                'user_id'           => $fauzan?->id,
                'jenis_pengeluaran' => 'IO',
                'ppbj_number'       => 'PPBJ-' . now()->year . '-0007',
                'approval_step'     => 4,
                'status'            => 'Approved',
                'created_at'        => now()->startOfYear()->addMonths(4)->addDays(1),
                'updated_at'        => now()->startOfYear()->addMonths(4)->addDays(5),
            ],

            // Human Capital & General Services
            [
                'user_id'           => $fitri?->id,
                'jenis_pengeluaran' => 'IR',
                'ppbj_number'       => 'PPBJ-' . now()->year . '-0008',
                'approval_step'     => 4,
                'status'            => 'Approved',
                'created_at'        => now()->startOfYear()->addMonths(2)->addDays(15),
                'updated_at'        => now()->startOfYear()->addMonths(2)->addDays(20),
            ],
            [
                'user_id'           => $fitri?->id,
                'jenis_pengeluaran' => 'FR',
                'ppbj_number'       => 'PPBJ-' . now()->year . '-0009',
                'approval_step'     => 4,
                'status'            => 'Approved',
                'created_at'        => now()->startOfYear()->addMonths(4)->addDays(15),
                'updated_at'        => now()->startOfYear()->addMonths(4)->addDays(20),
            ],
        ];

        foreach ($ppbjData as $data) {
            if (! $data['user_id']) continue;
            Ppbj::firstOrCreate(
                ['ppbj_number' => $data['ppbj_number']],
                $data
            );
        }

        $this->command->info('  → PpbjSeeder: ' . count($ppbjData) . ' PPBJ seeded.');
    }
}