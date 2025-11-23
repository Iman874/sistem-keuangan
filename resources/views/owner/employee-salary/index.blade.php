@extends('owner.layouts.app')

@section('title','Gaji Karyawan')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gaji Karyawan</h1>
        <div class="btn-group">
            @if(auth()->user()->role==='owner' || (method_exists(auth()->user(),'hasPermission') && auth()->user()->hasPermission('salary.create')))
                <a href="{{ route('owner.employee-salary.createPayment') }}" class="btn btn-sm btn-success"><i class="fas fa-money-bill"></i> Bayar Gaji</a>
                <a href="{{ route('owner.employee-salary.createEmployee') }}" class="btn btn-sm btn-primary"><i class="fas fa-user-plus"></i> Tambah Karyawan</a>
            @endif
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form method="GET" class="form-inline">
                <label class="mr-2">Bulan</label>
                <select name="month" class="form-control mr-2">
                    @for($m=1;$m<=12;$m++)
                        <option value="{{ $m }}" {{ $m==$month?'selected':'' }}>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endfor
                </select>
                <label class="mr-2">Tahun</label>
                <input type="number" name="year" class="form-control mr-2" value="{{ $year }}" style="width:120px"/>
                <button class="btn btn-primary">Filter</button>
            </form>
        </div>
        <div class="card-body">
            <h5 class="mb-2">Pembayaran Bulan Ini</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Metode</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                            <th>Invoice</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $p)
                            <tr>
                                <td>{{ $p->paid_date->format('d/m/Y') }}</td>
                                <td>{{ $p->employee->name }}</td>
                                <td>{{ $p->employee->role }}</td>
                                <td>{{ strtoupper($p->method) }}</td>
                                <td>Rp {{ number_format($p->amount,0,',','.') }}</td>
                                <td>{{ $p->description }}</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('owner.employee-salary.invoice',$p->id) }}">Lihat</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">Belum ada pembayaran bulan ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Karyawan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>HP</th>
                            <th>Role</th>
                            <th>Gaji Pokok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $e)
                            <tr>
                                <td>{{ $e->name }}</td>
                                <td>{{ $e->email }}</td>
                                <td>{{ $e->phone }}</td>
                                <td>{{ $e->role }}</td>
                                <td>Rp {{ number_format($e->base_salary,0,',','.') }}</td>
                                <td>
                                    @php $isActiveNow = method_exists($e,'getIsCurrentlyActiveAttribute') ? $e->is_currently_active : (bool)$e->active; @endphp
                                    <span class="badge badge-{{ $isActiveNow?'success':'secondary' }}">{{ $isActiveNow?'Aktif':'Nonaktif' }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('owner.employee-salary.editEmployee',$e->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('owner.employee-salary.destroyEmployee',$e->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus karyawan?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">Belum ada data karyawan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
