<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('incomes', 'customer_name')) {
            Schema::table('incomes', function (Blueprint $table) {
                $table->string('customer_name', 100)->nullable()->after('description');
            });
        }
        if (!Schema::hasColumn('incomes', 'customer_email')) {
            Schema::table('incomes', function (Blueprint $table) {
                $table->string('customer_email', 150)->nullable()->after('customer_name');
            });
        }
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            if (Schema::hasColumn('incomes', 'customer_email')) {
                $table->dropColumn('customer_email');
            }
            if (Schema::hasColumn('incomes', 'customer_name')) {
                $table->dropColumn('customer_name');
            }
        });
    }
};
