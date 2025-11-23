<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Analisis Keuangan</title>
    <style>
        body{font-family:DejaVu Sans,Arial,sans-serif;font-size:12px;}
        table{width:100%;border-collapse:collapse;margin-top:10px;}
        th,td{border:1px solid #666;padding:4px 6px;text-align:left;}
        th{background:#f0f0f0;}
        .text-right{text-align:right;}
    </style>
</head>
<body>
    <h3 style="margin:0 0 4px;">Laporan Analisis Keuangan</h3>
    <div style="font-size:11px;">Dataset: {{ ucfirst($dataset) }} | Periode: {{ $start }} - {{ $end }} | Dibuat: {{ $generatedAt }}</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis</th>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Jenis Kategori Detail</th>
                <th>Tipe Pembayaran</th>
                <th>Jumlah (Rp)</th>
                <th>User</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            @php $i=1; $grand=0; @endphp
            @forelse($rows as $r)
                @php $grand += (int)$r['amount']; @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $r['type'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($r['date'])->format('d/m/Y') }}</td>
                    <td>{{ $r['category'] }}</td>
                    <td>
                        @if($r['type']==='Pengeluaran')
                            {{ !empty($r['expense_type']) && $r['expense_type']!='-' ? $r['expense_type'] : '' }}
                        @elseif($r['type']==='Pemasukkan')
                            {{ !empty($r['income_type']) && $r['income_type']!='-' ? $r['income_type'] : '' }}
                        @else
                            {{-- kosong --}}
                        @endif
                    </td>
                    <td>{{ $r['payment_type'] ?? '-' }}</td>
                    <td class="text-right">{{ number_format($r['amount'],0,',','.') }}</td>
                    <td>{{ $r['user'] }}</td>
                    <td>{{ ucfirst($r['role']) }}</td>
                </tr>
            @empty
                <tr><td colspan="9" style="text-align:center;color:#888;">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6">Total</th>
                <th class="text-right">{{ number_format($grand,0,',','.') }}</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>
</body>
</html>
