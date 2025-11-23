<div class="card shadow">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
            <div class="d-flex align-items-center">
                <img src="{{ asset('assets/img/monoframe.png') }}" height="40" class="mr-2">
                <div>
                    <div class="text-muted">MONO FRAME</div>
                    <h3 class="mb-0">INVOICE</h3>
                </div>
            </div>
            <div class="text-right text-muted">
                <div>Tanggal: {{ $invoice->date->format('d/m/Y') }}</div>
                <div>No Invoice: {{ $invoice->number }}</div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <div class="p-3 bg-light rounded">
                    <div class="small text-muted">KEPADA:</div>
                    <div>{{ $invoice->customer_name ?? '-' }}</div>
                    <div class="small text-muted">{{ $invoice->customer_email }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 bg-light rounded">
                    <div class="small text-muted">KASIR:</div>
                    <div>{{ $invoice->cashier->name }}</div>
                    <div class="small text-muted">Pembayaran: {{ strtoupper($invoice->payment_type ?? '-') }}</div>
                </div>
            </div>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>KETERANGAN</th>
                        <th class="text-right">HARGA</th>
                        <th class="text-right">JML</th>
                        <th class="text-right">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                @if($invoice->type==='income')
                    @foreach($invoice->incomes as $row)
                        <tr>
                            <td>{{ $row->category->nama_pemasukkan ?? $row->description }}</td>
                            <td class="text-right">Rp {{ number_format($row->unit_price ?? $row->amount,0,',','.') }}</td>
                            <td class="text-right">{{ $row->qty ?? 1 }}</td>
                            <td class="text-right">Rp {{ number_format($row->amount,0,',','.') }}</td>
                        </tr>
                    @endforeach
                @else
                    @foreach($invoice->expends as $row)
                        <tr>
                            <td>{{ $row->description }}</td>
                            <td class="text-right">Rp {{ number_format($row->amount,0,',','.') }}</td>
                            <td class="text-right">1</td>
                            <td class="text-right">Rp {{ number_format($row->amount,0,',','.') }}</td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right">SUB TOTAL</td>
                        <td class="text-right">Rp {{ number_format($invoice->subtotal,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right">PAJAK</td>
                        <td class="text-right">Rp {{ number_format($invoice->tax,0,',','.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right">TOTAL</td>
                        <td class="text-right">Rp {{ number_format($invoice->total,0,',','.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
