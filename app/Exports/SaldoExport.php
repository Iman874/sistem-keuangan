<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SaldoExport implements FromCollection, WithHeadings
{
    protected Collection $transfers;
    protected Collection $topups;

    public function __construct($transfers, $topups)
    {
        $this->transfers = collect($transfers);
        $this->topups = collect($topups);
    }

    public function collection()
    {
        $rows = collect();
        $this->transfers->each(function($t) use (&$rows){
            $rows->push([
                'transfer',
                $t->date,
                $t->time,
                $t->source_account,
                $t->destination_account,
                '',
                $t->amount,
                $t->note,
                optional($t->user)->name,
                $t->invoice_income_id,
                $t->invoice_expend_id,
            ]);
        });
        $this->topups->each(function($r) use (&$rows){
            $rows->push([
                'topup',
                optional($r->date)->format('Y-m-d'),
                is_string($r->time) ? $r->time : (optional($r->time)->format('H:i:s')),
                '',
                '',
                $r->account,
                $r->amount,
                $r->note,
                optional($r->user)->name,
                '',
                '',
            ]);
        });
        return $rows;
    }

    public function headings(): array
    {
        return ['Tipe','Tanggal','Waktu','Sumber','Tujuan','Akun','Jumlah','Catatan','User','Invoice Income','Invoice Expense'];
    }
}
