@extends('kasir.layouts.app')

@section('title', 'Data Pemasukkan')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Data Pemasukkan</h1>
        <div class="btn-group" role="group">
            <a href="{{ route('kasir.income.create') }}" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-primary">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Pemasukkan
            </a>
            <a href="{{ route('kasir.invoice.createIncome') }}" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-info">
                <i class="fas fa-file-invoice fa-sm text-white-50"></i> Tambah Multi Transaksi
            </a>
            <a href="{{ route('kasir.session-report.index') }}" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-success">
                <i class="fas fa-file-alt fa-sm text-white-50"></i> Laporan Sesi
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pemasukkan</h6>
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

            @forelse($groupedIncomes as $date => $incomes)
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
                                                        <th>Kategori</th>
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
                                                            <td>
                                                                {{ $income->category->nama_pemasukkan ?? 'Kategori Dihapus' }}
                                                                @if($income->description)
                                                                    <br><small>{{ $income->description }}</small>
                                                                @endif
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
                                                                <a href="{{ route('kasir.income.edit', $income->id) }}" class="btn btn-sm btn-warning">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form action="{{ route('kasir.income.destroy', $income->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                                @if($income->invoice_id)
                                                                    <a href="{{ route('kasir.invoice.show',$income->invoice_id) }}" class="btn btn-sm btn-info"><i class="fas fa-file-invoice"></i></a>
                                                                    <a href="{{ route('kasir.invoice.print',$income->invoice_id) }}" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-print"></i></a>
                                                                @else
                                                                    <a href="{{ route('kasir.invoice.fromIncome',$income->id) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-file-invoice"></i> Invoice</a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center">Tidak ada data</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                                <tfoot>
                                                    <tr class="bg-light">
                                                        <th>Total</th>
                                                        <th colspan="3">Rp {{ number_format($morningTotal, 0, ',', '.') }}</th>
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
                                                        <th>Kategori</th>
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
                                                            <td>
                                                                {{ $income->category->nama_pemasukkan ?? 'Kategori Dihapus' }}
                                                                @if($income->description)
                                                                    <br><small>{{ $income->description }}</small>
                                                                @endif
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
                                                                <a href="{{ route('kasir.income.edit', $income->id) }}" class="btn btn-sm btn-warning">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form action="{{ route('kasir.income.destroy', $income->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                                @if($income->invoice_id)
                                                                    <a href="{{ route('kasir.invoice.show',$income->invoice_id) }}" class="btn btn-sm btn-info"><i class="fas fa-file-invoice"></i></a>
                                                                    <a href="{{ route('kasir.invoice.print',$income->invoice_id) }}" target="_blank" class="btn btn-sm btn-secondary"><i class="fas fa-print"></i></a>
                                                                @else
                                                                    <a href="{{ route('kasir.invoice.fromIncome',$income->id) }}" class="btn btn-sm btn-outline-info"><i class="fas fa-file-invoice"></i> Invoice</a>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center">Tidak ada data</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                                <tfoot>
                                                    <tr class="bg-light">
                                                        <th>Total</th>
                                                        <th colspan="3">Rp {{ number_format($afternoonTotal, 0, ',', '.') }}</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Daily Total (Dipindahkan ke bawah) -->

                        <!-- Multi Transaksi (Grouped as Invoice) -->
                        <div class="mt-4 card border-left-primary">
                            <div class="text-white card-header bg-primary">
                                <h6 class="m-0 font-weight-bold">Multi Transaksi</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Sesi Pagi -->
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
                                                                <th>Invoice</th>
                                                                <th>Waktu</th>
                                                                <th>Tipe</th>
                                                                <th>Total</th>
                                                                <th>Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $multiMorningTotal = 0; @endphp
                                                            @forelse(($multiInvoices ?? collect())->filter(function($inv){
                                                                // sesi dari item pertama
                                                                $s = optional($inv->incomes->first())->session;
                                                                return $s === 'pagi';
                                                            }) as $inv)
                                                                @php $multiMorningTotal += (int) $inv->total; @endphp
                                                                <tr>
                                                                    <td>{{ $inv->number }}</td>
                                                                    <td>{{ $inv->time ? $inv->time->format('H:i') : '-' }}</td>
                                                                    <td><span class="badge badge-{{ $inv->payment_type === 'qris' ? 'primary' : 'secondary' }}">{{ strtoupper($inv->payment_type) }}</span></td>
                                                                    <td>Rp {{ number_format($inv->total, 0, ',', '.') }}</td>
                                                                    <td>
                                                                        <a href="{{ route('kasir.invoice.show', $inv->id) }}" class="btn btn-sm btn-info" title="Lihat"><i class="fas fa-file-invoice"></i></a>
                                                                        <a href="{{ route('kasir.invoice.print', $inv->id) }}" target="_blank" class="btn btn-sm btn-secondary" title="Cetak"><i class="fas fa-print"></i></a>
                                                                        <a href="{{ route('kasir.invoice.edit', $inv->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                                        <form action="{{ route('kasir.invoice.destroy', $inv->id) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin menghapus invoice ini? Semua item akan dihapus.')"><i class="fas fa-trash"></i></button>
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr><td colspan="5" class="text-center">Tidak ada data</td></tr>
                                                            @endforelse
                                                        </tbody>
                                                        <tfoot>
                                                            <tr class="bg-light">
                                                                <th>Total</th>
                                                                <th colspan="3">Rp {{ number_format($multiMorningTotal, 0, ',', '.') }}</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Sesi Sore -->
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
                                                                <th>Invoice</th>
                                                                <th>Waktu</th>
                                                                <th>Tipe</th>
                                                                <th>Total</th>
                                                                <th>Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @php $multiAfternoonTotal = 0; @endphp
                                                            @forelse(($multiInvoices ?? collect())->filter(function($inv){
                                                                $s = optional($inv->incomes->first())->session;
                                                                return $s === 'sore';
                                                            }) as $inv)
                                                                @php $multiAfternoonTotal += (int) $inv->total; @endphp
                                                                <tr>
                                                                    <td>{{ $inv->number }}</td>
                                                                    <td>{{ $inv->time ? $inv->time->format('H:i') : '-' }}</td>
                                                                    <td><span class="badge badge-{{ $inv->payment_type === 'qris' ? 'primary' : 'secondary' }}">{{ strtoupper($inv->payment_type) }}</span></td>
                                                                    <td>Rp {{ number_format($inv->total, 0, ',', '.') }}</td>
                                                                    <td>
                                                                        <a href="{{ route('kasir.invoice.show', $inv->id) }}" class="btn btn-sm btn-info" title="Lihat"><i class="fas fa-file-invoice"></i></a>
                                                                        <a href="{{ route('kasir.invoice.print', $inv->id) }}" target="_blank" class="btn btn-sm btn-secondary" title="Cetak"><i class="fas fa-print"></i></a>
                                                                        <a href="{{ route('kasir.invoice.edit', $inv->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                                                        <form action="{{ route('kasir.invoice.destroy', $inv->id) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin menghapus invoice ini? Semua item akan dihapus.')"><i class="fas fa-trash"></i></button>
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <tr><td colspan="5" class="text-center">Tidak ada data</td></tr>
                                                            @endforelse
                                                        </tbody>
                                                        <tfoot>
                                                            <tr class="bg-light">
                                                                <th>Total</th>
                                                                <th colspan="3">Rp {{ number_format($multiAfternoonTotal, 0, ',', '.') }}</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Totals Card (Baru) -->
                        <div class="mt-4 text-white card bg-success">
                            <div class="card-body">
                                <h5 class="font-weight-bold mb-2">Ringkasan Total Pendapatan {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="p-3 mb-2 bg-white text-dark rounded">
                                            <strong>Total Single Transaksi:</strong><br>
                                            Rp {{ number_format($singleTotalToday, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 mb-2 bg-white text-dark rounded">
                                            <strong>Total Multi Transaksi:</strong><br>
                                            Rp {{ number_format($multiTotalToday, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 mb-2 bg-white text-dark rounded">
                                            <strong>Total Semua Transaksi:</strong><br>
                                            Rp {{ number_format($overallTotalToday, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    Belum ada data pemasukkan. Silahkan tambahkan data pemasukkan baru.
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