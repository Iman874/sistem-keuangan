<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncomeSessionReport;
use App\Models\Income;
use App\Notifications\SessionReportDecided;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeApprovalController extends Controller
{
    protected function ensureCanApprove()
    {
        $user = auth()->user();
        if (!($user && $user instanceof \App\Models\User && method_exists($user,'hasPermission') && $user->hasPermission('income.approve'))) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $this->ensureCanApprove();
        $reports = IncomeSessionReport::with('cashier')
            ->where('status','pending')
            ->orderBy('date','desc')
            ->orderBy('session')
            ->paginate(15);
        return view('admin.income-approvals.index', compact('reports'));
    }

    public function show(IncomeSessionReport $report)
    {
        $this->ensureCanApprove();
        $report->load(['cashier','incomes.category']);
        return view('admin.income-approvals.show', compact('report'));
    }

    public function approve(IncomeSessionReport $report)
    {
        $this->ensureCanApprove();
        if ($report->status !== 'pending') {
            return back()->with('error','Hanya dapat menyetujui laporan berstatus pending.');
        }
        $approvalNote = request()->input('approval_note');
        DB::transaction(function () use ($report, $approvalNote) {
            $report->status = 'approved';
            $report->manager_id = auth()->id();
            $report->decided_at = now();
            if ($approvalNote) {
                $report->approval_note = $approvalNote; // optional
            }
            $report->save();
        });
        // Mark related admin notifications as read automatically
        \Illuminate\Notifications\DatabaseNotification::where('type', \App\Notifications\SessionReportSubmitted::class)
            ->where('data->report_id', $report->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        // Notify cashier of decision
        if ($report->cashier) {
            $report->cashier->notify(new SessionReportDecided($report));
        }
        return redirect()->route('admin.income-approvals.show', $report)->with('success','Laporan disetujui.');
    }

    public function reject(Request $request, IncomeSessionReport $report)
    {
        $this->ensureCanApprove();
        if ($report->status !== 'pending') {
            return back()->with('error','Hanya dapat menolak laporan berstatus pending.');
        }
        $request->validate([
            'note' => 'required|string|max:500'
        ]);
        DB::transaction(function () use ($report, $request) {
            $report->status = 'rejected';
            $report->manager_id = auth()->id();
            $report->decided_at = now();
            $report->note = $request->note;
            // clear any previous approval note if existed from earlier cycle
            $report->approval_note = null;
            $report->save();
            // unlock incomes for resubmission
            Income::where('session_report_id', $report->id)->update(['session_report_id' => null]);
        });
        // Mark related admin notifications as read automatically on reject
        \Illuminate\Notifications\DatabaseNotification::where('type', \App\Notifications\SessionReportSubmitted::class)
            ->where('data->report_id', $report->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        if ($report->cashier) {
            $report->cashier->notify(new SessionReportDecided($report));
        }
        return redirect()->route('admin.income-approvals.show', $report)->with('success','Laporan ditolak dan item telah dibuka untuk perbaikan.');
    }
}
