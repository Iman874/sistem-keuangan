@extends('kasir.layouts.app')

@section('title', 'Data Pengeluaran')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Data Pengeluaran</h1>
        <a href="{{ route('kasir.expend.create') }}" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-primary">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Pengeluaran
        </a>
    </div>

    <!-- Content -->
    <div class="row">
        <!-- Daily Expenses Tab -->
        <div class="mb-4 col-12">
            <ul class="nav nav-tabs" id="expenseTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="daily-tab" data-toggle="tab" href="#daily" role="tab" 
                       aria-controls="daily" aria-selected="true">Pengeluaran Harian</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="monthly-tab" data-toggle="tab" href="#monthly" role="tab" 
                       aria-controls="monthly" aria-selected="false">Pengeluaran Bulanan</a>
                </li>
            </ul>
            
            <div class="tab-content" id="expenseTabContent">
                <!-- Daily Expenses -->
                <div class="tab-pane fade show active" id="daily" role="tabpanel" aria-labelledby="daily-tab">
                    <div class="mb-4 shadow card">
                        <div class="py-3 card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Pengeluaran Harian</h6>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            
                            @forelse($dailyExpenses as $date => $expenses)
                                <div class="mb-4 card">
                                    <div class="text-white card-header bg-primary">
                                        <h6 class="m-0 font-weight-bold">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</h6>
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
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered" width="100%" cellspacing="0">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Deskripsi</th>
                                                                        <th>Waktu</th>
                                                                        <th>Jumlah</th>
                                                                        <th>Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php $morningTotal = 0; @endphp
                                                                    @forelse($expenses->where('session', 'pagi') as $expense)
                                                                        @php $morningTotal += $expense->amount; @endphp
                                                                        <tr>
                                                                            <td>{{ $expense->description }}</td>
                                                                            <td>@include('kasir.expend.expend-time')</td>
                                                                            <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                                                            <td>
                                                                                <a href="{{ route('kasir.expend.show', $expense->id) }}" class="btn btn-sm btn-info">
                                                                                    <i class="fas fa-eye"></i>
                                                                                </a>
                                                                                <a href="{{ route('kasir.expend.edit', $expense->id) }}" class="btn btn-sm btn-warning">
                                                                                    <i class="fas fa-edit"></i>
                                                                                </a>
                                                                                <form action="{{ route('kasir.expend.destroy', $expense->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                </form>
                                                                                @if($expense->invoice_id)
                                                                                    <a href="{{ route('kasir.invoice.show',$expense->invoice_id) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-file-invoice"></i></a>
                                                                                    <a href="{{ route('kasir.invoice.print',$expense->invoice_id) }}" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-print"></i></a>
                                                                                @else
                                                                                    <a href="{{ route('kasir.invoice.fromExpend',$expense->id) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-file-invoice"></i> Invoice</a>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="4" class="text-center">Tidak ada data</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr class="bg-light">
                                                                        <th>Total</th>
                                                                        <th></th>
                                                                        <th colspan="2">Rp {{ number_format($morningTotal, 0, ',', '.') }}</th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
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
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered" width="100%" cellspacing="0">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Deskripsi</th>
                                                                        <th>Waktu</th>
                                                                        <th>Jumlah</th>
                                                                        <th>Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php $afternoonTotal = 0; @endphp
                                                                    @forelse($expenses->where('session', 'sore') as $expense)
                                                                        @php $afternoonTotal += $expense->amount; @endphp
                                                                        <tr>
                                                                            <td>{{ $expense->description }}</td>
                                                                            <td>@include('kasir.expend.expend-time')</td>
                                                                            <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                                                            <td>
                                                                                <a href="{{ route('kasir.expend.show', $expense->id) }}" class="btn btn-sm btn-info">
                                                                                    <i class="fas fa-eye"></i>
                                                                                </a>
                                                                                <a href="{{ route('kasir.expend.edit', $expense->id) }}" class="btn btn-sm btn-warning">
                                                                                    <i class="fas fa-edit"></i>
                                                                                </a>
                                                                                <form action="{{ route('kasir.expend.destroy', $expense->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                </form>
                                                                                @if($expense->invoice_id)
                                                                                    <a href="{{ route('kasir.invoice.show',$expense->invoice_id) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-file-invoice"></i></a>
                                                                                    <a href="{{ route('kasir.invoice.print',$expense->invoice_id) }}" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-print"></i></a>
                                                                                @else
                                                                                    <a href="{{ route('kasir.invoice.fromExpend',$expense->id) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-file-invoice"></i> Invoice</a>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="4" class="text-center">Tidak ada data</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr class="bg-light">
                                                                        <th>Total</th>
                                                                        <th></th>
                                                                        <th colspan="2">Rp {{ number_format($afternoonTotal, 0, ',', '.') }}</th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Daily Total -->
                                        <div class="text-white card bg-danger">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-8">
                                                        <h4 class="mb-0 font-weight-bold">Total Pengeluaran Tanggal {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</h4>
                                                    </div>
                                                    <div class="text-right col-md-4">
                                                        <h4 class="mb-0 font-weight-bold">
                                                            Rp {{ number_format($expenses->sum('amount'), 0, ',', '.') }}
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info">
                                    Belum ada data pengeluaran harian. Silahkan tambahkan data pengeluaran baru.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                <!-- Monthly Expenses -->
                <div class="tab-pane fade" id="monthly" role="tabpanel" aria-labelledby="monthly-tab">
                    <div class="mb-4 shadow card">
                        <div class="py-3 card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Pengeluaran Bulanan</h6>
                        </div>
                        <div class="card-body">
                            @forelse($monthlyExpenses as $month => $expenses)
                                <div class="mb-4 card">
                                    <div class="text-white card-header bg-primary">
                                        <h6 class="m-0 font-weight-bold">{{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}</h6>
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
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered" width="100%" cellspacing="0">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Tanggal</th>
                                                                        <th>Waktu</th>
                                                                        <th>Deskripsi</th>
                                                                        <th>Jumlah</th>
                                                                        <th>Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php $morningTotal = 0; @endphp
                                                                    @forelse($expenses->where('session', 'pagi') as $expense)
                                                                        @php $morningTotal += $expense->amount; @endphp
                                                                        <tr>
                                                                            <td>{{ $expense->date->format('d/m/Y') }}</td>
                                                                            <td>@include('kasir.expend.expend-time')</td>
                                                                            <td>{{ $expense->description }}</td>
                                                                            <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                                                            <td>
                                                                                <a href="{{ route('kasir.expend.show', $expense->id) }}" class="btn btn-sm btn-info">
                                                                                    <i class="fas fa-eye"></i>
                                                                                </a>
                                                                                <a href="{{ route('kasir.expend.edit', $expense->id) }}" class="btn btn-sm btn-warning">
                                                                                    <i class="fas fa-edit"></i>
                                                                                </a>
                                                                                <form action="{{ route('kasir.expend.destroy', $expense->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                </form>
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="5" class="text-center">Tidak ada data</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr class="bg-light">
                                                                        <th colspan="3">Total</th>
                                                                        <th colspan="2">Rp {{ number_format($morningTotal, 0, ',', '.') }}</th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
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
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered" width="100%" cellspacing="0">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Tanggal</th>
                                                                        <th>Waktu</th>
                                                                        <th>Deskripsi</th>
                                                                        <th>Jumlah</th>
                                                                        <th>Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php $afternoonTotal = 0; @endphp
                                                                    @forelse($expenses->where('session', 'sore') as $expense)
                                                                        @php $afternoonTotal += $expense->amount; @endphp
                                                                        <tr>
                                                                            <td>{{ $expense->date->format('d/m/Y') }}</td>
                                                                            <td>@include('kasir.expend.expend-time')</td>
                                                                            <td>{{ $expense->description }}</td>
                                                                            <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                                                            <td>
                                                                                <a href="{{ route('kasir.expend.show', $expense->id) }}" class="btn btn-sm btn-info">
                                                                                    <i class="fas fa-eye"></i>
                                                                                </a>
                                                                                <a href="{{ route('kasir.expend.edit', $expense->id) }}" class="btn btn-sm btn-warning">
                                                                                    <i class="fas fa-edit"></i>
                                                                                </a>
                                                                                <form action="{{ route('kasir.expend.destroy', $expense->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                </form>
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="5" class="text-center">Tidak ada data</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr class="bg-light">
                                                                        <th colspan="3">Total</th>
                                                                        <th colspan="2">Rp {{ number_format($afternoonTotal, 0, ',', '.') }}</th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Monthly Total -->
                                        <div class="text-white card bg-danger">
                                            <div class="card-body">
                                                <div class="row align-items-center">
                                                    <div class="col-md-8">
                                                        <h4 class="mb-0 font-weight-bold">Total Pengeluaran Bulan {{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}</h4>
                                                    </div>
                                                    <div class="text-right col-md-4">
                                                        <h4 class="mb-0 font-weight-bold">
                                                            Rp {{ number_format($expenses->sum('amount'), 0, ',', '.') }}
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="alert alert-info">
                                    Belum ada data pengeluaran bulanan. Silahkan tambahkan data pengeluaran baru.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.table').DataTable({
            "paging": false,
            "searching": false,
            "info": false
        });
    });
</script>
@endsection