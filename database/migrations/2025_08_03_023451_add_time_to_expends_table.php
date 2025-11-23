<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the column already exists
        if (!Schema::hasColumn('expends', 'time')) {
            Schema::table('expends', function (Blueprint $table) {
                // Add time column
                $table->time('time')->after('date')->nullable();
            });

            // Set default time for existing records
            DB::table('expends')->whereNull('time')->update([
                'time' => '12:00:00'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('expends', 'time')) {
            Schema::table('expends', function (Blueprint $table) {
                $table->dropColumn('time');
            });
        }
    }
};
