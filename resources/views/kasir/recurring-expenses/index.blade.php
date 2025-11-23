@extends('kasir.layouts.app')
@section('title','Pembayaran Rutin')
@section('content')
<div class="container-fluid">
  <h1 class="h4 mb-3">Pembayaran Rutin</h1>
  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
  <div class="card shadow">
    <div class="card-body table-responsive">
      <table class="table table-bordered table-sm">
        <thead>
          <tr>
            <th>Nama</th><th>Jumlah</th><th>Jatuh Tempo</th><th>Status</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($expenses as $e)
            @php $dueIn = $e->next_due_date->diffInDays($today,false); @endphp
            <tr class="{{ $dueIn <=3 && $dueIn >=0 ? 'table-warning' : ($dueIn<0 ? 'table-danger' : '') }}">
              <td>{{ $e->name }}</td>
              <td>Rp {{ number_format($e->amount,0,',','.') }}</td>
              <td>{{ $e->next_due_date->format('d/m/Y') }}</td>
              <td>
                @if($dueIn < 0)
                  <span class="badge badge-danger">Lewat Jatuh Tempo</span>
                @elseif($dueIn <=3)
                  <span class="badge badge-warning">Segera Dibayar ({{ $dueIn }} hari)</span>
                @else
                  <span class="badge badge-success">On Track</span>
                @endif
              </td>
              <td>
                <a href="{{ route('kasir.recurring-expenses.pay',$e->id) }}" class="btn btn-sm btn-primary">Bayar</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center">Tidak ada data</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
