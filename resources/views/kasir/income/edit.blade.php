@extends('kasir.layouts.app')

@section('title', 'Edit Pemasukkan')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Edit Pemasukkan</h1>
        <a href="{{ route('kasir.income.index') }}" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Content -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Pemasukkan</h6>
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

            <form action="{{ route('kasir.income.update', $income->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', $income->date->format('Y-m-d')) }}" required>
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
                                <option value="pagi" {{ old('session', $income->session) == 'pagi' ? 'selected' : '' }}>Pagi</option>
                                <option value="sore" {{ old('session', $income->session) == 'sore' ? 'selected' : '' }}>Sore</option>
                            </select>
                            @error('session')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Info waktu otomatis -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Waktu input awal: {{ $income->time ? $income->time->format('H:i') : 'Tidak ada' }}
                    <br>
                    <small>Catatan: Waktu input tidak dapat diubah untuk menjaga integritas data.</small>
                </div>
                
                <div class="mb-3">
                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center">
                            <label for="pemasukkan_id" class="mb-0">Kategori Pemasukkan <span class="text-danger">*</span></label>
                            <div class="form-inline">
                                <label class="mr-2">Jenis:</label>
                                <select id="filter_type" class="form-control form-control-sm">
                                    <option value="all">Semua</option>
                                    <option value="indoor">Indoor</option>
                                    <option value="outdoor">Outdoor</option>
                                </select>
                            </div>
                        </div>
                        <select class="form-control @error('pemasukkan_id') is-invalid @enderror mt-2" id="pemasukkan_id" name="pemasukkan_id" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" data-type="{{ $category->type ?? 'indoor' }}" {{ old('pemasukkan_id', $income->pemasukkan_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->nama_pemasukkan }} ({{ strtoupper($category->type ?? 'indoor') }})
                                </option>
                            @endforeach
                        </select>
                        @error('pemasukkan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-group">
                        <label for="description">Deskripsi Tambahan</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2" placeholder="Contoh: 2 gantungan kunci, tambahan stiker, dll">{{ old('description', $income->description) }}</textarea>
                        <small class="form-text text-muted">Tambahkan detail atau keterangan tambahan jika diperlukan.</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Data Pelanggan (Opsional) -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_name">Nama Pelanggan (Opsional)</label>
                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="customer_name" name="customer_name" value="{{ old('customer_name', $income->customer_name) }}" maxlength="100" placeholder="Nama pelanggan">
                            @error('customer_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_email">Email Pelanggan (Opsional)</label>
                            <input type="email" class="form-control @error('customer_email') is-invalid @enderror" id="customer_email" name="customer_email" value="{{ old('customer_email', $income->customer_email) }}" maxlength="150" placeholder="email@contoh.com">
                            @error('customer_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- tipe pembayaran di sini -->
                <div class="form-group">
                    <label>Tipe Pembayaran <span class="text-danger">*</span></label>
                    <div class="mb-2 custom-control custom-radio">
                        <input type="radio" id="payment_cash" name="payment_type" value="cash" class="custom-control-input" {{ old('payment_type', $income->payment_type ?? 'cash') == 'cash' ? 'checked' : '' }} required>
                        <label class="custom-control-label" for="payment_cash">Cash</label>
                    </div>
                    <div class="mb-3 custom-control custom-radio">
                        <input type="radio" id="payment_qris" name="payment_type" value="qris" class="custom-control-input" {{ old('payment_type', $income->payment_type ?? '') == 'qris' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="payment_qris">QRIS</label>
                    </div>
                    @error('payment_type')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <!-- End of tipe pembayaran -->
                
                <div class="form-group">
                    <label for="amount">Jumlah (Rp) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="text" class="form-control rupiah-input @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ number_format((int)old('amount', $income->amount),0,',','.') }}" required placeholder="0" inputmode="numeric">
                    </div>
                    @error('amount')
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

@section('scripts')
<script>
    (function(){
        const filter = document.getElementById('filter_type');
        const select = document.getElementById('pemasukkan_id');
        function applyFilter(){
            const val = filter.value;
            [...select.options].forEach(opt => {
                if(opt.value === '') return;
                const type = opt.getAttribute('data-type') || 'indoor';
                opt.hidden = !(val === 'all' || type === val);
            });
            const sel = select.options[select.selectedIndex];
            if(sel && sel.hidden){ select.selectedIndex = 0; }
        }
        filter.addEventListener('change', applyFilter);
        applyFilter();

        // Pastikan amount dikirim tanpa titik ribuan
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e){
            const amountInput = document.getElementById('amount');
            if(amountInput){
                // Hilangkan hanya titik ribuan, abaikan koma/desimal
                let val = amountInput.value.replace(/\./g, '');
                // Jika ada koma/desimal, ambil hanya bagian sebelum koma
                val = val.split(',')[0];
                amountInput.value = val;
            }
        });
    })();
</script>
@endsection