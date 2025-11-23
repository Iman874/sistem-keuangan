<?php

namespace App\Notifications;

use App\Models\RecurringExpense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RecurringExpenseDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public RecurringExpense $expense)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'recurring_expense_due',
            'recurring_expense_id' => $this->expense->id,
            'name' => $this->expense->name,
            'amount' => $this->expense->amount,
            'next_due_date' => $this->expense->next_due_date->format('Y-m-d'),
            'message' => 'Pengingat pembayaran rutin: '.$this->expense->name.' jatuh tempo '.$this->expense->next_due_date->format('d/m/Y')
        ];
    }
}
