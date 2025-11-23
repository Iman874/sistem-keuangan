@extends('admin.layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Dashboard Admin</h1>
        <div class="small text-muted">Tampilan menyesuaikan izin per fitur.</div>
    </div>

    <div class="row">
        @if(auth()->user()->hasPermission('income.approve'))
        <div class="mb-4 col-xl-4 col-md-6">
            <div class="py-2 shadow card border-left-secondary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="m-0 font-weight-bold text-secondary">Persetujuan Pemasukkan</h6>
                        <div>
                            <a href="{{ route('admin.income-approvals.index') }}" class="btn btn-sm btn-secondary">Lihat</a>
                        </div>
                    </div>
                    <p class="mb-2 small text-muted">Review & approve laporan sesi kasir.</p>
                    <a href="{{ route('admin.income-approvals.index') }}" class="btn btn-sm btn-outline-secondary">Pending <span class="badge badge-pill badge-secondary" id="pending-approvals-count">...</span></a>
                </div>
            </div>
        </div>
        @endif
        @if(auth()->user()->hasPermission('users.read'))
        <div class="mb-4 col-xl-4 col-md-6">
            <div class="py-2 shadow card border-left-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="m-0 font-weight-bold text-primary">User Management</h6>
                        <div>
                            @if(auth()->user()->hasPermission('users.create'))
                                <a href="{{ route('owner.users.create') }}" class="btn btn-sm btn-primary">Tambah</a>
                            @endif
                        </div>
                    </div>
                    <p class="mb-2 small text-muted">Kelola user sistem (lihat berdasarkan izin).</p>
                    <a href="{{ route('owner.users.index') }}" class="btn btn-sm btn-outline-primary">Lihat Data</a>
                </div>
            </div>
        </div>
        @endif

            @if(auth()->user()->hasPermission('pemasukkan.read'))
            <div class="mb-4 col-xl-4 col-md-6">
                <div class="py-2 shadow card border-left-info h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="m-0 font-weight-bold text-info">Pemasukkan (Owner)</h6>
                            <div>
                                @if(auth()->user()->hasPermission('pemasukkan.create'))
                                    <a href="{{ route('owner.pemasukkan.create') }}" class="btn btn-sm btn-info">Tambah</a>
                                @endif
                            </div>
                        </div>
                        <p class="mb-2 small text-muted">Kelola master pemasukkan (owner scope).</p>
                        <a href="{{ route('owner.pemasukkan.index') }}" class="btn btn-sm btn-outline-info">Lihat Data</a>
                    </div>
                </div>
            </div>
            @endif

            @if(auth()->user()->hasPermission('modal.read'))
            <div class="mb-4 col-xl-4 col-md-6">
                <div class="py-2 shadow card border-left-warning h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="m-0 font-weight-bold text-warning">Modal</h6>
                            <div>
                                @if(auth()->user()->hasPermission('modal.create'))
                                    <a href="{{ route('owner.modal.create') }}" class="btn btn-sm btn-warning">Tambah</a>
                                @endif
                            </div>
                        </div>
                        <p class="mb-2 small text-muted">Kelola data modal/inventaris.</p>
                        <a href="{{ route('owner.modal.index') }}" class="btn btn-sm btn-outline-warning">Lihat Data</a>
                    </div>
                </div>
            </div>
            @endif

        @if(auth()->user()->hasPermission('income.read'))
        <div class="mb-4 col-xl-4 col-md-6">
            <div class="py-2 shadow card border-left-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="m-0 font-weight-bold text-success">Pemasukkan (Kasir)</h6>
                        <div>
                            @if(auth()->user()->hasPermission('income.export'))
                                <a href="{{ route('owner.kasir-income.report') }}" class="btn btn-sm btn-success">Export</a>
                            @endif
                        </div>
                    </div>
                    <p class="mb-2 small text-muted">Pantau pemasukkan kasir.</p>
                    <a href="{{ route('owner.kasir-income.index') }}" class="btn btn-sm btn-outline-success">Lihat Data</a>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()->hasPermission('expense.read'))
        <div class="mb-4 col-xl-4 col-md-6">
            <div class="py-2 shadow card border-left-danger h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="m-0 font-weight-bold text-danger">Pengeluaran (Kasir)</h6>
                        <div>
                            @if(auth()->user()->hasPermission('expense.export'))
                                <a href="{{ route('owner.kasir-expend.report') }}" class="btn btn-sm btn-danger">Export</a>
                            @endif
                        </div>
                    </div>
                    <p class="mb-2 small text-muted">Pantau pengeluaran kasir.</p>
                    <a href="{{ route('owner.kasir-expend.index') }}" class="btn btn-sm btn-outline-danger">Lihat Data</a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="row">
        @if(auth()->user()->hasPermission('users.read'))
        <div class="col-lg-6">
            <div class="shadow card">
                <div class="flex-row py-3 card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Ringkas User</h6>
                    <div>
                        @if(auth()->user()->hasPermission('users.create'))
                            <a href="{{ route('owner.users.create') }}" class="btn btn-sm btn-primary">Tambah User</a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-0">Gunakan menu "Lihat Data" untuk aksi edit/hapus sesuai izin Anda.</p>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()->hasPermission('income.read') || auth()->user()->hasPermission('expense.read'))
        <div class="col-lg-6">
            <div class="shadow card">
                <div class="flex-row py-3 card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary">Laporan Keuangan</h6>
                    <div>
                        @if(auth()->user()->hasPermission('income.export') || auth()->user()->hasPermission('expense.export'))
                            <a href="{{ route('owner.financial.custom.report', ['transaction_type' => 'both', 'format' => 'pdf']) }}" class="btn btn-sm btn-secondary" target="_blank">Unduh PDF</a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-0">Akses laporan sesuai izin export.</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
