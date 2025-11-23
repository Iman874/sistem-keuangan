@extends('kasir.layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Dashboard Kasir</h1>
    </div>

    <!-- Filter Card: Sesi Pagi/Sore/All -->
    <div class="row">
        <div class="mb-4 col-xl-4 col-md-6">
            <div class="py-2 shadow card border-left-info h-100">
                <div class="card-body">
                    <form method="GET" action="{{ route('kasir.dashboard') }}" class="form-inline">
                        <div class="mr-3">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Filter Sesi Hari Ini</div>
                            <div class="small text-muted">Default otomatis sesuai jam sekarang</div>
                        </div>
                        <div class="ml-auto">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-sm btn-outline-info {{ ($sessionFilter ?? 'pagi') === 'pagi' ? 'active' : '' }}">
                                    <input type="radio" name="session" value="pagi" autocomplete="off" {{ ($sessionFilter ?? 'pagi') === 'pagi' ? 'checked' : '' }}> Pagi
                                </label>
                                <label class="btn btn-sm btn-outline-info {{ ($sessionFilter ?? 'pagi') === 'sore' ? 'active' : '' }}">
                                    <input type="radio" name="session" value="sore" autocomplete="off" {{ ($sessionFilter ?? 'pagi') === 'sore' ? 'checked' : '' }}> Sore
                                </label>
                                <label class="btn btn-sm btn-outline-info {{ ($sessionFilter ?? 'pagi') === 'all' ? 'active' : '' }}">
                                    <input type="radio" name="session" value="all" autocomplete="off" {{ ($sessionFilter ?? 'pagi') === 'all' ? 'checked' : '' }}> All
                                </label>
                            </div>
                            <button type="submit" class="ml-2 btn btn-sm btn-primary"><i class="fas fa-filter"></i> Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Summary Cards (Row 1) -->
    <div class="row">
        <!-- Pemasukkan Hari Ini Card -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="py-2 shadow card border-left-primary h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-primary text-uppercase">
                                Pemasukkan Hari Ini</div>
                            <div class="mb-0 text-gray-800 h5 font-weight-bold">Rp {{ number_format($todayIncome, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-calendar-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pengeluaran Hari Ini Card -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="py-2 shadow card border-left-danger h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-danger text-uppercase">
                                Pengeluaran Hari Ini</div>
                            <div class="mb-0 text-gray-800 h5 font-weight-bold">Rp {{ number_format($todayExpense, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-calendar-minus fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Saldo Hari Ini Card -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="py-2 shadow card border-left-success h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-success text-uppercase">
                                Saldo Hari Ini</div>
                            <div class="mb-0 text-gray-800 h5 font-weight-bold">Rp {{ number_format($todayIncome - $todayExpense, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saldo Saat Ini Card -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="py-2 shadow card border-left-success h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-success text-uppercase">
                                Saldo Saat Ini</div>
                            <div class="mb-0 text-gray-800 h5 font-weight-bold">Rp {{ number_format($currentSaldo, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-wallet fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Row - Summary Cards (Row 2) -->
    <div class="row">
        <!-- Total Transaksi Hari Ini Card -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="py-2 shadow card border-left-info h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-info text-uppercase">
                                Total Transaksi Hari Ini</div>
                            <div class="mb-0 text-gray-800 h5 font-weight-bold">{{ $todayTransactions }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-clipboard-list fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('kasir.card_info.qris_today')
        @include('kasir.card_info.cash_today')
    </div>

    <!-- Content Row - Charts -->
    <div class="row">
        <!-- Area Chart - Weekly Finances -->
        <div class="col-xl-8 col-lg-7">
            <div class="mb-4 shadow card">
                <div class="flex-row py-3 card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Keuangan Minggu Ini</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="text-gray-400 fas fa-ellipsis-v fa-sm fa-fw"></i>
                        </a>
                        <div class="shadow dropdown-menu dropdown-menu-right animated--fade-in"
                             aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Opsi:</div>
                            <a class="dropdown-item" href="#">Minggu Ini</a>
                            <a class="dropdown-item" href="#">Bulan Ini</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">Tampilkan Semua</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="weeklyFinanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart - Income vs Expense -->
        <div class="col-xl-4 col-lg-5">
            <div class="mb-4 shadow card">
                <div class="flex-row py-3 card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Pemasukkan vs Pengeluaran</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="text-gray-400 fas fa-ellipsis-v fa-sm fa-fw"></i>
                        </a>
                        <div class="shadow dropdown-menu dropdown-menu-right animated--fade-in"
                             aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Periode:</div>
                            <a class="dropdown-item" href="#">Hari Ini</a>
                            <a class="dropdown-item" href="#">Minggu Ini</a>
                            <a class="dropdown-item" href="#">Bulan Ini</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="pt-4 pb-2 chart-pie">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Pemasukkan
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Pengeluaran
                        </span>
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
                <div class="py-3 card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Pemasukkan Hari Ini</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Sesi</th>
                                    <th>Tipe</th>
                                    <th>Kategori</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentIncomes as $income)
                                <tr>
                                    <td>{{ $income->date instanceof \Carbon\Carbon ? $income->date->format('d/m/Y') : \Carbon\Carbon::parse($income->date)->format('d/m/Y') }}</td>
                                    <td>{{ ucfirst($income->session) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $income->payment_type == 'qris' ? 'primary' : 'secondary' }}">
                                            {{ strtoupper($income->payment_type ?? 'CASH') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($income->other_source)
                                            <span class="badge badge-info">Lainnya</span>
                                            <small>{{ $income->description }}</small>
                                        @else
                                            {{ $income->category->nama_pemasukkan ?? 'Kategori Dihapus' }}
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data pemasukkan hari ini</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <a href="{{ route('kasir.income.index') }}" class="btn btn-primary btn-block">
                            Lihat Semua Pemasukkan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Expenses -->
        <div class="mb-4 col-lg-6">
            <div class="mb-4 shadow card">
                <div class="py-3 card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Pengeluaran Hari Ini</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Sesi</th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentExpenses as $expense)
                                <tr>
                                    <td>{{ $expense->date instanceof \Carbon\Carbon ? $expense->date->format('d/m/Y') : \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</td>
                                    <td>{{ ucfirst($expense->session) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $expense->type == 'harian' ? 'warning' : 'info' }}">
                                            {{ ucfirst($expense->type) }}
                                        </span>
                                        {{ Str::limit($expense->description, 30) }}
                                    </td>
                                    <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data pengeluaran hari ini</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <a href="{{ route('kasir.expend.index') }}" class="btn btn-primary btn-block">
                            Lihat Semua Pengeluaran
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row - Summary per Shift -->
    <div class="row">
        <div class="mb-4 col-lg-6">
            <div class="mb-4 shadow card">
                <div class="py-3 card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Ringkasan Sesi Pagi</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h4 class="small font-weight-bold">Pemasukkan <span class="float-right">Rp {{ number_format($morningIncome, 0, ',', '.') }}</span></h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h4 class="small font-weight-bold">Pengeluaran <span class="float-right">Rp {{ number_format($morningExpense, 0, ',', '.') }}</span></h4>
                            </div>
                        </div>
                    </div>
                    <h4 class="small font-weight-bold">Saldo <span class="float-right">Rp {{ number_format($morningIncome - $morningExpense, 0, ',', '.') }}</span></h4>
                    <div class="mb-4 progress">
                        @php
                            $morningPercentage = $morningIncome > 0 ? min(100, max(0, ($morningIncome - $morningExpense) / $morningIncome * 100)) : 0;
                        @endphp
                        <div class="progress-bar {{ $morningPercentage >= 50 ? 'bg-success' : 'bg-danger' }}" role="progressbar" style="width: {{ $morningPercentage }}%"
                             aria-valuenow="{{ $morningPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4 col-lg-6">
            <div class="mb-4 shadow card">
                <div class="py-3 card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Ringkasan Sesi Sore</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h4 class="small font-weight-bold">Pemasukkan <span class="float-right">Rp {{ number_format($afternoonIncome, 0, ',', '.') }}</span></h4>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h4 class="small font-weight-bold">Pengeluaran <span class="float-right">Rp {{ number_format($afternoonExpense, 0, ',', '.') }}</span></h4>
                            </div>
                        </div>
                    </div>
                    <h4 class="small font-weight-bold">Saldo <span class="float-right">Rp {{ number_format($afternoonIncome - $afternoonExpense, 0, ',', '.') }}</span></h4>
                    <div class="mb-4 progress">
                        @php
                            $afternoonPercentage = $afternoonIncome > 0 ? min(100, max(0, ($afternoonIncome - $afternoonExpense) / $afternoonIncome * 100)) : 0;
                        @endphp
                        <div class="progress-bar {{ $afternoonPercentage >= 50 ? 'bg-success' : 'bg-danger' }}" role="progressbar" style="width: {{ $afternoonPercentage }}%"
                             aria-valuenow="{{ $afternoonPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
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

// Weekly Finance Chart
var ctx = document.getElementById("weeklyFinanceChart");
var myLineChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: {!! json_encode($weekLabels) !!},
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
      data: {!! json_encode($weekIncomes) !!},
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
      data: {!! json_encode($weekExpenses) !!},
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
          maxTicksLimit: 7
        }
      }],
      yAxes: [{
        ticks: {
          maxTicksLimit: 5,
          padding: 10,
          // Include a thousand separator in the ticks
          callback: function(value, index, values) {
            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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
          return datasetLabel + ': Rp ' + tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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
    labels: ["Pemasukkan", "Pengeluaran"],
    datasets: [{
      data: [{{ $todayIncome }}, {{ $todayExpense }}],
      backgroundColor: ['#4e73df', '#e74a3b'],
      hoverBackgroundColor: ['#2e59d9', '#be2617'],
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
          return data.labels[tooltipItem.index] + ': Rp ' + data.datasets[0].data[tooltipItem.index].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
      }
    },
    legend: {
      display: false
    },
    cutoutPercentage: 80,
  },
});
</script>
@endsection