@extends(auth()->check() && auth()->user()->role === 'admin' ? 'admin.layouts.app' : 'owner.layouts.app')

@section('title','Invoice '.$invoice->number)

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Invoice {{ $invoice->number }}</h1>
        <div>
            <a href="{{ route('owner.invoice.print', $invoice->id) }}" class="btn btn-sm btn-primary" target="_blank"><i class="fas fa-print"></i> Cetak</a>
            @if(auth()->user()->role==='kasir')
            <a href="{{ route('kasir.invoice.createIncome') }}" class="btn btn-sm btn-secondary">Transaksi Baru</a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @include('owner.invoice.template', ['invoice' => $invoice])
</div>
@endsection