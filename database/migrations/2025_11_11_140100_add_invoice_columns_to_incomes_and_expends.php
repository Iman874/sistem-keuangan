<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('incomes', 'invoice_id')) {
            Schema::table('incomes', function (Blueprint $table) {
                $table->unsignedBigInteger('invoice_id')->nullable()->after('session_report_id');
            });
        }
        if (!Schema::hasColumn('incomes', 'qty')) {
            Schema::table('incomes', function (Blueprint $table) {
                $table->unsignedInteger('qty')->nullable()->default(1)->after('amount');
            });
        }
        if (!Schema::hasColumn('incomes', 'unit_price')) {
            Schema::table('incomes', function (Blueprint $table) {
                $table->decimal('unit_price', 14, 2)->nullable()->after('qty');
            });
        }
        if (!Schema::hasColumn('expends', 'invoice_id')) {
            Schema::table('expends', function (Blueprint $table) {
                $table->unsignedBigInteger('invoice_id')->nullable()->after('receipt_image');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('incomes', 'invoice_id')) {
            Schema::table('incomes', function (Blueprint $table) { $table->dropColumn(['invoice_id']); });
        }
        if (Schema::hasColumn('incomes', 'qty')) {
            Schema::table('incomes', function (Blueprint $table) { $table->dropColumn(['qty']); });
        }
        if (Schema::hasColumn('incomes', 'unit_price')) {
            Schema::table('incomes', function (Blueprint $table) { $table->dropColumn(['unit_price']); });
        }
        if (Schema::hasColumn('expends', 'invoice_id')) {
            Schema::table('expends', function (Blueprint $table) { $table->dropColumn(['invoice_id']); });
        }
    }
};
