<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan Lengkap</title>
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
        
        .container {
            width: 100%;
            padding: 10px 15px;
            box-sizing: border-box;
        }
        
        .header {
            padding-bottom: 12px;
            margin-bottom: 20px;
            border-bottom: 2px solid #4e73df;
            position: relative;
            height: 85px;
        }
        
        .logo-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 65%;
        }
        
        .monoframe-container {
            position: absolute;
            right: 0;
            top: 0;
            width: 30%;
            text-align: right;
        }
        
        .logo {
            height: 75px;
            float: left;
            margin-right: 15px;
        }
        
        .monoframe {
            height: 75px;
        }
        
        .company-info {
            float: left;
            padding-top: 5px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #4e73df;
        }
        
        .company-details {
            font-size: 10px;
            margin: 0;
            line-height: 1.4;
            color: #555;
        }
        
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        
        .report-title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin: 30px 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #2e59d9;
        }
        
        .report-subtitle {
            text-align: center;
            margin: 0 0 30px 0;
            font-size: 13px;
            color: #555;
            font-weight: 600;
        }
        
        .summary-section {
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f8f9fc;
            border-radius: 5px;
            border: 1px solid #e3e6f0;
        }
        
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            color: #4e73df;
            margin: 0 0 15px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .summary-container {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
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
        
        .box-income {
            background-color: #eef5ff;
            border-left: 4px solid #4e73df;
        }
        
        .box-expense {
            background-color: #fdf1f1;
            border-left: 4px solid #e74a3b;
        }
        
        .box-profit {
            background-color: #edf9f0;
            border-left: 4px solid #1cc88a;
        }
        
        .box-capital {
            background-color: #f8f9fc;
            border-left: 4px solid #858796;
        }
        
        .box-morning {
            background-color: #fff8e6;
            border-left: 4px solid #f6c23e;
        }
        
        .box-afternoon {
            background-color: #e6f7ff;
            border-left: 4px solid #36b9cc;
        }
        
        .summary-label {
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
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin: 30px 0 10px 0;
            color: #4e73df;
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 5px;
        }
        
        .section-title-expenses {
            color: #e74a3b;
        }
        
        .section-title-capital {
            color: #1cc88a;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 10px;
        }
        
        table.data-table th {
            background-color: #f8f9fc;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #e3e6f0;
        }
        
        table.data-table-income th {
            color: #4e73df;
        }
        
        table.data-table-expense th {
            color: #e74a3b;
        }
        
        table.data-table-capital th {
            color: #1cc88a;
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
        
        .badge-success {
            background-color: #1cc88a;
        }
        
        .badge-secondary {
            background-color: #858796;
        }
        
        .badge-danger {
            background-color: #e74a3b;
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
            margin-top: 40px;
            padding-top: 10px;
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
            background-color: #eaecf4;
            padding: 6px 10px;
            font-weight: bold;
            color: #5a5c69;
            border-radius: 3px;
        }
        
        .comparison-section {
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
        }
        
        .comparison-box {
            width: 48%;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e3e6f0;
        }
        
        .comparison-title {
            font-size: 12px;
            font-weight: bold;
            margin: 0 0 10px 0;
            text-align: center;
            padding-bottom: 5px;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .morning-box {
            background-color: #fffbf0;
            border-left: 4px solid #f6c23e;
        }
        
        .afternoon-box {
            background-color: #f0faff;
            border-left: 4px solid #36b9cc;
        }
        
        .comparison-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 10px;
        }
        
        .comparison-label {
            font-weight: bold;
        }
        
        .comparison-value {
            text-align: right;
            font-weight: bold;
        }
        
        .progress-container {
            width: 100%;
            height: 8px;
            background-color: #eaecf4;
            border-radius: 4px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            border-radius: 4px;
        }
        
        .progress-income {
            background-color: #4e73df;
        }
        
        .progress-expense {
            background-color: #e74a3b;
        }
        
        .progress-profit {
            background-color: #1cc88a;
        }
        
        .chart-container {
            margin: 20px 0;
            padding: 15px;
            background-color: #fff;
            border-radius: 5px;
            border: 1px solid #e3e6f0;
        }
        
        .category-list {
            margin-top: 15px;
        }
        
        .category-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 5px 0;
            border-bottom: 1px dashed #eaecf4;
        }
        
        .category-color {
            width: 12px;
            height: 12px;
            display: inline-block;
            margin-right: 5px;
            border-radius: 2px;
            vertical-align: middle;
        }
        
        .category-name {
            flex-grow: 1;
        }
        
        .category-amount {
            text-align: right;
            font-weight: bold;
            margin-right: 10px;
        }
        
        .category-percentage {
            width: 50px;
            text-align: right;
            color: #555;
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
                        Jl. Srigunting No.6, Air Tawar Bar., Kec. Padang Utara<br>
                        Kota Padang, Sumatera Barat 25132<br>
                        Telp: 082323426600 | Email: monoframestudio01@gmail.com
                    </p>
                </div>
            </div>
            <div class="monoframe-container">
                <img src="{{ public_path('assets/img/monoframe.png') }}" alt="Monoframe" class="monoframe" onerror="this.style.display='none'">
            </div>
        </div>
        
        <h1 class="report-title">Laporan Keuangan Lengkap</h1>
        <p class="report-subtitle">Periode: {{ $startDateFormatted }} sampai {{ $endDateFormatted }}</p>
        
        <!-- Financial Summary Section -->
        <div class="summary-section">
            <h2 class="summary-title">Ringkasan Keuangan</h2>
            
            <div class="summary-container">
                <div class="summary-row">
                    <div class="summary-box box-income">
                        <p class="summary-label">Total Pemasukkan</p>
                        <p class="summary-value">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                    </div>
                    <div class="summary-box box-expense">
                        <p class="summary-label">Total Pengeluaran</p>
                        <p class="summary-value">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                    </div>
                    <div class="summary-box box-profit">
                        <p class="summary-label">Total Profit</p>
                        <p class="summary-value">Rp {{ number_format($totalProfit, 0, ',', '.') }}</p>
                    </div>
                    <div class="summary-box box-capital">
                        <p class="summary-label">Total Modal</p>
                        <p class="summary-value">Rp {{ number_format($totalCapital, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Session Comparison -->
        <div class="comparison-section">
            <div class="comparison-box morning-box">
                <p class="comparison-title">Sesi Pagi</p>
                
                <div class="comparison-row">
                    <span class="comparison-label">Pemasukkan:</span>
                    <span class="comparison-value">Rp {{ number_format($morningIncome, 0, ',', '.') }}</span>
                </div>
                
                <div class="comparison-row">
                    <span class="comparison-label">Pengeluaran:</span>
                    <span class="comparison-value">Rp {{ number_format($morningExpense, 0, ',', '.') }}</span>
                </div>
                
                <div class="comparison-row">
                    <span class="comparison-label">Profit:</span>
                    <span class="comparison-value">Rp {{ number_format($morningIncome - $morningExpense, 0, ',', '.') }}</span>
                </div>
                
                <div class="progress-container">
                    <div class="progress-bar progress-profit" 
                         style="width: {{ ($morningIncome > 0 && ($morningIncome - $morningExpense) > 0) ? min(100, max(0, (($morningIncome - $morningExpense) / $morningIncome) * 100)) : 0 }}%">
                    </div>
                </div>
            </div>
            
            <div class="comparison-box afternoon-box">
                <p class="comparison-title">Sesi Sore</p>
                
                <div class="comparison-row">
                    <span class="comparison-label">Pemasukkan:</span>
                    <span class="comparison-value">Rp {{ number_format($afternoonIncome, 0, ',', '.') }}</span>
                </div>
                
                <div class="comparison-row">
                    <span class="comparison-label">Pengeluaran:</span>
                    <span class="comparison-value">Rp {{ number_format($afternoonExpense, 0, ',', '.') }}</span>
                </div>
                
                <div class="comparison-row">
                    <span class="comparison-label">Profit:</span>
                    <span class="comparison-value">Rp {{ number_format($afternoonIncome - $afternoonExpense, 0, ',', '.') }}</span>
                </div>
                
                <div class="progress-container">
                    <div class="progress-bar progress-profit" 
                         style="width: {{ ($afternoonIncome > 0 && ($afternoonIncome - $afternoonExpense) > 0) ? min(100, max(0, (($afternoonIncome - $afternoonExpense) / $afternoonIncome) * 100)) : 0 }}%">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Income Categories Chart -->
        <div class="chart-container">
            <h2 class="summary-title">Distribusi Pemasukkan berdasarkan Kategori</h2>
            
            <div class="category-list">
                @php 
                    $colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69']; 
                @endphp
                
                @forelse($incomeByCategory as $index => $category)
                    @php
                        $color = $colors[$index % count($colors)];
                        $amount = $incomeCategoryValues[$index];
                        $percentage = $totalIncome > 0 ? round(($amount / $totalIncome) * 100, 1) : 0;
                    @endphp
                    
                    <div class="category-item">
                        <div>
                            <span class="category-color" style="background-color: {{ $color }}"></span>
                            <span class="category-name">{{ $category }}</span>
                        </div>
                        <span class="category-amount">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                        <span class="category-percentage">{{ $percentage }}%</span>
                    </div>
                @empty
                    <p class="text-center">Tidak ada data kategori pemasukkan</p>
                @endforelse
            </div>
        </div>
        
        <!-- Page Break -->
        <div class="page-break"></div>
        
        <!-- Income Transactions -->
        <h2 class="section-title">Detail Transaksi Pemasukkan</h2>
        
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
                @php 
                    $no = 1; 
                    $currentDate = null;
                    $incomesGrouped = $incomes->sortByDesc('date')->groupBy(function($item) {
                        return $item->date->format('Y-m-d');
                    });
                @endphp
                
                @forelse($incomesGrouped as $date => $dateIncomes)
                    <tr>
                        <td colspan="6" class="date-group">{{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</td>
                    </tr>
                    
                    @foreach($dateIncomes as $income)
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ $income->date->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $income->session == 'pagi' ? 'warning' : 'info' }}">
                                    {{ ucfirst($income->session) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($income->other_source)
                                    <span class="badge badge-secondary">Lainnya</span>
                                @else
                                    <span class="badge badge-primary">Kategori</span>
                                @endif
                            </td>
                            <td>
                                @if($income->other_source)
                                    {{ $income->description }}
                                @else
                                    {{ $income->category->nama_pemasukkan ?? 'Kategori Dihapus' }}
                                @endif
                            </td>
                            <td class="amount-cell">Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data pemasukkan untuk periode ini</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-right">TOTAL PEMASUKKAN</th>
                    <th class="amount-cell">Rp {{ number_format($totalIncome, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
        
        <!-- Expense Transactions (including Salary Payments) -->
        <h2 class="section-title section-title-expenses">Detail Transaksi Pengeluaran (Termasuk Gaji)</h2>
        
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
                @php 
                    $no = 1; 
                    $currentDate = null;
                    // Use mergedExpenses passed from controller (Expend + SalaryPayment normalized)
                    $expensesGrouped = $mergedExpenses->sortByDesc('date')->groupBy(function($item) {
                        return $item->date->format('Y-m-d');
                    });
                @endphp
                
                @forelse($expensesGrouped as $date => $dateExpenses)
                    <tr>
                        <td colspan="6" class="date-group">{{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</td>
                    </tr>
                    
                    @foreach($dateExpenses as $expense)
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ $expense->date->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <span class="badge badge-{{ $expense->session == 'pagi' ? 'warning' : 'info' }}">
                                    {{ ucfirst($expense->session) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $type = $expense->type ?? 'lainnya';
                                    $badgeClass = 'secondary';
                                    if ($type === 'harian') $badgeClass = 'primary';
                                    elseif ($type === 'bulanan') $badgeClass = 'success';
                                    elseif ($type === 'gaji') $badgeClass = 'danger';
                                @endphp
                                <span class="badge badge-{{ $badgeClass }}">{{ ucfirst($type) }}</span>
                            </td>
                            <td>{{ $expense->description }}</td>
                            <td class="amount-cell">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data pengeluaran untuk periode ini</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-right">TOTAL PENGELUARAN</th>
                    <th class="amount-cell">Rp {{ number_format($totalExpense, 0, ',', '.') }}</th>
                </tr>
                <tr>
                    <th colspan="5" class="text-right">Subtotal Gaji</th>
                    <th class="amount-cell">Rp {{ number_format($totalSalaryExpense ?? 0, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
        
        <!-- Page Break -->
        <div class="page-break"></div>
        
        <!-- Capital/Modal Transactions -->
        <h2 class="section-title section-title-capital">Detail Modal / Inventaris</h2>
        
        <table class="data-table data-table-capital">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="11%">Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Deskripsi</th>
                    <th width="18%">Harga</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $no = 1; 
                    $modalsGrouped = $modals->sortByDesc('tanggal')->groupBy(function($item) {
                        return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
                    });
                @endphp
                
                @forelse($modalsGrouped as $date => $dateModals)
                    <tr>
                        <td colspan="5" class="date-group">{{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</td>
                    </tr>
                    
                    @foreach($dateModals as $modal)
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ \Carbon\Carbon::parse($modal->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $modal->nama_barang }}</td>
                            <td>{{ $modal->deskripsi ?? '-' }}</td>
                            <td class="amount-cell">Rp {{ number_format($modal->harga, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data modal untuk periode ini</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">TOTAL MODAL</th>
                    <th class="amount-cell">Rp {{ number_format($modals->sum('harga'), 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>
        
        <div class="footer">
            <p>Laporan ini dibuat pada: {{ $currentDate }} | Monoframe Studio Management System</p>
        </div>
    </div>
</body>
</html>