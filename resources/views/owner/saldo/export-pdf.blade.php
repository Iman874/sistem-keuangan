<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        h2 { text-align:center; color:#1f4fff; margin: 0 0 8px 0; }
        .meta { text-align:center; margin-bottom: 10px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #ddd; padding:6px; }
        thead th { background:#f1f5ff; }
        .right { text-align:right; }
        .muted { color:#666; font-size:11px; }
        .section { margin-top:16px; }
        .total { font-weight:bold; }
    </style>
</head>
<body>
    <h2>LAPORAN SALDO</h2>
    <div class="meta">
        @if($start && $end)
            Periode: {{ \Carbon\Carbon::parse($start)->translatedFormat('d F Y') }} sampai {{ \Carbon\Carbon::parse($end)->translatedFormat('d F Y') }}<br>
        @else
            Periode: All Time<br>
        @endif
        Jenis Data: {{ strtoupper($dataset) }}
        @if($account) | Akun: {{ strtoupper($account) }} @endif
        @if($user) | User: {{ $user->name }} @endif
    </div>

    @if(($dataset==='transfer' || $dataset==='both') && isset($transfers))
    <div class="section">
        <strong>Detail Transfer</strong>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Sumber</th>
                    <th>Tujuan</th>
                    <th>Jumlah</th>
                    <th>Catatan</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
            @php $totalT = 0; @endphp
            @forelse($transfers as $t)
                @php $totalT += (float)$t->amount; @endphp
                <tr>
                    <td>{{ $t->date }}</td>
                    <td>{{ $t->time }}</td>
                    <td>{{ ucfirst($t->source_account) }}</td>
                    <td>{{ ucfirst($t->destination_account) }}</td>
                    <td class="right">Rp {{ number_format($t->amount,0,',','.') }}</td>
                    <td>{{ $t->note }}</td>
                    <td>{{ optional($t->user)->name }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">Tidak ada data</td></tr>
            @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="right">Total</th>
                    <th class="right">Rp {{ number_format($totalT,0,',','.') }}</th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    @if(($dataset==='topup' || $dataset==='both') && isset($topups))
    <div class="section">
        <strong>Detail Topup</strong>
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Akun</th>
                    <th>Jumlah</th>
                    <th>Catatan</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
            @php $totalU = 0; @endphp
            @forelse($topups as $r)
                @php $totalU += (float)$r->amount; @endphp
                <tr>
                    <td>{{ optional($r->date)->format('d/m/Y') }}</td>
                    <td>{{ is_string($r->time) ? $r->time : optional($r->time)->format('H:i:s') }}</td>
                    <td>{{ ucfirst($r->account) }}</td>
                    <td class="right">Rp {{ number_format($r->amount,0,',','.') }}</td>
                    <td>{{ $r->note }}</td>
                    <td>{{ optional($r->user)->name }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">Tidak ada data</td></tr>
            @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="right">Total</th>
                    <th class="right">Rp {{ number_format($totalU,0,',','.') }}</th>
                    <th colspan="2"></th>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    <div class="muted" style="margin-top:16px; text-align:right;">Laporan dibuat: {{ $generatedAt }}</div>
</body>
</html>