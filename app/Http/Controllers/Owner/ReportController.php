<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Expend;
use App\Models\Income;
use App\Models\Modal;
use App\Models\Pemasukkan;
use App\Models\SalaryPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PDF;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomReportExport;

class ReportController extends Controller
{
    public function generateFinancialReport(Request $request)
    {
        // Validate request
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Get date range
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        // Format for display
        $startDateFormatted = $startDate->format('d F Y');
        $endDateFormatted = $endDate->format('d F Y');
        
        // Initialize these arrays to prevent undefined variable errors
        $incomeByCategory = [];
        $incomeCategoryValues = [];
        
        // Get income data (only approved session reports)
        $incomes = Income::with(['category','sessionReport'])
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereHas('sessionReport', function($q){
                $q->where('status','approved');
            })
            ->orderBy('date', 'desc')
            ->get();
    
        // Get expense data
        $expenses = Expend::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date', 'desc')
            ->get();

        // Get salary payment data (treated as expenses)
        $salaryPayments = SalaryPayment::with('employee')
            ->whereBetween('paid_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('paid_date', 'desc')
            ->get();

        // Normalize salary payment attributes to align with expense table structure
        $salaryPayments->each(function($sp){
            $sp->date = $sp->paid_date; // for unified date grouping
            $sp->session = '-'; // no session concept for salary
            $sp->type = 'gaji';
            $sp->description = 'Gaji: '.($sp->employee->name ?? 'Karyawan').($sp->description ? ' - '.$sp->description : '');
        });

        // Merge regular expenses and salary payments for unified listing
        $mergedExpenses = $expenses->concat($salaryPayments);
    
        // Get modal data
        $modals = Modal::whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('tanggal', 'desc')
            ->get();
    
    // Calculate totals (+ separate payment type summary)
    $totalIncome = $incomes->sum('amount');
    $totalCashIncome = $incomes->where('payment_type','cash')->sum('amount');
    $totalQrisIncome = $incomes->where('payment_type','qris')->sum('amount');
        $totalSalaryExpense = $salaryPayments->sum('amount');
        $totalExpense = $expenses->sum('amount') + $totalSalaryExpense;
        $totalProfit = $totalIncome - $totalExpense;
        $totalCapital = Modal::sum('harga');
    
        // Session data
        $morningIncome = $incomes->where('session', 'pagi')->sum('amount');
        // Salary payments do not belong to a session; keep session breakdown to regular expenses only
        $morningExpense = $expenses->where('session', 'pagi')->sum('amount');
        $afternoonIncome = $incomes->where('session', 'sore')->sum('amount');
        $afternoonExpense = $expenses->where('session', 'sore')->sum('amount');
        
        // Category summaries for pie chart data
        $categories = Pemasukkan::all();
        foreach ($categories as $category) {
            $amount = $incomes->where('pemasukkan_id', $category->id)->sum('amount');
            if ($amount > 0) {
                $incomeByCategory[] = $category->nama_pemasukkan;
                $incomeCategoryValues[] = $amount;
            }
        }
        
        // Removed legacy other_source aggregation (column deprecated)
        
        // Current date for the report
        $currentDate = Carbon::now()->format('d F Y H:i:s');
        
        // Generate PDF
        $pdf = PDF::loadView('owner.reports.financial', compact(
            'startDateFormatted',
            'endDateFormatted',
            'incomes',
            'mergedExpenses',
            'salaryPayments',
            'modals',
            'totalIncome',
            'totalExpense',
            'totalProfit',
            'totalCapital',
            'totalCashIncome',
            'totalQrisIncome',
            'totalSalaryExpense',
            'morningIncome',
            'morningExpense',
            'afternoonIncome',
            'afternoonExpense',
            'incomeByCategory',
            'incomeCategoryValues',
            'currentDate'
        ));
        
        // Set paper to landscape for better tables
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download('laporan-keuangan-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Generate custom report (PDF, Excel, CSV) based on transaction type & range.
     * Expected query params:
     * - transaction_type: income|expense|both
     * - start_date, end_date: already calculated client-side
     * - format: pdf|xlsx|csv
     */
    public function generateCustomReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'transaction_type' => 'nullable|in:income,expense,both',
            'format' => 'nullable|in:pdf,xlsx,csv'
        ]);

        $transactionType = $request->input('transaction_type', 'expense');
        $format = $request->input('format', 'pdf');

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Base queries within range
        $incomeQuery = Income::with(['category','user','sessionReport'])
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereHas('sessionReport', function($q){
                $q->where('status','approved');
            })
            ->orderBy('date');
        $expenseQuery = Expend::with('user')
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date');

        $salaryPaymentQuery = SalaryPayment::with(['employee','creator'])
            ->whereBetween('paid_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('paid_date');

        $incomes = collect();
        $expenses = collect();
        $salaryPayments = collect();

        if ($transactionType === 'income' || $transactionType === 'both') {
            $incomes = $incomeQuery->get();
        }
        if ($transactionType === 'expense' || $transactionType === 'both') {
            $expenses = $expenseQuery->get();
            $salaryPayments = $salaryPaymentQuery->get();
            // Normalize salary payments inline for unified display/export
            $salaryPayments->each(function($sp){
                $sp->date = $sp->paid_date;
                $sp->session = '-';
                $sp->description = 'Gaji: '.($sp->employee->name ?? 'Karyawan').($sp->description ? ' - '.$sp->description : '');
            });
            $expenses = $expenses->concat($salaryPayments);
        }

        $totalIncome = $incomes->sum('amount');
        $totalExpense = $expenses->sum('amount');
        $totalProfit = $totalIncome - $totalExpense;

        if ($format === 'pdf') {
            // Use dedicated custom view for trimmed output
            $pdf = PDF::loadView('owner.reports.custom', [
                'startDateFormatted' => $startDate->format('d F Y'),
                'endDateFormatted' => $endDate->format('d F Y'),
                'incomes' => $incomes,
                'expenses' => $expenses,
                'totalIncome' => $totalIncome,
                'totalExpense' => $totalExpense,
                'totalProfit' => $totalProfit,
                'currentDate' => Carbon::now()->format('d F Y H:i:s')
            ])->setPaper('a4','landscape');
            return $pdf->download('laporan-custom-'.$transactionType.'-'.$startDate->format('Ymd').'-'.$endDate->format('Ymd').'.pdf');
        }

        if ($format === 'csv') {
            $filename = 'laporan-custom-'.$transactionType.'-'.$startDate->format('Ymd').'-'.$endDate->format('Ymd').'.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ];
            $callback = function() use ($incomes, $expenses, $transactionType) {
                $FH = fopen('php://output','w');
                // Header
                if ($transactionType === 'income' || $transactionType === 'both') {
                    fputcsv($FH, ['INCOME','Tanggal','Sesi','Jumlah','Sumber/Kategori','Kasir']);
                    foreach ($incomes as $i) {
                        fputcsv($FH, [
                            'INCOME',
                            $i->date->format('Y-m-d'),
                            $i->session,
                            $i->amount,
                            $i->other_source ? $i->description : ($i->category->nama_pemasukkan ?? ''),
                            optional($i->user)->name
                        ]);
                    }
                }
                if ($transactionType === 'expense' || $transactionType === 'both') {
                    fputcsv($FH, ['EXPENSE','Tanggal','Sesi','Jumlah','Deskripsi','Kasir']);
                    foreach ($expenses as $e) {
                        $typeLabel = ($e->type ?? '') === 'gaji' ? 'EXPENSE-SALARY' : 'EXPENSE';
                        $kasir = null;
                        if (method_exists($e,'user')) { // Expend
                            $kasir = optional($e->user)->name;
                        }
                        if (($e->type ?? '') === 'gaji' && isset($e->creator)) {
                            $kasir = optional($e->creator)->name;
                        }
                        fputcsv($FH, [
                            $typeLabel,
                            optional($e->date)->format('Y-m-d'),
                            $e->session,
                            $e->amount,
                            $e->description,
                            $kasir
                        ]);
                    }
                }
                fclose($FH);
            };
            return new StreamedResponse($callback, 200, $headers);
        }

        if ($format === 'xlsx') {
            return Excel::download(
                new CustomReportExport($incomes, $expenses, $transactionType),
                'laporan-custom-'.$transactionType.'-'.$startDate->format('Ymd').'-'.$endDate->format('Ymd').'.xlsx'
            );
        }

        // Default fallback PDF using custom view
        $pdf = PDF::loadView('owner.reports.custom', [
            'startDateFormatted' => $startDate->format('d F Y'),
            'endDateFormatted' => $endDate->format('d F Y'),
            'incomes' => $incomes,
            'expenses' => $expenses,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'totalProfit' => $totalProfit,
            'currentDate' => Carbon::now()->format('d F Y H:i:s')
        ])->setPaper('a4','landscape');
        return $pdf->download('laporan-custom-'.$transactionType.'-'.$startDate->format('Ymd').'-'.$endDate->format('Ymd').'.pdf');
    }
}