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
        // First add nullable columns if they don't exist
        Schema::table('expends', function (Blueprint $table) {
            if (!Schema::hasColumn('expends', 'user_id')) {
                $table->foreignId('user_id')->after('id')->nullable();
            }
            if (!Schema::hasColumn('expends', 'time')) {
                $table->time('time')->after('date')->nullable();
            }
        });

        // Check if there are valid users
        $validUserIds = DB::table('users')->pluck('id')->toArray();

        if (empty($validUserIds)) {
            // Hindari insert langsung di migration. Biarkan seeder menambahkan user default.
            // Untuk menjaga konsistensi, gunakan null sementara dan biarkan constraint tetap nullable sebelum diisi via seeder.
            $defaultUserId = null;
        } else {
            $defaultUserId = $validUserIds[0]; // Ambil ID user pertama yang valid
        }

        // Set default values for existing records
        if (!is_null($defaultUserId)) {
            DB::table('expends')->whereNull('user_id')->update([
                'user_id' => $defaultUserId,
                'time' => '12:00:00'
            ]);
        } else {
            DB::table('expends')->whereNull('time')->update(['time' => '12:00:00']);
        }

        // Update any invalid user_id references
        $invalidRecords = DB::table('expends')
            ->whereNotNull('user_id')
            ->whereNotIn('user_id', $validUserIds)
            ->update(['user_id' => $defaultUserId]);

        // Make user_id NOT NULL and add constraint if it exists
    if (Schema::hasColumn('expends', 'user_id')) {
            Schema::table('expends', function (Blueprint $table) {
        // Tetap nullable jika belum ada user; nanti bisa diatur NOT NULL setelah seeder user berjalan.
        $table->foreignId('user_id')->nullable()->change();

                // Check if the foreign key constraint already exists
                $foreignKeys = DB::select(
                    "SELECT * FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = ? 
                    AND TABLE_NAME = 'expends'
                    AND COLUMN_NAME = 'user_id'
                    AND REFERENCED_TABLE_NAME = 'users'",
                    [config('database.connections.mysql.database')]
                );

                if (empty($foreignKeys)) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expends', function (Blueprint $table) {
            // Check if foreign key exists before dropping it
            $foreignKeys = DB::select(
                "SELECT * FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = ? 
                AND TABLE_NAME = 'expends'
                AND COLUMN_NAME = 'user_id'
                AND REFERENCED_TABLE_NAME = 'users'",
                [config('database.connections.mysql.database')]
            );

            if (!empty($foreignKeys)) {
                $table->dropForeign(['user_id']);
            }

            // Drop columns if they exist
            if (Schema::hasColumn('expends', 'user_id')) {
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('expends', 'time')) {
                $table->dropColumn('time');
            }
        });
    }
};
