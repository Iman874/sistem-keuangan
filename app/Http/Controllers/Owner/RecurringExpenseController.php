<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\RecurringExpense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RecurringExpenseController extends Controller
{
    public function index()
    {
        $this->authorizeAccess();
        $expenses = RecurringExpense::orderBy('next_due_date')->get();
        return view('owner.recurring-expenses.index', compact('expenses'));
    }

    public function create()
    {
        $this->authorizeAccess();
        return view('owner.recurring-expenses.form');
    }

    public function store(Request $request)
    {
        $this->authorizeAccess();
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'next_due_date' => 'required|date',
            'active' => 'sometimes|boolean'
        ]);
        $data['frequency'] = 'monthly';
        $data['active'] = $request->boolean('active', true);
        RecurringExpense::create($data);
        return redirect()->route('owner.recurring-expenses.index')->with('success','Recurring expense ditambahkan');
    }

    public function edit(RecurringExpense $recurring_expense)
    {
        $this->authorizeAccess();
        return view('owner.recurring-expenses.form', ['expense' => $recurring_expense]);
    }

    public function update(Request $request, RecurringExpense $recurring_expense)
    {
        $this->authorizeAccess();
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'next_due_date' => 'required|date',
            'active' => 'sometimes|boolean'
        ]);
        $data['active'] = $request->boolean('active', true);
        $recurring_expense->update($data);
        return redirect()->route('owner.recurring-expenses.index')->with('success','Recurring expense diperbarui');
    }

    public function destroy(RecurringExpense $recurring_expense)
    {
        $this->authorizeAccess();
        $recurring_expense->delete();
        return redirect()->route('owner.recurring-expenses.index')->with('success','Recurring expense dihapus');
    }

    protected function authorizeAccess(): void
    {
        $user = auth()->user();
        if (!($user && in_array($user->role,['owner','admin']))) {
            abort(403);
        }
    }
}
