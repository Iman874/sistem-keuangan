<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\IncomeSessionReport;
use App\Models\User;
use App\Notifications\SessionReportSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SessionReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
            // Allow all kasir by role as baseline; also allow if explicit permission present
            if (!($user && $user instanceof \App\Models\User && ($user->role === 'kasir' || (method_exists($user,'hasPermission') && $user->hasPermission('income.submit'))))) {
                abort(403);
            }
        $date = $request->get('date', now()->format('Y-m-d'));
        $session = $request->get('session', 'pagi');
        $userId = auth()->id();

        // Latest report (any status) for this date/session
        $latestReport = IncomeSessionReport::where('cashier_id', $userId)
            ->where('date', $date)
            ->where('session', $session)
            ->orderByDesc('id')
            ->first();

        // History reports for today (approved or rejected) excluding drafts (drafts are implicit via incomes)
        $historyReports = IncomeSessionReport::where('cashier_id', $userId)
            ->where('date', $date)
            ->where('session', $session)
            ->whereIn('status', ['approved','rejected'])
            ->orderByDesc('id')
            ->get();

        // Draft incomes (not yet linked)
        $draftIncomes = Income::with('category')
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->where('session', $session)
            ->whereNull('session_report_id')
            ->get();

        $totals = [
            'cash' => $draftIncomes->where('payment_type','cash')->sum('amount'),
            'qris' => $draftIncomes->where('payment_type','qris')->sum('amount'),
        ];

        return view('kasir.session-report.index', [
            'date' => $date,
            'session' => $session,
            'draftIncomes' => $draftIncomes,
            'totals' => $totals,
            'report' => $latestReport, // backward compatibility variable name
            'historyReports' => $historyReports,
        ]);
    }

    public function submit(Request $request)
    {
        $user = auth()->user();
            if (!($user && $user instanceof \App\Models\User && ($user->role === 'kasir' || (method_exists($user,'hasPermission') && $user->hasPermission('income.submit'))))) {
                abort(403);
            }
        $request->validate([
            'date' => 'required|date',
            'session' => 'required|in:pagi,sore',
            'verified' => 'accepted',
        ],[
            'verified.accepted' => 'Anda harus mencentang verifikasi sebelum submit.'
        ]);

        $date = $request->input('date');
        $session = $request->input('session');
        $userId = auth()->id();

        // Determine if latest report allows new submission
        $latest = IncomeSessionReport::where('cashier_id', $userId)
            ->where('date', $date)
            ->where('session', $session)
            ->orderByDesc('id')
            ->first();
        // If there's an existing rejected report, reuse it (update) instead of creating a new row
        $isResubmissionOfRejected = $latest && $latest->status === 'rejected';

        // Prevent creating if an active (pending/approved) report exists
        if ($latest && in_array($latest->status, ['pending','approved'])) {
            return redirect()->route('kasir.session-report.index', ['date'=>$date,'session'=>$session])
                ->with('error', 'Laporan sesi sudah dibuat atau menunggu persetujuan.');
        }

        // Calculate next attempt number (increment if rejected exists)
        $nextAttempt = $isResubmissionOfRejected ? (($latest->attempt ?? 1) + 1) : 1;

        $incomes = Income::where('user_id', $userId)
            ->whereDate('date', $date)
            ->where('session', $session)
            ->whereNull('session_report_id')
            ->get();

        if ($incomes->isEmpty()) {
            return redirect()->route('kasir.session-report.index', ['date'=>$date,'session'=>$session])
                ->with('error', 'Tidak ada data draft untuk disubmit.');
        }

        $totalCash = $incomes->where('payment_type','cash')->sum('amount');
        $totalQris = $incomes->where('payment_type','qris')->sum('amount');

        DB::transaction(function () use ($latest,$isResubmissionOfRejected,$userId,$date,$session,$totalCash,$totalQris,$incomes,$nextAttempt) {
            if ($isResubmissionOfRejected) {
                // Update existing rejected row
                $latest->update([
                    'verified_by_cashier' => true,
                    'submitted_at' => now(),
                    'status' => 'pending',
                    'attempt' => $nextAttempt,
                    'total_cash' => $totalCash,
                    'total_qris' => $totalQris,
                    'note' => null,
                    'manager_id' => null,
                    'decided_at' => null,
                ]);
                $report = $latest;
            } else {
                $report = IncomeSessionReport::create([
                    'cashier_id' => $userId,
                    'date' => $date,
                    'session' => $session,
                    'verified_by_cashier' => true,
                    'submitted_at' => now(),
                    'status' => 'pending',
                    'attempt' => $nextAttempt,
                    'total_cash' => $totalCash,
                    'total_qris' => $totalQris,
                ]);
            }

            Income::whereIn('id', $incomes->pluck('id'))
                ->update(['session_report_id' => $report->id]);

            $admins = User::where('role','admin')->get();
            foreach ($admins as $admin) {
                if (method_exists($admin,'hasPermission') && $admin->hasPermission('income.approve')) {
                    $admin->notify(new SessionReportSubmitted($report));
                }
            }
        });

        return redirect()->route('kasir.session-report.index', ['date'=>$date,'session'=>$session])
            ->with('success', 'Laporan sesi berhasil dikirim (status pending).');
    }

    public function resubmit(Request $request)
    {
        $user = auth()->user();
        if (!($user && $user instanceof \App\Models\User && ($user->role === 'kasir' || (method_exists($user,'hasPermission') && $user->hasPermission('income.submit'))))) {
            abort(403);
        }
        $request->validate([
            'date' => 'required|date',
            'session' => 'required|in:pagi,sore',
            'verified' => 'accepted',
        ],[
            'verified.accepted' => 'Anda harus mencentang verifikasi sebelum submit ulang.'
        ]);
        $date = $request->input('date');
        $session = $request->input('session');
        $userId = auth()->id();

        $latest = IncomeSessionReport::where('cashier_id', $userId)
            ->where('date', $date)
            ->where('session', $session)
            ->orderByDesc('id')
            ->first();
        if (!$latest || $latest->status !== 'rejected') {
            return redirect()->route('kasir.session-report.index', ['date'=>$date,'session'=>$session])
                ->with('error','Tidak ada laporan ditolak untuk disubmit ulang.');
        }

        $incomes = Income::where('user_id', $userId)
            ->whereDate('date',$date)
            ->where('session',$session)
            ->whereNull('session_report_id')
            ->get();
        if ($incomes->isEmpty()) {
            return redirect()->route('kasir.session-report.index', ['date'=>$date,'session'=>$session])
                ->with('error','Tidak ada draft untuk submit ulang.');
        }
        $totalCash = $incomes->where('payment_type','cash')->sum('amount');
        $totalQris = $incomes->where('payment_type','qris')->sum('amount');
        $nextAttempt = ($latest->attempt ?? 1) + 1;

        DB::transaction(function () use ($latest,$userId,$date,$session,$totalCash,$totalQris,$incomes,$nextAttempt) {
            // Update existing rejected report instead of creating a new row (avoid unique constraint violation)
            $latest->update([
                'verified_by_cashier' => true,
                'submitted_at' => now(),
                'status' => 'pending',
                'attempt' => $nextAttempt,
                'total_cash' => $totalCash,
                'total_qris' => $totalQris,
                'note' => null, // clear previous rejection note
                'manager_id' => null,
                'decided_at' => null,
            ]);
            Income::whereIn('id', $incomes->pluck('id'))
                ->update(['session_report_id' => $latest->id]);
            $admins = User::where('role','admin')->get();
            foreach ($admins as $admin) {
                if (method_exists($admin,'hasPermission') && $admin->hasPermission('income.approve')) {
                    $admin->notify(new SessionReportSubmitted($latest));
                }
            }
        });
        return redirect()->route('kasir.session-report.index', ['date'=>$date,'session'=>$session])
            ->with('success','Submit ulang laporan berhasil dikirim (status pending).');
    }
}
