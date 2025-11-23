@extends('admin.layouts.app')
@section('title','Persetujuan Pemasukkan')
@section('content')
<div class="container-fluid">
  <div class="mb-4 d-sm-flex align-items-center justify-content-between">
    <h1 class="mb-0 text-gray-800 h3">Persetujuan Pemasukkan</h1>
  </div>

  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

  <div class="shadow card">
    <div class="py-3 card-header">
      <h6 class="m-0 font-weight-bold text-secondary">Pending Reports</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Sesi</th>
              <th>Kasir</th>
              <th>Total Cash</th>
              <th>Total QRIS</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($reports as $r)
              <tr>
                <td>{{ \Carbon\Carbon::parse($r->date)->format('Y-m-d') }}</td>
                <td>{{ ucfirst($r->session) }}</td>
                <td>{{ $r->cashier->name ?? '-' }}</td>
                <td>Rp {{ number_format($r->total_cash,0,',','.') }}</td>
                <td>Rp {{ number_format($r->total_qris,0,',','.') }}</td>
                <td>
                  <a href="{{ route('admin.income-approvals.show',$r->id) }}" class="btn btn-sm btn-secondary">Detail</a>
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center">Tidak ada laporan pending</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {{ $reports->links() }}
    </div>
  </div>
</div>
@endsection