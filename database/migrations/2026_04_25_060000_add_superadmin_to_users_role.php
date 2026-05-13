<?php
// database/migrations/2026_04_25_060000_add_superadmin_to_users_role.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite: alter column type by recreating — but for this project
        // we simply modify the enum. For SQLite we use a pragma approach.
        // For MySQL, we alter the enum directly.

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('staff','ka_dept','ka_div','accounting','superadmin') DEFAULT 'staff'");
        }
        // SQLite doesn't enforce enum constraints, so no action needed
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('staff','ka_dept','ka_div','accounting') DEFAULT 'staff'");
        }
    }
};
