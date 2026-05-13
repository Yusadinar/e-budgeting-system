<?php
// database/seeders/InternalAgreementSeeder.php

namespace Database\Seeders;

use App\Models\InternalAgreement;
use App\Models\ProposalHarga;
use Illuminate\Database\Seeder;

class InternalAgreementSeeder extends Seeder
{
    public function run(): void
    {
        $year = now()->year;

        // Helper: ambil PH berdasarkan nomor
        $ph = fn(string $no) => ProposalHarga::where('ph_number', $no)->first();

        // Hanya PH yang Approved yang punya IA
        $iaData = [
            [
                'ph_id'         => $ph('001/PURCH/IPPI/Jan/' . $year)?->id,
                'ia_number'     => '001/IA/IPPI/Jan/' . $year,
                'sap_doc_no'    => '5100012345',
                'final_nominal' => 122_500_000,
                'approval_step' => 8,
                'status_ia'     => 'Approved',
                'created_at'    => now()->startOfYear()->addDays(20),
                'updated_at'    => now()->startOfYear()->addDays(25), // January realization
            ],
            [
                'ph_id'         => $ph('002/PURCH/IPPI/Mar/' . $year)?->id,
                'ia_number'     => '002/IA/IPPI/Mar/' . $year,
                'sap_doc_no'    => '5100012678',
                'final_nominal' => 85_000_000,
                'approval_step' => 8,
                'status_ia'     => 'Approved',
                'created_at'    => now()->startOfYear()->addMonths(2)->addDays(20),
                'updated_at'    => now()->startOfYear()->addMonths(2)->addDays(25), // March realization
            ],
            [
                'ph_id'         => $ph('003/PURCH/IPPI/Feb/' . $year)?->id,
                'ia_number'     => '003/IA/IPPI/Feb/' . $year,
                'sap_doc_no'    => '5100013001',
                'final_nominal' => 43_750_000,
                'approval_step' => 8,
                'status_ia'     => 'Approved',
                'created_at'    => now()->startOfYear()->addMonths(1)->addDays(26),
                'updated_at'    => now()->startOfYear()->addMonths(2)->addDays(2), // Early March (or Feb) realization
            ],
            [
                'ph_id'         => $ph('004/PURCH/IPPI/Mar/' . $year)?->id,
                'ia_number'     => '004/IA/IPPI/Mar/' . $year,
                'sap_doc_no'    => '5100013450',
                'final_nominal' => 345_000_000,
                'approval_step' => 8,
                'status_ia'     => 'Approved',
                'created_at'    => now()->startOfYear()->addMonths(3)->addDays(1),
                'updated_at'    => now()->startOfYear()->addMonths(3)->addDays(10), // April realization
            ],
        ];

        foreach ($iaData as $data) {
            if (! $data['ph_id']) continue;

            InternalAgreement::firstOrCreate(
                ['ia_number' => $data['ia_number']],
                $data
            );
        }

        $this->command->info('  → InternalAgreementSeeder: ' . count($iaData) . ' IA seeded.');
    }
}