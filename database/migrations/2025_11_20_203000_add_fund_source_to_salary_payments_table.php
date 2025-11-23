<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('salary_payments') && !Schema::hasColumn('salary_payments','fund_source')) {
            Schema::table('salary_payments', function (Blueprint $table) {
                $table->enum('fund_source', ['kasir','bank','tunai'])->default('kasir')->after('method');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('salary_payments') && Schema::hasColumn('salary_payments','fund_source')) {
            Schema::table('salary_payments', function (Blueprint $table) {
                $table->dropColumn('fund_source');
            });
        }
    }
};
