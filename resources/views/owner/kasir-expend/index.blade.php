@extends(auth()->check() && auth()->user()->role === 'admin' ? 'admin.layouts.app' : 'owner.layouts.app')

@section('title', 'Data Pengeluaran Kasir')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Data Pengeluaran Kasir</h1>
        <a href="{{ route('owner.kasir-expend.report') }}?{{ http_build_query(request()->all()) }}" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-primary">
            <i class="fas fa-download fa-sm text-white-50"></i> Generate Laporan
        </a>
    </div>

    <!-- Filter Card -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header">
            <h6 class="m-0 font-weight-bold text-primary">Filter Pengeluaran</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('owner.kasir-expend.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="start_date">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="end_date">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="session">Sesi</label>
                            <select class="form-control" id="session" name="session">
                                <option value="">Semua Sesi</option>
                                <option value="pagi" {{ request('session') == 'pagi' ? 'selected' : '' }}>Pagi</option>
                                <option value="sore" {{ request('session') == 'sore' ? 'selected' : '' }}>Sore</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="type">Jenis</label>
                            <select class="form-control" id="type" name="type">
                                <option value="">Semua Jenis</option>
                                <option value="harian" {{ request('type') == 'harian' ? 'selected' : '' }}>Harian</option>
                                <option value="bulanan" {{ request('type') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="user_id">Kasir</label>
                            <select class="form-control" id="user_id" name="user_id">
                                <option value="">Semua Kasir</option>
                                @foreach(\App\Models\User::where('role', 'kasir')->get() as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="category_id">Kategori</label>
                            <select class="form-control" id="category_id" name="category_id">
                                <option value="">Semua Kategori</option>
                                @foreach(($categories ?? []) as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('owner.kasir-expend.index') }}" class="btn btn-secondary">
                        <i class="fas fa-sync"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <!-- Total Pengeluaran Card -->
        <div class="mb-4 col-xl-4 col-md-6">
            <div class="py-2 shadow card border-left-primary h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-primary text-uppercase">
                                Total Pengeluaran</div>
                            <div class="mb-0 text-gray-800 h5 font-weight-bold">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-money-bill fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sesi Pagi Card -->
        <div class="mb-4 col-xl-4 col-md-6">
            <div class="py-2 shadow card border-left-warning h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-warning text-uppercase">
                                Pengeluaran Sesi Pagi</div>
                            <div class="mb-0 text-gray-800 h5 font-weight-bold">Rp {{ number_format($totalMorning, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-sun fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sesi Sore Card -->
        <div class="mb-4 col-xl-4 col-md-6">
            <div class="py-2 shadow card border-left-info h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-info text-uppercase">
                                Pengeluaran Sesi Sore</div>
                            <div class="mb-0 text-gray-800 h5 font-weight-bold">Rp {{ number_format($totalAfternoon, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-moon fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses Tab -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header">
            <ul class="nav nav-tabs card-header-tabs" id="expenseTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="daily-tab" data-toggle="tab" href="#daily" role="tab" aria-controls="daily" aria-selected="true">Pengeluaran Harian</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="monthly-tab" data-toggle="tab" href="#monthly" role="tab" aria-controls="monthly" aria-selected="false">Pengeluaran Bulanan</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="expenseTabContent">
                <!-- Daily Expenses -->
                <div class="tab-pane fade show active" id="daily" role="tabpanel" aria-labelledby="daily-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dailyTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Sesi</th>
                                    <th>Kasir</th>
                                    <th>Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @forelse($groupedExpenditures['daily'] ?? [] as $expense)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $expense->date->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge badge-{{ $expense->session == 'pagi' ? 'warning' : 'info' }}">
                                                {{ ucfirst($expense->session) }}
                                            </span>
                                        </td>
                                        <td>{{ $expense->user ? $expense->user->name : 'N/A' }}</td>
                                        <td>{{ $expense->category->name ?? '-' }}</td>
                                        <td>{{ Str::limit($expense->description, 50) }}</td>
                                        <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                        <td>
                                            <a href="{{ route('owner.kasir-expend.show', $expense->id) }}" class="btn btn-info btn-sm mb-1">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            @if($expense->invoice_id)
                                                @if(auth()->user() && auth()->user()->role==='kasir')
                                                    <a href="{{ route('kasir.invoice.show',$expense->invoice_id) }}" class="btn btn-secondary btn-sm" title="Lihat Invoice"><i class="fas fa-file-invoice"></i> Invoice</a>
                                                    <a href="{{ route('kasir.invoice.print',$expense->invoice_id) }}" target="_blank" class="btn btn-dark btn-sm" title="Cetak"><i class="fas fa-print"></i></a>
                                                @else
                                                    <a href="{{ route('owner.invoice.show',$expense->invoice_id) }}" class="btn btn-secondary btn-sm" title="Lihat Invoice"><i class="fas fa-file-invoice"></i> Invoice</a>
                                                    <a href="{{ route('owner.invoice.print',$expense->invoice_id) }}" target="_blank" class="btn btn-dark btn-sm" title="Cetak"><i class="fas fa-print"></i></a>
                                                @endif
                                            @elseif(auth()->user() && auth()->user()->role==='kasir')
                                                <a href="{{ route('kasir.invoice.fromExpend', $expense->id) }}" class="btn btn-outline-info btn-sm" title="Buat Invoice"><i class="fas fa-file-invoice"></i> Invoice</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data pengeluaran harian.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Monthly Expenses -->
                <div class="tab-pane fade" id="monthly" role="tabpanel" aria-labelledby="monthly-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="monthlyTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Sesi</th>
                                    <th>Kasir</th>
                                    <th>Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @forelse($groupedExpenditures['monthly'] ?? [] as $expense)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $expense->date->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge badge-{{ $expense->session == 'pagi' ? 'warning' : 'info' }}">
                                                {{ ucfirst($expense->session) }}
                                            </span>
                                        </td>
                                        <td>{{ $expense->user ? $expense->user->name : 'N/A' }}</td>
                                        <td>{{ $expense->category->name ?? '-' }}</td>
                                        <td>{{ Str::limit($expense->description, 50) }}</td>
                                        <td>Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                        <td>
                                            <a href="{{ route('owner.kasir-expend.show', $expense->id) }}" class="btn btn-info btn-sm mb-1">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            @if($expense->invoice_id)
                                                @if(auth()->user() && auth()->user()->role==='kasir')
                                                    <a href="{{ route('kasir.invoice.show',$expense->invoice_id) }}" class="btn btn-secondary btn-sm" title="Lihat Invoice"><i class="fas fa-file-invoice"></i> Invoice</a>
                                                    <a href="{{ route('kasir.invoice.print',$expense->invoice_id) }}" target="_blank" class="btn btn-dark btn-sm" title="Cetak"><i class="fas fa-print"></i></a>
                                                @else
                                                    <a href="{{ route('owner.invoice.show',$expense->invoice_id) }}" class="btn btn-secondary btn-sm" title="Lihat Invoice"><i class="fas fa-file-invoice"></i> Invoice</a>
                                                    <a href="{{ route('owner.invoice.print',$expense->invoice_id) }}" target="_blank" class="btn btn-dark btn-sm" title="Cetak"><i class="fas fa-print"></i></a>
                                                @endif
                                            @elseif(auth()->user() && auth()->user()->role==='kasir')
                                                <a href="{{ route('kasir.invoice.fromExpend', $expense->id) }}" class="btn btn-outline-info btn-sm" title="Buat Invoice"><i class="fas fa-file-invoice"></i> Invoice</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data pengeluaran bulanan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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
        $('#dailyTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
        
        $('#monthlyTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    });
</script>
@endsection