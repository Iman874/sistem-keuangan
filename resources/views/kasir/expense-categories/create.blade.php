@extends('kasir.layouts.app')

@section('title', 'Tambah Kategori Pengeluaran')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Tambah Kategori Pengeluaran</h1>
        <a href="{{ route('kasir.expend.create') }}" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Content -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Kategori</h6>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('kasir.expense-categories.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="name">Nama Kategori <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Jenis Kategori <span class="text-danger">*</span></label>
                    <div class="custom-control custom-radio">
                        <input type="radio" id="type_daily" name="type" value="harian" class="custom-control-input" {{ request('type') == 'harian' || old('type') == 'harian' ? 'checked' : '' }} required>
                        <label class="custom-control-label" for="type_daily">Harian</label>
                    </div>
                    <div class="custom-control custom-radio">
                        <input type="radio" id="type_monthly" name="type" value="bulanan" class="custom-control-input" {{ request('type') == 'bulanan' || old('type') == 'bulanan' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="type_monthly">Bulanan</label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Kategori
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
