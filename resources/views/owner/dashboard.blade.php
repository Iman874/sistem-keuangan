@extends(auth()->check() && auth()->user()->role === 'admin' ? 'admin.layouts.app' : 'owner.layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
<div class="mb-4 d-sm-flex align-items-center justify-content-between">
    <h1 class="mb-0 text-gray-800 h3">Dashboard {{ auth()->check() && auth()->user()->role === 'admin' ? 'Admin' : 'Owner' }}</h1>
    <div>
        <a href="{{ route('owner.kasir-income.report') }}" class="mr-2 shadow-sm d-none d-sm-inline-block btn btn-sm btn-primary">
            <i class="fas fa-download fa-sm text-white-50"></i> Laporan Pemasukkan
        </a>
        <a href="{{ route('owner.kasir-expend.report') }}" class="mr-2 shadow-sm d-none d-sm-inline-block btn btn-sm btn-success">
            <i class="fas fa-download fa-sm text-white-50"></i> Laporan Pengeluaran
        </a>
        <a href="#" class="mr-2 shadow-sm d-none d-sm-inline-block btn btn-sm btn-secondary" data-toggle="modal" data-target="#window_dashboard_donwload_laporan">
            <i class="fas fa-filter fa-sm text-white-50"></i> Pengeluaran Custom
        </a>
        <a href="#" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-dark" data-toggle="modal" data-target="#reportModal">
            <i class="fas fa-file-pdf fa-sm text-white-50"></i> Laporan Keuangan Custom
        </a>
    </div>
</div>

<!-- Filter Card -->
<div class="mb-4 shadow card">
    <div class="py-3 card-header">
        <h6 class="m-0 font-weight-bold text-primary">Filter Data</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('owner.dashboard') }}" method="GET" id="filter-form">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <div class="mb-0 form-group">
                        <label>Periode:</label>
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            <label class="btn btn-outline-primary {{ $period == 'daily' ? 'active' : '' }}">
                                <input type="radio" name="period" value="daily" {{ $period == 'daily' ? 'checked' : '' }} onchange="this.form.submit()"> Harian
                            </label>
                            <label class="btn btn-outline-primary {{ $period == 'weekly' ? 'active' : '' }}">
                                <input type="radio" name="period" value="weekly" {{ $period == 'weekly' ? 'checked' : '' }} onchange="this.form.submit()"> Mingguan
                            </label>
                            <label class="btn btn-outline-primary {{ $period == 'monthly' ? 'active' : '' }}">
                                <input type="radio" name="period" value="monthly" {{ $period == 'monthly' ? 'checked' : '' }} onchange="this.form.submit()"> Bulanan
                            </label>
                            <label class="btn btn-outline-primary {{ $period == 'yearly' ? 'active' : '' }}">
                                <input type="radio" name="period" value="yearly" {{ $period == 'yearly' ? 'checked' : '' }} onchange="this.form.submit()"> Tahunan
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-0 form-group">
                        <label for="date">Tanggal {{ $period == 'yearly' ? 'Tahun' : ($period == 'monthly' ? 'Bulan' : ($period == 'daily' ? 'Hari' : 'Minggu')) }}:</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ $selectedDate->format('Y-m-d') }}" onchange="this.form.submit()">
                    </div>
                </div>
                <div class="col-md-3">
                    @if($period == 'daily' || $period == 'weekly' || $period == 'monthly' || $period == 'yearly')
                    <div class="mb-0 form-group">
                        <label for="date_info">Periode:</label>
                        <input type="text" class="form-control" id="date_info" value="{{ $dateRangeLabel }}" readonly>
                    </div>
                    @endif
                </div>
                <div class="text-right col-md-1">
                    <a href="{{ route('owner.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-sync"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

    <!-- Content Row - Summary Cards -->
    <div class="row">
        <!-- Total Pemasukkan Card -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="py-2 shadow card border-left-primary h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-primary text-uppercase">
                                Total Pemasukkan ({{ $currentMonthYear }})</div>
                            <div class="mb-0 text-gray-800 h5 font-weight-bold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-calendar fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saldo Kasir Saat Ini Card -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="py-2 shadow card border-left-teal h-100" style="border-left-color:#20c997!important;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-success text-uppercase">Saldo Kasir Saat Ini</div>
                            <div class="mb-0 text-gray-800 h5 font-weight-bold">Rp {{ number_format($currentKasirSaldo ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-cash-register fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pengeluaran Card -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="py-2 shadow card border-left-danger h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-danger text-uppercase">
                                Total Pengeluaran ({{ $currentMonthYear }})</div>
                            <div class="mb-0 text-gray-800 h5 font-weight-bold">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-calendar-minus fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pemasukkan QRIS Card -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="py-2 shadow card border-left-info h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-info text-uppercase">Pemasukkan QRIS ({{ $currentMonthYear }})</div>
                            <div class="mb-0 text-gray-800 h5 font-weight-bold">Rp {{ number_format($incomeQrisTotal ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-qrcode fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pemasukkan Cash Card -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="py-2 shadow card border-left-secondary h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-secondary text-uppercase">Pemasukkan Cash ({{ $currentMonthYear }})</div>
                            <div class="mb-0 text-gray-800 h5 font-weight-bold">Rp {{ number_format($incomeCashTotal ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->check() && ((auth()->user()->role === 'owner') || (auth()->user()->role === 'admin' && auth()->user()->hasPermission('salary.read'))))
        <!-- Pengeluaran Gaji Card -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="py-2 shadow card border-left-warning h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-warning text-uppercase">
                                Pengeluaran Gaji ({{ $currentMonthYear }})
                            </div>
                            <div class="mb-0 text-gray-800 h5 font-weight-bold">Rp {{ number_format($salaryExpense ?? 0, 0, ',', '.') }}</div>
                            @php
                                $salaryShare = ($totalExpense > 0 && isset($salaryExpense)) ? round(($salaryExpense / $totalExpense) * 100, 1) : 0;
                            @endphp
                            <div class="small text-muted">Proporsi: {{ $salaryShare }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-user-tie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->check() && auth()->user()->role === 'owner')
        <!-- Total Profit Card (Owner only) -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="py-2 shadow card border-left-success h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-success text-uppercase">
                                Profit ({{ $currentMonthYear }})</div>
                            <div class="mb-0 text-gray-800 h5 font-weight-bold">Rp {{ number_format($profit, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->check() && auth()->user()->role === 'owner')
        <!-- Modal Tersedia Card (Owner only) -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="py-2 shadow card border-left-info h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-info text-uppercase">Modal Tersedia
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="mb-0 mr-3 text-gray-800 h5 font-weight-bold">Rp {{ number_format($availableCapital, 0, ',', '.') }}</div>
                                </div>
                                <div class="col">
                                    <div class="mr-2 progress progress-sm">
                                        <div class="progress-bar bg-info" role="progressbar" 
                                            style="width: {{ min(100, ($availableCapital / ($totalCapital > 0 ? $totalCapital : 1)) * 100) }}%" 
                                            aria-valuenow="{{ min(100, ($availableCapital / ($totalCapital > 0 ? $totalCapital : 1)) * 100) }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-clipboard-list fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

@if(auth()->check() && auth()->user()->role === 'owner')
<div class="row">
    <div class="mb-4 col-lg-6">
        <div class="shadow card">
            <div class="flex-row py-3 card-header d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Analisis Balik Modal</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h4 class="small font-weight-bold">
                        Progress Balik Modal 
                        <span class="float-right">
                            @php
                                $roiProgress = $totalCapital > 0 ? min(100, max(0, ($profit / $totalCapital) * 100)) : 0;
                            @endphp
                            {{ number_format($roiProgress, 1) }}%
                        </span>
                    </h4>
                    <div class="mb-4 progress">
                        <div class="progress-bar {{ $roiProgress < 30 ? 'bg-danger' : ($roiProgress < 70 ? 'bg-warning' : 'bg-success') }}" 
                             role="progressbar" style="width: {{ $roiProgress }}%"
                             aria-valuenow="{{ $roiProgress }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th style="width: 50%">Total Modal</th>
                                <td>Rp {{ number_format($totalCapital, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Total Profit {{ $period == 'weekly' ? 'Minggu Ini' : ($period == 'monthly' ? 'Bulan Ini' : 'Tahun Ini') }}</th>
                                <td>Rp {{ number_format($profit, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Profit Rata-rata per Hari</th>
                                <td>Rp {{ number_format($averageDailyProfit, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th>Estimasi Hari Hingga Balik Modal</th>
                                <td>
                                    @if(isset($showROI) && $showROI)
                                        {{ $daysUntilROI }} Hari
                                        <small class="text-muted d-block">
                                            ({{ $estimatedROIDate->format('d F Y') }})
                                        </small>
                                    @else
                                        <span class="text-danger">Tidak dapat diestimasi</span>
                                        <small class="text-muted d-block">
                                            Profit tidak mencukupi untuk perhitungan
                                        </small>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3 small text-muted">
                    <i class="fas fa-info-circle"></i> Estimasi balik modal dihitung berdasarkan profit rata-rata per hari dari periode {{ $period == 'daily' ? 'harian' : ($period == 'weekly' ? 'mingguan' : ($period == 'monthly' ? 'bulanan' : 'tahunan')) }} yang Anda pilih.
                </div>
            </div>
        </div>
    </div>
</div>
@endif

    <!-- Content Row - Charts -->
    <div class="row">
        <!-- Area Chart - Monthly Finances -->
        <div class="col-xl-8 col-lg-7">
            <div class="mb-4 shadow card">
                <div class="flex-row py-3 card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        @if($period == 'daily')
                            Ikhtisar Keuangan Harian ({{ $dateRangeLabel }})
                        @elseif($period == 'weekly')
                            Ikhtisar Keuangan Mingguan ({{ $dateRangeLabel }})
                        @elseif($period == 'monthly')
                            Ikhtisar Keuangan Bulanan ({{ $dateRangeLabel }})
                        @else
                            Ikhtisar Keuangan Tahunan ({{ $selectedDate->format('Y') }})
                        @endif
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="text-gray-400 fas fa-ellipsis-v fa-sm fa-fw"></i>
                        </a>
                        <div class="shadow dropdown-menu dropdown-menu-right animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Options:</div>
                            <a class="dropdown-item" href="#">View Full Report</a>
                            <a class="dropdown-item" href="#">Download CSV</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="myAreaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart - Income Distribution -->
        <div class="col-xl-4 col-lg-5">
            <div class="mb-4 shadow card">
                <div class="flex-row py-3 card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Distribusi Pemasukkan</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="text-gray-400 fas fa-ellipsis-v fa-sm fa-fw"></i>
                        </a>
                        <div class="shadow dropdown-menu dropdown-menu-right animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Options:</div>
                            <a class="dropdown-item" href="#">View Details</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="pt-4 pb-2 chart-pie">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($incomeCategories as $index => $category)
                        <span class="mr-2">
                            <i class="fas fa-circle" style="color: {{ $pieChartColors[$index % count($pieChartColors)] }}"></i> {{ $category }}
                        </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Session Comparison -->
    <div class="row">
        <div class="col-12">
            <div class="mb-4 shadow card">
                <div class="py-3 card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Perbandingan Sesi Pagi & Sore ({{ $currentMonthYear }})</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Morning Session -->
                        <div class="col-md-6">
                            <div class="mb-3 card border-left-warning">
                                <div class="text-white card-header bg-warning">
                                    <h6 class="m-0 font-weight-bold">Sesi Pagi</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="small font-weight-bold">Pemasukkan <span class="float-right">Rp {{ number_format($morningIncome, 0, ',', '.') }}</span></h4>
                                            <div class="mb-4 progress">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h4 class="small font-weight-bold">Pengeluaran <span class="float-right">Rp {{ number_format($morningExpense, 0, ',', '.') }}</span></h4>
                                            <div class="mb-4 progress">
                                                <div class="progress-bar bg-danger" role="progressbar" 
                                                    style="width: {{ $morningIncome > 0 ? min(100, ($morningExpense / $morningIncome) * 100) : 0 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <h4 class="small font-weight-bold">Profit <span class="float-right">Rp {{ number_format($morningIncome - $morningExpense, 0, ',', '.') }}</span></h4>
                                    <div class="progress">
                                        <div class="progress-bar {{ ($morningIncome - $morningExpense) > 0 ? 'bg-success' : 'bg-danger' }}" role="progressbar" 
                                            style="width: {{ $morningIncome > 0 ? min(100, max(0, (($morningIncome - $morningExpense) / $morningIncome) * 100)) : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Afternoon Session -->
                        <div class="col-md-6">
                            <div class="mb-3 card border-left-info">
                                <div class="text-white card-header bg-info">
                                    <h6 class="m-0 font-weight-bold">Sesi Sore</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="small font-weight-bold">Pemasukkan <span class="float-right">Rp {{ number_format($afternoonIncome, 0, ',', '.') }}</span></h4>
                                            <div class="mb-4 progress">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h4 class="small font-weight-bold">Pengeluaran <span class="float-right">Rp {{ number_format($afternoonExpense, 0, ',', '.') }}</span></h4>
                                            <div class="mb-4 progress">
                                                <div class="progress-bar bg-danger" role="progressbar" 
                                                    style="width: {{ $afternoonIncome > 0 ? min(100, ($afternoonExpense / $afternoonIncome) * 100) : 0 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <h4 class="small font-weight-bold">Profit <span class="float-right">Rp {{ number_format($afternoonIncome - $afternoonExpense, 0, ',', '.') }}</span></h4>
                                    <div class="progress">
                                        <div class="progress-bar {{ ($afternoonIncome - $afternoonExpense) > 0 ? 'bg-success' : 'bg-danger' }}" role="progressbar" 
                                            style="width: {{ $afternoonIncome > 0 ? min(100, max(0, (($afternoonIncome - $afternoonExpense) / $afternoonIncome) * 100)) : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Recent Transactions -->
    <div class="row">
        <!-- Recent Income -->
        <div class="mb-4 col-lg-6">
            <div class="mb-4 shadow card">
                <div class="flex-row py-3 card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Pemasukkan Kasir Terbaru</h6>
                    <a href="{{ route('owner.kasir-income.index') }}" class="btn btn-sm btn-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Tanggal/Waktu</th>
                                    <th>Sesi</th>
                                    <th>Tipe</th>
                                    <th>Sumber</th>
                                    <th>Jumlah</th>
                                    <th>Kasir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentIncomes as $income)
                                <tr>
                                    <td>
                                        {{ $income->date->format('d/m/Y') }}
                                        <div class="small text-gray-600">{{ $income->time ? $income->time->format('H:i:s') : '-' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $income->session == 'pagi' ? 'warning' : 'info' }}">
                                            {{ ucfirst($income->session) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $income->payment_type == 'qris' ? 'primary' : 'secondary' }}">
                                            {{ strtoupper($income->payment_type ?? 'CASH') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($income->other_source)
                                            <span class="badge badge-secondary">Lainnya</span>
                                            <small>{{ Str::limit($income->description, 20) }}</small>
                                        @else
                                            {{ $income->category->nama_pemasukkan ?? 'Kategori Dihapus' }}
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge badge-light">
                                            {{ $income->user ? $income->user->name : 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data pemasukkan terbaru</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="mb-4 col-lg-6">
            <div class="mb-4 shadow card">
                <div class="flex-row py-3 card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Pengeluaran Kasir Terbaru</h6>
                    <a href="{{ route('owner.kasir-expend.index') }}" class="btn btn-sm btn-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Tanggal/Waktu</th>
                                    <th>Sesi</th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah</th>
                                    <th>Kasir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentExpenses as $expense)
                                <tr>
                                    <td>
                                        {{ $expense->date->format('d/m/Y') }}
                                        <div class="small text-gray-600">{{ $expense->time ? $expense->time->format('H:i:s') : '-' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $expense->session == 'pagi' ? 'warning' : 'info' }}">
                                            {{ ucfirst($expense->session) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $expense->type == 'harian' ? 'primary' : 'success' }}">
                                            {{ ucfirst($expense->type) }}
                                        </span>
                                        {{ Str::limit($expense->description, 20) }}
                                    </td>
                                    <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if($expense->user)
                                            <span class="badge badge-primary">{{ $expense->user->name }}</span>
                                        @else
                                            <span class="badge badge-secondary">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data pengeluaran terbaru</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Financial Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Laporan Keuangan Custom</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('owner.financial.report') }}" method="GET" target="_blank">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download fa-sm text-white-50"></i> Download Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<!-- Window: Download Laporan (Baru) -->
<div class="modal fade" id="window_dashboard_donwload_laporan" tabindex="-1" role="dialog" aria-labelledby="windowDashboardDownloadLaporanLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="windowDashboardDownloadLaporanLabel">Laporan Custom</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('owner.financial.custom.report') }}" method="GET" target="_blank" id="form-download-laporan-custom">
                <div class="modal-body">
                    <!-- Jenis Transaksi -->
                    <div class="form-group">
                        <label for="transaction_type">Jenis Transaksi</label>
                        <select class="form-control" id="transaction_type" name="transaction_type">
                            <option value="expense" selected>Pengeluaran</option>
                            <option value="income">Pemasukan</option>
                            <option value="both">Keduanya</option>
                        </select>
                    </div>

                    <!-- Rentang Waktu -->
                    <div class="form-group">
                        <label>Rentang Waktu</label>
                        <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                            <label class="btn btn-outline-primary active flex-fill">
                                <input type="radio" name="range_type" value="daily" autocomplete="off" checked> Harian
                            </label>
                            <label class="btn btn-outline-primary flex-fill">
                                <input type="radio" name="range_type" value="weekly" autocomplete="off"> Mingguan
                            </label>
                            <label class="btn btn-outline-primary flex-fill">
                                <input type="radio" name="range_type" value="monthly" autocomplete="off"> Bulanan
                            </label>
                            <label class="btn btn-outline-primary flex-fill">
                                <input type="radio" name="range_type" value="yearly" autocomplete="off"> Tahunan
                            </label>
                        </div>
                    </div>

                    <!-- Input Dinamis untuk Rentang Waktu -->
                    <div id="range-inputs">
                        <div class="form-group" data-range="daily">
                            <label for="daily_date">Tanggal</label>
                            <input type="date" class="form-control" id="daily_date">
                        </div>
                        <div class="form-group d-none" data-range="weekly">
                            <label for="weekly_week">Minggu</label>
                            <input type="week" class="form-control" id="weekly_week">
                        </div>
                        <div class="form-group d-none" data-range="monthly">
                            <label for="monthly_month">Bulan</label>
                            <input type="month" class="form-control" id="monthly_month">
                        </div>
                        <div class="form-group d-none" data-range="yearly">
                            <label for="yearly_year">Tahun</label>
                            <input type="number" class="form-control" id="yearly_year" min="2000" max="2099" step="1" placeholder="{{ date('Y') }}">
                        </div>
                    </div>

                    <!-- Format Unduhan -->
                    <div class="form-group">
                        <label>Format Unduhan</label>
                        <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                            <label class="btn btn-outline-secondary active flex-fill">
                                <input type="radio" name="format" value="pdf" autocomplete="off" checked> PDF
                            </label>
                            <label class="btn btn-outline-secondary flex-fill">
                                <input type="radio" name="format" value="xlsx" autocomplete="off"> Excel
                            </label>
                            <label class="btn btn-outline-secondary flex-fill">
                                <input type="radio" name="format" value="csv" autocomplete="off"> CSV
                            </label>
                        </div>
                    </div>

                    <!-- Hidden start/end date untuk kompatibilitas route lama -->
                    <input type="hidden" name="start_date" id="custom_start_date">
                    <input type="hidden" name="end_date" id="custom_end_date">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-download-laporan-custom">
                        <i class="fas fa-download fa-sm text-white-50"></i> Download Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/vendor/chart.js/Chart.min.js') }}"></script>
<script>
// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Area Chart
function number_format(number, decimals, dec_point, thousands_sep) {
  number = (number + '').replace(',', '').replace(' ', '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? '.' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? ',' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}

// Area Chart Example
var ctx = document.getElementById("myAreaChart");
var myLineChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: {!! json_encode($monthLabels) !!},
    datasets: [{
      label: "Pemasukkan",
      lineTension: 0.3,
      backgroundColor: "rgba(78, 115, 223, 0.05)",
      borderColor: "rgba(78, 115, 223, 1)",
      pointRadius: 3,
      pointBackgroundColor: "rgba(78, 115, 223, 1)",
      pointBorderColor: "rgba(78, 115, 223, 1)",
      pointHoverRadius: 3,
      pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
      pointHoverBorderColor: "rgba(78, 115, 223, 1)",
      pointHitRadius: 10,
      pointBorderWidth: 2,
      data: {!! json_encode($monthlyIncomes) !!},
    },
    {
      label: "Pengeluaran",
      lineTension: 0.3,
      backgroundColor: "rgba(231, 74, 59, 0.05)",
      borderColor: "rgba(231, 74, 59, 1)",
      pointRadius: 3,
      pointBackgroundColor: "rgba(231, 74, 59, 1)",
      pointBorderColor: "rgba(231, 74, 59, 1)",
      pointHoverRadius: 3,
      pointHoverBackgroundColor: "rgba(231, 74, 59, 1)",
      pointHoverBorderColor: "rgba(231, 74, 59, 1)",
      pointHitRadius: 10,
      pointBorderWidth: 2,
      data: {!! json_encode($monthlyExpenses) !!},
    }],
  },
  options: {
    maintainAspectRatio: false,
    layout: {
      padding: {
        left: 10,
        right: 25,
        top: 25,
        bottom: 0
      }
    },
    scales: {
      xAxes: [{
        time: {
          unit: 'date'
        },
        gridLines: {
          display: false,
          drawBorder: false
        },
        ticks: {
          maxTicksLimit: 12
        }
      }],
      yAxes: [{
        ticks: {
          maxTicksLimit: 5,
          padding: 10,
          callback: function(value, index, values) {
            return 'Rp ' + number_format(value);
          }
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: false,
          borderDash: [2],
          zeroLineBorderDash: [2]
        }
      }],
    },
    legend: {
      display: true
    },
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      titleFontSize: 14,
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      intersect: false,
      mode: 'index',
      caretPadding: 10,
      callbacks: {
        label: function(tooltipItem, chart) {
          var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
          return datasetLabel + ': Rp ' + number_format(tooltipItem.yLabel);
        }
      }
    }
  }
});

// Pie Chart
var ctx = document.getElementById("myPieChart");
var myPieChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: {!! json_encode($incomeCategories) !!},
    datasets: [{
      data: {!! json_encode($incomeCategoryValues) !!},
      backgroundColor: {!! json_encode($pieChartColors) !!},
      hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f6c23e', '#e74a3b', '#36b9cc', '#1cc88a'],
      hoverBorderColor: "rgba(234, 236, 244, 1)",
    }],
  },
  options: {
    maintainAspectRatio: false,
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
      callbacks: {
        label: function(tooltipItem, data) {
          var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
          var percentage = Math.round((data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index] / 
            data.datasets[tooltipItem.datasetIndex].data.reduce((a, b) => a + b, 0)) * 100);
            
          return data.labels[tooltipItem.index] + ': Rp ' + number_format(data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index]) + 
            ' (' + percentage + '%)';
        }
      }
    },
    legend: {
      display: false
    },
    cutoutPercentage: 70,
  },
});

$(document).ready(function() {
    // Handle period change
    $('input[name="period"]').change(function() {
        $('#filter-form').submit();
    });
    
    // Handle date change
    $('#date').change(function() {
        $('#filter-form').submit();
    });

    // ===== Window: Download Laporan (Baru) =====
    // Toggle input sesuai rentang waktu
    $('input[name="range_type"]').on('change', function() {
        var val = $(this).val();
        $('#range-inputs [data-range]').addClass('d-none');
        $('#range-inputs [data-range="' + val + '"]').removeClass('d-none');
    });

    // Set nilai default untuk input tanggal
    var today = new Date();
    var yyyy = today.getFullYear();
    var mm = (today.getMonth() + 1).toString().padStart(2, '0');
    var dd = today.getDate().toString().padStart(2, '0');
    $('#daily_date').val(yyyy + '-' + mm + '-' + dd);
    $('#monthly_month').val(yyyy + '-' + mm);
    $('#yearly_year').val(yyyy);

    // Hitung start/end date sebelum submit
    $('#form-download-laporan-custom').on('submit', function(e) {
        // Hitung berdasarkan pilihan rentang
        var type = $('input[name="range_type"]:checked').val();
        var start, end;

        function format(d) {
            var y = d.getFullYear();
            var m = (d.getMonth() + 1).toString().padStart(2, '0');
            var day = d.getDate().toString().padStart(2, '0');
            return y + '-' + m + '-' + day;
        }

        if (type === 'daily') {
            var d = new Date($('#daily_date').val());
            start = end = format(d);
        } else if (type === 'weekly') {
            var w = $('#weekly_week').val(); // format: YYYY-Wnn
            if (!w) { // fallback ke minggu berjalan
                var cur = new Date();
                var day = cur.getDay();
                var diff = cur.getDate() - day + (day === 0 ? -6 : 1); // Senin
                var monday = new Date(cur.setDate(diff));
                var sunday = new Date(monday); sunday.setDate(monday.getDate() + 6);
                start = format(monday); end = format(sunday);
            } else {
                var parts = w.split('-W');
                var wy = parseInt(parts[0], 10); var wn = parseInt(parts[1], 10);
                // Dapatkan tanggal Senin pada minggu ke-wn
                var jan4 = new Date(wy, 0, 4);
                var dayOfWeek = jan4.getDay() || 7; // 1..7
                var monday = new Date(jan4);
                monday.setDate(jan4.getDate() - dayOfWeek + 1 + (wn - 1) * 7);
                var sunday = new Date(monday); sunday.setDate(monday.getDate() + 6);
                start = format(monday); end = format(sunday);
            }
        } else if (type === 'monthly') {
            var mval = $('#monthly_month').val() || (yyyy + '-' + mm);
            var arr = mval.split('-');
            var y = parseInt(arr[0], 10); var m0 = parseInt(arr[1], 10) - 1;
            var first = new Date(y, m0, 1);
            var last = new Date(y, m0 + 1, 0);
            start = format(first); end = format(last);
        } else { // yearly
            var yval = parseInt($('#yearly_year').val() || yyyy, 10);
            start = yval + '-01-01';
            end = yval + '-12-31';
        }

        $('#custom_start_date').val(start);
        $('#custom_end_date').val(end);
    });
});
</script>
@endsection