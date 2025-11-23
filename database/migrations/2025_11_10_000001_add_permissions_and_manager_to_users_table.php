<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'permissions')) {
                $table->json('permissions')->nullable()->after('role');
            }
            // Adjust enum role to include manager/admin if column is enum
            try {
                $table->enum('role', ['owner', 'manager', 'kasir'])->default('kasir')->change();
            } catch (\Throwable $e) {
                // Silently ignore if not supported by the database driver (e.g., sqlite dev)
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'permissions')) {
                $table->dropColumn('permissions');
            }
            try {
                $table->enum('role', ['owner', 'kasir'])->default('kasir')->change();
            } catch (\Throwable $e) {
                // ignore
            }
        });
    }
};
