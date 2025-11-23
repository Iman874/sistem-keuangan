<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            if (!Schema::hasColumn('incomes', 'session_report_id')) {
                $table->foreignId('session_report_id')->nullable()->after('user_id')->constrained('income_session_reports')->nullOnDelete();
            }
        });

        // Backfill existing incomes grouping by cashier/date/session
        $groups = DB::table('incomes')
            ->select('user_id','date','session',
                DB::raw('SUM(CASE WHEN payment_type = "cash" THEN amount ELSE 0 END) as total_cash'),
                DB::raw('SUM(CASE WHEN payment_type = "qris" THEN amount ELSE 0 END) as total_qris'),
                DB::raw('COUNT(*) as cnt'))
            ->whereNull('session_report_id')
            ->groupBy('user_id','date','session')
            ->get();

        foreach ($groups as $g) {
            if (!$g->user_id) { // skip records without user
                continue;
            }
            $reportId = DB::table('income_session_reports')->insertGetId([
                'cashier_id' => $g->user_id,
                'date' => $g->date,
                'session' => $g->session,
                'verified_by_cashier' => true,
                'submitted_at' => now(),
                'status' => 'approved', // legacy considered approved
                'manager_id' => null,
                'decided_at' => now(),
                'note' => 'Auto-approved legacy data',
                'total_cash' => $g->total_cash,
                'total_qris' => $g->total_qris,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('incomes')
                ->where('user_id', $g->user_id)
                ->where('date', $g->date)
                ->where('session', $g->session)
                ->update(['session_report_id' => $reportId]);
        }
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            if (Schema::hasColumn('incomes', 'session_report_id')) {
                $table->dropForeign(['session_report_id']);
                $table->dropColumn('session_report_id');
            }
        });
    }
};
