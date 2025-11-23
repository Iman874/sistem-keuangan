<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pemasukkan', function (Blueprint $table) {
            if (!Schema::hasColumn('pemasukkan', 'type')) {
                $table->enum('type', ['indoor','outdoor'])->default('indoor')->after('nama_pemasukkan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pemasukkan', function (Blueprint $table) {
            if (Schema::hasColumn('pemasukkan', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
