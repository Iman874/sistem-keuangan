<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('income_session_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cashier_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->enum('session', ['pagi','sore']);
            $table->boolean('verified_by_cashier')->default(false);
            $table->dateTime('submitted_at')->nullable();
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('decided_at')->nullable();
            $table->text('note')->nullable();
            $table->decimal('total_cash', 12, 2)->default(0);
            $table->decimal('total_qris', 12, 2)->default(0);
            $table->timestamps();
            $table->unique(['cashier_id','date','session'], 'unique_session_per_cashier');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('income_session_reports');
    }
};
