<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Ensure enum includes 'admin' (and keep 'owner','kasir'). Keep 'manager' for backward compatibility.
        // Using raw SQL to avoid requiring doctrine/dbal for change().
        if (Schema::hasColumn('users', 'role')) {
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('owner','admin','manager','kasir') NOT NULL DEFAULT 'kasir'");
        }
    }

    public function down(): void
    {
        // Revert to previous known safe set without 'admin' (owner, manager, kasir)
        if (Schema::hasColumn('users', 'role')) {
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('owner','manager','kasir') NOT NULL DEFAULT 'kasir'");
        }
    }
};
