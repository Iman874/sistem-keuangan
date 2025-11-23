@php
    $employee = $payment->employee;
    $monthName = \Carbon\Carbon::create()->month($payment->month)->translatedFormat('F');
    $base = (float)($payment->base_salary ?? ($employee->base_salary ?? 0));
    $deduction = 0.0;
    if($payment->deduction_type && $payment->deduction_value){
        if($payment->deduction_type==='percent'){
            $deduction = round($base * (min(max((float)$payment->deduction_value,0),100)/100),2);
        }else{
            $deduction = (float)$payment->deduction_value;
        }
    }
    $bonus = 0.0;
    if($payment->bonus_type && $payment->bonus_value){
        if($payment->bonus_type==='percent'){
            $bonus = round($base * (min(max((float)$payment->bonus_value,0),100)/100),2);
        }else{
            $bonus = (float)$payment->bonus_value;
        }
    }
    $subtotal = $base;
    $net = max(0, $base - $deduction + $bonus);
@endphp

<div class="card shadow">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <img src="{{ asset('assets/img/monoframe.png') }}" alt="Logo" style="height:50px">
                <div class="mt-2 small text-muted">Tanggal: {{ $payment->paid_date?->format('d/m/Y') }}</div>
                <div class="small text-muted">Metode: {{ strtoupper($payment->method) }}</div>
                <div class="small text-muted">Sumber Dana: {{ strtoupper($payment->fund_source ?? 'KASIR') }}</div>
            </div>
            <div class="col-md-6 text-right">
                <h4 class="mb-1">Invoice Gaji</h4>
                <div class="small">Periode: {{ $monthName }} {{ $payment->year }}</div>
                <div class="small">No: PAY-{{ str_pad($payment->id,6,'0',STR_PAD_LEFT) }}</div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="font-weight-bold">Dibayarkan Kepada</h6>
                <div>{{ $employee->name }}</div>
                <div class="small text-muted">{{ $employee->role }}</div>
                @if(!empty($employee->email))<div class="small">{{ $employee->email }}</div>@endif
                @if(!empty($employee->phone))<div class="small">{{ $employee->phone }}</div>@endif
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Deskripsi</th>
                        <th class="text-right" style="width:180px">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Gaji Pokok - {{ $monthName }} {{ $payment->year }}</td>
                        <td class="text-right">{{ 'Rp '.number_format($base,0,',','.') }}</td>
                    </tr>
                    @if($payment->deduction_type && $payment->deduction_value)
                    <tr>
                        <td>
                            Pemotongan @if($payment->deduction_type==='percent') ({{ (float)$payment->deduction_value }}%) @endif
                            @if(!empty($payment->deduction_desc)) - {{ $payment->deduction_desc }} @endif
                        </td>
                        <td class="text-right text-danger">-{{ 'Rp '.number_format($deduction,0,',','.') }}</td>
                    </tr>
                    @endif
                    @if($payment->bonus_type && $payment->bonus_value)
                    <tr>
                        <td>
                            Bonus @if($payment->bonus_type==='percent') ({{ (float)$payment->bonus_value }}%) @endif
                            @if(!empty($payment->bonus_desc)) - {{ $payment->bonus_desc }} @endif
                        </td>
                        <td class="text-right text-success">+{{ 'Rp '.number_format($bonus,0,',','.') }}</td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-right">Subtotal</th>
                        <th class="text-right">{{ 'Rp '.number_format($subtotal,0,',','.') }}</th>
                    </tr>
                    <tr>
                        <th class="text-right">Pajak (0%)</th>
                        <th class="text-right">Rp 0</th>
                    </tr>
                    <tr>
                        <th class="text-right">Total Dibayarkan</th>
                        <th class="text-right">{{ 'Rp '.number_format($net,0,',','.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if(!empty($payment->description))
        <div class="mt-3 small"><strong>Catatan:</strong> {{ $payment->description }}</div>
        @endif
    </div>
</div>
