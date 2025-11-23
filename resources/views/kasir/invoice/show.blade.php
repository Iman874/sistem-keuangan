@extends('kasir.layouts.app')

@section('title','Invoice '.$invoice->number)

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Invoice {{ $invoice->number }}</h1>
        <div>
            <a href="{{ route('kasir.invoice.print', $invoice->id) }}" class="btn btn-sm btn-primary" target="_blank"><i class="fas fa-print"></i> Cetak</a>
            <a href="{{ route('kasir.invoice.createIncome') }}" class="btn btn-sm btn-secondary">Transaksi Baru</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @include('kasir.invoice.template', ['invoice' => $invoice])

    <div class="card mt-3">
        <div class="card-body">
            <form method="POST" action="{{ route('kasir.invoice.email', $invoice->id) }}" class="form-inline">
                @csrf
                <label class="mr-2">Kirim ke Email:</label>
                <input type="email" name="email" value="{{ $invoice->customer_email }}" class="form-control mr-2" placeholder="email@pelanggan.com" required/>
                <button class="btn btn-success">Kirim</button>
            </form>
        </div>
    </div>
</div>
@endsection
