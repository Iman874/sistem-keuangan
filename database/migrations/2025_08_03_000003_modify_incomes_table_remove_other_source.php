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
        // Langkah 1: Perbaiki data terlebih dahulu
        DB::statement('UPDATE incomes SET pemasukkan_id = (SELECT id FROM pemasukkan LIMIT 1) WHERE pemasukkan_id IS NULL');

        // Langkah 2: Hapus foreign key constraint lama
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropForeign(['pemasukkan_id']);
        });

        // Langkah 3: Hapus kolom other_source dan ubah pemasukkan_id
        Schema::table('incomes', function (Blueprint $table) {
            // Menghapus kolom other_source
            $table->dropColumn('other_source');

            // Mengubah kolom pemasukkan_id menjadi tidak nullable
            $table->foreignId('pemasukkan_id')->nullable(false)->change();

            // Menambahkan kembali foreign key dengan constraint yang benar
            $table->foreign('pemasukkan_id')->references('id')->on('pemasukkan')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Langkah 1: Hapus foreign key constraint baru
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropForeign(['pemasukkan_id']);
        });

        // Langkah 2: Kembalikan seperti semula
        Schema::table('incomes', function (Blueprint $table) {
            // Menambah kembali kolom other_source
            $table->boolean('other_source')->default(false)->after('date');

            // Mengubah kembali pemasukkan_id menjadi nullable
            $table->foreignId('pemasukkan_id')->nullable()->change();

            // Menambahkan kembali foreign key dengan constraint lama
            $table->foreign('pemasukkan_id')->references('id')->on('pemasukkan')->onDelete('set null');
        });
    }
};
