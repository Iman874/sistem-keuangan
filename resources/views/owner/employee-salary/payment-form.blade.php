@extends('owner.layouts.app')

@section('title','Bayar Gaji')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Bayar Gaji Karyawan</h1>
        <a href="{{ route('owner.employee-salary.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('owner.employee-salary.storePayment') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Karyawan</label>
                        <select name="user_salary_id" id="user_salary_id" class="form-control" required>
                            <option value="">- Pilih -</option>
                            @foreach($employees as $e)
                                <option value="{{ $e->id }}" data-base="{{ (float)$e->base_salary }}">{{ $e->name }} ({{ $e->role }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Bulan</label>
                        <select name="month" class="form-control" required>
                            @for($m=1;$m<=12;$m++)
                                <option value="{{ $m }}" {{ $m===$month?'selected':'' }}>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Tahun</label>
                        <input type="number" name="year" class="form-control" value="{{ $year }}" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Gaji Pokok</label>
                        <input type="text" id="base_salary_display" class="form-control" value="Rp 0" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Total Dibayarkan (otomatis)</label>
                        <input type="text" name="amount" id="amount" class="form-control rupiah-input" inputmode="numeric" placeholder="0" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Metode</label>
                        <select name="method" class="form-control" required>
                            <option value="cash">Cash</option>
                            <option value="qris">QRIS</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Sumber Dana</label>
                        <select name="fund_source" class="form-control" required>
                            <option value="kasir">Saldo Kasir</option>
                            <option value="bank">Saldo Bank</option>
                            <option value="tunai">Saldo Tunai</option>
                        </select>
                    </div>
                </div>

                <hr>
                <h6 class="text-primary">Pemotongan Gaji (Opsional)</h6>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Jenis</label>
                        <select name="deduction_type" class="form-control">
                            <option value="">- Tidak Ada -</option>
                            <option value="percent">Persen (%)</option>
                            <option value="fixed">Nominal</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Nilai</label>
                        <input type="text" name="deduction_value" class="form-control" min="0" step="0.01" placeholder="0">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Deskripsi</label>
                        <input type="text" name="deduction_desc" class="form-control" placeholder="Alasan pemotongan (opsional)">
                    </div>
                </div>

                <h6 class="text-success">Bonus (Opsional)</h6>
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Jenis</label>
                        <select name="bonus_type" class="form-control">
                            <option value="">- Tidak Ada -</option>
                            <option value="percent">Persen (%)</option>
                            <option value="fixed">Nominal</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Nilai</label>
                        <input type="text" name="bonus_value" class="form-control" min="0" step="0.01" placeholder="0">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Deskripsi</label>
                        <input type="text" name="bonus_desc" class="form-control" placeholder="Keterangan bonus (opsional)">
                    </div>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <button class="btn btn-success">Simpan Pembayaran</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function formatRupiah(n){
        n = n || 0; return 'Rp ' + (Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
    }
    function parseCurrency(v){ v = (v||''); v = v.replace(/\D/g,''); var n = parseFloat(v); return isNaN(n)?0:n; }

    function recalc(){
        var base = parseFloat($('#user_salary_id option:selected').data('base')) || 0;
        var dType = $('[name="deduction_type"]').val();
        var bType = $('[name="bonus_type"]').val();
        var dRaw = $('[name="deduction_value"]').val();
        var bRaw = $('[name="bonus_value"]').val();
        var dVal = dType==='percent' ? (parseFloat(dRaw)||0) : parseCurrency(dRaw);
        var bVal = bType==='percent' ? (parseFloat(bRaw)||0) : parseCurrency(bRaw);

        var deduction = 0;
        if(dType==='percent') deduction = base * Math.min(Math.max(dVal,0),100)/100; else if(dType==='fixed') deduction = dVal;
        var bonus = 0;
        if(bType==='percent') bonus = base * Math.min(Math.max(bVal,0),100)/100; else if(bType==='fixed') bonus = bVal;

        var net = Math.max(0, base - deduction + bonus);
        $('#base_salary_display').val(formatRupiah(base));
        $('#amount').val((Math.round(net)).toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.'));
    }

    $(document).ready(function(){
        // Recalc when employee or fields change
        $('#user_salary_id').on('change', recalc);
        $('[name="deduction_type"], [name="deduction_value"], [name="bonus_type"], [name="bonus_value"]').on('input change', function(e){
            // When percent is selected, enforce 0..100 range live
            var t = e.target;
            var name = t.getAttribute('name');
            if(name==='deduction_value' && $('[name="deduction_type"]').val()==='percent'){
                var v = t.value.replace(/[^0-9.]/g,'');
                var n = parseFloat(v); if(isNaN(n)) n = 0; if(n>100) n=100; if(n<0) n=0; t.value = n.toString();
            }
            if(name==='bonus_value' && $('[name="bonus_type"]').val()==='percent'){
                var v2 = t.value.replace(/[^0-9.]/g,'');
                var n2 = parseFloat(v2); if(isNaN(n2)) n2 = 0; if(n2>100) n2=100; if(n2<0) n2=0; t.value = n2.toString();
            }
            recalc();
        });

        function applyTypeBehavior(){
            var dType = $('[name="deduction_type"]').val();
            var bType = $('[name="bonus_type"]').val();
            var dInput = $('[name="deduction_value"]');
            var bInput = $('[name="bonus_value"]');

            if(dType==='fixed'){
                dInput.addClass('rupiah-input').attr({inputmode:'numeric', placeholder:'0'}).removeAttr('max').removeAttr('min').removeAttr('step');
            } else if(dType==='percent'){
                // clear currency formatting when switching to percent
                dInput.removeClass('rupiah-input').attr({inputmode:'decimal', placeholder:'0-100', step:'0.01', min:'0', max:'100'});
                dInput.val('');
            } else { dInput.removeClass('rupiah-input'); }

            if(bType==='fixed'){
                bInput.addClass('rupiah-input').attr({inputmode:'numeric', placeholder:'0'}).removeAttr('max').removeAttr('min').removeAttr('step');
            } else if(bType==='percent'){
                bInput.removeClass('rupiah-input').attr({inputmode:'decimal', placeholder:'0-100', step:'0.01', min:'0', max:'100'});
                bInput.val('');
            } else { bInput.removeClass('rupiah-input'); }
        }
        $('[name="deduction_type"], [name="bonus_type"]').on('change', function(){ applyTypeBehavior(); recalc(); });

        // Initialize once on load
        applyTypeBehavior();
        recalc();
    });
</script>
@endpush
