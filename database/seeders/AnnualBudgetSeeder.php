<?php
// database/seeders/AnnualBudgetSeeder.php

namespace Database\Seeders;

use App\Models\AnnualBudget;
use App\Models\Department;
use Illuminate\Database\Seeder;

class AnnualBudgetSeeder extends Seeder
{
    public function run(): void
    {
        $year = now()->year;

        // Konfigurasi budget per departemen (sesuai struktur organisasi baru)
        $budgets = [
            // ── Plant IBEK ──
            'IBEK-MKT-001' => [
                'total_plan'     => 1_500_000_000,   // 1.5 Miliar
                'total_used'     => 420_000_000,
                'total_reserved' => 180_000_000,
            ],
            'IBEK-ENG-002' => [
                'total_plan'     => 2_000_000_000,
                'total_used'     => 650_000_000,
                'total_reserved' => 250_000_000,
            ],
            'IBEK-MTC-003' => [
                'total_plan'     => 1_800_000_000,
                'total_used'     => 510_000_000,
                'total_reserved' => 200_000_000,
            ],
            'IBEK-MFG-004' => [
                'total_plan'     => 3_000_000_000,   // 3 Miliar
                'total_used'     => 1_200_000_000,
                'total_reserved' => 350_000_000,
            ],
            'IBEK-PPLC-005' => [
                'total_plan'     => 1_200_000_000,
                'total_used'     => 380_000_000,
                'total_reserved' => 150_000_000,
            ],

            // ── Plant IKAR ──
            'IKAR-ACC-006' => [
                'total_plan'     => 500_000_000,
                'total_used'     => 150_000_000,
                'total_reserved' => 50_000_000,
            ],
            'IKAR-PRC-007' => [
                'total_plan'     => 400_000_000,
                'total_used'     => 100_000_000,
                'total_reserved' => 40_000_000,
            ],
            'IKAR-QMS-008' => [
                'total_plan'     => 600_000_000,
                'total_used'     => 150_000_000,
                'total_reserved' => 80_000_000,
            ],
            'IKAR-MTC-009' => [
                'total_plan'     => 1_500_000_000,
                'total_used'     => 480_000_000,
                'total_reserved' => 200_000_000,
            ],
            'IKAR-MFG-010' => [
                'total_plan'     => 2_500_000_000,
                'total_used'     => 875_000_000,
                'total_reserved' => 300_000_000,
            ],

            // ── Corporate ──
            'CORP-HCGS-011' => [
                'total_plan'     => 1_200_000_000,
                'total_used'     => 310_000_000,
                'total_reserved' => 125_000_000,
            ],
        ];

        foreach ($budgets as $code => $data) {
            $dept = Department::where('budget_code', $code)->first();
            if (! $dept) continue;

            AnnualBudget::updateOrCreate(
                [
                    'dept_id'     => $dept->id,
                    'fiscal_year' => $year,
                ],
                [
                    'total_plan'     => $data['total_plan'],
                    'total_used'     => $data['total_used'],
                    'total_reserved' => $data['total_reserved'],
                    'cost_center_id' => null,
                ]
            );
        }

        $this->command->info('  → AnnualBudgetSeeder: ' . count($budgets) . ' budgets seeded (FY' . $year . ').');
    }
}