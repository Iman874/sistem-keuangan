<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        body{ font-family: Arial, Helvetica, sans-serif; color:#333; }
        .invoice{ max-width: 800px; margin:0 auto; padding:24px; }
        .header{ display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #ddd; padding-bottom:12px; }
        .brand{ display:flex; align-items:center; }
        .brand img{ height:40px; margin-right:10px; }
        @media print {
            .brand img { filter:none !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        h1{ margin:0; font-size:28px; letter-spacing:1px; }
        .muted{ color:#777; font-size:12px; }
        .grid{ display:flex; justify-content:space-between; margin-top:16px; }
        .box{ width:48%; background:#f6f8fa; padding:12px; border-radius:8px; }
        table{ width:100%; border-collapse:collapse; margin-top:16px; }
        th,td{ border:1px solid #e5e7eb; padding:8px; font-size:14px; }
        th{ background:#f0f3f6; text-align:left; }
        tfoot td{ font-weight:bold; }
        .right{ text-align:right; }
        .signature{ margin-top:24px; display:flex; justify-content:space-between; align-items:center; }
        .small{ font-size:12px; }
        @media print{ .no-print{ display:none; } body{ background:#fff; } }
    </style>
</head>
<body>
<div class="invoice">
    <div class="header">
        <div class="brand">
            <img src="{{ asset('assets/img/monoframe.png') }}" alt="Mono Frame" class="mr-2">
            <div>
                <div class="muted">MONO FRAME</div>
                <h1>INVOICE</h1>
            </div>
        </div>
        <div class="muted">
            <div>Tanggal: {{ $invoice->date->format('d/m/Y') }}</div>
            <div>No Invoice: {{ $invoice->number }}</div>
        </div>
    </div>

    <div class="grid">
        <div class="box">
            <div class="small">KEPADA:</div>
            <div>{{ $invoice->customer_name ?? '-' }}</div>
            <div class="small">{{ $invoice->customer_email }}</div>
        </div>
        <div class="box">
            <div class="small">KASIR:</div>
            <div>{{ $invoice->cashier->name }}</div>
            <div class="small">Pembayaran: {{ strtoupper($invoice->payment_type ?? '-') }}</div>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th style="width:45%">KETERANGAN</th>
            <th class="right" style="width:15%">HARGA</th>
            <th class="right" style="width:10%">JML</th>
            <th class="right" style="width:20%">TOTAL</th>
        </tr>
        </thead>
        <tbody>
        @if($invoice->type==='income')
            @foreach($invoice->incomes as $row)
                <tr>
                    <td>{{ $row->category->nama_pemasukkan ?? $row->description }}</td>
                    <td class="right">Rp {{ number_format($row->unit_price ?? $row->amount,0,',','.') }}</td>
                    <td class="right">{{ $row->qty ?? 1 }}</td>
                    <td class="right">Rp {{ number_format($row->amount,0,',','.') }}</td>
                </tr>
            @endforeach
        @else
            @foreach($invoice->expends as $row)
                <tr>
                    <td>{{ $row->description }}</td>
                    <td class="right">Rp {{ number_format($row->amount,0,',','.') }}</td>
                    <td class="right">1</td>
                    <td class="right">Rp {{ number_format($row->amount,0,',','.') }}</td>
                </tr>
            @endforeach
        @endif
        </tbody>
        <tfoot>
        <tr>
            <td colspan="3" class="right">SUB TOTAL</td>
            <td class="right">Rp {{ number_format($invoice->subtotal,0,',','.') }}</td>
        </tr>
        <tr>
            <td colspan="3" class="right">PAJAK</td>
            <td class="right">Rp {{ number_format($invoice->tax,0,',','.') }}</td>
        </tr>
        <tr>
            <td colspan="3" class="right">TOTAL</td>
            <td class="right">Rp {{ number_format($invoice->total,0,',','.') }}</td>
        </tr>
        </tfoot>
    </table>

    <div class="signature">
        <div>
            <div class="small">TERIMAKASIH ATAS PEMBELIAN ANDA</div>
        </div>
        <div style="text-align:right">
            <div class="small">Mono Frame</div>
            <div style="margin-top:40px">______________________</div>
        </div>
    </div>

    <div class="no-print" style="margin-top:16px; text-align:center;">
        <button onclick="window.print()">Cetak</button>
    </div>
</div>
</body>
</html>
