@extends('kasir.layouts.app')

@section('title', 'Edit Pengeluaran')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Edit Pengeluaran</h1>
        <a href="{{ route('kasir.expend.index') }}" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Content -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Pengeluaran</h6>
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

            <form action="{{ route('kasir.expend.update', $expend->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $expend->date->format('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="session">Sesi <span class="text-danger">*</span></label>
                            <select class="form-control @error('session') is-invalid @enderror" id="session" name="session" required>
                                <option value="">-- Pilih Sesi --</option>
                                <option value="pagi" {{ old('session', $expend->session) == 'pagi' ? 'selected' : '' }}>Pagi</option>
                                <option value="sore" {{ old('session', $expend->session) == 'sore' ? 'selected' : '' }}>Sore</option>
                            </select>
                            @error('session')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Jenis Pengeluaran <span class="text-danger">*</span></label>
                    <div class="mb-2 custom-control custom-radio">
                        <input type="radio" id="type_daily" name="type" value="harian" class="custom-control-input" {{ old('type', $expend->type) == 'harian' ? 'checked' : '' }} required>
                        <label class="custom-control-label" for="type_daily">Harian</label>
                    </div>
                    <div class="mb-3 custom-control custom-radio">
                        <input type="radio" id="type_monthly" name="type" value="bulanan" class="custom-control-input" {{ old('type', $expend->type) == 'bulanan' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="type_monthly">Bulanan</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Deskripsi Pengeluaran <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description', $expend->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="amount">Jumlah (Rp) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="text" class="form-control rupiah-input @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ number_format((int)old('amount', $expend->amount),0,',','.') }}" required placeholder="0" inputmode="numeric">
                    </div>
                    @error('amount')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="receipt_image">Bukti Pengeluaran</label>
                    @if($expend->receipt_image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/receipts/' . $expend->receipt_image) }}" alt="Receipt" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                    @endif
                    <input type="file" class="form-control-file @error('receipt_image') is-invalid @enderror" id="receipt_image" name="receipt_image">
                    <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maksimal 2MB. Biarkan kosong jika tidak ingin mengubah gambar.</small>
                    @error('receipt_image')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
            </form>
        </div>
    </div>
</div>
@endsection