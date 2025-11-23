@extends('kasir.layouts.app')

@section('title', 'Tambah Pengeluaran')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Tambah Pengeluaran</h1>
        <a href="{{ route('kasir.expend.index') }}" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Content -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Pengeluaran</h6>
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

            <form action="{{ route('kasir.expend.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
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
                                <option value="pagi" {{ old('session') == 'pagi' ? 'selected' : '' }}>Pagi</option>
                                <option value="sore" {{ old('session') == 'sore' ? 'selected' : '' }}>Sore</option>
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
                        <input type="radio" id="type_daily" name="type" value="harian" class="custom-control-input type-radio" {{ old('type', 'harian') == 'harian' ? 'checked' : '' }} required>
                        <label class="custom-control-label" for="type_daily">Harian</label>
                    </div>
                    <div class="mb-3 custom-control custom-radio">
                        <input type="radio" id="type_monthly" name="type" value="bulanan" class="custom-control-input type-radio" {{ old('type') == 'bulanan' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="type_monthly">Bulanan</label>
                    </div>
                </div>
                
                <!-- Add a hidden input to store the actual category_id that will be submitted -->
                <input type="hidden" name="category_id" id="selected_category_id">
                
                <div class="form-group" id="daily-categories" style="{{ old('type', 'harian') == 'harian' ? '' : 'display: none;' }}">
                    <label for="category_id_daily">Kategori Pengeluaran Harian <span class="text-danger">*</span></label>
                    <div class="d-flex">
                        <select class="form-control category-select @error('category_id') is-invalid @enderror" id="category_id_daily" data-type="harian" {{ old('type', 'harian') == 'harian' ? 'required' : '' }}>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($dailyCategories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('kasir.expense-categories.create') }}?type=harian" class="btn btn-sm btn-primary ml-2" title="Tambah Kategori Baru">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                    @error('category_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group" id="monthly-categories" style="{{ old('type') == 'bulanan' ? '' : 'display: none;' }}">
                    <label for="category_id_monthly">Kategori Pengeluaran Bulanan <span class="text-danger">*</span></label>
                    <div class="d-flex">
                        <select class="form-control category-select @error('category_id') is-invalid @enderror" id="category_id_monthly" data-type="bulanan" {{ old('type') == 'bulanan' ? 'required' : '' }}>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($monthlyCategories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('kasir.expense-categories.create') }}?type=bulanan" class="btn btn-sm btn-primary ml-2" title="Tambah Kategori Baru">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                    @error('category_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="description">Deskripsi Pengeluaran <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
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
                        <input type="text" class="form-control rupiah-input @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') ? number_format((int)old('amount'),0,',','.') : '' }}" required placeholder="0" inputmode="numeric">
                    </div>
                    @error('amount')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="receipt_image">Bukti Pengeluaran (Opsional)</label>
                    <input type="file" class="form-control-file @error('receipt_image') is-invalid @enderror" id="receipt_image" name="receipt_image">
                    <small class="form-text text-muted">Format: JPG, JPEG, PNG. Maksimal 2MB.</small>
                    @error('receipt_image')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle showing/hiding categories based on expense type
        $('.type-radio').change(function() {
            const type = $('input[name="type"]:checked').val();
            
            if (type === 'harian') {
                $('#daily-categories').show();
                $('#monthly-categories').hide();
                $('#category_id_daily').prop('required', true);
                $('#category_id_monthly').prop('required', false);
                
                // Set the hidden category_id to the value of the daily category
                $('#selected_category_id').val($('#category_id_daily').val());
            } else {
                $('#daily-categories').hide();
                $('#monthly-categories').show();
                $('#category_id_daily').prop('required', false);
                $('#category_id_monthly').prop('required', true);
                
                // Set the hidden category_id to the value of the monthly category
                $('#selected_category_id').val($('#category_id_monthly').val());
            }
        });
        
        // When a category is selected, update the hidden field
        $('.category-select').change(function() {
            if ($(this).is(':visible')) {
                $('#selected_category_id').val($(this).val());
            }
        });
        
        // Trigger the change event initially to set up the form correctly
        $('.type-radio:checked').change();
        
        // On form submission, ensure the selected category is set
        $('form').submit(function() {
            const type = $('input[name="type"]:checked').val();
            
            if (type === 'harian') {
                $('#selected_category_id').val($('#category_id_daily').val());
            } else {
                $('#selected_category_id').val($('#category_id_monthly').val());
            }
            
            // Verify that a category is selected
            if (!$('#selected_category_id').val()) {
                alert('Silakan pilih kategori pengeluaran terlebih dahulu.');
                return false;
            }
            
            return true;
        });
    });
</script>
@endsection
@endsection