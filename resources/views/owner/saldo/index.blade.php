@extends(auth()->check() && auth()->user()->role === 'admin' ? 'admin.layouts.app' : 'owner.layouts.app')
@section('title','Saldo Management')
@section('content')
<div class="container-fluid">
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Saldo Management</h1>
        <div>
            <button class="mr-2 shadow-sm d-none d-sm-inline-block btn btn-sm btn-secondary" data-toggle="modal" data-target="#modalExportCustom">
                <i class="fas fa-file-export fa-sm text-white-50"></i> Export Custom
            </button>
            <button class="shadow-sm d-none d-sm-inline-block btn btn-sm btn-primary" data-toggle="modal" data-target="#modalTopup">
                <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Saldo
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-3 alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-3 alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Balances Row -->
    <div class="row">
        @php $accIcons = ['kasir'=>'fa-cash-register','bank'=>'fa-university','tunai'=>'fa-wallet']; @endphp
        @foreach(['kasir','bank','tunai'] as $acc)
        <div class="mb-4 col-xl-4 col-md-6">
            <div class="py-2 shadow card border-left-primary h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-primary text-uppercase">Saldo {{ ucfirst($acc) }}</div>
                            <div class="mb-0 text-gray-800 h5 font-weight-bold">Rp {{ number_format($balances[$acc]->balance ?? 0,0,',','.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas {{ $accIcons[$acc] ?? 'fa-coins' }} fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        <div class="mb-4 col-xl-12">
            <div class="py-2 shadow card border-left-success h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-success text-uppercase">Total Saldo</div>
                            <div class="mb-0 text-gray-800 h4 font-weight-bold">Rp {{ number_format($saldoTotal,0,',','.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-equals fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal/Asset Value (not part of saldo total) -->
        <div class="mb-4 col-xl-12">
            <div class="py-2 shadow card border-left-info h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="mr-2 col">
                            <div class="mb-1 text-xs font-weight-bold text-info text-uppercase">Nilai Modal (Asset)</div>
                            <div class="mb-0 text-gray-800 h4 font-weight-bold">Rp {{ number_format($modalValue ?? 0,0,',','.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="text-gray-300 fas fa-boxes fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Form -->
    <div class="mb-4 row">
        <div class="col-lg-5">
            <div class="shadow card">
                <div class="py-3 card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Transfer Saldo Antar Akun</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('owner.saldo.transfer') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="source_account">Sumber</label>
                            <select name="source_account" id="source_account" class="form-control" required>
                                <option value="" disabled selected>Pilih Akun</option>
                                @foreach(['kasir','bank','tunai'] as $acc)
                                    <option value="{{ $acc }}" {{ old('source_account')===$acc?'selected':'' }}>{{ ucfirst($acc) }} (Rp {{ number_format($balances[$acc]->balance ?? 0,0,',','.') }})</option>
                                @endforeach
                            </select>
                            @error('source_account')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group">
                            <label for="destination_account">Tujuan</label>
                            <select name="destination_account" id="destination_account" class="form-control" required>
                                <option value="" disabled selected>Pilih Akun</option>
                                @foreach(['kasir','bank','tunai'] as $acc)
                                    <option value="{{ $acc }}" {{ old('destination_account')===$acc?'selected':'' }}>{{ ucfirst($acc) }}</option>
                                @endforeach
                            </select>
                            @error('destination_account')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group">
                            <label for="amount">Jumlah</label>
                            <input type="text" inputmode="numeric" name="amount" id="amount" class="form-control rupiah-input" value="{{ old('amount') ? number_format((int)old('amount'),0,',','.') : '' }}" placeholder="0" required>
                            @error('amount')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group">
                            <label for="note">Catatan (opsional)</label>
                            <input type="text" name="note" id="note" class="form-control" maxlength="255" value="{{ old('note') }}" placeholder="Misal: Setoran ke bank">
                            @error('note')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-exchange-alt"></i> Transfer</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="shadow card h-100">
                <div class="py-3 card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Transfer</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" width="100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Sumber -> Tujuan</th>
                                    <th>Jumlah</th>
                                    <th>Catatan</th>
                                    <th>User</th>
                                    <th>Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transfers as $t)
                                <tr>
                                    <td>
                                        {{ \Carbon\Carbon::parse($t->date)->format('d/m/Y') }}
                                        <div class="small text-gray-600">{{ $t->time }}</div>
                                    </td>
                                    <td><span class="badge badge-info">{{ ucfirst($t->source_account) }}</span> <i class="fas fa-arrow-right"></i> <span class="badge badge-success">{{ ucfirst($t->destination_account) }}</span></td>
                                    <td>Rp {{ number_format($t->amount,0,',','.') }}</td>
                                    <td>{{ $t->note ? Str::limit($t->note,30) : '-' }}</td>
                                    <td>{{ $t->user->name ?? 'N/A' }}</td>
                                    <td class="text-nowrap">
                                        @if($t->invoice_income_id)
                                            <a class="btn btn-sm btn-light" href="{{ route('owner.invoice.show',$t->invoice_income_id) }}" target="_blank" title="Invoice Income"><i class="fas fa-file-invoice-dollar text-success"></i></a>
                                        @endif
                                        @if($t->invoice_expend_id)
                                            <a class="btn btn-sm btn-light" href="{{ route('owner.invoice.show',$t->invoice_expend_id) }}" target="_blank" title="Invoice Expense"><i class="fas fa-file-invoice text-danger"></i></a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center">Belum ada transfer</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div>
                        {{ $transfers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Saldo (Topup) -->
    <div class="mb-4 row">
        <div class="col-12">
            <div class="shadow card">
                <div class="py-3 card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Saldo (Topup)</h6>
                    <a href="{{ route('owner.saldo.topup.export') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-file-export"></i> Export</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" width="100%">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Akun</th>
                                    <th>Jumlah</th>
                                    <th>Catatan</th>
                                    <th>User</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topups as $r)
                                <tr>
                                    <td>
                                        {{ $r->date ? $r->date->format('d/m/Y') : '' }}
                                        <div class="small text-gray-600">{{ is_string($r->time) ? $r->time : ($r->time ? $r->time->format('H:i:s') : '') }}</div>
                                    </td>
                                    <td><span class="badge badge-info text-uppercase">{{ $r->account }}</span></td>
                                    <td>Rp {{ number_format($r->amount,0,',','.') }}</td>
                                    <td>{{ $r->note ? \Illuminate\Support\Str::limit($r->note,40) : '-' }}</td>
                                    <td>{{ $r->user->name ?? '-' }}</td>
                                    <td class="text-nowrap">
                                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalEditTopup{{ $r->id }}"><i class="fas fa-edit"></i></button>
                                        <form action="{{ route('owner.saldo.topup.destroy',$r->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus topup ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <!-- Edit Modal per Row -->
                                <div class="modal fade" id="modalEditTopup{{ $r->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                  <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title">Edit Topup</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                          <span aria-hidden="true">&times;</span>
                                        </button>
                                      </div>
                                      <form action="{{ route('owner.saldo.topup.update',$r->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>Akun</label>
                                                <select name="account" class="form-control" required>
                                                    @foreach(['kasir','bank','tunai'] as $acc)
                                                    <option value="{{ $acc }}" {{ $r->account===$acc?'selected':'' }}>{{ ucfirst($acc) }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Jumlah</label>
                                                <input type="text" name="amount" inputmode="numeric" class="form-control rupiah-input" value="{{ number_format((int)$r->amount,0,',','.') }}" placeholder="0" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Catatan</label>
                                                <input type="text" name="note" class="form-control" value="{{ $r->note }}" maxlength="255">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                                @empty
                                <tr><td colspan="6" class="text-center">Belum ada topup</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div>
                        {{ $topups->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Tambah Saldo -->
    <div class="modal fade" id="modalTopup" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Saldo</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="{{ route('owner.saldo.topup.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Akun</label>
                    <select name="account" class="form-control" required>
                        <option value="" disabled selected>Pilih Akun</option>
                        <option value="kasir">Kasir</option>
                        <option value="bank">Bank</option>
                        <option value="tunai">Tunai</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="text" name="amount" inputmode="numeric" class="form-control rupiah-input" placeholder="0" required>
                </div>
                <div class="form-group">
                    <label>Catatan (opsional)</label>
                    <input type="text" name="note" class="form-control" maxlength="255" placeholder="Misal: Tambah modal operasional">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal: Export Custom -->
    <div class="modal fade" id="modalExportCustom" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Export Custom</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form action="{{ route('owner.saldo.export.custom') }}" method="GET" target="_blank" id="form-export-custom">
            <div class="modal-body">
                <div class="form-group">
                    <label>Jenis Data</label>
                    <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                        <label class="btn btn-outline-primary active flex-fill">
                            <input type="radio" name="dataset" value="both" autocomplete="off" checked> Keduanya
                        </label>
                        <label class="btn btn-outline-primary flex-fill">
                            <input type="radio" name="dataset" value="transfer" autocomplete="off"> Transfer
                        </label>
                        <label class="btn btn-outline-primary flex-fill">
                            <input type="radio" name="dataset" value="topup" autocomplete="off"> Topup
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Rentang Waktu</label>
                    <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                        <label class="btn btn-outline-secondary active flex-fill"><input type="radio" name="range_type" value="daily" autocomplete="off" checked> Harian</label>
                        <label class="btn btn-outline-secondary flex-fill"><input type="radio" name="range_type" value="weekly" autocomplete="off"> Mingguan</label>
                        <label class="btn btn-outline-secondary flex-fill"><input type="radio" name="range_type" value="monthly" autocomplete="off"> Bulanan</label>
                        <label class="btn btn-outline-secondary flex-fill"><input type="radio" name="range_type" value="yearly" autocomplete="off"> Tahunan</label>
                        <label class="btn btn-outline-secondary flex-fill"><input type="radio" name="range_type" value="all" autocomplete="off"> All Time</label>
                    </div>
                </div>

                <div id="range-inputs-export">
                    <div class="form-group" data-range="daily">
                        <label for="export_daily_date">Tanggal</label>
                        <input type="date" class="form-control" id="export_daily_date">
                    </div>
                    <div class="form-group d-none" data-range="weekly">
                        <label for="export_weekly_week">Minggu</label>
                        <input type="week" class="form-control" id="export_weekly_week">
                    </div>
                    <div class="form-group d-none" data-range="monthly">
                        <label for="export_monthly_month">Bulan</label>
                        <input type="month" class="form-control" id="export_monthly_month">
                    </div>
                    <div class="form-group d-none" data-range="yearly">
                        <label for="export_yearly_year">Tahun</label>
                        <input type="number" class="form-control" id="export_yearly_year" min="2000" max="2099" step="1" placeholder="{{ date('Y') }}">
                    </div>
                </div>

                <input type="hidden" name="start_date" id="export_start_date">
                <input type="hidden" name="end_date" id="export_end_date">

                <div class="form-group">
                    <label for="export_account">Akun</label>
                    <select class="form-control" id="export_account" name="account">
                        <option value="">Semua Akun</option>
                        <option value="kasir">Kasir</option>
                        <option value="bank">Bank</option>
                        <option value="tunai">Tunai</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="export_user_id">User</label>
                    <select class="form-control" id="export_user_id" name="user_id">
                        <option value="">Semua User</option>
                        @foreach(($users ?? []) as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Format</label>
                    <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                        <label class="btn btn-outline-secondary active flex-fill"><input type="radio" name="format" value="csv" autocomplete="off" checked> CSV</label>
                        <label class="btn btn-outline-secondary flex-fill"><input type="radio" name="format" value="pdf" autocomplete="off"> PDF</label>
                        <label class="btn btn-outline-secondary flex-fill"><input type="radio" name="format" value="xlsx" autocomplete="off"> Excel</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-export-custom">Download</button>
            </div>
          </form>
        </div>
      </div>
    </div>

@push('scripts')
<script>
(function(){
    function format(d){ var y=d.getFullYear(); var m=(d.getMonth()+1).toString().padStart(2,'0'); var day=d.getDate().toString().padStart(2,'0'); return y+'-'+m+'-'+day; }
    function computeRange(){
        var type = $('input[name="range_type"]:checked').val();
        if(type==='all'){ $('#export_start_date').val(''); $('#export_end_date').val(''); return; }
        var start='', end='';
        if(type==='daily'){
            var d = new Date($('#export_daily_date').val()); start=end=format(d);
        } else if(type==='weekly'){
            var w=$('#export_weekly_week').val();
            if(!w){ var cur=new Date(); var day=cur.getDay(); var diff=cur.getDate()-day+(day===0?-6:1); var monday=new Date(cur.setDate(diff)); var sunday=new Date(monday); sunday.setDate(monday.getDate()+6); start=format(monday); end=format(sunday); }
            else { var parts=w.split('-W'); var wy=parseInt(parts[0],10), wn=parseInt(parts[1],10); var jan4=new Date(wy,0,4); var dow=jan4.getDay()||7; var monday=new Date(jan4); monday.setDate(jan4.getDate()-dow+1+(wn-1)*7); var sunday=new Date(monday); sunday.setDate(monday.getDate()+6); start=format(monday); end=format(sunday); }
        } else if(type==='monthly'){
            var mval=$('#export_monthly_month').val(); var arr=(mval?mval:(new Date().getFullYear()+'-'+(new Date().getMonth()+1).toString().padStart(2,'0'))).split('-'); var y=parseInt(arr[0],10), m=parseInt(arr[1],10)-1; var first=new Date(y,m,1); var last=new Date(y,m+1,0); start=format(first); end=format(last);
        } else if(type==='yearly'){
            var y=parseInt($('#export_yearly_year').val()||new Date().getFullYear(),10); start=y+'-01-01'; end=y+'-12-31';
        }
        $('#export_start_date').val(start); $('#export_end_date').val(end);
    }
    $('input[name="range_type"]').on('change', function(){
        var v=$(this).val(); $('#range-inputs-export [data-range]').addClass('d-none'); if(v!=='all'){ $('#range-inputs-export [data-range="'+v+'"]').removeClass('d-none'); }
    });
    $('#form-export-custom').on('submit', function(){ computeRange(); });
    // defaults
    var t=new Date(); var yyyy=t.getFullYear(); var mm=(t.getMonth()+1).toString().padStart(2,'0'); var dd=t.getDate().toString().padStart(2,'0');
    $('#export_daily_date').val(yyyy+'-'+mm+'-'+dd); $('#export_monthly_month').val(yyyy+'-'+mm); $('#export_yearly_year').val(yyyy);
})();
</script>
@endpush
</div>
@endsection