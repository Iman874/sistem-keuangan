<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Income;
use App\Models\Expend;
use App\Models\Pemasukkan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;

class InvoiceController extends Controller
{
    protected function guardKasir()
    {
        $user = auth()->user();
        if (!($user && $user instanceof \App\Models\User && $user->role === 'kasir')) {
            abort(403);
        }
    }

    public function createIncome()
    {
        $this->guardKasir();
        $categories = Pemasukkan::orderBy('nama_pemasukkan')->get();
        return view('kasir.invoice.create_income', compact('categories'));
    }

    public function storeIncome(Request $request)
    {
        $this->guardKasir();
        // Filter empty item rows before validation (avoid misleading errors on deleted rows)
        $rawItems = $request->input('items', []);
        $filteredItems = collect($rawItems)->filter(function($row){
            return isset($row['pemasukkan_id']) && $row['pemasukkan_id'] && isset($row['qty']) && $row['qty'] !== '' && isset($row['unit_price']) && $row['unit_price'] !== '';
        })->values()->toArray();

        $input = [
            'customer_name' => $request->input('customer_name'),
            'customer_email' => $request->input('customer_email'),
            'payment_type' => $request->input('payment_type'),
            'session' => $request->input('session'),
            'items' => $filteredItems,
        ];

        $rules = [
            'customer_name' => 'nullable|string|max:100',
            'customer_email' => 'nullable|email',
            'payment_type' => 'required|in:cash,qris',
            'session' => 'required|in:pagi,sore',
            'items' => 'required|array|min:1',
            'items.*.pemasukkan_id' => 'required|exists:pemasukkan,id',
            'items.*.description' => 'nullable|string|max:255',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];

        $validator = \Validator::make($input, $rules);
        if ($validator->fails()) {
            if (config('app.debug')) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            $errorCode = 'INV'.now()->format('YmdHis').substr(md5(json_encode($validator->errors()->all())),0,6);
            \Log::warning('Invoice validation error '.$errorCode, ['errors'=>$validator->errors()->all()]);
            return redirect()->back()->with('error','Terjadi kesalahan [Kode '.$errorCode.']')->withInput();
        }
        $validated = $validator->validated();

        $userId = auth()->id();
        $date = now()->toDateString();
        $time = now()->format('H:i:s');

        $subtotal = 0;
        foreach ($validated['items'] as $it) {
            $subtotal += ($it['qty'] * $it['unit_price']);
        }
        $tax = 0; // 0% as requested
        $total = $subtotal + $tax;

        $invoiceNumber = 'INV-'.now()->format('Ymd-His').'-'.substr(str_pad((string)$userId,2,'0',STR_PAD_LEFT),-2);

        try {
        DB::transaction(function () use ($validated,$userId,$date,$time,$subtotal,$tax,$total,$invoiceNumber) {
            $invoice = Invoice::create([
                'number' => $invoiceNumber,
                'type' => 'income',
                'cashier_id' => $userId,
                'customer_name' => $validated['customer_name'] ?? null,
                'customer_email' => $validated['customer_email'] ?? null,
                'payment_type' => $validated['payment_type'],
                'date' => $date,
                'time' => $time,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);

            foreach ($validated['items'] as $it) {
                $amount = $it['qty'] * $it['unit_price'];
                Income::create([
                    'user_id' => $userId,
                    'pemasukkan_id' => $it['pemasukkan_id'],
                    'session' => $validated['session'],
                    'amount' => $amount,
                    'qty' => $it['qty'],
                    'unit_price' => $it['unit_price'],
                    'date' => $date,
                    'time' => now(),
                    'description' => $it['description'] ?? null,
                    'customer_name' => $validated['customer_name'] ?? null,
                    'customer_email' => $validated['customer_email'] ?? null,
                    'payment_type' => $validated['payment_type'],
                    'invoice_id' => $invoice->id,
                ]);
            }
        });
        } catch(\Throwable $e) {
            if (config('app.debug')) {
                throw $e; // allow full stack in debug
            }
            $errorCode = 'INVTX'.now()->format('YmdHis').substr(md5($e->getMessage()),0,6);
            \Log::error('Invoice store fatal '.$errorCode, ['exception'=>$e]);
            return redirect()->back()->with('error','Terjadi kesalahan [Kode '.$errorCode.']')->withInput();
        }

        // Send email if provided (fail-safe: don't break flow if mail fails)
        if (!empty($validated['customer_email'])) {
            try {
                $invoice = Invoice::where('number',$invoiceNumber)->first();
                if ($invoice) {
                    Mail::to($validated['customer_email'])->send(new InvoiceMail($invoice));
                }
            } catch (\Throwable $mailEx) {
                \Log::warning('Invoice email send failed', [
                    'invoice' => $invoiceNumber,
                    'error' => $mailEx->getMessage(),
                ]);
                // continue without interrupting the transaction result
            }
        }

        return redirect()->route('kasir.invoice.show', ['invoice' => Invoice::where('number',$invoiceNumber)->first()->id])
            ->with('success','Invoice berhasil dibuat.');
    }

    public function show(Invoice $invoice)
    {
        $this->guardKasir();
        $invoice->load(['incomes.category','expends','cashier']);
        return view('kasir.invoice.show', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        $this->guardKasir();
        $invoice->load(['incomes.category','expends','cashier']);
        return view('kasir.invoice.print', compact('invoice'));
    }

    public function email(Invoice $invoice, Request $request)
    {
        $this->guardKasir();
        $request->validate(['email' => 'required|email']);
        Mail::to($request->email)->send(new InvoiceMail($invoice));
        return back()->with('success','Invoice terkirim ke email.');
    }

    public function edit(Invoice $invoice)
    {
        $this->guardKasir();
        if ($invoice->cashier_id !== auth()->id()) abort(403);
        $invoice->load('incomes.category');
        $categories = Pemasukkan::orderBy('nama_pemasukkan')->get();
        // Precompute arrays for Blade JSON (avoid complex inline mapping causing parse issues)
        $categoriesData = $categories->map(function($c){
            return [
                'id' => $c->id,
                'name' => $c->nama_pemasukkan,
            ];
        });
        $existingItems = $invoice->incomes->map(function($i){
            return [
                'pemasukkan_id' => $i->pemasukkan_id,
                'description' => $i->description,
                'qty' => $i->qty ?? 1,
                'unit_price' => $i->unit_price ?? $i->amount,
            ];
        });
        return view('kasir.invoice.edit_income', compact('invoice','categories','categoriesData','existingItems'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->guardKasir();
        if ($invoice->cashier_id !== auth()->id()) abort(403);

        // Filter empty rows dan parse unit_price
        $rawItems = $request->input('items', []);
        $filteredItems = collect($rawItems)->filter(function($row){
            return isset($row['pemasukkan_id']) && $row['pemasukkan_id'] && isset($row['qty']) && $row['qty'] !== '' && isset($row['unit_price']) && $row['unit_price'] !== '';
        })->map(function($row){
            // Pastikan unit_price diubah dari format ribuan ke integer
            $row['unit_price'] = (int)str_replace('.', '', preg_replace('/[^\d.]/', '', $row['unit_price']));
            return $row;
        })->values()->toArray();

        $input = [
            'customer_name' => $request->input('customer_name'),
            'customer_email' => $request->input('customer_email'),
            'payment_type' => $request->input('payment_type'),
            'session' => $request->input('session'),
            'items' => $filteredItems,
        ];

        $rules = [
            'customer_name' => 'nullable|string|max:100',
            'customer_email' => 'nullable|email',
            'payment_type' => 'required|in:cash,qris',
            'session' => 'required|in:pagi,sore',
            'items' => 'required|array|min:1',
            'items.*.pemasukkan_id' => 'required|exists:pemasukkan,id',
            'items.*.description' => 'nullable|string|max:255',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];

        $validator = \Validator::make($input, $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();

        $subtotal = 0;
        foreach ($validated['items'] as $it) {
            $subtotal += ($it['qty'] * $it['unit_price']);
        }
        $tax = 0;
        $total = $subtotal + $tax;

        try {
            DB::transaction(function() use ($invoice,$validated,$subtotal,$tax,$total){
                // Update invoice fields
                $invoice->customer_name = $validated['customer_name'] ?? null;
                $invoice->customer_email = $validated['customer_email'] ?? null;
                $invoice->payment_type = $validated['payment_type'];
                $invoice->subtotal = $subtotal;
                $invoice->tax = $tax;
                $invoice->total = $total;
                $invoice->save();

                // Replace incomes
                Income::where('invoice_id',$invoice->id)->delete();
                foreach ($validated['items'] as $it) {
                    $amount = $it['qty'] * $it['unit_price'];
                    Income::create([
                        'user_id' => auth()->id(),
                        'pemasukkan_id' => $it['pemasukkan_id'],
                        'session' => $validated['session'],
                        'amount' => $amount,
                        'qty' => $it['qty'],
                        'unit_price' => $it['unit_price'],
                        'date' => $invoice->date->toDateString(),
                        'time' => now(),
                        'description' => $it['description'] ?? null,
                        'customer_name' => $validated['customer_name'] ?? null,
                        'customer_email' => $validated['customer_email'] ?? null,
                        'payment_type' => $validated['payment_type'],
                        'invoice_id' => $invoice->id,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Gagal memperbarui invoice: '.$e->getMessage())->withInput();
        }

        return redirect()->route('kasir.invoice.show',$invoice->id)->with('success','Invoice berhasil diperbarui.');
    }

    public function destroy(Invoice $invoice)
    {
        $this->guardKasir();
        if ($invoice->cashier_id !== auth()->id()) abort(403);
        try {
            DB::transaction(function() use ($invoice){
                Income::where('invoice_id',$invoice->id)->delete();
                $invoice->delete();
            });
        } catch (\Throwable $e) {
            return redirect()->back()->with('error','Gagal menghapus invoice: '.$e->getMessage());
        }
        return redirect()->route('kasir.income.index')->with('success','Invoice berhasil dihapus.');
    }

    // Generate invoice for a single income
    public function fromIncome(Income $income)
    {
        $this->guardKasir();
        if ($income->invoice_id) {
            return redirect()->route('kasir.invoice.show',$income->invoice_id);
        }
        $userId = auth()->id();
        $date = $income->date->toDateString();
        $time = $income->time ? $income->time->format('H:i:s') : now()->format('H:i:s');
        $invoiceNumber = 'INV-'.now()->format('Ymd-His').'-'.substr(str_pad((string)$userId,2,'0',STR_PAD_LEFT),-2);
        DB::transaction(function () use ($income,$userId,$date,$time,$invoiceNumber) {
            $invoice = Invoice::create([
                'number' => $invoiceNumber,
                'type' => 'income',
                'cashier_id' => $userId,
                'customer_name' => $income->customer_name,
                'customer_email' => $income->customer_email,
                'payment_type' => $income->payment_type,
                'date' => $date,
                'time' => $time,
                'subtotal' => $income->amount,
                'tax' => 0,
                'total' => $income->amount,
            ]);
            $income->invoice_id = $invoice->id;
            if (empty($income->qty)) $income->qty = 1;
            if (empty($income->unit_price)) $income->unit_price = $income->amount; // treat as single item
            $income->save();
        });
        return redirect()->route('kasir.invoice.show', Invoice::where('number',$invoiceNumber)->first()->id);
    }

    // Generate invoice for a single expense
    public function fromExpend(Expend $expend)
    {
        $this->guardKasir();
        if ($expend->invoice_id) {
            return redirect()->route('kasir.invoice.show',$expend->invoice_id);
        }
        $userId = auth()->id();
        $date = $expend->date->toDateString();
        $time = $expend->time ? $expend->time->format('H:i:s') : now()->format('H:i:s');
        $invoiceNumber = 'INV-'.now()->format('Ymd-His').'-'.substr(str_pad((string)$userId,2,'0',STR_PAD_LEFT),-2);
        DB::transaction(function () use ($expend,$userId,$date,$time,$invoiceNumber) {
            $invoice = Invoice::create([
                'number' => $invoiceNumber,
                'type' => 'expense',
                'cashier_id' => $userId,
                'date' => $date,
                'time' => $time,
                'subtotal' => $expend->amount,
                'tax' => 0,
                'total' => $expend->amount,
            ]);
            $expend->invoice_id = $invoice->id;
            $expend->save();
        });
        return redirect()->route('kasir.invoice.show', Invoice::where('number',$invoiceNumber)->first()->id);
    }
}
