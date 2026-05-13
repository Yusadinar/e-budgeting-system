<?php
// database/seeders/BudgetLogSeeder.php

namespace Database\Seeders;

use App\Models\BudgetLog;
use App\Models\Department;
use Illuminate\Database\Seeder;

class BudgetLogSeeder extends Seeder
{
    public function run(): void
    {
        $year = now()->year;

        $eng   = Department::where('budget_code', 'IBEK-ENG-002')->first();
        $mtcIB = Department::where('budget_code', 'IBEK-MTC-003')->first();
        $mfgIB = Department::where('budget_code', 'IBEK-MFG-004')->first();
        $hcgs  = Department::where('budget_code', 'CORP-HCGS-011')->first();

        $logs = [
            // ── Engineering Department (IBEK) ──
            [
                'dept_id'      => $eng?->id,
                'reference_no' => '001/PURCH/IPPI/Jan/' . $year,
                'amount'       => 125_000_000,
                'log_type'     => 'reserve',
                'description'  => 'Hold anggaran PH: Pengadaan Tools Engineering Stamping',
                'created_at'   => now()->subMonths(5),
            ],
            [
                'dept_id'      => $eng?->id,
                'reference_no' => '001/IA/IPPI/Jan/' . $year,
                'amount'       => 122_500_000,
                'log_type'     => 'actual_deduction',
                'description'  => 'Realisasi IA: Pengadaan Tools Engineering Stamping',
                'created_at'   => now()->subMonths(4),
            ],
            [
                'dept_id'      => $eng?->id,
                'reference_no' => '002/PURCH/IPPI/Mar/' . $year,
                'amount'       => 85_000_000,
                'log_type'     => 'reserve',
                'description'  => 'Hold anggaran PH: Lisensi Software CAD/CAM Tahunan',
                'created_at'   => now()->subMonths(3),
            ],
            [
                'dept_id'      => $eng?->id,
                'reference_no' => '002/IA/IPPI/Mar/' . $year,
                'amount'       => 85_000_000,
                'log_type'     => 'actual_deduction',
                'description'  => 'Realisasi IA: Lisensi Software CAD/CAM Tahunan',
                'created_at'   => now()->subMonths(2)->subWeeks(2),
            ],

            // ── Maintenance Department (IBEK) ──
            [
                'dept_id'      => $mtcIB?->id,
                'reference_no' => '003/PURCH/IPPI/Feb/' . $year,
                'amount'       => 350_000_000,
                'log_type'     => 'reserve',
                'description'  => 'Hold anggaran PH: Overhaul Mesin Press Line #2',
                'created_at'   => now()->subMonths(3),
            ],
            [
                'dept_id'      => $mtcIB?->id,
                'reference_no' => '003/IA/IPPI/Feb/' . $year,
                'amount'       => 345_000_000,
                'log_type'     => 'actual_deduction',
                'description'  => 'Realisasi IA: Overhaul Mesin Press Line #2',
                'created_at'   => now()->subMonths(2),
            ],
            [
                'dept_id'      => $mtcIB?->id,
                'reference_no' => 'RCL-MTC-' . $year . '-001',
                'amount'       => 75_000_000,
                'log_type'     => 'reclass',
                'description'  => 'Reklasifikasi anggaran dari cost center Dies Maintenance',
                'created_at'   => now()->subMonths(4),
            ],

            // ── Manufacturing Department (IBEK) ──
            [
                'dept_id'      => $mfgIB?->id,
                'reference_no' => '005/PURCH/IPPI/Apr/' . $year,
                'amount'       => 280_000_000,
                'log_type'     => 'reserve',
                'description'  => 'Hold anggaran PH: Upgrade Conveyor Line Produksi',
                'created_at'   => now()->subMonths(2),
            ],
            [
                'dept_id'      => $mfgIB?->id,
                'reference_no' => 'ADJ-MFG-' . $year . '-001',
                'amount'       => 200_000_000,
                'log_type'     => 'increase',
                'description'  => 'Penambahan pagu anggaran Manufacturing Q3 — persetujuan Direktur',
                'created_at'   => now()->subMonths(3),
            ],

            // ── Human Capital & General Services ──
            [
                'dept_id'      => $hcgs?->id,
                'reference_no' => '007/PURCH/IPPI/Mei/' . $year,
                'amount'       => 38_500_000,
                'log_type'     => 'reserve',
                'description'  => 'Hold anggaran PH: Pelatihan & Sertifikasi Karyawan',
                'created_at'   => now()->subMonth(),
            ],
            [
                'dept_id'      => $hcgs?->id,
                'reference_no' => '008/PURCH/IPPI/Jun/' . $year,
                'amount'       => 45_000_000,
                'log_type'     => 'reserve',
                'description'  => 'Hold anggaran PH: Percetakan Seragam Karyawan Baru',
                'created_at'   => now()->subWeeks(3),
            ],
        ];

        foreach ($logs as $log) {
            if (! $log['dept_id']) continue;
            BudgetLog::create($log);
        }

        $this->command->info('  → BudgetLogSeeder: ' . count($logs) . ' logs seeded.');
    }
}