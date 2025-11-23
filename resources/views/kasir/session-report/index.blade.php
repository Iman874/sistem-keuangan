@extends('kasir.layouts.app')

@section('title','Laporan Sesi Pemasukkan')

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Laporan Sesi Pemasukkan</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-4 card">
        <div class="py-3 card-header">
            <form method="GET" action="{{ route('kasir.session-report.index') }}" class="form-inline">
                <label class="mr-2">Tanggal:</label>
                <input type="date" name="date" value="{{ $date }}" class="form-control mr-3" />
                <label class="mr-2">Sesi:</label>
                <select name="session" class="form-control mr-3">
                    <option value="pagi" {{ $session=='pagi'?'selected':'' }}>Pagi</option>
                    <option value="sore" {{ $session=='sore'?'selected':'' }}>Sore</option>
                </select>
                <button class="btn btn-primary">Filter</button>
            </form>
        </div>
        <div class="card-body">
            @if($report)
                <div class="p-3 mb-3 border rounded {{ $report->status=='pending' ? 'border-warning' : ($report->status=='approved' ? 'border-success' : 'border-danger') }}">
                    <strong>Status Laporan Terbaru (Attempt {{ $report->attempt ?? 1 }}):</strong> {{ strtoupper($report->status) }}<br>
                    <small>Total Cash: Rp {{ number_format($report->total_cash,0,',','.') }} | Total QRIS: Rp {{ number_format($report->total_qris,0,',','.') }}</small>
                </div>
            @endif

            <h5 class="mb-3">Draft Pemasukkan (Belum Disubmit)</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kategori</th>
                            <th>Waktu</th>
                            <th>Payment</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($draftIncomes as $inc)
                            <tr>
                                <td>{{ $inc->category->nama_pemasukkan ?? '-' }}</td>
                                <td>{{ $inc->time ? $inc->time->format('H:i') : '-' }}</td>
                                <td><span class="badge badge-{{ $inc->payment_type=='qris'?'primary':'secondary' }}">{{ strtoupper($inc->payment_type) }}</span></td>
                                <td>Rp {{ number_format($inc->amount,0,',','.') }}</td>
                                <td>{{ $inc->description }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">Tidak ada draft untuk sesi ini.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-light">
                            <th colspan="2">Total Cash</th>
                            <th colspan="3">Rp {{ number_format($totals['cash'],0,',','.') }}</th>
                        </tr>
                        <tr class="bg-light">
                            <th colspan="2">Total QRIS</th>
                            <th colspan="3">Rp {{ number_format($totals['qris'],0,',','.') }}</th>
                        </tr>
                        <tr class="bg-light">
                            <th colspan="2">Grand Total</th>
                            <th colspan="3">Rp {{ number_format($totals['cash'] + $totals['qris'],0,',','.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if((!$report || $report->status=='rejected') && $draftIncomes->count())
            <form method="POST" action="{{ route('kasir.session-report.submit') }}" class="mt-3">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}" />
                <input type="hidden" name="session" value="{{ $session }}" />
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="verified" id="verified" value="1" required>
                    <label class="form-check-label" for="verified">
                        Saya telah memverifikasi data pemasukkan sesi ini (Attempt {{ ($report && $report->status=='rejected') ? (($report->attempt ?? 1)+1) : 1 }}).
                    </label>
                </div>
                <button class="btn btn-success" type="submit">{{ $report && $report->status=='rejected' ? 'Submit Ulang Laporan Sesi' : 'Submit Laporan Sesi' }}</button>
            </form>
            @endif

            <hr>
            <h5 class="mt-4">Riwayat Laporan Hari Ini (Approved / Rejected)</h5>
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Attempt</th>
                            <th>Status</th>
                            <th>Total Cash</th>
                            <th>Total QRIS</th>
                            <th>Dibuat</th>
                            <th>Diputuskan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historyReports as $h)
                            <tr class="{{ $h->status=='approved' ? 'table-success' : 'table-danger' }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $h->attempt ?? 1 }}</td>
                                <td>{{ strtoupper($h->status) }}</td>
                                <td>Rp {{ number_format($h->total_cash,0,',','.') }}</td>
                                <td>Rp {{ number_format($h->total_qris,0,',','.') }}</td>
                                <td>{{ $h->submitted_at ? $h->submitted_at->format('H:i') : '-' }}</td>
                                <td>{{ $h->decided_at ? $h->decided_at->format('H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">Belum ada riwayat (approved/rejected) untuk hari ini.</td></tr>
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
// future JS enhancements
</script>
@endsection
