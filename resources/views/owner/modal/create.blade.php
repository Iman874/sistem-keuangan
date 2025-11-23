@extends(auth()->check() && auth()->user()->role === 'admin' ? 'admin.layouts.app' : 'owner.layouts.app')

@section('title', 'Tambah Modal')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Tambah Modal</h1>
        <a href="{{ route('owner.modal.index') }}" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Modal</h6>
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

            <form action="{{ route('owner.modal.store') }}" method="POST">
                @csrf
                
                <div id="modal-items-container">
                    <!-- Initial modal item form -->
                    <div class="p-3 mb-3 border rounded modal-item">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_barang_0">Nama Barang <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('modal_items.0.nama_barang') is-invalid @enderror" 
                                        id="nama_barang_0" name="modal_items[0][nama_barang]" value="{{ old('modal_items.0.nama_barang') }}" required>
                                    @error('modal_items.0.nama_barang')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="harga_0">Harga (Rp) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="text" class="form-control rupiah-input @error('modal_items.0.harga') is-invalid @enderror" 
                                            id="harga_0" name="modal_items[0][harga]" value="{{ old('modal_items.0.harga') ? number_format((int)old('modal_items.0.harga'),0,',','.') : '' }}" placeholder="0" inputmode="numeric" required>
                                    </div>
                                    @error('modal_items.0.harga')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_0">Tanggal (Opsional)</label>
                                    <input type="date" class="form-control @error('modal_items.0.tanggal') is-invalid @enderror" 
                                        id="tanggal_0" name="modal_items[0][tanggal]" value="{{ old('modal_items.0.tanggal') }}">
                                    @error('modal_items.0.tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="deskripsi_0">Deskripsi (Opsional)</label>
                                    <textarea class="form-control @error('modal_items.0.deskripsi') is-invalid @enderror" 
                                        id="deskripsi_0" name="modal_items[0][deskripsi]" rows="2">{{ old('modal_items.0.deskripsi') }}</textarea>
                                    @error('modal_items.0.deskripsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Additional modal items will be added here -->
                </div>
                
                <div class="mb-3 d-flex justify-content-between">
                    <button type="button" id="add-modal-item" class="btn btn-success">
                        <i class="fas fa-plus"></i> Tambah Modal Lain
                    </button>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Semua
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let modalItemCount = 1;
        
        // Add new modal item form
        $('#add-modal-item').click(function() {
            const newItem = `
                <div class="p-3 mb-3 border rounded modal-item">
                    <div class="mb-2 d-flex justify-content-between">
                        <h6 class="font-weight-bold">Modal Tambahan #${modalItemCount}</h6>
                        <button type="button" class="btn btn-sm btn-danger remove-modal-item">
                            <i class="fas fa-times"></i> Hapus
                        </button>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_barang_${modalItemCount}">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" 
                                    id="nama_barang_${modalItemCount}" name="modal_items[${modalItemCount}][nama_barang]" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="harga_${modalItemCount}">Harga (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </div>
                                    <input type="text" class="form-control rupiah-input" 
                                        id="harga_${modalItemCount}" name="modal_items[${modalItemCount}][harga]" placeholder="0" inputmode="numeric" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_${modalItemCount}">Tanggal (Opsional)</label>
                                <input type="date" class="form-control" 
                                    id="tanggal_${modalItemCount}" name="modal_items[${modalItemCount}][tanggal]">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="deskripsi_${modalItemCount}">Deskripsi (Opsional)</label>
                                <textarea class="form-control" 
                                    id="deskripsi_${modalItemCount}" name="modal_items[${modalItemCount}][deskripsi]" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('#modal-items-container').append(newItem);
            modalItemCount++;
        });
        
        // Remove modal item form
        $(document).on('click', '.remove-modal-item', function() {
            $(this).closest('.modal-item').remove();
        });
    });
</script>
@endsection