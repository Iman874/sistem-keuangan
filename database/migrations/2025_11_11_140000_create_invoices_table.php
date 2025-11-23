<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->string('number')->unique();
                $table->enum('type', ['income','expense']);
                $table->unsignedBigInteger('cashier_id');
                $table->string('customer_name')->nullable();
                $table->string('customer_email')->nullable();
                $table->enum('payment_type', ['cash','qris'])->nullable();
                $table->date('date');
                $table->time('time');
                $table->decimal('subtotal', 14, 2)->default(0);
                $table->decimal('tax', 14, 2)->default(0);
                $table->decimal('total', 14, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
