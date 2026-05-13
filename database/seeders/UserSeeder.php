<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID department untuk referensi
        $mkt   = Department::where('budget_code', 'IBEK-MKT-001')->first();
        $eng   = Department::where('budget_code', 'IBEK-ENG-002')->first();
        $mtcIB = Department::where('budget_code', 'IBEK-MTC-003')->first();
        $mfgIB = Department::where('budget_code', 'IBEK-MFG-004')->first();
        $pplc  = Department::where('budget_code', 'IBEK-PPLC-005')->first();

        $acc   = Department::where('budget_code', 'IKAR-ACC-006')->first();
        $prc   = Department::where('budget_code', 'IKAR-PRC-007')->first();
        $qms   = Department::where('budget_code', 'IKAR-QMS-008')->first();
        $mtcIK = Department::where('budget_code', 'IKAR-MTC-009')->first();
        $mfgIK = Department::where('budget_code', 'IKAR-MFG-010')->first();
        $hcgs  = Department::where('budget_code', 'CORP-HCGS-011')->first();

        $defaultPw = Hash::make('password');

        $users = [
            // ═══════════════════════════════════════════════════
            //  I. LEVEL DIREKTUR (DIRECTORATE)
            // ═══════════════════════════════════════════════════
            [
                'name'              => 'Octa Yudha Prihandiyanto',
                'email'             => 'octa.yudha@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'man_dir',
                'dept_id'           => null,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Riana Budiwijayanti',
                'email'             => 'riana.budiwi@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'fin_dir',
                'dept_id'           => null,
                'email_verified_at' => now(),
            ],

            // ═══════════════════════════════════════════════════
            //  II. LEVEL DIVISI / PLANT HEAD (Ka. Div)
            // ═══════════════════════════════════════════════════
            [
                'name'              => 'Sriyanto',
                'email'             => 'sriyanto@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_div',
                'dept_id'           => $mfgIK?->id, // Primary dept: Manufacturing IKAR
                'email_verified_at' => now(),
            ],

            // ═══════════════════════════════════════════════════
            //  III. LEVEL DEPARTEMEN (Ka. Dept) - Plant IBEK
            // ═══════════════════════════════════════════════════
            [
                'name'              => 'Permana Aditya',
                'email'             => 'permana.aditya@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_dept',
                'dept_id'           => $mkt?->id,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Bayu Prakosa',
                'email'             => 'bayu.prakosa@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_dept',
                'dept_id'           => $eng?->id,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'M. Raief Adiputra',
                'email'             => 'raief.adiputra@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_dept',
                'dept_id'           => $mtcIB?->id,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Hery Prabowo',
                'email'             => 'hery.prabowo@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_dept',
                'dept_id'           => $mfgIB?->id,
                'email_verified_at' => now(),
            ],

            // ═══════════════════════════════════════════════════
            //  III. LEVEL DEPARTEMEN (Ka. Dept) - Plant IKAR
            // ═══════════════════════════════════════════════════
            [
                'name'              => 'Eko Hutajulu',
                'email'             => 'eko.hutajulu@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_dept',
                'dept_id'           => $mtcIK?->id,
                'email_verified_at' => now(),
            ],

            // ═══════════════════════════════════════════════════
            //  III. LEVEL DEPARTEMEN - Di Bawah Fin & HC Director
            // ═══════════════════════════════════════════════════
            [
                'name'              => 'Yuli Yulianti',
                'email'             => 'yuli.yulianti@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_dept',
                'dept_id'           => $acc?->id, // Moved from IARM to Finance Accounting
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Ahmad Fadillah',
                'email'             => 'ahmad.fadillah@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_dept',
                'dept_id'           => $hcgs?->id,
                'email_verified_at' => now(),
            ],

            // ═══════════════════════════════════════════════════
            //  IV. LEVEL SEKSI (Section) - Ka. Seksi (ka_sie)
            //      Di bawah Plant IBEK
            // ═══════════════════════════════════════════════════
            [
                'name'              => 'Arif Setiawan',
                'email'             => 'arif.setiawan@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $mkt?->id,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Aditya Rangga Pratama',
                'email'             => 'aditya.rangga@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $mkt?->id,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'M. Fadli Arif Setiawan',
                'email'             => 'fadli.arif@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $eng?->id,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Deddy Supriyadi',
                'email'             => 'deddy.supriyadi@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $mtcIB?->id,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'M. Omida Nur Aziz',
                'email'             => 'omida.aziz@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $mtcIB?->id,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Bagas Budi Rangga',
                'email'             => 'bagas.rangga@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $mfgIB?->id,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Supriadi',
                'email'             => 'supriadi@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $mfgIB?->id,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Alberta Purnita Sari',
                'email'             => 'alberta.purnita@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $pplc?->id,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Alvyn Pramana Kartika Putra',
                'email'             => 'alvyn.pramana@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $pplc?->id,
                'email_verified_at' => now(),
            ],

            // ═══════════════════════════════════════════════════
            //  IV. LEVEL SEKSI (Section) - Di bawah Plant IKAR
            // ═══════════════════════════════════════════════════
            [
                'name'              => 'Novina',
                'email'             => 'novina@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $qms?->id,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Ilham Maula Widiyasa',
                'email'             => 'ilham.maula@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $mtcIK?->id,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Budi Sutiyo',
                'email'             => 'budi.sutiyo@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $mfgIK?->id,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'M. Azka Raihan',
                'email'             => 'azka.raihan@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $mfgIK?->id,
                'email_verified_at' => now(),
            ],

            // ═══════════════════════════════════════════════════
            //  IV. LEVEL SEKSI (Section) - Finance, HC & Audit
            // ═══════════════════════════════════════════════════
            [
                'name'              => 'Chandrika Aulia Putri',
                'email'             => 'chandrika.aulia@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $acc?->id, // Moved from IARM to Finance Accounting
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Labibah Arifiana',
                'email'             => 'labibah.arifiana@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $acc?->id, // Moved from IARM to Finance Accounting
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Sheilla Nur Rahma',
                'email'             => 'sheilla.rahma@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $acc?->id, // Moved from IARM to Finance Accounting
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Fauzan Nurdinsyah',
                'email'             => 'fauzan.nurdinsyah@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $prc?->id, // Moved to Procurement
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Bramansyah Haidar Islami',
                'email'             => 'bramansyah.haidar@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $prc?->id, // Moved to Procurement
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Fitri Nurmala Sari',
                'email'             => 'fitri.nurmala@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $hcgs?->id,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Dwi Kuntala Sari',
                'email'             => 'dwi.kuntala@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $hcgs?->id,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Ahmad Hamdan M.',
                'email'             => 'ahmad.hamdan@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $hcgs?->id,
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Imam Syaeful Aji',
                'email'             => 'imam.syaeful@establish.dev',
                'password'          => $defaultPw,
                'role'              => 'ka_sie',
                'dept_id'           => $hcgs?->id,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('  → UserSeeder: ' . count($users) . ' users seeded.');
    }
}
