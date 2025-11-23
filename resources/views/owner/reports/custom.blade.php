<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Custom</title>
    <style>
        body {
            font-family: "Arial", "Helvetica", sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: white;
        }
        .container { width:100%; padding:10px 15px; box-sizing:border-box; }
        .report-title { font-size:18px; font-weight:bold; text-align:center; margin:30px 0 5px; text-transform:uppercase; letter-spacing:1px; color:#2e59d9; }
        .report-subtitle { text-align:center; margin:0 0 25px; font-size:13px; color:#555; font-weight:600; }
        table.data-table { width:100%; border-collapse:collapse; margin-bottom:25px; font-size:10px; }
        table.data-table th { background-color:#f8f9fc; font-weight:bold; text-align:left; padding:8px; border:1px solid #e3e6f0; }
        table.data-table td { padding:8px; border:1px solid #e3e6f0; vertical-align:middle; }
        table.data-table tr:nth-child(even) { background-color:#f9fafc; }
        .text-right { text-align:right; }
        .text-center { text-align:center; }
        .amount-cell { font-weight:bold; text-align:right; white-space:nowrap; }
        .section-title { font-size:14px; font-weight:bold; margin:30px 0 10px; color:#4e73df; border-bottom:1px solid #e3e6f0; padding-bottom:5px; }
        .section-title-expenses { color:#e74a3b; }
        .badge { display:inline-block; padding:3px 6px; font-size:9px; font-weight:bold; border-radius:3px; color:#fff; }
        .badge-warning { background:#f6c23e; }
        .badge-info { background:#36b9cc; }
        .badge-primary { background:#4e73df; }
        .badge-success { background:#1cc88a; }
        .badge-secondary { background:#858796; }
        .badge-danger { background:#e74a3b; }
        .footer { text-align:right; font-size:9px; color:#858796; margin-top:25px; padding-top:10px; border-top:1px solid #e3e6f0; }
        .date-group { background-color:#eaecf4; padding:6px 10px; font-weight:bold; color:#5a5c69; border-radius:3px; }
    </style>
</head>
<body>
<div class="container">
    <h1 class="report-title">Laporan Custom</h1>
    <p class="report-subtitle">Periode: {{ $startDateFormatted }} sampai {{ $endDateFormatted }}</p>

    @if($incomes && $incomes->count())
        <h2 class="section-title">Detail Transaksi Pemasukkan</h2>
        @php 
            $incomesGrouped = $incomes->sortByDesc('date')->groupBy(function($item){ return optional($item->date)->format('Y-m-d'); });
            $noIncome = 1; 
        @endphp
        <table class="data-table data-table-income">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="11%">Tanggal</th>
                    <th width="7%">Sesi</th>
                    <th width="10%">Sumber</th>
                    <th>Deskripsi/Kategori</th>
                    <th width="18%">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incomesGrouped as $date => $group)
                    <tr>
                        <td colspan="6" class="date-group">{{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</td>
                    </tr>
                    @foreach($group as $i)
                        <tr>
                            <td class="text-center">{{ $noIncome++ }}</td>
                            <td>{{ optional($i->date)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $i->session == 'pagi' ? 'warning' : 'info' }}">{{ ucfirst($i->session) }}</span>
                            </td>
                            <td class="text-center">
                                @if($i->other_source)
                                    <span class="badge badge-secondary">Lainnya</span>
                                @else
                                    <span class="badge badge-primary">Kategori</span>
                                @endif
                            </td>
                            <td>{{ $i->other_source ? $i->description : ($i->category->nama_pemasukkan ?? 'Kategori Dihapus') }}</td>
                            <td class="amount-cell">Rp {{ number_format($i->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr><td colspan="6" class="text-center">Tidak ada data pemasukkan</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-right">TOTAL PEMASUKKAN</th>
                    <th class="amount-cell">Rp {{ number_format($totalIncome, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    @endif

    @if($expenses && $expenses->count())
        <h2 class="section-title section-title-expenses">Detail Transaksi Pengeluaran</h2>
        @php 
            $expensesGrouped = $expenses->sortByDesc('date')->groupBy(function($item){ return optional($item->date)->format('Y-m-d'); });
            $noExpense = 1; 
        @endphp
        <table class="data-table data-table-expense">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="11%">Tanggal</th>
                    <th width="7%">Sesi</th>
                    <th width="10%">Jenis</th>
                    <th>Deskripsi</th>
                    <th width="18%">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expensesGrouped as $date => $group)
                    <tr>
                        <td colspan="6" class="date-group">{{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</td>
                    </tr>
                    @foreach($group as $e)
                        @php
                            $type = $e->type ?? 'lainnya';
                            $badgeClass = 'secondary';
                            if ($type === 'harian') $badgeClass = 'primary';
                            elseif ($type === 'bulanan') $badgeClass = 'success';
                            elseif ($type === 'gaji') $badgeClass = 'danger';
                        @endphp
                        <tr>
                            <td class="text-center">{{ $noExpense++ }}</td>
                            <td>{{ optional($e->date)->format('d/m/Y') }}</td>
                            <td class="text-center"><span class="badge badge-{{ ($e->session == 'pagi') ? 'warning' : 'info' }}">{{ $e->session == '-' ? '-' : ucfirst($e->session) }}</span></td>
                            <td class="text-center"><span class="badge badge-{{ $badgeClass }}">{{ ucfirst($type) }}</span></td>
                            <td>{{ $e->description }}</td>
                            <td class="amount-cell">Rp {{ number_format($e->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr><td colspan="6" class="text-center">Tidak ada data pengeluaran</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-right">TOTAL PENGELUARAN</th>
                    <th class="amount-cell">Rp {{ number_format($totalExpense, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
    @endif

    <div class="footer">
        <p>Laporan dibuat: {{ $currentDate }} | Monoframe Studio Management System</p>
    </div>
</div>
</body>
</html>
