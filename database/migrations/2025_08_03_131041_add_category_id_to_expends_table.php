<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expends', function (Blueprint $table) {
            // Add foreign key for expense category
            $table->foreignId('category_id')->nullable()->constrained('expense_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expends', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
