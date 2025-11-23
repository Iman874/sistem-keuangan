<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RecurringExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','description','amount','frequency','next_due_date','last_paid_date','active','reminders_sent','last_reminder_date'
    ];

    protected $casts = [
        'next_due_date' => 'date',
        'last_paid_date' => 'date',
        'active' => 'boolean'
    ];

    public function payments()
    {
        return $this->hasMany(RecurringExpensePayment::class);
    }

    public function isInReminderWindow(): bool
    {
        if(!$this->active) return false;
        $due = $this->next_due_date instanceof Carbon ? $this->next_due_date : Carbon::parse($this->next_due_date);
        $today = Carbon::today();
        // 3,2,1 days before due date
        return $today->between($due->copy()->subDays(3), $due->copy()->subDay());
    }

    public function resetReminderCycle(): void
    {
        $this->reminders_sent = 0;
        $this->last_reminder_date = null;
        $this->save();
    }
}
