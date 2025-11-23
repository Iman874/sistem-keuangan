<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('salary_payments')) {
            Schema::create('salary_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_salary_id');
                $table->unsignedSmallInteger('month');
                $table->unsignedSmallInteger('year');
                $table->date('paid_date');
                $table->decimal('amount', 14, 2);
                $table->enum('method', ['cash','qris','transfer'])->default('cash');
                $table->string('reference')->nullable();
                $table->text('description')->nullable();
                $table->unsignedBigInteger('created_by');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }
};
