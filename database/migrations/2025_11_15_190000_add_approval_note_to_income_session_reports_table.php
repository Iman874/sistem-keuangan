<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('income_session_reports') && !Schema::hasColumn('income_session_reports','approval_note')) {
            Schema::table('income_session_reports', function (Blueprint $table) {
                $table->text('approval_note')->nullable()->after('note');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('income_session_reports') && Schema::hasColumn('income_session_reports','approval_note')) {
            Schema::table('income_session_reports', function (Blueprint $table) {
                $table->dropColumn('approval_note');
            });
        }
    }
};
