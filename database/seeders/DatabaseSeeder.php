<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Urutan wajib diikuti karena ada foreign key dependency
        $this->call([
            DepartmentSeeder::class,        // 1. Master department dulu
            UserSeeder::class,              // 2. User butuh dept_id
            AnnualBudgetSeeder::class,      // 3. Budget butuh dept_id
            PpbjSeeder::class,              // 4. PPBJ butuh user_id
            ProposalHargaSeeder::class,     // 5. PH butuh ppbj_id
            InternalAgreementSeeder::class, // 6. IA butuh ph_id
            BudgetLogSeeder::class,         // 7. Log butuh dept_id & referensi nomor dokumen
            SuperadminSeeder::class,        // 8. Superadmin user
        ]);

        $this->command->info('✅ Semua data dummy berhasil di-seed!');
        $this->command->newLine();
        $this->command->info('══════════════════════════════════════════');
        $this->command->info('  AKUN LOGIN UTAMA');
        $this->command->info('══════════════════════════════════════════');
        $this->command->table(
            ['Role', 'Nama', 'Email', 'Password'],
            [
                ['Man. Director',  'Octa Yudha Prihandiyanto', 'octa.yudha@establish.dev',     'password'],
                ['Fin. Director',  'Riana Budiwijayanti',      'riana.budiwi@establish.dev',    'password'],
                ['Ka. Div (IKAR)', 'Sriyanto',                 'sriyanto@establish.dev',        'password'],
                ['Ka. Dept',       'Permana Aditya',           'permana.aditya@establish.dev',  'password'],
                ['Ka. Dept',       'Bayu Prakosa',             'bayu.prakosa@establish.dev',    'password'],
                ['Ka. Dept',       'Yuli Yulianti',            'yuli.yulianti@establish.dev',   'password'],
                ['Ka. Seksi',      'Arif Setiawan',            'arif.setiawan@establish.dev',   'password'],
                ['Ka. Seksi',      'Alberta Purnita Sari',     'alberta.purnita@establish.dev', 'password'],
                ['Superadmin',     'Super Admin',              'superadmin@establish.dev',      'password'],
            ]
        );
    }
}