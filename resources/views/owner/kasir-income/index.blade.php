@extends(auth()->check() && auth()->user()->role === 'admin' ? 'admin.layouts.app' : 'owner.layouts.app')

@section('title', 'Data Pemasukkan Kasir')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Data Pemasukkan Kasir</h1>
        <a href="{{ route('owner.kasir-income.report') }}?{{ http_build_query(request()->all()) }}" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-primary">
            <i class="fas fa-download fa-sm text-white-50"></i> Generate Laporan
        </a>
    </div>

    <!-- Filter Card -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header">
            <h6 class="m-0 font-weight-bold text-primary">Filter Pemasukkan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('owner.kasir-income.index') }}" method="GET">
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
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="category_id">Kategori</label>
                            <select class="form-control" id="category_id" name="category_id">
                                <option value="">Semua Kategori</option>
                                @foreach(($categories ?? []) as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_pemasukkan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('owner.kasir-income.index') }}" class="btn btn-secondary">
                        <i class="fas fa-sync"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <!-- Total Pemasukkan Card -->
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="py-2 shadow card border-left-success h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-success text-uppercase">
                                Total Pemasukkan</div>
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
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="py-2 shadow card border-left-warning h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-warning text-uppercase">
                                Pemasukkan Sesi Pagi</div>
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
        <div class="mb-4 col-xl-3 col-md-6">
            <div class="py-2 shadow card border-left-info h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-info text-uppercase">
                                Pemasukkan Sesi Sore</div>
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

    <!-- Income List -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pemasukkan Kasir</h6>
        </div>
        <div class="card-body">
            @forelse($groupedIncomes as $date => $incomes)
                <div class="mb-4 card">
                    <div class="text-white card-header bg-primary">
                        <h6 class="m-0 font-weight-bold">{{ Carbon\Carbon::parse($date)->format('d F Y') }}</h6>
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
                                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th>Kategori</th>
                                                        <th>Deskripsi</th>
                                                        <th>Kasir</th>
                                                        <th>Waktu</th>
                                                        <th>Tipe</th>
                                                        <th>Jumlah</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $morningTotal = 0; @endphp
                                                    @forelse($incomes->where('session', 'pagi') as $income)
                                                        @php $morningTotal += $income->amount; @endphp
                                                        <tr>
                                                            <td>{{ $income->category->nama_pemasukkan ?? 'Kategori Dihapus' }}</td>
                                                            <td>{{ $income->description ? Str::limit($income->description,80) : '-' }}</td>
                                                            <td>
                                                                <span class="badge badge-success">
                                                                    {{ $income->user ? $income->user->name : 'Tidak Diketahui' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                {{ $income->time ? $income->time->format('H:i') : '-' }}
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-{{ $income->payment_type == 'qris' ? 'primary' : 'secondary' }}">
                                                                    {{ strtoupper($income->payment_type ?? 'CASH') }}
                                                                </span>
                                                            </td>
                                                            <td>Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                                                            <td>
                                                                <a href="{{ route('owner.kasir-income.show', $income->id) }}" class="btn btn-info btn-sm mb-1">
                                                                    <i class="fas fa-eye"></i> Detail
                                                                </a>
                                                                @if($income->invoice_id)
                                                                    @if(auth()->user() && auth()->user()->role==='kasir')
                                                                        <a href="{{ route('kasir.invoice.show',$income->invoice_id) }}" class="btn btn-secondary btn-sm" title="Lihat Invoice"><i class="fas fa-file-invoice"></i> Invoice</a>
                                                                        <a href="{{ route('kasir.invoice.print',$income->invoice_id) }}" target="_blank" class="btn btn-dark btn-sm" title="Cetak"><i class="fas fa-print"></i></a>
                                                                    @else
                                                                        <a href="{{ route('owner.invoice.show',$income->invoice_id) }}" class="btn btn-secondary btn-sm" title="Lihat Invoice"><i class="fas fa-file-invoice"></i> Invoice</a>
                                                                        <a href="{{ route('owner.invoice.print',$income->invoice_id) }}" target="_blank" class="btn btn-dark btn-sm" title="Cetak"><i class="fas fa-print"></i></a>
                                                                    @endif
                                                                @elseif(auth()->user() && auth()->user()->role==='kasir')
                                                                    <a href="{{ route('kasir.invoice.fromIncome', $income->id) }}" class="btn btn-outline-info btn-sm" title="Buat Invoice"><i class="fas fa-file-invoice"></i> Invoice</a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center">Tidak ada data pemasukkan pagi.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                                <tfoot>
                                                    <tr class="bg-light">
                                                        <th>Total</th>
                                                        <th colspan="5">Rp {{ number_format($morningTotal, 0, ',', '.') }}</th>
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
                                            <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th>Kategori</th>
                                                        <th>Deskripsi</th>
                                                        <th>Kasir</th>
                                                        <th>Waktu</th>
                                                        <th>Tipe</th>
                                                        <th>Jumlah</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $afternoonTotal = 0; @endphp
                                                    @forelse($incomes->where('session', 'sore') as $income)
                                                        @php $afternoonTotal += $income->amount; @endphp
                                                        <tr>
                                                            <td>{{ $income->category->nama_pemasukkan ?? 'Kategori Dihapus' }}</td>
                                                            <td>{{ $income->description ? Str::limit($income->description,80) : '-' }}</td>
                                                            <td>
                                                                <span class="badge badge-success">
                                                                    {{ $income->user ? $income->user->name : 'Tidak Diketahui' }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                {{ $income->time ? $income->time->format('H:i') : '-' }}
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-{{ $income->payment_type == 'qris' ? 'primary' : 'secondary' }}">
                                                                    {{ strtoupper($income->payment_type ?? 'CASH') }}
                                                                </span>
                                                            </td>
                                                            <td>Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                                                            <td>
                                                                <a href="{{ route('owner.kasir-income.show', $income->id) }}" class="btn btn-info btn-sm mb-1">
                                                                    <i class="fas fa-eye"></i> Detail
                                                                </a>
                                                                @if($income->invoice_id)
                                                                    @if(auth()->user() && auth()->user()->role==='kasir')
                                                                        <a href="{{ route('kasir.invoice.show',$income->invoice_id) }}" class="btn btn-secondary btn-sm" title="Lihat Invoice"><i class="fas fa-file-invoice"></i> Invoice</a>
                                                                        <a href="{{ route('kasir.invoice.print',$income->invoice_id) }}" target="_blank" class="btn btn-dark btn-sm" title="Cetak"><i class="fas fa-print"></i></a>
                                                                    @else
                                                                        <a href="{{ route('owner.invoice.show',$income->invoice_id) }}" class="btn btn-secondary btn-sm" title="Lihat Invoice"><i class="fas fa-file-invoice"></i> Invoice</a>
                                                                        <a href="{{ route('owner.invoice.print',$income->invoice_id) }}" target="_blank" class="btn btn-dark btn-sm" title="Cetak"><i class="fas fa-print"></i></a>
                                                                    @endif
                                                                @elseif(auth()->user() && auth()->user()->role==='kasir')
                                                                    <a href="{{ route('kasir.invoice.fromIncome', $income->id) }}" class="btn btn-outline-info btn-sm" title="Buat Invoice"><i class="fas fa-file-invoice"></i> Invoice</a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center">Tidak ada data pemasukkan sore.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                                <tfoot>
                                                    <tr class="bg-light">
                                                        <th>Total</th>
                                                        <th colspan="5">Rp {{ number_format($afternoonTotal, 0, ',', '.') }}</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Daily Total -->
                        <div class="text-white card bg-success">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h4 class="mb-0 font-weight-bold">Total Pemasukkan Tanggal {{ Carbon\Carbon::parse($date)->format('d F Y') }}</h4>
                                    </div>
                                    <div class="text-right col-md-4">
                                        <h4 class="mb-0 font-weight-bold">
                                            Rp {{ number_format($incomes->sum('amount'), 0, ',', '.') }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    Belum ada data pemasukkan kasir. Silahkan tambahkan filter untuk menampilkan data.
                </div>
            @endforelse
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