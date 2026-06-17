<?php
// database/seeders/CostCenterSeeder.php

namespace Database\Seeders;

use App\Models\AnnualBudget;
use App\Models\CostCenter;
use Illuminate\Database\Seeder;

class CostCenterSeeder extends Seeder
{
    public function run(): void
    {
        $masterData = [
            // === FOH IBEK (PLANT: IBEK, TYPE: FOH) ===
            // Manufacturing
            ['dept' => 'Manufacturing', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Shearing', 'code' => 'P-901-3711'],
            ['dept' => 'Manufacturing', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Line A', 'code' => 'P-902-3711'],
            ['dept' => 'Manufacturing', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Line B', 'code' => 'P-903-3711'],
            ['dept' => 'Manufacturing', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Line C', 'code' => 'P-904-3711'],
            ['dept' => 'Manufacturing', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Line D', 'code' => 'P-905-3711'],
            ['dept' => 'Manufacturing', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Metal Finish', 'code' => 'P-906-3711'],
            ['dept' => 'Manufacturing', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Assy', 'code' => 'P-917-3711'],
            ['dept' => 'Manufacturing', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Assy MM/CO', 'code' => 'P-910-3711'],
            ['dept' => 'Manufacturing', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Cutting', 'code' => 'P-925-3711'],
            ['dept' => 'Manufacturing', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Project Mesin D Line', 'code' => 'P-705-3711'],
            ['dept' => 'Manufacturing', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Admin & Foreman', 'code' => 'P-101-3711'],

            // Quality
            ['dept' => 'Quality', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK QC - Line A', 'code' => 'Q-902-3711'],
            ['dept' => 'Quality', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK QC - Line B', 'code' => 'Q-903-3711'],
            ['dept' => 'Quality', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK QC - Line C', 'code' => 'Q-904-3711'],
            ['dept' => 'Quality', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK QC - Line D', 'code' => 'Q-905-3711'],
            ['dept' => 'Quality', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK QC - Assy', 'code' => 'Q-917-3711'],
            ['dept' => 'Quality', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK QC - Plant', 'code' => null],

            // Maintenance & PPLC
            ['dept' => 'Maintenance', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Die Shop', 'code' => 'S-101-3711'],
            ['dept' => 'Maintenance', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Plant Service', 'code' => 'S-201-3711'],
            ['dept' => 'Maintenance', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Die Making', 'code' => 'S-301-3711'],
            ['dept' => 'PPLC', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK PPC', 'code' => 'L-101-3711'],
            ['dept' => 'PPLC', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Inventory Raw Material', 'code' => 'L-201-3711'],
            ['dept' => 'PPLC', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Toolroom', 'code' => 'L-202-3711'],
            ['dept' => 'PPLC', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Delivery', 'code' => 'L-301-3711'],
            ['dept' => 'PPLC', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK Inventory Finish Part', 'code' => 'L-302-3711'],

            // GA
            ['dept' => 'GA', 'plant' => 'IBEK', 'type' => 'FOH', 'name' => 'IBEK GA - Ops/Scrap', 'code' => 'G-201-3711'],

            // === FOH IKAR (PLANT: IKAR, TYPE: FOH) ===
            ['dept' => 'Manufacturing', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR Line E', 'code' => 'P-902-3712'],
            ['dept' => 'Manufacturing', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR Line F', 'code' => 'P-903-3712'],
            ['dept' => 'Manufacturing', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR Line K', 'code' => 'P-904-3712'],
            ['dept' => 'Manufacturing', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR Metal Finish', 'code' => 'P-906-3712'],
            ['dept' => 'Manufacturing', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR Assy', 'code' => 'P-912-3712'],
            ['dept' => 'Manufacturing', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR Assy D26', 'code' => 'P-914-3712'],
            ['dept' => 'Manufacturing', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR Admin & Foreman', 'code' => 'P-101-3712'],

            ['dept' => 'Quality', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR QC - Line E', 'code' => 'Q-902-3712'],
            ['dept' => 'Quality', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR QC - Line F', 'code' => 'Q-903-3712'],
            ['dept' => 'Quality', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR QC - Line K', 'code' => 'Q-904-3712'],
            ['dept' => 'Quality', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR QC - Assy', 'code' => 'Q-912-3712'],
            ['dept' => 'Quality', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR QC - Assy D26', 'code' => 'Q-914-3712'],
            ['dept' => 'Quality', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR QC - Plant', 'code' => 'Q-102-3712'],
            ['dept' => 'Quality', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR QC - GP 12', 'code' => 'Q-103-3712'],

            ['dept' => 'Maintenance', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR DIE SHOP', 'code' => 'S-101-3712'],
            ['dept' => 'Maintenance', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR PLANT SERVICE', 'code' => 'S-201-3712'],

            ['dept' => 'PPLC', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR PPC', 'code' => null],
            ['dept' => 'PPLC', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR INVENTORY RAW MATERIAL', 'code' => 'L-201-3712'],
            ['dept' => 'PPLC', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR TOOLROOM & PPC', 'code' => 'L-202-3712'],
            ['dept' => 'PPLC', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR DELIVERY', 'code' => 'L-301-3712'],
            ['dept' => 'PPLC', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR INVENTORY FINISH PART', 'code' => 'L-302-3712'],
            ['dept' => 'PPLC', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR DELIVERY D26', 'code' => 'L-914-3712'],

            ['dept' => 'GA', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR GA - Ops/Scrap', 'code' => 'G-201-3712'],
            ['dept' => 'Human Resource', 'plant' => 'IKAR', 'type' => 'FOH', 'name' => 'IKAR HRD', 'code' => 'H-101-3712'],

            // === FOH COY & OPEX HO (PLANT: HO) ===
            ['dept' => 'Purchasing', 'plant' => 'HO', 'type' => 'FOH', 'name' => 'HO Procurement', 'code' => 'I-101-3710'],
            ['dept' => 'Engineering', 'plant' => 'HO', 'type' => 'FOH', 'name' => 'HO Produc Engineering', 'code' => 'M-201-3710'],
            ['dept' => 'Quality Assurance', 'plant' => 'HO', 'type' => 'FOH', 'name' => 'HO Quality Assurance', 'code' => 'Q-101-3710'],

            ['dept' => 'FIN & ACC', 'plant' => 'HO', 'type' => 'OPEX', 'name' => 'Accounting', 'code' => 'A-101-3710'],
            ['dept' => 'FIN & ACC', 'plant' => 'HO', 'type' => 'OPEX', 'name' => 'Finance', 'code' => 'F-101-3710'],

            ['dept' => 'HRGA', 'plant' => 'HO', 'type' => 'OPEX', 'name' => 'General Affair', 'code' => 'G-101-3710'],
            ['dept' => 'HRGA', 'plant' => 'HO', 'type' => 'OPEX', 'name' => 'Human resource', 'code' => 'H-101-3710'],

            ['dept' => 'Marketing', 'plant' => 'HO', 'type' => 'OPEX', 'name' => 'Marketing', 'code' => 'M-101-3710'],
        ];

        $fiscalYear = now()->year;

        foreach ($masterData as $item) {
            $cc = CostCenter::updateOrCreate(
                ['cost_center_name' => $item['name'], 'plant' => $item['plant']],
                [
                    'department_group'  => $item['dept'],
                    'expense_type'      => $item['type'],
                    'cost_center_code'  => $item['code'],
                ]
            );

            // Buat annual budget dummy Rp 100.000.000 per cost center
            AnnualBudget::updateOrCreate(
                [
                    'cost_center_id' => $cc->id,
                    'fiscal_year'    => $fiscalYear,
                ],
                [
                    'dept_id'        => null,
                    'total_plan'     => 100000000,
                    'total_used'     => 0,
                    'total_reserved' => 0,
                ]
            );
        }

        $this->command->info("✅ Seeded " . count($masterData) . " Cost Centers with Rp 100.000.000 budget each for FY {$fiscalYear}.");
    }
}
