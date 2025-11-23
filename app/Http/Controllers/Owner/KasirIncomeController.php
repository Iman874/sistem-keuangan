<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class KasirIncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Income::with(['category', 'user'])->whereHas('user', function($q){ $q->where('role','kasir'); });

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        // Filter by session
        if ($request->filled('session') && in_array($request->input('session'), ['pagi', 'sore'])) {
            $query->where('session', $request->input('session'));
        }

        // Filter by kasir (user)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        // Filter by category
        if($request->filled('category_id')){
            $query->where('pemasukkan_id',$request->category_id);
        }

        // Get incomes and group by date
        $incomes = $query->orderBy('date', 'desc')->get();
        $groupedIncomes = $incomes->groupBy('date');

        // Calculate totals
        $totalAmount = $incomes->sum('amount');
        $totalMorning = $incomes->where('session', 'pagi')->sum('amount');
        $totalAfternoon = $incomes->where('session', 'sore')->sum('amount');

        $categories = \App\Models\Pemasukkan::orderBy('nama_pemasukkan')->get(['id','nama_pemasukkan']);

        return view('owner.kasir-income.index', compact(
            'groupedIncomes',
            'totalAmount',
            'totalMorning',
            'totalAfternoon',
            'categories'
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $income = Income::with(['category','user'])->findOrFail($id);
        // Auto-create invoice if owner/admin views and no invoice exists yet
        $role = auth()->user()->role ?? null;
        if(in_array($role,['owner','admin']) && !$income->invoice_id){
            $cashierId = $income->user_id ?: (auth()->id());
            $invoiceNumber = 'INV-'.now()->format('Ymd-His').'-'.substr(str_pad((string)$cashierId,2,'0',STR_PAD_LEFT),-2);
            \DB::transaction(function() use ($income,$cashierId,$invoiceNumber){
                $invoice = Invoice::create([
                    'number' => $invoiceNumber,
                    'type' => 'income',
                    'cashier_id' => $cashierId,
                    'customer_name' => $income->customer_name,
                    'customer_email' => $income->customer_email,
                    'payment_type' => $income->payment_type ?? 'cash',
                    'date' => $income->date->toDateString(),
                    'time' => $income->time ? $income->time->format('H:i:s') : now()->format('H:i:s'),
                    'subtotal' => $income->amount,
                    'tax' => 0,
                    'total' => $income->amount,
                ]);
                if(empty($income->qty)) $income->qty = 1;
                if(empty($income->unit_price)) $income->unit_price = $income->amount;
                $income->invoice_id = $invoice->id;
                $income->save();
            });
            // Refresh relation bindings
            $income->load('invoice');
        }
        return view('owner.kasir-income.show', compact('income'));
    }

    /**
     * Download report as PDF
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function downloadReport(Request $request)
    {
        $query = Income::with('category')->whereHas('user', function($q){ $q->where('role','kasir'); });

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        // Filter by session
        if ($request->filled('session') && in_array($request->input('session'), ['pagi', 'sore'])) {
            $query->where('session', $request->input('session'));
        }

        // Filter by kasir (user)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        // Filter by category
        if($request->filled('category_id')){
            $query->where('pemasukkan_id',$request->category_id);
        }

        $incomes = $query->orderBy('date', 'desc')->get();

        $pdf = Pdf::loadView('owner.kasir-income.report', compact('incomes'));

        return $pdf->download('laporan-pemasukkan-kasir.pdf');
    }
}
