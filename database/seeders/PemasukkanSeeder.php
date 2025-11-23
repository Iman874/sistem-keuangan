<?php

namespace Database\Seeders;

use App\Models\Pemasukkan;
use Illuminate\Database\Seeder;

class PemasukkanSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Penjualan Barang',
            'Jasa Layanan',
            'Pendapatan Lain-lain',
            'Diskon/Refund Masuk',
        ];

        foreach ($categories as $name) {
            Pemasukkan::firstOrCreate([
                'nama_pemasukkan' => $name,
            ]);
        }
    }
}
