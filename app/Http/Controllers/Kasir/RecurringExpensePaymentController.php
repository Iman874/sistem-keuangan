<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\RecurringExpense;
use App\Models\RecurringExpensePayment;
use App\Models\Expend;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RecurringExpensePaymentController extends Controller
{
    public function index()
    {
        $this->authorizeCashier();
        $today = Carbon::today();
        $expenses = RecurringExpense::where('active', true)->orderBy('next_due_date')->get();
        return view('kasir.recurring-expenses.index', compact('expenses','today'));
    }

    public function pay(RecurringExpense $recurring_expense)
    {
        $this->authorizeCashier();
        return view('kasir.recurring-expenses.pay', ['expense'=>$recurring_expense]);
    }

    public function store(Request $request, RecurringExpense $recurring_expense)
    {
        $this->authorizeCashier();
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'session' => 'required|in:pagi,sore',
            'description' => 'nullable|string'
        ]);
        // Create expend record
        $expend = Expend::create([
            'user_id' => auth()->id(),
            'session' => $data['session'],
            'amount' => $data['amount'],
            'date' => Carbon::today()->format('Y-m-d'),
            'time' => Carbon::now()->format('H:i:s'),
            'type' => 'bulanan',
            'description' => $data['description'] ?? ('Pembayaran rutin: '.$recurring_expense->name),
        ]);
        // Create invoice
        $invoice = Invoice::create([
            'number' => 'RE-'.Carbon::now()->format('YmdHis'),
            'type' => 'expense',
            'cashier_id' => auth()->id(),
            'date' => Carbon::today()->format('Y-m-d'),
            'time' => Carbon::now()->format('H:i:s'),
            'subtotal' => $data['amount'],
            'tax' => 0,
            'total' => $data['amount'],
            'notes' => 'Invoice pembayaran rutin: '.$recurring_expense->name,
        ]);
        // Link invoice to expend
        $expend->invoice_id = $invoice->id;
        $expend->save();
        // Record payment
        RecurringExpensePayment::create([
            'recurring_expense_id' => $recurring_expense->id,
            'expend_id' => $expend->id,
            'invoice_id' => $invoice->id,
            'cashier_id' => auth()->id(),
            'paid_date' => Carbon::today()->format('Y-m-d'),
            'amount' => $data['amount']
        ]);
        // Update recurring_expense cycle
        $recurring_expense->last_paid_date = Carbon::today()->format('Y-m-d');
        $recurring_expense->next_due_date = Carbon::parse($recurring_expense->next_due_date)->addMonth();
        $recurring_expense->reminders_sent = 0;
        $recurring_expense->last_reminder_date = null;
        $recurring_expense->save();
        return redirect()->route('kasir.recurring-expenses.index')->with('success','Pembayaran rutin berhasil, invoice: '.$invoice->number);
    }

    protected function authorizeCashier(): void
    {
        $user = auth()->user();
        if (!($user && $user->role === 'kasir')) {
            abort(403);
        }
    }
}
