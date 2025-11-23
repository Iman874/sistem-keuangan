@extends('kasir.layouts.app')

@section('title','Edit Invoice Multi Transaksi')

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-sm-flex align-items-center justify-content-between">
        <h1 class="mb-0 text-gray-800 h3">Edit Invoice Multi Transaksi</h1>
        <a href="{{ route('kasir.income.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('kasir.invoice.update',$invoice->id) }}">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Nama Pelanggan (opsional)</label>
                        <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name',$invoice->customer_name) }}" />
                    </div>
                    <div class="form-group col-md-4">
                        <label>Email Pelanggan (opsional)</label>
                        <input type="email" name="customer_email" class="form-control" placeholder="email@pelanggan.com" value="{{ old('customer_email',$invoice->customer_email) }}" />
                    </div>
                    <div class="form-group col-md-2">
                        <label>Sesi</label>
                        <select name="session" class="form-control">
                            <option value="pagi" {{ old('session', optional($invoice->incomes->first())->session) == 'pagi' ? 'selected' : '' }}>Pagi</option>
                            <option value="sore" {{ old('session', optional($invoice->incomes->first())->session) == 'sore' ? 'selected' : '' }}>Sore</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Payment</label>
                        <select name="payment_type" class="form-control">
                            <option value="cash" {{ old('payment_type',$invoice->payment_type) == 'cash' ? 'selected' : '' }}>CASH</option>
                            <option value="qris" {{ old('payment_type',$invoice->payment_type) == 'qris' ? 'selected' : '' }}>QRIS</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="items-table">
                        <thead>
                        <tr>
                            <th>Item/Kategori</th>
                            <th>Deskripsi</th>
                            <th class="text-right" style="width: 120px;">Qty</th>
                            <th class="text-right" style="width: 160px;">Harga Satuan</th>
                            <th class="text-right" style="width: 160px;">Total</th>
                            <th style="width: 60px;"></th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                        <tr>
                            <td colspan="6">
                                <button type="button" class="btn btn-sm btn-primary" id="add-row"><i class="fas fa-plus"></i> Tambah Item</button>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="text-right">
                    <h5 class="font-weight-bold">Subtotal: Rp <span id="subtotal">0</span></h5>
                    <h6>Pajak (0%): Rp <span id="tax">0</span></h6>
                    <h4 class="font-weight-bold">Total: Rp <span id="grandTotal">0</span></h4>
                </div>

                <button class="btn btn-success">Simpan Perubahan Invoice</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Data injected safely from controller (already JSON encoded collections):
window.invoiceCategories = @json($categoriesData);
window.invoiceExistingItems = @json($existingItems);
    (function(){
        const categories = window.invoiceCategories || [];
        const existing = window.invoiceExistingItems || [];
        const tbody = document.querySelector('#items-table tbody');
        let rowIndex = 0;
        const fmt = n=> (Math.round(n)).toLocaleString('id-ID');
        const recalc = ()=>{
            let subtotal = 0;
            tbody.querySelectorAll('tr').forEach(tr=>{
                const qty = parseFloat(tr.querySelector('.item-qty').value||0);
                let priceRaw = tr.querySelector('.item-price').value||'';
                priceRaw = priceRaw.replace(/\./g, '');
                priceRaw = priceRaw.split(',')[0];
                const price = parseFloat(priceRaw)||0;
                const total = qty*price;
                tr.querySelector('.item-total').textContent = fmt(total);
                subtotal += total;
            });
            document.getElementById('subtotal').textContent = fmt(subtotal);
            document.getElementById('tax').textContent = fmt(0);
            document.getElementById('grandTotal').textContent = fmt(subtotal);
        };
        const addRow = (data={})=>{
            const tr = document.createElement('tr');
            const options = categories.map(c=>`<option value="${c.id}" ${data.pemasukkan_id==c.id?'selected':''}>${c.name}</option>`).join('');
            const idx = rowIndex++;
            tr.innerHTML = `
                <td><select name="items[${idx}][pemasukkan_id]" class="form-control" required>${options}</select></td>
                <td><input type="text" name="items[${idx}][description]" value="${data.description||''}" class="form-control" placeholder="Keterangan"/></td>
                <td><input type="number" min="1" value="${data.qty||1}" name="items[${idx}][qty]" class="form-control text-right item-qty" required/></td>
                <td><input type="text" inputmode="numeric" value="${((parseInt(data.unit_price)||0).toLocaleString('id-ID'))}" name="items[${idx}][unit_price]" class="form-control text-right item-price rupiah-input" placeholder="0" required/></td>
                <td class="text-right">Rp <span class="item-total">0</span></td>
                <td><button type="button" class="btn btn-sm btn-danger del-row" aria-label="Hapus baris"><i class="fas fa-trash"></i></button></td>
            `;
            tbody.appendChild(tr);
            tr.querySelector('.item-qty').addEventListener('input', recalc);
            tr.querySelector('.item-price').addEventListener('input', recalc);
            tr.querySelector('.del-row').addEventListener('click', ()=>{ tr.remove(); recalc(); });
            recalc();
        };
        document.getElementById('add-row').addEventListener('click', ()=>addRow());
        if(existing.length){
            existing.forEach(r=>addRow(r));
        } else {
            addRow();
        }

        // Pastikan semua unit_price dikirim tanpa titik/koma sebelum submit
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e){
            tbody.querySelectorAll('.item-price').forEach(function(input){
                let val = input.value.replace(/\./g, '');
                val = val.split(',')[0]; // ambil sebelum koma
                input.value = val;
            });
        });
    })();
</script>
@endsection