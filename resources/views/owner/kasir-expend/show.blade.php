@extends(auth()->check() && auth()->user()->role === 'admin' ? 'admin.layouts.app' : 'owner.layouts.app')

@section('title', 'Detail Pengeluaran Kasir')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Detail Pengeluaran Kasir</h1>
        <a href="{{ route('owner.kasir-expend.index') }}" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Detail Card -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Pengeluaran</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th style="width: 40%">Tanggal</th>
                            <td>{{ $expend->date->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Waktu</th>
                            <td>{{ $expend->time ? \Carbon\Carbon::parse($expend->time)->format('H:i') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Sesi</th>
                            <td>
                                <span class="badge badge-{{ $expend->session == 'pagi' ? 'warning' : 'info' }} px-3 py-2">
                                    {{ $expend->session == 'pagi' ? 'Pagi' : 'Sore' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Jenis</th>
                            <td>
                                <span class="badge badge-{{ $expend->type == 'harian' ? 'primary' : 'success' }} px-3 py-2">
                                    {{ $expend->type == 'harian' ? 'Harian' : 'Bulanan' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Kasir</th>
                            <td>{{ $expend->user ? $expend->user->name : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $expend->description }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td class="font-weight-bold">Rp {{ number_format($expend->amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat Pada</th>
                            <td>{{ $expend->created_at->format('d F Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Diperbarui Pada</th>
                            <td>{{ $expend->updated_at->format('d F Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="text-white card bg-dark border-0">
                        <div class="d-flex justify-content-between align-items-center card-header bg-secondary text-white border-0">
                            <h6 class="m-0 font-weight-bold">Ringkasan @if($expend->invoice_id)<span class="small font-weight-normal">(Invoice: {{ $expend->invoice->number ?? $expend->invoice_id }})</span>@endif</h6>
                            @if($expend->invoice_id)
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('owner.invoice.show',$expend->invoice_id) }}" class="btn btn-outline-light" title="Lihat Invoice"><i class="fas fa-file-invoice"></i></a>
                                    <a href="{{ route('owner.invoice.print',$expend->invoice_id) }}" target="_blank" class="btn btn-outline-light" title="Cetak"><i class="fas fa-print"></i></a>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><i class="fas fa-calendar-alt mr-2"></i> Tanggal:<br><strong>{{ $expend->date->format('d F Y') }}</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><i class="fas fa-clock mr-2"></i> Waktu:<br><strong>{{ $expend->time ? \Carbon\Carbon::parse($expend->time)->format('H:i') : '-' }}</strong></p>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <p class="mb-2"><i class="fas fa-sun mr-2"></i> Sesi:<br><strong>{{ $expend->session=='pagi'?'Pagi':'Sore' }}</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><i class="fas fa-tags mr-2"></i> Jenis:<br><strong>{{ $expend->type=='harian'?'Harian':'Bulanan' }}</strong></p>
                                </div>
                            </div>
                            <hr class="border-light">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><i class="fas fa-user mr-2"></i> Kasir:<br><strong>{{ $expend->user? $expend->user->name : 'N/A' }}</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><i class="fas fa-money-bill-wave mr-2"></i> Jumlah:<br><strong>Rp {{ number_format($expend->amount,0,',','.') }}</strong></p>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <p class="mb-0"><i class="fas fa-align-left mr-2"></i> Deskripsi:<br><strong>{{ $expend->description ?: '-' }}</strong></p>
                                </div>
                            </div>
                            @if($expend->receipt_image)
                                <hr class="border-light">
                                <div class="text-center">
                                    <img src="{{ asset('storage/receipts/' . $expend->receipt_image) }}" alt="Bukti Pengeluaran" class="img-fluid rounded">
                                    <div class="mt-3">
                                        <a href="{{ asset('storage/receipts/' . $expend->receipt_image) }}" class="btn btn-primary btn-sm" target="_blank"><i class="fas fa-search-plus"></i> Lihat Lengkap</a>
                                        <a href="{{ asset('storage/receipts/' . $expend->receipt_image) }}" class="btn btn-success btn-sm" download><i class="fas fa-download"></i> Unduh</a>
                                    </div>
                                </div>
                            @else
                                <div class="mt-3 alert alert-info mb-0"><i class="fas fa-info-circle mr-2"></i> Tidak ada bukti pengeluaran diunggah.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection