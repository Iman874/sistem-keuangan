<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringExpensePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'recurring_expense_id','expend_id','invoice_id','cashier_id','paid_date','amount'
    ];

    protected $casts = [
        'paid_date' => 'date'
    ];

    public function recurringExpense()
    {
        return $this->belongsTo(RecurringExpense::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function expend()
    {
        return $this->belongsTo(Expend::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class,'cashier_id');
    }
}
