<?php
// database/migrations/2024_01_01_000002_add_dept_id_and_role_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan dept_id SETELAH id
            $table->foreignId('dept_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('departments')
                  ->nullOnDelete();

            // Tambahkan role SETELAH password
            $table->enum('role', ['staff', 'ka_dept', 'ka_div', 'accounting'])
                  ->default('staff')
                  ->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['dept_id']);
            $table->dropColumn(['dept_id', 'role']);
        });
    }
};