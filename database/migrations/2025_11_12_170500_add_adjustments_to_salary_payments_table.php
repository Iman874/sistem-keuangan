<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('salary_payments')) {
            if (!Schema::hasColumn('salary_payments', 'base_salary')) {
                Schema::table('salary_payments', function (Blueprint $table) {
                    $table->decimal('base_salary', 15, 2)->nullable()->after('year');
                });
            }
            if (!Schema::hasColumn('salary_payments', 'deduction_type')) {
                Schema::table('salary_payments', function (Blueprint $table) {
                    $table->string('deduction_type')->nullable()->after('base_salary'); // percent|fixed
                });
            }
            if (!Schema::hasColumn('salary_payments', 'deduction_value')) {
                Schema::table('salary_payments', function (Blueprint $table) {
                    $table->decimal('deduction_value', 15, 2)->nullable()->after('deduction_type');
                });
            }
            if (!Schema::hasColumn('salary_payments', 'deduction_desc')) {
                Schema::table('salary_payments', function (Blueprint $table) {
                    $table->text('deduction_desc')->nullable()->after('deduction_value');
                });
            }
            if (!Schema::hasColumn('salary_payments', 'bonus_type')) {
                Schema::table('salary_payments', function (Blueprint $table) {
                    $table->string('bonus_type')->nullable()->after('deduction_desc'); // percent|fixed
                });
            }
            if (!Schema::hasColumn('salary_payments', 'bonus_value')) {
                Schema::table('salary_payments', function (Blueprint $table) {
                    $table->decimal('bonus_value', 15, 2)->nullable()->after('bonus_type');
                });
            }
            if (!Schema::hasColumn('salary_payments', 'bonus_desc')) {
                Schema::table('salary_payments', function (Blueprint $table) {
                    $table->text('bonus_desc')->nullable()->after('bonus_value');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('salary_payments')) {
            if (Schema::hasColumn('salary_payments', 'bonus_desc')) {
                Schema::table('salary_payments', function (Blueprint $table) { $table->dropColumn('bonus_desc'); });
            }
            if (Schema::hasColumn('salary_payments', 'bonus_value')) {
                Schema::table('salary_payments', function (Blueprint $table) { $table->dropColumn('bonus_value'); });
            }
            if (Schema::hasColumn('salary_payments', 'bonus_type')) {
                Schema::table('salary_payments', function (Blueprint $table) { $table->dropColumn('bonus_type'); });
            }
            if (Schema::hasColumn('salary_payments', 'deduction_desc')) {
                Schema::table('salary_payments', function (Blueprint $table) { $table->dropColumn('deduction_desc'); });
            }
            if (Schema::hasColumn('salary_payments', 'deduction_value')) {
                Schema::table('salary_payments', function (Blueprint $table) { $table->dropColumn('deduction_value'); });
            }
            if (Schema::hasColumn('salary_payments', 'deduction_type')) {
                Schema::table('salary_payments', function (Blueprint $table) { $table->dropColumn('deduction_type'); });
            }
            if (Schema::hasColumn('salary_payments', 'base_salary')) {
                Schema::table('salary_payments', function (Blueprint $table) { $table->dropColumn('base_salary'); });
            }
        }
    }
};
