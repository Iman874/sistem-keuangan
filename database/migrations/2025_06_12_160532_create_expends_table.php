<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expends', function (Blueprint $table) {
            $table->id();
            $table->enum('session', ['pagi', 'sore']);
            $table->decimal('amount', 12, 2);
            $table->date('date');
            $table->enum('type', ['harian', 'bulanan']);
            $table->text('description');
            $table->string('receipt_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expends');
    }
};