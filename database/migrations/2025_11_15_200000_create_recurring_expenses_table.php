<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('recurring_expenses')) {
            Schema::create('recurring_expenses', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('amount', 14, 2);
                $table->enum('frequency', ['monthly'])->default('monthly');
                $table->date('next_due_date');
                $table->date('last_paid_date')->nullable();
                $table->boolean('active')->default(true);
                $table->unsignedTinyInteger('reminders_sent')->default(0); // count in current cycle (max 3)
                $table->date('last_reminder_date')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_expenses');
    }
};
