<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pemasukkan Kasir</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .container {
            width: 100%;
            padding: 15px;
            box-sizing: border-box;
        }
        
        .header {
            width: 100%;
            border-bottom: 2px solid #4e73df;
            padding-bottom: 15px;
            margin-bottom: 20px;
            position: relative;
            height: 80px;
        }
        
        .logo-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 70%;
        }
        
        .monoframe-container {
            position: absolute;
            right: 0;
            top: 0;
            width: 25%;
            text-align: right;
        }
        
        .logo {
            height: 70px;
            float: left;
            margin-right: 15px;
        }
        
        .monoframe {
            height: 70px;
        }
        
        .company-info {
            float: left;
            padding-top: 5px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #4e73df;
        }
        
        .company-details {
            font-size: 10px;
            margin: 0;
            line-height: 1.3;
        }
        
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        
        .report-title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 25px 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #2e59d9;
        }
        
        .report-subtitle {
            text-align: center;
            margin: 0 0 25px 0;
            font-size: 12px;
            color: #555;
        }
        
        .summary-container {
            margin-bottom: 25px;
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
        }
        
        .summary-row {
            display: table-row;
        }
        
        .summary-box {
            display: table-cell;
            width: 25%;
            padding: 12px;
            border-radius: 4px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .box-total {
            background-color: #eef5ff;
            border-left: 4px solid #4e73df;
        }
        
        .box-morning {
            background-color: #fff8e6;
            border-left: 4px solid #f6c23e;
        }
        
        .box-afternoon {
            background-color: #e6f7ff;
            border-left: 4px solid #36b9cc;
        }
        
        .box-category {
            background-color: #edf9f0;
            border-left: 4px solid #1cc88a;
        }
        
        .summary-title {
            font-size: 10px;
            text-transform: uppercase;
            margin: 0 0 5px 0;
            font-weight: bold;
            color: #555;
            letter-spacing: 0.5px;
        }
        
        .summary-value {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            color: #333;
        }
        
        .filter-info {
            margin-bottom: 20px;
            padding: 12px 15px;
            background-color: #f8f9fc;
            border-radius: 4px;
            border-left: 4px solid #858796;
        }
        
        .filter-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
            color: #4e73df;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 25px 0 10px 0;
            color: #4e73df;
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 5px;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 10px;
        }
        
        table.data-table th {
            background-color: #f8f9fc;
            color: #4e73df;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #e3e6f0;
        }
        
        table.data-table td {
            padding: 8px;
            border: 1px solid #e3e6f0;
            vertical-align: middle;
        }
        
        table.data-table tr:nth-child(even) {
            background-color: #f9fafc;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-bold {
            font-weight: bold;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 3px;
            color: white;
        }
        
        .badge-warning {
            background-color: #f6c23e;
        }
        
        .badge-info {
            background-color: #36b9cc;
        }
        
        .badge-primary {
            background-color: #4e73df;
        }
        
        .badge-secondary {
            background-color: #858796;
        }
        
        tfoot tr {
            background-color: #f2f6fc !important;
        }
        
        tfoot th {
            padding: 10px 8px !important;
        }
        
        .footer {
            text-align: right;
            font-size: 9px;
            color: #858796;
            margin-top: 30px;
            padding-top: 8px;
            border-top: 1px solid #e3e6f0;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .amount-cell {
            font-weight: bold;
            text-align: right;
            white-space: nowrap;
        }
        
        .date-group {
            margin-bottom: 5px;
            background-color: #eaecf4;
            padding: 5px 10px;
            font-weight: bold;
            color: #5a5c69;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="clearfix header">
            <div class="logo-container">
                <img src="{{ public_path('assets/img/logo.png') }}" alt="Logo Perusahaan" class="logo" onerror="this.style.display='none'">
                <div class="company-info">
                    <h1 class="company-name">Monoframe Studio</h1>
                    <p class="company-details">
                        Jl. Srigunting No.6, Air Tawar Bar., Kec. Padang Utara, Kota Padang<br>
                        Sumatera Barat 25132 | Telp: 082323426600 | Email: monoframestudio01@gmail.com
                    </p>
                </div>
            </div>
            <div class="monoframe-container">
                <img src="{{ public_path('assets/img/monoframe.png') }}" alt="Monoframe" class="monoframe" onerror="this.style.display='none'">
            </div>
        </div>
        
        <h1 class="report-title">Laporan Pemasukkan Kasir</h1>
        
        @php
            $startDate = request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d F Y') : 'Semua Data';
            $endDate = request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d F Y') : 'Semua Data';
            
            $totalAmount = $incomes->sum('amount');
            $totalMorning = $incomes->where('session', 'pagi')->sum('amount');
            $totalAfternoon = $incomes->where('session', 'sore')->sum('amount');
            $totalCategory = $incomes->where('other_source', false)->sum('amount');
            $totalOther = $incomes->where('other_source', true)->sum('amount');
            
            $morningPercentage = $totalAmount > 0 ? round(($totalMorning / $totalAmount) * 100, 1) : 0;
            $afternoonPercentage = $totalAmount > 0 ? round(($totalAfternoon / $totalAmount) * 100, 1) : 0;
        @endphp
        
        <p class="report-subtitle">Periode: {{ $startDate }} sampai {{ $endDate }}</p>
        
        @if(request('session') || request('source_type'))
        <div class="filter-info">
            @if(request('session'))
                <p><span class="filter-label">Filter Sesi:</span> {{ ucfirst(request('session')) }}</p>
            @endif
            
            @if(request('source_type'))
                <p><span class="filter-label">Filter Sumber:</span> {{ request('source_type') == 'category' ? 'Kategori' : 'Lainnya' }}</p>
            @endif
        </div>
        @endif
        
        <div class="summary-container">
            <div class="summary-row">
                <div class="summary-box box-total">
                    <p class="summary-title">Total Pemasukkan</p>
                    <p class="summary-value">Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
                </div>
                <div class="summary-box box-morning">
                    <p class="summary-title">Sesi Pagi ({{ $morningPercentage }}%)</p>
                    <p class="summary-value">Rp {{ number_format($totalMorning, 0, ',', '.') }}</p>
                </div>
                <div class="summary-box box-afternoon">
                    <p class="summary-title">Sesi Sore ({{ $afternoonPercentage }}%)</p>
                    <p class="summary-value">Rp {{ number_format($totalAfternoon, 0, ',', '.') }}</p>
                </div>
                <div class="summary-box box-category">
                    <p class="summary-title">Kategori / Lainnya</p>
                    <p class="summary-value" style="font-size: 12px;">
                        Rp {{ number_format($totalCategory, 0, ',', '.') }}<br>
                        <span style="font-size: 10px;">Lainnya: Rp {{ number_format($totalOther, 0, ',', '.') }}</span>
                    </p>
                </div>
            </div>
        </div>
        
        <h2 class="section-title">Detail Transaksi Pemasukkan</h2>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="11%">Tanggal</th>
                    <th width="7%">Sesi</th>
                    <th width="16%">Kategori</th>
                    <th width="12%">Jenis Transaksi</th>
                    <th>Deskripsi</th>
                    <th width="18%">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $no = 1; 
                    $currentDate = null;
                    $incomes = $incomes->sortByDesc('date');
                @endphp
                
                @forelse($incomes as $income)
                    @php
                        $date = $income->date->format('Y-m-d');
                        $showDateHeader = $currentDate !== $date;
                        $currentDate = $date;
                    @endphp
                    
                    @if($showDateHeader)
                        <tr>
                            <td colspan="7" class="date-group">{{ $income->date->format('l, d F Y') }}</td>
                        </tr>
                    @endif
                    
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $income->date->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <span class="badge badge-{{ $income->session == 'pagi' ? 'warning' : 'info' }}">
                                {{ ucfirst($income->session) }}
                            </span>
                        </td>
                        <td>{{ $income->category->nama_pemasukkan ?? 'Kategori Dihapus' }}</td>
                        <td class="text-center">
                            <span class="badge badge-{{ $income->payment_type == 'qris' ? 'primary' : 'secondary' }}">{{ strtoupper($income->payment_type ?? 'CASH') }}</span>
                        </td>
                        <td>{{ $income->description ? Str::limit($income->description,80) : '-' }}</td>
                        <td class="amount-cell">Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data pemasukkan untuk periode ini</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" class="text-right">TOTAL PEMASUKKAN</th>
                    <th class="amount-cell">Rp {{ number_format($totalAmount, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
        
        <div class="footer">
            <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i:s') }} | Monoframe Studio Management System</p>
        </div>
    </div>
</body>
</html>