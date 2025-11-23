<div class="mb-4 col-xl-3 col-md-6">
    <div class="py-2 shadow card border-left-primary h-100">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="mr-2 col">
                    <div class="mb-1 text-xs font-weight-bold text-primary text-uppercase">
                        Pemasukkan QRIS Hari Ini</div>
                    <div class="mb-0 text-gray-800 h5 font-weight-bold">Rp {{ number_format($todayQrisIncome ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="col-auto">
                    <i class="text-gray-300 fas fa-qrcode fa-2x"></i>
                </div>
            </div>
        </div>
    </div>
</div>
