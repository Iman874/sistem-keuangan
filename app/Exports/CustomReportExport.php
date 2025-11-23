<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CustomReportExport implements WithMultipleSheets
{
    private Collection $incomes;
    private Collection $expenses;
    private string $transactionType;

    public function __construct(Collection $incomes, Collection $expenses, string $transactionType)
    {
        $this->incomes = $incomes;
        $this->expenses = $expenses;
        $this->transactionType = $transactionType;
    }

    public function sheets(): array
    {
        $sheets = [];

        if ($this->transactionType === 'income' || $this->transactionType === 'both') {
            $sheets[] = new IncomeSheet($this->incomes);
        }

        if ($this->transactionType === 'expense' || $this->transactionType === 'both') {
            $sheets[] = new ExpenseSheet($this->expenses);
        }

        return $sheets;
    }
}
