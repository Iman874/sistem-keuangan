<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RecurringExpense;
use App\Models\User;
use App\Notifications\RecurringExpenseDueNotification;
use Carbon\Carbon;

class SendRecurringExpenseReminders extends Command
{
    protected $signature = 'recurring-expenses:send-reminders';
    protected $description = 'Kirim pengingat pembayaran rutin 3 hari sebelum jatuh tempo';

    public function handle(): int
    {
        $today = Carbon::today();
        $expenses = RecurringExpense::where('active', true)->get();
        $cashiers = User::where('role','kasir')->get();
        $count = 0;
        foreach ($expenses as $expense) {
            $due = $expense->next_due_date instanceof Carbon ? $expense->next_due_date : Carbon::parse($expense->next_due_date);
            // Skip if already paid this cycle
            if ($expense->last_paid_date && $expense->last_paid_date >= $due->subMonth()) {
                continue;
            }
            if ($today->between($due->copy()->subDays(3), $due->copy()->subDay())) {
                // Send once per day max; limit to 3 reminders per cycle
                if ($expense->last_reminder_date == $today->toDateString()) {
                    continue; // already sent today
                }
                if ($expense->reminders_sent >= 3) {
                    continue; // reached limit
                }
                foreach ($cashiers as $cashier) {
                    $cashier->notify(new RecurringExpenseDueNotification($expense));
                }
                $expense->last_reminder_date = $today->toDateString();
                $expense->reminders_sent += 1;
                $expense->save();
                $count++;
            }
        }
        $this->info('Pengingat dikirim: '.$count);
        return Command::SUCCESS;
    }
}
