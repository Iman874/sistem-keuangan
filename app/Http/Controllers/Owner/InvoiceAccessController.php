<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Invoice;

class InvoiceAccessController extends Controller
{
    protected function ensureAccess()
    {
        $u = auth()->user();
        if(!$u) abort(403);
        if($u->role === 'owner') return;
        if($u->role === 'admin') {
            // Admin must have at least one related permission (export or read income/expense)
            if(method_exists($u,'hasPermission') && ($u->hasPermission('saldo.export') || $u->hasPermission('income.read') || $u->hasPermission('expense.read'))){
                return;
            }
        }
        abort(403);
    }

    public function show(Invoice $invoice)
    {
        $this->ensureAccess();
        $invoice->load(['incomes.category','expends','cashier']);
        return view('owner.invoice.show', compact('invoice'));
    }

    public function print(Invoice $invoice)
    {
        $this->ensureAccess();
        $invoice->load(['incomes.category','expends','cashier']);
        return view('owner.invoice.print', compact('invoice'));
    }
}
