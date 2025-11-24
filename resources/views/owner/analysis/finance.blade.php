@extends(auth()->check() && auth()->user()->role === 'admin' ? 'admin.layouts.app' : 'owner.layouts.app')
@section('title','Analisis Keuangan')

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Analisis Keuangan</h1>
        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#financeExportModal"><i class="fas fa-download"></i> Export Custom</button>
    </div>

    <!-- Card Filter Utama -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header">
            <h6 class="m-0 font-weight-bold text-primary">Filter Utama</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('owner.analysis.finance') }}" id="main-filter-form">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Mode Analisis</label>
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            @foreach(['kategori'=>'Analisis Kategori','pantau'=>'Pantau Keuangan'] as $key=>$label)
                                <label class="btn btn-outline-primary {{ $mode===$key ? 'active' : '' }}">
                                    <input type="radio" name="mode" value="{{ $key }}" {{ $mode===$key ? 'checked' : '' }} autocomplete="off"> {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label>Periode</label>
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            @foreach(['daily'=>'Harian','weekly'=>'Mingguan','monthly'=>'Bulanan','yearly'=>'Tahunan'] as $key=>$label)
                                <label class="btn btn-outline-secondary {{ $period===$key ? 'active' : '' }}">
                                    <input type="radio" name="period" value="{{ $key }}" {{ $period===$key ? 'checked' : '' }} autocomplete="off"> {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Role</label>
                        <select name="role" class="form-control" onchange="document.getElementById('main-filter-form').submit()">
                            <option value="">Semua Role</option>
                            @foreach($roles as $r)
                                <option value="{{ $r }}" {{ $roleSelected===$r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Tipe Pengeluaran</label>
                        <select name="expense_type" class="form-control" onchange="document.getElementById('main-filter-form').submit()">
                            <option value="">Semua Tipe</option>
                            @foreach(['harian'=>'Harian','bulanan'=>'Bulanan'] as $val=>$lbl)
                                <option value="{{ $val }}" {{ ($expenseType ?? '')===$val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Tipe Pemasukkan</label>
                        <select name="income_type" class="form-control" onchange="document.getElementById('main-filter-form').submit()">
                            <option value="">Semua Tipe</option>
                            @foreach(['indoor'=>'Indoor','outdoor'=>'Outdoor'] as $val=>$lbl)
                                <option value="{{ $val }}" {{ ($incomeType ?? '')===$val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Dynamic period selectors -->
                    @if($period==='daily')
                        <div class="col-md-3 mb-3">
                            <label>Tanggal</label>
                            <input type="date" name="day_date" class="form-control" value="{{ $dayDate ? \Carbon\Carbon::parse($dayDate)->format('Y-m-d') : $startDate->format('Y-m-d') }}">
                        </div>
                    @elseif($period==='weekly')
                        <div class="col-md-2 mb-3">
                            <label>Tahun</label>
                            <select name="week_year" class="form-control">
                                @foreach($yearsList as $y)
                                    <option value="{{ $y }}" {{ $weekYear==$y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Bulan</label>
                            <select name="week_month" class="form-control">
                                @foreach($monthsList as $m)
                                    <option value="{{ $m['num'] }}" {{ $weekMonth==$m['num'] ? 'selected' : '' }}>{{ ucfirst($m['label']) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Minggu</label>
                            <select name="week_index" class="form-control">
                                @foreach($weeksList as $idx=>$rng)
                                    <option value="{{ $idx }}" {{ $weekIndex==$idx ? 'selected' : '' }}>Minggu {{ $idx }} ({{ $rng }})</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($period==='monthly')
                        <div class="col-md-2 mb-3">
                            <label>Tahun</label>
                            <select name="month_year" class="form-control">
                                @foreach($yearsList as $y)
                                    <option value="{{ $y }}" {{ $monthYear==$y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Bulan</label>
                            <select name="month_month" class="form-control">
                                @foreach($monthsList as $m)
                                    <option value="{{ $m['num'] }}" {{ $monthMonth==$m['num'] ? 'selected' : '' }}>{{ ucfirst($m['label']) }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($period==='yearly')
                        <div class="col-md-2 mb-3">
                            <label>Tahun</label>
                            <select name="year_year" class="form-control">
                                @foreach($yearsList as $y)
                                    <option value="{{ $y }}" {{ $yearYear==$y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <div>
                            <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-filter"></i> Terapkan</button>
                            <a href="{{ route('owner.analysis.finance') }}" class="btn btn-secondary"><i class="fas fa-sync"></i> Reset</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{--
    <!-- Fitur Belum selesai -->
    <!-- Card Filter Detail -->
    @if($mode==='pantau')
    <div class="mb-4 card" id="globalDetailFilterCard">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filter Detail Tambahan</h6>
            <div class="custom-control custom-switch">
                <input type="hidden" name="global_filter_active" value="1">
                <input type="checkbox" class="custom-control-input" id="globalFilterSwitch" name="global_filter_active" value="1" {{ request('global_filter_active') ? 'checked' : '' }}>
                <label class="custom-control-label" for="globalFilterSwitch">Switch Off/On</label>
            </div>
        </div>

        <div class="card-body" id="globalFilterBody" style="display:{{ request('global_filter_active') ? '' : 'none' }};">
            <!-- FILTER ROLE & USER -->
            <form method="GET" action="{{ route('owner.analysis.finance') }}" id="global-detail-filter-form">
                <!-- Hidden main filter params -->
                <input type="hidden" name="mode" value="{{ $mode }}">
                <input type="hidden" name="period" value="{{ $period }}">
                <input type="hidden" name="role" value="{{ $roleSelected }}">
                <input type="hidden" name="income_type" value="{{ $incomeType }}">
                <input type="hidden" name="expense_type" value="{{ $expenseType }}">
                <input type="hidden" name="global_filter_active" value="1">
                @if($period==='daily')
                    <input type="hidden" name="day_date" value="{{ $dayDate ? $dayDate : $startDate->format('Y-m-d') }}">
                @elseif($period==='weekly')
                    <input type="hidden" name="week_year" value="{{ $weekYear }}">
                    <input type="hidden" name="week_month" value="{{ $weekMonth }}">
                    <input type="hidden" name="week_index" value="{{ $weekIndex }}">
                @elseif($period==='monthly')
                    <input type="hidden" name="month_year" value="{{ $monthYear }}">
                    <input type="hidden" name="month_month" value="{{ $monthMonth }}">
                @elseif($period==='yearly')
                    <input type="hidden" name="year_year" value="{{ $yearYear }}">
                @endif
                <div class="mb-3">
                    <label>Filter Role & User</label>
                    <div class="mb-2">
                        <button type="button" class="btn btn-sm btn-light" onclick="toggleRoles(true)">Pilih Semua Role</button>
                        <button type="button" class="btn btn-sm btn-light" onclick="toggleRoles(false)">Kosongkan Role</button>
                        <button type="button" class="btn btn-sm btn-light" onclick="toggleUsers(true)">Pilih Semua User</button>
                        <button type="button" class="btn btn-sm btn-light" onclick="toggleUsers(false)">Kosongkan User</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width:120px;">Role</th>
                                    <th>User (Checklist)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($roles as $r)
                                <tr>
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input role-check" id="global-role{{ $r }}" name="roles[]" value="{{ $r }}" {{ in_array($r, $filterRoles) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="global-role{{ $r }}">{{ ucfirst($r) }}</label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap">
                                            @foreach(($usersByRole[$r] ?? []) as $u)
                                            <div class="custom-control custom-checkbox mr-3 mb-1">
                                                <input type="checkbox" class="custom-control-input user-check user-check-{{ $r }}" id="global-user{{ $u->id }}" name="users[]" value="{{ $u->id }}" {{ in_array($u->id, request()->input('users', [])) ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="global-user{{ $u->id }}">{{ $u->name }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <small class="text-muted">Checklist role/user untuk menyaring transaksi. Kosongkan untuk semua.</small>
                </div>

            <hr>

                <!-- KATEGORI PEMASUKKAN & PENGELUARAN -->
                <div class="form-row">
                    <div class="col-md-6">
                        <label>Kategori Pemasukkan</label>
                        <div class="mb-2">
                            <button type="button" class="btn btn-sm btn-light" onclick="toggleIncome(true)">Pilih Semua</button>
                            <button type="button" class="btn btn-sm btn-light" onclick="toggleIncome(false)">Kosongkan</button>
                        </div>
                        <div class="row">
                            @foreach($presetCategoriesIncome as $c)
                            <div class="col-6">
                                <div class="custom-control custom-checkbox mb-1">
                                    <input type="checkbox" class="custom-control-input income-check" id="global-inc{{ $c->id }}" name="income_categories[]" value="{{ $c->id }}" {{ in_array($c->id, $filterIncomeCats) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="global-inc{{ $c->id }}">{{ $c->nama_pemasukkan }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <small class="text-muted">Kosongkan untuk semua kategori pemasukkan.</small>
                    </div>

                    <div class="col-md-6">
                        <label>Kategori Pengeluaran</label>
                        <div class="mb-2">
                            <button type="button" class="btn btn-sm btn-light" onclick="toggleExpense(true)">Pilih Semua</button>
                            <button type="button" class="btn btn-sm btn-light" onclick="toggleExpense(false)">Kosongkan</button>
                        </div>
                        <div class="row">
                            @foreach($presetCategoriesExpense as $c)
                            <div class="col-6">
                                <div class="custom-control custom-checkbox mb-1">
                                    <input type="checkbox" class="custom-control-input expense-check" id="global-exp{{ $c->id }}" <input type="checkbox" ... name="expense_categories[]" value="{{ $c->id }}" {{ in_array($c->id, $filterExpenseCats) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="global-exp{{ $c->id }}">{{ $c->name }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <small class="text-muted">Kosongkan untuk semua kategori pengeluaran.</small>
                    </div>

                    <div class="text-right mt-3">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-filter"></i> Terapkan
                        </button>
                        <a href="{{ route('owner.analysis.finance') }}" class="btn btn-secondary">
                            <i class="fas fa-sync"></i> Reset
                        </a>
                    </div>

                </form>
            </div>

        </div>
    </div>
    @endif
    --}}

    
   
    @if($mode==='kategori')
    <!-- Card Filter Kedua (Dinamis) -->
    <div class="mb-4 shadow card">
        <div class="py-3 card-header d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filter Tambahan (Dinamis)</h6>
            @if($mode==='kategori')
            <form method="GET" action="{{ route('owner.analysis.finance') }}" id="dyn-filter-toggle" class="m-0 p-0">
                <!-- Preserve existing main filters -->
                <input type="hidden" name="mode" value="{{ $mode }}">
                <input type="hidden" name="period" value="{{ $period }}">
                <input type="hidden" name="role" value="{{ $roleSelected }}">
                <input type="hidden" name="income_type" value="{{ $incomeType }}">
                <input type="hidden" name="expense_type" value="{{ $expenseType }}">
                <!-- Period dynamic fields to persist -->
                @if($period==='daily')
                    <input type="hidden" name="day_date" value="{{ $dayDate ? $dayDate : $startDate->format('Y-m-d') }}">
                @elseif($period==='weekly')
                    <input type="hidden" name="week_year" value="{{ $weekYear }}">
                    <input type="hidden" name="week_month" value="{{ $weekMonth }}">
                    <input type="hidden" name="week_index" value="{{ $weekIndex }}">
                @elseif($period==='monthly')
                    <input type="hidden" name="month_year" value="{{ $monthYear }}">
                    <input type="hidden" name="month_month" value="{{ $monthMonth }}">
                @elseif($period==='yearly')
                    <input type="hidden" name="year_year" value="{{ $yearYear }}">
                @endif
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="dynActiveSwitch" name="dyn_active" value="1" {{ $dynActive ? 'checked' : '' }} onchange="document.getElementById('dyn-filter-toggle').submit()">
                    <label class="custom-control-label" for="dynActiveSwitch">{{ $dynActive? 'Aktif' : 'Switch Off/On' }}</label>
                </div>
            </form>
            @endif
        </div>
        <div class="card-body">
            @if($mode==='kategori')
                @if(!$dynActive)
                    <div class="text-muted small mb-3">Aktifkan switch untuk menganalisis tren waktu kategori tertentu (histogram).</div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Kategori Pemasukkan</label>
                            <select class="form-control" disabled>
                                <option>Tidak Ada</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Kategori Pengeluaran</label>
                            <select class="form-control" disabled>
                                <option>Tidak Ada</option>
                            </select>
                        </div>
                    </div>
                @else
                    <form method="GET" action="{{ route('owner.analysis.finance') }}" id="dyn-filter-form">
                        <!-- Persist main selections -->
                        <input type="hidden" name="mode" value="{{ $mode }}">
                        <input type="hidden" name="period" value="{{ $period }}">
                        <input type="hidden" name="role" value="{{ $roleSelected }}">
                        <input type="hidden" name="income_type" value="{{ $incomeType }}">
                        <input type="hidden" name="expense_type" value="{{ $expenseType }}">
                        <input type="hidden" name="dyn_active" value="1">
                        @if($period==='daily')
                            <input type="hidden" name="day_date" value="{{ $dayDate ? $dayDate : $startDate->format('Y-m-d') }}">
                        @elseif($period==='weekly')
                            <input type="hidden" name="week_year" value="{{ $weekYear }}">
                            <input type="hidden" name="week_month" value="{{ $weekMonth }}">
                            <input type="hidden" name="week_index" value="{{ $weekIndex }}">
                        @elseif($period==='monthly')
                            <input type="hidden" name="month_year" value="{{ $monthYear }}">
                            <input type="hidden" name="month_month" value="{{ $monthMonth }}">
                        @elseif($period==='yearly')
                            <input type="hidden" name="year_year" value="{{ $yearYear }}">
                        @endif
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Pilih Kategori Pemasukkan</label>
                                <select name="dyn_income_cat" class="form-control" onchange="document.getElementById('dyn-filter-form').submit()">
                                    <option value="">-- Tidak Dipilih --</option>
                                    @foreach($presetCategoriesIncome as $c)
                                        <option value="{{ $c->id }}" {{ $dynIncomeCat==$c->id ? 'selected' : '' }}>{{ $c->nama_pemasukkan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Pilih Kategori Pengeluaran</label>
                                <select name="dyn_expense_cat" class="form-control" onchange="document.getElementById('dyn-filter-form').submit()">
                                    <option value="">-- Tidak Dipilih --</option>
                                    @foreach($presetCategoriesExpense as $c)
                                        <option value="{{ $c->id }}" {{ $dynExpenseCat==$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <small class="text-muted">Histogram mengikuti periode saat ini ({{ ucfirst($period) }}).</small>
                    </form>
                @endif
            @endif
        </div>
    </div>
    @endif

    @if($mode==='kategori')
    <div class="row">
        <!-- Card Tren Pemasukan -->
        <div class="col-lg-6 mb-4">
            <div class="shadow card h-100">
                <div class="py-3 card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Tren Pemasukkan per Kategori</h6>
                </div>
                <div class="card-body">
                    <div class="chart-fixed-height">
                        <canvas id="incomeDonutChart"></canvas>
                    </div>
                    <div class="mt-3 small text-muted">
                        Menampilkan hingga 5 kategori terbesar + "Kategori Lainnya".
                    </div>
                </div>
            </div>
        </div>
        <!-- Card Tren Pengeluaran -->
        <div class="col-lg-6 mb-4">
            <div class="shadow card h-100">
                <div class="py-3 card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-danger">Tren Pengeluaran per Kategori</h6>
                </div>
                <div class="card-body">
                    <div class="chart-fixed-height">
                        <canvas id="expenseDonutChart"></canvas>
                    </div>
                    <div class="mt-3 small text-muted">
                        Menampilkan hingga 5 kategori terbesar + "Kategori Lainnya".
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($dynActive && ($dynIncomeCat || $dynExpenseCat))
    <div class="row">
        @if($dynIncomeCat)
        <div class="col-lg-6 mb-4">
            <div class="shadow card h-100">
                <div class="py-3 card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Histogram Pemasukkan {{ $dynIncomeName ?? 'Kategori' }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-fixed-height">
                        <canvas id="dynIncomeBar"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if($dynExpenseCat)
        <div class="col-lg-6 mb-4">
            <div class="shadow card h-100">
                <div class="py-3 card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-danger">Histogram Pengeluaran {{ $dynExpenseName ?? 'Kategori' }}</h6>
                </div>
                <div class="card-body">
                    <div class="chart-fixed-height">
                        <canvas id="dynExpenseBar"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif
    @endif
    @if($mode==='pantau')
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="shadow card">
                <div class="py-3 card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Semua Transaksi (Pemasukkan & Pengeluaran)</h6>
                    <span class="small text-muted">Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0" id="pantauTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Kategori</th>
                                    <th class="text-right">Jumlah (Rp)</th>
                                    <th>User</th>
                                    <th>Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $rowNo = 1; @endphp
                                @forelse($monitorTransactions as $t)
                                    <tr>
                                        <td>{{ $rowNo++ }}</td>
                                        <td data-order="{{ \Carbon\Carbon::parse($t['date'])->format('Y-m-d') }}">{{ \Carbon\Carbon::parse($t['date'])->format('d/m/Y') }}</td>
                                        <td><span class="badge badge-{{ $t['type']==='Pemasukkan' ? 'success' : 'danger' }}">{{ $t['type'] }}</span></td>
                                        <td>{{ $t['category'] }}</td>
                                        <td class="text-right">{{ number_format($t['amount'],0,',','.') }}</td>
                                        <td>{{ $t['user'] }}</td>
                                        <td>{{ ucfirst($t['role']) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted">Tidak ada transaksi pada periode ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="shadow card h-100">
                <div class="py-3 card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Ringkasan Kategori Pemasukkan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0" id="summaryIncomeTable" width="100%" cellspacing="0">
                            <thead class="thead-light"><tr><th>Kategori</th><th class="text-right">Total (Rp)</th></tr></thead>
                            <tbody>
                                @php
                                    $data = ($mode==='kategori') ? $summaryIncome : $monitorIncomeCategories;
                                @endphp
                                @if($data->count() > 0)
                                    @foreach($data as $c)
                                        <tr><td>{{ $c['name'] }}</td><td class="text-right">{{ number_format($c['total'],0,',','.') }}</td></tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="2" class="text-center text-muted">Tidak ada data.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="shadow card h-100">
                <div class="py-3 card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-danger">Ringkasan Kategori Pengeluaran</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0" id="summaryExpenseTable" width="100%" cellspacing="0">
                            <thead class="thead-light"><tr><th>Kategori</th><th class="text-right">Total (Rp)</th></tr></thead>
                            <tbody>
                                @php
                                    $data = ($mode==='kategori') ? $summaryExpense : $monitorExpenseCategories;
                                @endphp
                                @if($data->count() > 0)
                                    @foreach($data as $c)
                                        <tr><td>{{ $c['name'] }}</td><td class="text-right">{{ number_format($c['total'],0,',','.') }}</td></tr>
                                    @endforeach
                                @else
                                    <tr><td colspan="2" class="text-center text-muted">Tidak ada data.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.chart-fixed-height{height:340px;position:relative;}
.chart-fixed-height canvas{width:100%!important;height:100%!important;}
/* Spacing to match kasir tables */
#pantauTable th, #pantauTable td{ padding: .75rem 1rem; vertical-align: middle; }
#summaryIncomeTable th, #summaryIncomeTable td,
#summaryExpenseTable th, #summaryExpenseTable td{ padding:.65rem .9rem; vertical-align:middle; }
</style>
@endpush

@push('scripts')
<script src="{{ asset('assets/vendor/chart.js/Chart.min.js') }}"></script>
<script>
const incomeLabels = @json($incomeChartLabels);
const incomeData = @json($incomeChartData);
const expenseLabels = @json($expenseChartLabels);
const expenseData = @json($expenseChartData);
const dynIncomeLabels = @json($dynIncomeLabels);
const dynIncomeData = @json($dynIncomeData);
const dynExpenseLabels = @json($dynExpenseLabels);
const dynExpenseData = @json($dynExpenseData);

function buildDonut(id, labels, data, colors){
    var el = document.getElementById(id);
    if(!el) return;
    var ctx = el.getContext('2d');
    return new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                hoverBorderColor: 'rgba(234, 236, 244, 1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutoutPercentage: 65,
            animation: false,
            tooltips: {
                backgroundColor: 'white',
                bodyFontColor: '#858796',
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 12,
                yPadding: 12,
                displayColors: false,
                callbacks: {
                    label: function(tooltipItem, data){
                        var total = data.datasets[0].data.reduce(function(a,b){return a+b;},0);
                        var val = data.datasets[0].data[tooltipItem.index];
                        var pct = total>0 ? Math.round((val/total)*100) : 0;
                        return data.labels[tooltipItem.index] + ': Rp ' + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' ('+pct+'%)';
                    }
                }
            },
            legend: { display: true, position: 'bottom' }
        }
    });
}

@if($mode==='kategori')
    
    const palette = ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796','#5a5c69'];
    buildDonut('incomeDonutChart', incomeLabels, incomeData, palette);
    buildDonut('expenseDonutChart', expenseLabels, expenseData, palette);
    // Build dynamic histograms if active
    @if($dynActive && $dynIncomeCat)
    new Chart(document.getElementById('dynIncomeBar').getContext('2d'),{
        type:'bar',
        data:{ labels: dynIncomeLabels, datasets:[{ label:'Jumlah', data: dynIncomeData, backgroundColor:'#4e73df' }] },
        options:{ maintainAspectRatio:false, animation:false, tooltips:{ callbacks:{ label:function(t,d){ return 'Rp '+ t.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.'); } } }, scales:{ yAxes:[{ ticks:{ callback:function(v){ return 'Rp '+ v.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.'); } } }] } }
    });
    @endif
    @if($dynActive && $dynExpenseCat)
    new Chart(document.getElementById('dynExpenseBar').getContext('2d'),{
        type:'bar',
        data:{ labels: dynExpenseLabels, datasets:[{ label:'Jumlah', data: dynExpenseData, backgroundColor:'#e74a3b' }] },
        options:{ maintainAspectRatio:false, animation:false, tooltips:{ callbacks:{ label:function(t,d){ return 'Rp '+ t.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.'); } } }, scales:{ yAxes:[{ ticks:{ callback:function(v){ return 'Rp '+ v.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.'); } } }] } }
    });
    @endif
@endif

// Auto submit on radio change (vanilla JS to avoid jQuery dependency issues)
document.querySelectorAll('input[name="mode"], input[name="period"]').forEach(function(el){
    el.addEventListener('change', function(){
        var form = document.getElementById('main-filter-form');
        if(form){ form.submit(); }
    });
});

// DataTables init for Pantau mode (assuming jQuery & DataTables assets already globally loaded)
@if($mode==='pantau')
$(document).ready(function(){
    if($('#pantauTable').length){
        $('#pantauTable').DataTable({
            language:{ url:'//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json' },
            order:[[1,'desc']],
            pageLength:10,
            lengthChange:true,
            lengthMenu: [[10,25,50,100],[10,25,50,100]],
            // Custom DOM: length (l) on left, search (f) on right
            dom: '<"row"<"col-sm-6 d-flex align-items-center"l><"col-sm-6 d-flex justify-content-end"f>>rtip',
            paging:true,
            info:true,
            columnDefs:[{ targets:0, orderable:false }]
        });
    }
});
@endif
</script>
@endpush

@section('modal')
<div class="modal fade" id="financeExportModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Laporan Custom Analisis Keuangan</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>

      <form method="GET" action="{{ route('owner.analysis.finance.export') }}" id="finance-export-form">
      <div class="modal-body">

        <!-- Jenis, rentang, dan role -->
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Jenis Transaksi</label>
                <select name="dataset" class="form-control">
                    <option value="both">Semua</option>
                    <option value="income">Pemasukkan Saja</option>
                    <option value="expense">Pengeluaran Saja</option>
                </select>
            </div>

            <div class="form-group col-md-6">
                <label>Rentang Waktu</label>
                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                    @foreach(['daily'=>'Harian','weekly'=>'Mingguan','monthly'=>'Bulanan','yearly'=>'Tahunan'] as $rKey=>$rLbl)
                    <label class="btn btn-outline-secondary export-range-btn" data-range="{{ $rKey }}">
                        <input type="radio" name="range" value="{{ $rKey }}"> {{ $rLbl }}
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

         <div id="range-fields" class="mb-3">
            <!-- Dynamic date range inputs inserted by JS -->
            <div class="form-group" data-range="daily" style="display:none;">
                <label>Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="form-row" data-range="weekly" style="display:none;">
                <div class="form-group col-md-4">
                    <label>Tahun</label>
                    <select name="week_year" class="form-control">
                        @foreach($yearsList as $y)<option value="{{ $y }}">{{ $y }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Bulan</label>
                    <select name="week_month" class="form-control">
                        @foreach($monthsList as $m)<option value="{{ $m['num'] }}">{{ ucfirst($m['label']) }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Minggu</label>
                    <select name="week_index" class="form-control">
                        <option value="1">Minggu 1</option>
                        <option value="2">Minggu 2</option>
                        <option value="3">Minggu 3</option>
                        <option value="4">Minggu 4</option>
                        <option value="5">Minggu 5</option>
                    </select>
                </div>
            </div>
            <div class="form-row" data-range="monthly" style="display:none;">
                <div class="form-group col-md-6">
                    <label>Tahun</label>
                    <select name="month_year" class="form-control">@foreach($yearsList as $y)<option value="{{ $y }}">{{ $y }}</option>@endforeach</select>
                </div>
                <div class="form-group col-md-6">
                    <label>Bulan</label>
                    <select name="month_month" class="form-control">@foreach($monthsList as $m)<option value="{{ $m['num'] }}">{{ ucfirst($m['label']) }}</option>@endforeach</select>
                </div>
            </div>
            <div class="form-group" data-range="yearly" style="display:none;">
                <label>Tahun</label>
                <select name="year_year" class="form-control">@foreach($yearsList as $y)<option value="{{ $y }}">{{ $y }}</option>@endforeach</select>
            </div>
        </div>

        <hr>

        <div class="mb-3">
            <label>Filter Role & User</label>
            <div class="mb-2">
                <button type="button" class="btn btn-sm btn-light" onclick="toggleRoles(true)">Pilih Semua Role</button>
                <button type="button" class="btn btn-sm btn-light" onclick="toggleRoles(false)">Kosongkan Role</button>
                <button type="button" class="btn btn-sm btn-light" onclick="toggleUsers(true)">Pilih Semua User</button>
                <button type="button" class="btn btn-sm btn-light" onclick="toggleUsers(false)">Kosongkan User</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:120px;">Role</th>
                            <th>User (Checklist)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $r)
                        <tr>
                            <td>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input role-check" id="role{{ $r }}" name="roles[]" value="{{ $r }}" {{ in_array($r, (array)($roleSelected ?? [])) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="role{{ $r }}">{{ ucfirst($r) }}</label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap">
                                    @foreach(($usersByRole[$r] ?? []) as $u)
                                    <div class="custom-control custom-checkbox mr-3 mb-1">
                                        <input type="checkbox" class="custom-control-input user-check user-check-{{ $r }}" id="user{{ $u->id }}" name="users[]" value="{{ $u->id }}" {{ in_array($u->id, $userSelected ?? []) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="user{{ $u->id }}">{{ $u->name }}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <small class="text-muted">Checklist role untuk filter peran, checklist user untuk filter orang tertentu. Kosongkan untuk semua.</small>
        </div>

        <hr>

        <!-- PILIH KATEGORI BARU -->
        <div class="form-row">
            <div class="col-md-6">
                <label>Kategori Pemasukkan</label>

                <div class="mb-2">
                    <button type="button" class="btn btn-sm btn-light" onclick="toggleIncome(true)">Pilih Semua</button>
                    <button type="button" class="btn btn-sm btn-light" onclick="toggleIncome(false)">Kosongkan</button>
                </div>

                <div class="row">
                    @foreach($presetCategoriesIncome as $c)
                    <div class="col-6">
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input income-check"
                                   id="inc{{ $c->id }}" name="income_categories[]" value="{{ $c->id }}">
                            <label class="custom-control-label" for="inc{{ $c->id }}">{{ $c->nama_pemasukkan }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>

                <small class="text-muted">Biarkan kosong untuk semua kategori pemasukkan.</small>
            </div>

            <div class="col-md-6">
                <label>Kategori Pengeluaran</label>

                <div class="mb-2">
                    <button type="button" class="btn btn-sm btn-light" onclick="toggleExpense(true)">Pilih Semua</button>
                    <button type="button" class="btn btn-sm btn-light" onclick="toggleExpense(false)">Kosongkan</button>
                </div>

                <div class="row">
                    @foreach($presetCategoriesExpense as $c)
                    <div class="col-6">
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input expense-check"
                                   id="exp{{ $c->id }}" name="expense_categories[]" value="{{ $c->id }}">
                            <label class="custom-control-label" for="exp{{ $c->id }}">{{ $c->name }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>

                <small class="text-muted">Biarkan kosong untuk semua kategori pengeluaran.</small>
            </div>
        </div>

        <hr>

        <!-- Format -->
        <div class="form-group">
            <label>Format Unduhan</label>
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-outline-secondary active"><input type="radio" name="format" value="pdf" checked> PDF</label>
                <label class="btn btn-outline-secondary"><input type="radio" name="format" value="xlsx"> Excel</label>
                <label class="btn btn-outline-secondary"><input type="radio" name="format" value="csv"> CSV</label>
            </div>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-download"></i> Download</button>
      </div>

      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>

function toggleIncome(state){
    document.querySelectorAll('.income-check').forEach(el => el.checked = state);
}
function toggleExpense(state){
    document.querySelectorAll('.expense-check').forEach(el => el.checked = state);
}
function toggleRoles(state){
    document.querySelectorAll('.role-check').forEach(el => el.checked = state);
}
function toggleUsers(state){
    document.querySelectorAll('.user-check').forEach(el => el.checked = state);
}


// Range toggle inside export modal
function updateExportRange(selected){
  document.querySelectorAll('#range-fields [data-range]').forEach(function(el){
    if(el.getAttribute('data-range') === selected){
      el.style.display = ''; // biarkan bootstrap yang atur
    }else{
      el.style.display = 'none';
    }
  });
}
document.querySelectorAll('.export-range-btn input').forEach(function(radio){
  radio.addEventListener('change', function(){ updateExportRange(this.value); });
});
// Set default
updateExportRange('daily');

// Only enable filter detail JS in pantau mode
@if($mode==='pantau')
document.getElementById('globalFilterSwitch').addEventListener('change',function(){
    const body = document.getElementById('globalFilterBody');
    body.style.display = this.checked ? '' : 'none';
    // Submit form to persist state
    if(this.form){
        document.getElementById('global-detail-filter-form').submit();
    }
});
@endif

</script>
@endpush