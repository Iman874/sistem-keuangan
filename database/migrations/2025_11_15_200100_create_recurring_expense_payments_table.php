<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('recurring_expense_payments')) {
            Schema::create('recurring_expense_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('recurring_expense_id')->constrained('recurring_expenses')->cascadeOnDelete();
                $table->foreignId('expend_id')->nullable()->constrained('expends')->nullOnDelete();
                $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
                $table->foreignId('cashier_id')->constrained('users')->cascadeOnDelete();
                $table->date('paid_date');
                $table->decimal('amount', 14, 2);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_expense_payments');
    }
};
