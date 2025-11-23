<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Expend;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class KasirExpendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Expend::with(['user','category'])->whereHas('user', function($q){ $q->where('role','kasir'); });

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        // Filter by session
        if ($request->filled('session') && in_array($request->session, ['pagi', 'sore'])) {
            $query->where('session', $request->session);
        }

        // Filter by type
        if ($request->filled('type') && in_array($request->type, ['harian', 'bulanan'])) {
            $query->where('type', $request->type);
        }

        // Filter by kasir (user)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by category
        if($request->filled('category_id')){
            $query->where('category_id',$request->category_id);
        }

        // Get expenditures grouped by date
        $expenditures = $query->orderBy('date', 'desc')->get();
        $groupedExpenditures = $expenditures->groupBy(function ($item) {
            return $item->type == 'harian' ? 'daily' : 'monthly';
        });

        // Calculate totals
        $totalAmount = $expenditures->sum('amount');
        $totalMorning = $expenditures->where('session', 'pagi')->sum('amount');
        $totalAfternoon = $expenditures->where('session', 'sore')->sum('amount');

        $categories = \App\Models\ExpenseCategory::orderBy('name')->get(['id','name']);

        return view('owner.kasir-expend.index', compact(
            'groupedExpenditures',
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
        $expend = Expend::with(['user','category'])->findOrFail($id);
        $role = auth()->user()->role ?? null;
        if(in_array($role,['owner','admin']) && !$expend->invoice_id){
            $cashierId = $expend->user_id ?: (auth()->id());
            $invoiceNumber = 'INV-'.now()->format('Ymd-His').'-'.substr(str_pad((string)$cashierId,2,'0',STR_PAD_LEFT),-2);
            \DB::transaction(function() use ($expend,$cashierId,$invoiceNumber){
                $invoice = Invoice::create([
                    'number' => $invoiceNumber,
                    'type' => 'expense',
                    'cashier_id' => $cashierId,
                    'date' => $expend->date->toDateString(),
                    'time' => $expend->time ? $expend->time->format('H:i:s') : now()->format('H:i:s'),
                    'subtotal' => $expend->amount,
                    'tax' => 0,
                    'total' => $expend->amount,
                ]);
                $expend->invoice_id = $invoice->id;
                $expend->save();
            });
            $expend->load('invoice');
        }
        return view('owner.kasir-expend.show', compact('expend'));
    }

    /**
     * Download report as PDF
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function downloadReport(Request $request)
    {
        $query = Expend::with(['user','category'])->whereHas('user', function($q){ $q->where('role','kasir'); });

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        // Filter by session
        if ($request->filled('session') && in_array($request->session, ['pagi', 'sore'])) {
            $query->where('session', $request->session);
        }

        // Filter by type
        if ($request->filled('type') && in_array($request->type, ['harian', 'bulanan'])) {
            $query->where('type', $request->type);
        }

        // Filter by kasir (user)
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        // Filter by category
        if($request->filled('category_id')){
            $query->where('category_id',$request->category_id);
        }

        $expenditures = $query->orderBy('date', 'desc')->get();

        $pdf = Pdf::loadView('owner.kasir-expend.report', compact('expenditures'));

        return $pdf->download('laporan-pengeluaran-kasir.pdf');
    }
}
