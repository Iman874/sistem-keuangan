@extends('owner.layouts.app')

@section('title','Invoice Gaji')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Invoice Gaji</h1>
        <div>
            <a href="{{ route('owner.employee-salary.invoice.print',$payment->id) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-print"></i> Cetak</a>
            <a href="{{ route('owner.employee-salary.index', ['month'=>$payment->month,'year'=>$payment->year]) }}" class="btn btn-sm btn-secondary">Kembali</a>
        </div>
    </div>

    @include('owner.invoice-salary.template',['payment'=>$payment])
</div>
@endsection
