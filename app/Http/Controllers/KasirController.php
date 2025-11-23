<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Expend;
use App\Models\Saldo;
use App\Models\SaldoTopup;
use App\Models\SalaryPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class KasirController extends Controller
{
    public function dashboard(Request $request)
    {
        // Set timezone for all Carbon instances
        Carbon::setLocale('id');

        // Tanggal hari ini
        $today = Carbon::today();

        // Session filter: pagi | sore | all (default by time: <12 pagi, else sore)
        $sessionFilter = $request->input('session');
        if (!in_array($sessionFilter, ['pagi','sore','all'])) {
            $sessionFilter = Carbon::now()->hour < 12 ? 'pagi' : 'sore';
        }

        // Pemasukkan dan pengeluaran hari ini (filtered by session if not 'all')
        $todayIncome = Income::whereDate('date', $today)
            ->when($sessionFilter !== 'all', fn($q) => $q->where('session', $sessionFilter))
            ->sum('amount');
        $todayExpense = Expend::whereDate('date', $today)
            ->when($sessionFilter !== 'all', fn($q) => $q->where('session', $sessionFilter))
            ->sum('amount');
        $todayQrisIncome = Income::whereDate('date', $today)
            ->when($sessionFilter !== 'all', fn($q) => $q->where('session', $sessionFilter))
            ->where('payment_type','qris')->sum('amount');
        $todayCashIncome = Income::whereDate('date', $today)
            ->when($sessionFilter !== 'all', fn($q) => $q->where('session', $sessionFilter))
            ->where('payment_type','cash')->sum('amount');

        // Total transaksi hari ini
        $todayTransactions = Income::whereDate('date', $today)
                ->when($sessionFilter !== 'all', fn($q) => $q->where('session', $sessionFilter))
                ->count()
            + Expend::whereDate('date', $today)
                ->when($sessionFilter !== 'all', fn($q) => $q->where('session', $sessionFilter))
                ->count();

        // Pemasukkan dan pengeluaran per sesi (Pagi dan Sore)
    $morningIncome = Income::whereDate('date', $today)->where('session', 'pagi')->sum('amount');
    $morningExpense = Expend::whereDate('date', $today)->where('session', 'pagi')->sum('amount');
    $afternoonIncome = Income::whereDate('date', $today)->where('session', 'sore')->sum('amount');
    $afternoonExpense = Expend::whereDate('date', $today)->where('session', 'sore')->sum('amount');

        // Hitung saldo saat ini mengikuti logika halaman manajemen saldo:
        // Kasir = (approved incomes) - (all expenses + salary payments) + (topup kasir)
        // Total saldo = kasir + bank + tunai (tanpa nilai modal/asset)
        $approvedIncome = (float) Income::whereHas('sessionReport', function($q){
            $q->where('status','approved');
        })->sum('amount');
        $totalExpense = (float) Expend::sum('amount') + (float) SalaryPayment::sum('amount');
        $kasirTopups = (float) SaldoTopup::where('account','kasir')->sum('amount');
        $kasirComputed = $approvedIncome - $totalExpense + $kasirTopups;

        // Ambil saldo tersimpan untuk akun bank & tunai
            // Nilai yang ditampilkan hanya saldo kasir fisik
            $currentSaldo = $kasirComputed;

        // Data untuk grafik mingguan (7 hari terakhir)
        $startDate = Carbon::today()->subDays(6);
        $dates = [];
        $weekIncomes = [];
        $weekExpenses = [];
        $weekLabels = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dates[] = $date->format('Y-m-d');
            $weekLabels[] = $date->isoFormat('D MMM'); // Indonesian format

            $weekIncomes[] = Income::whereDate('date', $date)->sum('amount');
            $weekExpenses[] = Expend::whereDate('date', $date)->sum('amount');
        }

        // Transaksi terbaru - hanya untuk hari ini
        $recentIncomes = Income::with('category')
            ->whereDate('date', $today)
            ->when($sessionFilter !== 'all', fn($q) => $q->where('session', $sessionFilter))
            ->where('user_id', Auth::id()) // Only show transactions by the current kasir
            ->latest('date')
            ->latest('time') // Sort by time as well
            ->take(5)
            ->get()
            ->map(function ($income) {
                // Ensure date is a Carbon object
                if (is_string($income->date)) {
                    $income->date = Carbon::parse($income->date);
                }
                return $income;
            });

        $recentExpenses = Expend::whereDate('date', $today)
            ->when($sessionFilter !== 'all', fn($q) => $q->where('session', $sessionFilter))
            ->where('user_id', Auth::id()) // Only show transactions by the current kasir
            ->latest('date')
            ->latest('time') // Sort by time as well
            ->take(5)
            ->get()
            ->map(function ($expense) {
                // Ensure date is a Carbon object
                if (is_string($expense->date)) {
                    $expense->date = Carbon::parse($expense->date);
                }
                return $expense;
            });

        return View::make('kasir.dashboard', compact(
            'sessionFilter',
            'todayIncome',
            'todayExpense',
            'todayQrisIncome',
            'todayCashIncome',
            'todayTransactions',
            'morningIncome',
            'morningExpense',
            'afternoonIncome',
            'afternoonExpense',
            'weekLabels',
            'weekIncomes',
            'weekExpenses',
            'recentIncomes',
            'recentExpenses',
            'currentSaldo'
        ));
    }
}
