<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Expend;
use App\Models\Income;
use App\Models\Modal;
use App\Models\Pemasukkan;
use App\Models\SalaryPayment;
use App\Models\SaldoTopup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Set locale for Carbon
        Carbon::setLocale('id');

        // Get filter parameters
        $period = $request->input('period', 'daily'); // Default to daily
        $dateParam = $request->input('date');

        // Set default dates based on period
        if ($dateParam) {
            $selectedDate = Carbon::parse($dateParam);
        } else {
            $selectedDate = Carbon::now();
        }

        // Set date range based on period
        $startDate = null;
        $endDate = null;
        $dateRangeLabel = '';

        switch ($period) {
            case 'daily':
                $startDate = $selectedDate->copy()->startOfDay();
                $endDate = $selectedDate->copy()->endOfDay();
                $currentYear = $selectedDate->format('Y');
                $currentMonth = $selectedDate->format('m');
                $currentMonthYear = $selectedDate->isoFormat('D MMMM Y');
                $dateRangeLabel = $selectedDate->isoFormat('D MMMM Y');
                break;

            case 'weekly':
                $startDate = $selectedDate->copy()->startOfWeek(); // Monday
                $endDate = $selectedDate->copy()->endOfWeek(); // Sunday
                $currentYear = $selectedDate->format('Y');
                $currentMonth = $selectedDate->format('m');
                $currentMonthYear = $startDate->isoFormat('D MMMM') . ' - ' . $endDate->isoFormat('D MMMM Y');
                $dateRangeLabel = $startDate->isoFormat('D MMMM') . ' - ' . $endDate->isoFormat('D MMMM Y');
                break;

            case 'monthly':
                $startDate = $selectedDate->copy()->startOfMonth();
                $endDate = $selectedDate->copy()->endOfMonth();
                $currentYear = $selectedDate->format('Y');
                $currentMonth = $selectedDate->format('m');
                $currentMonthYear = $selectedDate->isoFormat('MMMM Y');
                $dateRangeLabel = $selectedDate->isoFormat('MMMM Y');
                break;

            case 'yearly':
                $startDate = $selectedDate->copy()->startOfYear();
                $endDate = $selectedDate->copy()->endOfYear();
                $currentYear = $selectedDate->format('Y');
                $currentMonth = $selectedDate->format('m');
                $currentMonthYear = $selectedDate->format('Y');
                $dateRangeLabel = 'Tahun ' . $selectedDate->format('Y');
                break;

            default:
                // Default to monthly
                $startDate = $selectedDate->copy()->startOfMonth();
                $endDate = $selectedDate->copy()->endOfMonth();
                $currentYear = $selectedDate->format('Y');
                $currentMonth = $selectedDate->format('m');
                $currentMonthYear = $selectedDate->isoFormat('MMMM Y');
                $dateRangeLabel = $selectedDate->isoFormat('MMMM Y');
                break;
        }

        // ======== MONTHLY FINANCES ========

        // Monthly income and expense data for the area chart
        $monthLabels = [];
        $monthlyIncomes = [];
        $monthlyExpenses = [];

        if ($period === 'monthly') {
            // Show all months in the current year
            for ($i = 1; $i <= 12; $i++) {
                $date = Carbon::createFromDate($currentYear, $i, 1);
                $monthLabels[] = $date->format('M');

                $monthIncome = Income::whereYear('date', $currentYear)
                    ->whereMonth('date', $i)
                    ->whereHas('sessionReport', function($q){ $q->where('status','approved'); })
                    ->sum('amount');

                $monthExpenseExpend = Expend::whereYear('date', $currentYear)
                    ->whereMonth('date', $i)
                    ->sum('amount');
                $monthExpenseSalary = SalaryPayment::whereYear('paid_date', $currentYear)
                    ->whereMonth('paid_date', $i)
                    ->sum('amount');
                $monthExpense = $monthExpenseExpend + $monthExpenseSalary;

                $monthlyIncomes[] = $monthIncome;
                $monthlyExpenses[] = $monthExpense;
            }
        } elseif ($period === 'weekly') {
            // Show days of the selected week
            for ($i = 0; $i < 7; $i++) {
                $date = $startDate->copy()->addDays($i);
                $monthLabels[] = $date->format('D');

                $dayIncome = Income::whereDate('date', $date->format('Y-m-d'))
                    ->whereHas('sessionReport', function($q){ $q->where('status','approved'); })
                    ->sum('amount');

                $dayExpenseExpend = Expend::whereDate('date', $date->format('Y-m-d'))
                    ->sum('amount');
                $dayExpenseSalary = SalaryPayment::whereDate('paid_date', $date->format('Y-m-d'))
                    ->sum('amount');
                $dayExpense = $dayExpenseExpend + $dayExpenseSalary;

                $monthlyIncomes[] = $dayIncome;
                $monthlyExpenses[] = $dayExpense;
            }
        } elseif ($period === 'daily') {
            // Show hours for the selected day
            $dailySalaryTotal = SalaryPayment::whereDate('paid_date', $selectedDate->format('Y-m-d'))
                ->sum('amount');
            for ($i = 0; $i < 24; $i += 2) { // Every 2 hours
                $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
                $monthLabels[] = $hour . ':00';

                $startHour = $selectedDate->copy()->setTime($i, 0, 0);
                $endHour = $selectedDate->copy()->setTime($i + 1, 59, 59);

                $hourIncome = Income::whereDate('date', $selectedDate->format('Y-m-d'))
                    ->whereTime('time', '>=', $startHour->format('H:i:s'))
                    ->whereTime('time', '<=', $endHour->format('H:i:s'))
                    ->whereHas('sessionReport', function($q){ $q->where('status','approved'); })
                    ->sum('amount');

                $hourExpenseExpend = Expend::whereDate('date', $selectedDate->format('Y-m-d'))
                    ->whereTime('time', '>=', $startHour->format('H:i:s'))
                    ->whereTime('time', '<=', $endHour->format('H:i:s'))
                    ->sum('amount');
                // Allocate all salary payments to first bucket for visibility (could refine later)
                $hourExpenseSalary = ($i === 0) ? $dailySalaryTotal : 0;
                $hourExpense = $hourExpenseExpend + $hourExpenseSalary;

                $monthlyIncomes[] = $hourIncome;
                $monthlyExpenses[] = $hourExpense;
            }
        }

        // For yearly period, you'll want to update the chart data to show months
        if ($period === 'yearly') {
            // Monthly labels for the whole year
            $monthLabels = [];
            $monthlyIncomes = [];
            $monthlyExpenses = [];

            for ($i = 1; $i <= 12; $i++) {
                $date = Carbon::createFromDate($currentYear, $i, 1);
                $monthLabels[] = $date->format('M');

                $monthIncome = Income::whereYear('date', $currentYear)
                    ->whereMonth('date', $i)
                    ->whereHas('sessionReport', function($q){ $q->where('status','approved'); })
                    ->sum('amount');

                $monthExpenseExpend = Expend::whereYear('date', $currentYear)
                    ->whereMonth('date', $i)
                    ->sum('amount');
                $monthExpenseSalary = SalaryPayment::whereYear('paid_date', $currentYear)
                    ->whereMonth('paid_date', $i)
                    ->sum('amount');
                $monthExpense = $monthExpenseExpend + $monthExpenseSalary;

                $monthlyIncomes[] = $monthIncome;
                $monthlyExpenses[] = $monthExpense;
            }
        }

        // ======== CURRENT PERIOD FINANCES ========

        // Total income and expense for current period
        $totalIncome = Income::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereHas('sessionReport', function($q){ $q->where('status','approved'); })
            ->sum('amount');

        // Breakdown by payment type (QRIS & CASH)
        $incomeQrisTotal = Income::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('payment_type','qris')
            ->whereHas('sessionReport', function($q){ $q->where('status','approved'); })
            ->sum('amount');

        $incomeCashTotal = Income::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where(function($q){
                $q->where('payment_type','cash')->orWhereNull('payment_type'); // treat null as cash legacy
            })
            ->whereHas('sessionReport', function($q){ $q->where('status','approved'); })
            ->sum('amount');

        $totalExpenseExpend = Expend::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('amount');
        $salaryExpense = SalaryPayment::whereBetween('paid_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('amount');
        $totalExpense = $totalExpenseExpend + $salaryExpense;
        $profit = $totalIncome - $totalExpense;

        // ======== MODAL / CAPITAL DATA ========
        // MOVE THIS SECTION BEFORE ROI CALCULATION
        $totalCapital = (float) Modal::sum('harga'); // Modal Awal (akumulasi seluruh pencatatan modal)
        $cumulativeIncome = (float) Income::sum('amount'); // Total seluruh pemasukkan tercatat
        $cumulativeExpense = (float) Expend::sum('amount') + (float) SalaryPayment::sum('amount'); // Termasuk gaji
        // FORMULA BARU: Modal Tersedia = Modal Awal + (Total Pemasukkan - Total Pengeluaran)
        // Penjelasan:
        // - Menganggap setiap pemasukkan menambah ketersediaan modal likuid.
        // - Setiap pengeluaran mengurangi ketersediaan.
        // - Tidak membedakan tipe pemasukkan/pengeluaran khusus; bisa disempurnakan nanti via kategori.
        $availableCapital = $totalCapital + ($cumulativeIncome - $cumulativeExpense);

        // ======== CURRENT KASIR SALDO (GLOBAL) ========
        // Saldo kasir saat ini = total pemasukkan approved - total pengeluaran (expend + gaji) + topup kasir
        $approvedIncomeAllTime = (float) Income::whereHas('sessionReport', function($q){ $q->where('status','approved'); })->sum('amount');
        $allExpensesAllTime = (float) Expend::sum('amount') + (float) SalaryPayment::sum('amount');
        $kasirTopupsAllTime = (float) SaldoTopup::where('account','kasir')->sum('amount');
        $currentKasirSaldo = $approvedIncomeAllTime - $allExpensesAllTime + $kasirTopupsAllTime;

        // Now calculate ROI after totalCapital is defined
        // Calculate average daily profit
        $dayCount = max(1, $startDate->diffInDays($endDate) + 1);
        $averageDailyProfit = $profit / $dayCount;

        // Calculate days until ROI if there's profit
        if ($averageDailyProfit > 0 && $totalCapital > 0) {
            $daysUntilROI = ceil($totalCapital / $averageDailyProfit);
            $estimatedROIDate = Carbon::now()->addDays($daysUntilROI);

            // Display ROI date only if it's a reasonable number (less than 10 years)
            $showROI = $daysUntilROI < 3650;
        } else {
            $showROI = false;
            $daysUntilROI = 0;
            $estimatedROIDate = Carbon::now();
        }

        // Morning and afternoon session data
        $morningIncome = Income::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('session', 'pagi')
            ->whereHas('sessionReport', function($q){ $q->where('status','approved'); })
            ->sum('amount');

        $morningExpense = Expend::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('session', 'pagi')
            ->sum('amount');

        $afternoonIncome = Income::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('session', 'sore')
            ->whereHas('sessionReport', function($q){ $q->where('status','approved'); })
            ->sum('amount');

        $afternoonExpense = Expend::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('session', 'sore')
            ->sum('amount');

        // ======== PIE CHART - INCOME CATEGORIES ========

        // Get income categories data for pie chart
        $incomeByCategory = DB::table('incomes')
            ->join('pemasukkan', 'incomes.pemasukkan_id', '=', 'pemasukkan.id')
            ->join('income_session_reports','incomes.session_report_id','=','income_session_reports.id')
            ->where('income_session_reports.status','approved')
            ->whereBetween('incomes.date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->select('pemasukkan.nama_pemasukkan', DB::raw('SUM(incomes.amount) as total'))
            ->groupBy('pemasukkan.nama_pemasukkan')
            ->get();

        $incomeCategories = $incomeByCategory->pluck('nama_pemasukkan')->toArray();
        $incomeCategoryValues = $incomeByCategory->pluck('total')->toArray();

        // Colors for the pie chart
        $pieChartColors = [
            '#4e73df', // Primary
            '#1cc88a', // Success
            '#36b9cc', // Info
            '#f6c23e', // Warning
            '#e74a3b', // Danger
            '#858796', // Secondary
            '#5a5c69', // Dark
        ];

        // ======== RECENT TRANSACTIONS ========

        $recentIncomes = Income::with(['category', 'user','sessionReport'])
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->whereHas('sessionReport', function($q){ $q->where('status','approved'); })
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->take(5)
            ->get();

        $recentExpenses = Expend::with(['user', 'category'])
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->take(5)
            ->get();

        return view('owner.dashboard', compact(
            'currentYear',
            'currentMonthYear',
            'monthLabels',
            'monthlyIncomes',
            'monthlyExpenses',
            'totalIncome',
            'incomeQrisTotal',
            'incomeCashTotal',
            'totalExpense',
            'salaryExpense',
            'profit',
            'morningIncome',
            'morningExpense',
            'afternoonIncome',
            'afternoonExpense',
            'incomeCategories',
            'incomeCategoryValues',
            'pieChartColors',
            'totalCapital',
            'availableCapital',
            'recentIncomes',
            'recentExpenses',
            'period',
            'selectedDate',
            'dateRangeLabel',
            'averageDailyProfit', // Add this variable
            'daysUntilROI',       // Add this variable
            'estimatedROIDate',   // Add this variable
            'showROI'             // Add this variable
            , 'currentKasirSaldo'
        ));
    }
}
