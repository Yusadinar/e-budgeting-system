<?php
// database/seeders/DepartmentSeeder.php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            // ── Plant IBEK Departments ──
            [
                'dept_name'   => 'Marketing',
                'budget_code' => 'IBEK-MKT-001',
            ],
            [
                'dept_name'   => 'Engineering',
                'budget_code' => 'IBEK-ENG-002',
            ],
            [
                'dept_name'   => 'Maintenance',
                'budget_code' => 'IBEK-MTC-003',
            ],
            [
                'dept_name'   => 'Manufacturing',
                'budget_code' => 'IBEK-MFG-004',
            ],
            [
                'dept_name'   => 'Production Planning & Logistic Control',
                'budget_code' => 'IBEK-PPLC-005',
            ],

            // ── Plant IKAR Departments ──
            [
                'dept_name'   => 'Finance Accounting',
                'budget_code' => 'IKAR-ACC-006',
            ],
            [
                'dept_name'   => 'Procurement',
                'budget_code' => 'IKAR-PRC-007',
            ],
            [
                'dept_name'   => 'Quality Management System',
                'budget_code' => 'IKAR-QMS-008',
            ],
            [
                'dept_name'   => 'Maintenance IKAR',
                'budget_code' => 'IKAR-MTC-009',
            ],
            [
                'dept_name'   => 'Manufacturing IKAR',
                'budget_code' => 'IKAR-MFG-010',
            ],

            // ── Di Bawah Finance & Human Capital Director ──
            [
                'dept_name'   => 'Human Capital & General Services',
                'budget_code' => 'CORP-HCGS-011',
            ],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['budget_code' => $dept['budget_code']],
                $dept
            );
        }

        $this->command->info('  → DepartmentSeeder: ' . count($departments) . ' departments seeded.');
    }
}