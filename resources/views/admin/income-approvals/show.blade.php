@extends('admin.layouts.app')
@section('title','Detail Laporan Sesi')
@section('content')
<div class="container-fluid">
  <div class="mb-4 d-sm-flex align-items-center justify-content-between">
    <h1 class="mb-0 text-gray-800 h3">Detail Laporan Sesi</h1>
    <a href="{{ route('admin.income-approvals.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
  </div>

  @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

  <div class="mb-3 card">
    <div class="py-3 card-header">
      <h6 class="m-0 font-weight-bold">Info Laporan</h6>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Tanggal</dt><dd class="col-sm-9">{{ \Carbon\Carbon::parse($report->date)->format('Y-m-d') }}</dd>
        <dt class="col-sm-3">Sesi</dt><dd class="col-sm-9">{{ ucfirst($report->session) }}</dd>
        <dt class="col-sm-3">Kasir</dt><dd class="col-sm-9">{{ $report->cashier->name ?? '-' }}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9"><span class="badge badge-{{ $report->status=='pending'?'warning':($report->status=='approved'?'success':'danger') }}">{{ strtoupper($report->status) }}</span></dd>
        <dt class="col-sm-3">Total Cash</dt><dd class="col-sm-9">Rp {{ number_format($report->total_cash,0,',','.') }}</dd>
        <dt class="col-sm-3">Total QRIS</dt><dd class="col-sm-9">Rp {{ number_format($report->total_qris,0,',','.') }}</dd>
        @if($report->note)
        <dt class="col-sm-3">Catatan</dt><dd class="col-sm-9">{{ $report->note }}</dd>
        @endif
        @if($report->approval_note)
        <dt class="col-sm-3">Catatan Approval</dt><dd class="col-sm-9">{{ $report->approval_note }}</dd>
        @endif
      </dl>
    </div>
  </div>

  <div class="card mb-4">
    <div class="py-3 card-header">
      <h6 class="m-0 font-weight-bold">Item Pemasukkan</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Kategori</th>
              <th>Waktu</th>
              <th>Payment</th>
              <th>Jumlah</th>
              <th>Deskripsi</th>
            </tr>
          </thead>
          <tbody>
            @php $sum=0; @endphp
            @foreach($report->incomes as $inc)
              @php $sum += $inc->amount; @endphp
              <tr>
                <td>{{ $inc->category->nama_pemasukkan ?? '-' }}</td>
                <td>{{ $inc->time? $inc->time->format('H:i') : '-' }}</td>
                <td><span class="badge badge-{{ $inc->payment_type=='qris'?'primary':'secondary' }}">{{ strtoupper($inc->payment_type) }}</span></td>
                <td>Rp {{ number_format($inc->amount,0,',','.') }}</td>
                <td>{{ $inc->description }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="bg-light"><th colspan="3">Total</th><th colspan="2">Rp {{ number_format($sum,0,',','.') }}</th></tr>
          </tfoot>
        </table>
      </div>

      @if($report->status=='pending')
      <div class="mt-3">
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#approveModal">Approve</button>
        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">Reject</button>
      </div>
      @endif
    </div>
  </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ route('admin.income-approvals.reject',$report->id) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="rejectModalLabel">Tolak Laporan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Catatan / Alasan Penolakan</label>
            <textarea name="note" class="form-control" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Tolak</button>
        </div>
      </form>
    </div>
  </div>
</div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <form action="{{ route('admin.income-approvals.approve',$report->id) }}" method="POST">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title" id="approveModalLabel">Setujui Laporan</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label>Catatan / Deskripsi Approval (Opsional)</label>
                <textarea name="approval_note" class="form-control" rows="3" placeholder="Boleh dikosongkan"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-success">Setujui</button>
            </div>
          </form>
        </div>
      </div>
    </div>
@endsection