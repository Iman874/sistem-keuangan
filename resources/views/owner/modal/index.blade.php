@extends(auth()->check() && auth()->user()->role === 'admin' ? 'admin.layouts.app' : 'owner.layouts.app')

@section('title', 'Data Modal')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Data Modal</h1>
        @if(auth()->user() && method_exists(auth()->user(),'hasPermission') && (auth()->user()->role==='owner' || auth()->user()->hasPermission('modal.create')))
        <a href="{{ route('owner.modal.create') }}" class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-primary">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Modal
        </a>
        @endif
    </div>

    <!-- DataTales Example -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Modal</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Harga (Rp)</th>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($modals as $modal)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $modal->nama_barang }}</td>
                            <td>{{ number_format($modal->harga, 0, ',', '.') }}</td>
                            <td>{{ $modal->tanggal ? date('d-m-Y', strtotime($modal->tanggal)) : '-' }}</td>
                            <td>{{ $modal->deskripsi ?? '-' }}</td>
                            <td>
                                @if(auth()->user() && method_exists(auth()->user(),'hasPermission') && (auth()->user()->role==='owner' || auth()->user()->hasPermission('modal.update')))
                                <a href="{{ route('owner.modal.edit', $modal->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @endif
                                @if(auth()->user() && method_exists(auth()->user(),'hasPermission') && (auth()->user()->role==='owner' || auth()->user()->hasPermission('modal.delete')))
                                <form action="{{ route('owner.modal.destroy', $modal->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>
@endsection