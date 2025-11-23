<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class IncomeSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithMapping
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
        return ['Tanggal', 'Sesi', 'Jumlah', 'Sumber/Kategori', 'Kasir'];
    }

    public function map($row): array
    {
        return [
            optional($row->date)->format('Y-m-d'),
            $row->session,
            $row->amount,
            $row->other_source ? $row->description : optional($row->category)->nama_pemasukkan,
            optional($row->user)->name,
        ];
    }

    public function title(): string
    {
        return 'Pemasukkan';
    }
}
