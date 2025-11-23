<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('user_salaries')) {
            if (!Schema::hasColumn('user_salaries', 'start_date')) {
                Schema::table('user_salaries', function (Blueprint $table) {
                    $table->date('start_date')->nullable()->after('active');
                });
            }
            if (!Schema::hasColumn('user_salaries', 'end_date')) {
                Schema::table('user_salaries', function (Blueprint $table) {
                    $table->date('end_date')->nullable()->after('start_date');
                });
            }
            if (!Schema::hasColumn('user_salaries', 'is_permanent')) {
                Schema::table('user_salaries', function (Blueprint $table) {
                    $table->boolean('is_permanent')->default(false)->after('end_date');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('user_salaries')) {
            if (Schema::hasColumn('user_salaries', 'is_permanent')) {
                Schema::table('user_salaries', function (Blueprint $table) { $table->dropColumn('is_permanent'); });
            }
            if (Schema::hasColumn('user_salaries', 'end_date')) {
                Schema::table('user_salaries', function (Blueprint $table) { $table->dropColumn('end_date'); });
            }
            if (Schema::hasColumn('user_salaries', 'start_date')) {
                Schema::table('user_salaries', function (Blueprint $table) { $table->dropColumn('start_date'); });
            }
        }
    }
};
