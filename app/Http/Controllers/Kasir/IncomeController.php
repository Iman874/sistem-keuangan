<?php

namespace App\Http\Controllers\Kasir;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Pemasukkan;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Hanya menampilkan data hari ini yang diinput oleh kasir yang login
        $today = now()->startOfDay()->format('Y-m-d');

        // Single-entry incomes (not part of multi invoice)
        $incomes = Income::with('category')
            ->where('user_id', auth()->id())
            ->whereDate('date', $today)
            ->whereNull('invoice_id')
            ->latest()
            ->get();

        // Multi-transaksi invoices of today for current kasir
        $multiInvoices = \App\Models\Invoice::with(['incomes' => function($q){
                $q->select('id','invoice_id','session');
            }])
            ->where('cashier_id', auth()->id())
            ->where('type','income')
            ->whereDate('date',$today)
            ->orderByDesc('time')
            ->get();

        // Group incomes by date (hanya satu tanggal: hari ini)
        $groupedIncomes = $incomes->groupBy('date');

        // Totals
        $singleTotalToday = $incomes->sum('amount');
        $multiTotalToday = $multiInvoices->sum(function($inv){ return (int) $inv->total; });
        $overallTotalToday = $singleTotalToday + $multiTotalToday;

        return view('kasir.income.index', compact(
            'groupedIncomes',
            'multiInvoices',
            'singleTotalToday',
            'multiTotalToday',
            'overallTotalToday'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Pemasukkan::all();
        return view('kasir.income.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'session' => 'required|in:pagi,sore',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:cash,qris',
            'pemasukkan_id' => 'required|exists:pemasukkan,id',
            'description' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:100',
            'customer_email' => 'nullable|email|max:150',
        ];

        $request->validate($rules);

        $income = new Income();
        $income->user_id = auth()->id(); // Menambahkan ID user yang login
        $income->session = $request->input('session');
        $income->date = $request->date;
        $income->time = now()->format('H:i:s'); // Menggunakan waktu server saat ini
        $income->amount = $request->amount;
        $income->payment_type = $request->payment_type;

        $income->pemasukkan_id = $request->pemasukkan_id;
        $income->description = $request->description;
        $income->customer_name = $request->customer_name;
        $income->customer_email = $request->customer_email;

        $income->save();

        return redirect()->route('kasir.income.index')
            ->with('success', 'Data pemasukkan berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $income = Income::findOrFail($id);
        // Block edit if linked to a session report that is not rejected
        if ($income->session_report_id && ($income->sessionReport && $income->sessionReport->status !== 'rejected')) {
            return redirect()->route('kasir.income.index')->with('error', 'Data sudah dikunci (sudah disubmit).');
        }
        $categories = Pemasukkan::all();
        return view('kasir.income.edit', compact('income', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $income = Income::findOrFail($id);
        if ($income->session_report_id && ($income->sessionReport && $income->sessionReport->status !== 'rejected')) {
            return redirect()->route('kasir.income.index')->with('error', 'Tidak dapat mengubah, data sudah disubmit.');
        }

        $rules = [
            'session' => 'required|in:pagi,sore',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:cash,qris',
            'pemasukkan_id' => 'required|exists:pemasukkan,id',
            'description' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:100',
            'customer_email' => 'nullable|email|max:150',
        ];

        $request->validate($rules);

        $income->session = $request->input('session');
        $income->date = $request->date;
        // Jika ini update, kita tetap pertahankan waktu yang lama
        // Jika memang ingin memperbarui waktu ke waktu saat ini, hapus komentar baris di bawah ini
        // $income->time = now()->format('H:i:s');
        // Pastikan format amount benar (hilangkan titik ribuan)
        $income->amount = (int)str_replace('.', '', $request->amount);
        $income->payment_type = $request->payment_type;

        $income->pemasukkan_id = $request->pemasukkan_id;
        $income->description = $request->description;
        $income->customer_name = $request->customer_name;
        $income->customer_email = $request->customer_email;

        $income->save();

        return redirect()->route('kasir.income.index')
            ->with('success', 'Data pemasukkan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $income = Income::findOrFail($id);
        if ($income->session_report_id && ($income->sessionReport && $income->sessionReport->status !== 'rejected')) {
            return redirect()->route('kasir.income.index')->with('error', 'Tidak dapat menghapus, data sudah disubmit.');
        }
        $income->delete();

        return redirect()->route('kasir.income.index')
            ->with('success', 'Data pemasukkan berhasil dihapus!');
    }
}
