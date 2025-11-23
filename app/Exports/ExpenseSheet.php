<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use App\Models\SalaryPayment;
use App\Models\Expend;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExpenseSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithMapping
{
    private Collection $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return ['Tanggal', 'Sesi', 'Jumlah', 'Deskripsi', 'Kasir'];
    }

    public function map($row): array
    {
        $isSalary = $row instanceof SalaryPayment || (($row->type ?? '') === 'gaji');
        if ($isSalary) {
            $date = optional($row->date ?? $row->paid_date)->format('Y-m-d');
            $session = '-';
            $amount = $row->amount;
            $description = $row->description ?? ('Gaji: '.optional($row->employee)->name);
            $kasir = optional($row->creator)->name; // creator of salary payment
            return [$date, $session, $amount, $description, $kasir];
        }
        // Regular expense (Expend)
        return [
            optional($row->date)->format('Y-m-d'),
            $row->session,
            $row->amount,
            $row->description,
            optional($row->user)->name,
        ];
    }

    public function title(): string
    {
        return 'Pengeluaran';
    }
}
