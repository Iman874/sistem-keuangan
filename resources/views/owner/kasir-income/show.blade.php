@extends(auth()->check() && auth()->user()->role === 'admin' ? 'admin.layouts.app' : 'owner.layouts.app')

@section('title', 'Detail Pemasukkan Kasir')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Detail Pemasukkan Kasir</h1>
        <a href="{{ route('owner.kasir-income.index') }}" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Detail Card -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Pemasukkan</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th style="width: 40%">Tanggal</th>
                            <td>{{ $income->date->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Sesi</th>
                            <td>
                                <span class="badge badge-{{ $income->session == 'pagi' ? 'warning' : 'info' }} px-3 py-2">
                                    {{ $income->session == 'pagi' ? 'Pagi' : 'Sore' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Sumber</th>
                            <td>
                                <span class="px-3 py-2 badge badge-primary">Kategori</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Kategori</th>
                            <td>
                                {{ $income->category->nama_pemasukkan ?? 'Kategori Dihapus' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>
                                {{ $income->description ?: '-' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td class="font-weight-bold">Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat Pada</th>
                            <td>{{ $income->created_at->format('d F Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Diperbarui Pada</th>
                            <td>{{ $income->updated_at->format('d F Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm summary-card">
                        <div class="d-flex justify-content-between align-items-center card-header bg-dark text-white summary-card-header">
                            <h6 class="m-0 font-weight-bold">Ringkasan @if($income->invoice_id)<span class="small font-weight-normal">(Invoice: {{ $income->invoice->number ?? $income->invoice_id }})</span>@endif</h6>
                            @if($income->invoice_id)
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('owner.invoice.show',$income->invoice_id) }}" class="btn btn-outline-light" title="Lihat Invoice"><i class="fas fa-file-invoice"></i></a>
                                    <a href="{{ route('owner.invoice.print',$income->invoice_id) }}" target="_blank" class="btn btn-outline-light" title="Cetak"><i class="fas fa-print"></i></a>
                                </div>
                            @endif
                        </div>
                        <div class="card-body bg-white">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <i class="mr-2 fas fa-calendar-alt"></i> Tanggal: <br>
                                        <strong>{{ $income->date->format('d F Y') }}</strong>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <i class="fas {{ $income->session == 'pagi' ? 'fa-sun' : 'fa-moon' }} mr-2"></i> Sesi: <br>
                                        <strong>{{ $income->session == 'pagi' ? 'Pagi' : 'Sore' }}</strong>
                                    </p>
                                </div>
                            </div>
                            <div class="mt-3 row">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <i class="fas fa-tags mr-2"></i> Sumber: <br>
                                        <strong>Kategori</strong>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <i class="mr-2 fas fa-money-bill-wave"></i> Jumlah: <br>
                                        <strong>Rp {{ number_format($income->amount, 0, ',', '.') }}</strong>
                                    </p>
                                </div>
                            </div>
                            <hr class="border-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-0">
                                        <i class="mr-2 fas fa-info-circle"></i> Kategori: <br>
                                        <strong>{{ $income->category->nama_pemasukkan ?? 'Kategori Dihapus' }}</strong>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-0">
                                        <i class="mr-2 fas fa-align-left"></i> Deskripsi: <br>
                                        <strong>{{ $income->description ?: '-' }}</strong>
                                    </p>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .summary-card-header { border-bottom: 0; }
    .summary-card .card-body p strong { color:#212529; }
    .summary-card .card-body { color:#495057; }
</style>
@endpush