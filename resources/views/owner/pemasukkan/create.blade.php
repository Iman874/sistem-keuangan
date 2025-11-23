@extends(auth()->check() && auth()->user()->role === 'admin' ? 'admin.layouts.app' : 'owner.layouts.app')

@section('title', 'Tambah Pemasukkan')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Tambah Pemasukkan</h1>
        <a href="{{ route('owner.pemasukkan.index') }}" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Pemasukkan</h6>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('owner.pemasukkan.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="nama_pemasukkan">Nama Pemasukkan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_pemasukkan') is-invalid @enderror" 
                           id="nama_pemasukkan" name="nama_pemasukkan" value="{{ old('nama_pemasukkan') }}" 
                           placeholder="Contoh: Penjualan Makanan, Penjualan Minuman, dll" required>
                    @error('nama_pemasukkan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Nama pemasukkan akan digunakan oleh kasir untuk mencatat pemasukkan.</small>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection