<?php
// database/seeders/SuperadminSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'superadmin@establish.dev'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'role'     => 'superadmin',
                'dept_id'  => null,
            ]
        );

        $this->command->info('✅ Superadmin user created: superadmin@establish.dev / password');
    }
}
