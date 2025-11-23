@extends('admin.layouts.app')
@section('title','Notifikasi')
@section('content')
<div class="container-fluid">
  <div class="mb-4 d-sm-flex align-items-center justify-content-between">
    <h1 class="mb-0 text-gray-800 h3">Notifikasi</h1>
  </div>

  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

  <div class="shadow card">
    <div class="py-3 card-header">
      <h6 class="m-0 font-weight-bold">Daftar Notifikasi</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Waktu</th>
              <th>Pesan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($notifications as $n)
              @php $data = $n->data; @endphp
              <tr>
                <td>{{ optional($n->created_at)->format('Y-m-d H:i') }}</td>
                <td>
                  @if(($data['type'] ?? '') === 'session_report_submitted')
                    Laporan sesi pemasukkan dikirim ({{ $data['date'] ?? '-' }} - {{ $data['session'] ?? '-' }}), Cash: Rp {{ number_format($data['total_cash'] ?? 0,0,',','.') }}, QRIS: Rp {{ number_format($data['total_qris'] ?? 0,0,',','.') }}
                  @else
                    {{ $data['message'] ?? 'Notifikasi' }}
                  @endif
                </td>
                <td>
                  @if($n->read_at)
                    <span class="badge badge-secondary">Dibaca</span>
                  @else
                    <span class="badge badge-danger">Baru</span>
                  @endif
                </td>
                <td>
                  @if($n->read_at)
                    <form action="{{ route('admin.notifications.toggle',$n->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-outline-secondary">Tandai Belum Dibaca</button></form>
                  @else
                    <form action="{{ route('admin.notifications.toggle',$n->id) }}" method="POST" class="d-inline">@csrf<button class="btn btn-sm btn-outline-secondary">Tandai Dibaca</button></form>
                  @endif
                  @if(($data['type'] ?? '') === 'session_report_submitted' && isset($data['report_id']))
                    <a href="{{ route('admin.income-approvals.show', $data['report_id']) }}" class="btn btn-sm btn-primary">Buka</a>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center">Tidak ada notifikasi</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      {{ $notifications->links() }}
    </div>
  </div>
</div>
@endsection