@extends('kasir.layouts.app')

@section('title', 'Detail Pengeluaran')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Detail Pengeluaran</h1>
        <a href="{{ route('kasir.expend.index') }}" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Content -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header">
            <h6 class="m-0 font-weight-bold text-primary">Detail Pengeluaran</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
                        <tr>
                            <th>Tanggal</th>
                            <td>{{ $expend->date->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Waktu</th>
                            <td>{{ $expend->time ? \Carbon\Carbon::parse($expend->time)->format('H:i') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Sesi</th>
                            <td>{{ $expend->session == 'pagi' ? 'Pagi' : 'Sore' }}</td>
                        </tr>
                        <tr>
                            <th>Jenis</th>
                            <td>{{ $expend->type == 'harian' ? 'Harian' : 'Bulanan' }}</td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $expend->description }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td>Rp {{ number_format($expend->amount, 0, ',', '.') }}</td>
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
                    @if($expend->receipt_image)
                        <div class="card">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold">Bukti Pengeluaran</h6>
                            </div>
                            <div class="text-center card-body">
                                <img src="{{ asset('storage/receipts/' . $expend->receipt_image) }}" alt="Bukti Pengeluaran" class="img-fluid">
                                <div class="mt-3">
                                    <a href="{{ asset('storage/receipts/' . $expend->receipt_image) }}" class="btn btn-primary btn-sm" target="_blank">
                                        <i class="fas fa-search-plus"></i> Lihat Gambar Lengkap
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Tidak ada bukti pengeluaran yang diunggah.
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="mt-4">
                <a href="{{ route('kasir.expend.edit', $expend->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit Pengeluaran
                </a>
                <form action="{{ route('kasir.expend.destroy', $expend->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus data ini?')">
                        <i class="fas fa-trash"></i> Hapus Pengeluaran
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection