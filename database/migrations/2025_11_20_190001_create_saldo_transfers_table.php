<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('saldo_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('source_account');
            $table->string('destination_account');
            $table->decimal('amount',15,2);
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->time('time');
            $table->text('note')->nullable();
            $table->unsignedBigInteger('income_id')->nullable();
            $table->unsignedBigInteger('expend_id')->nullable();
            $table->unsignedBigInteger('invoice_income_id')->nullable();
            $table->unsignedBigInteger('invoice_expend_id')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('saldo_transfers');
    }
};