<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Modal;
use Carbon\Carbon;

class ModalSeeder extends Seeder
{
    public function run(): void
    {
        // Total target modal: 50,000,000
        // Dibagi ke beberapa aset contoh.
        $items = [
            [
                'nama_barang' => 'Mesin Produksi A',
                'harga' => 18_000_000,
                'tanggal' => Carbon::today()->subDays(20)->toDateString(),
                'deskripsi' => 'Mesin utama untuk proses produksi harian.'
            ],
            [
                'nama_barang' => 'Perangkat Komputer Desain',
                'harga' => 12_000_000,
                'tanggal' => Carbon::today()->subDays(18)->toDateString(),
                'deskripsi' => 'PC high-end untuk desain dan rendering.'
            ],
            [
                'nama_barang' => 'Peralatan Kantor & Furnitur',
                'harga' => 5_000_000,
                'tanggal' => Carbon::today()->subDays(15)->toDateString(),
                'deskripsi' => 'Meja, kursi ergonomis, rak dokumen.'
            ],
            [
                'nama_barang' => 'Perangkat Jaringan & Server',
                'harga' => 7_000_000,
                'tanggal' => Carbon::today()->subDays(12)->toDateString(),
                'deskripsi' => 'Router, switch, NAS backup.'
            ],
            [
                'nama_barang' => 'Peralatan Pendukung Operasional',
                'harga' => 8_000_000,
                'tanggal' => Carbon::today()->subDays(10)->toDateString(),
                'deskripsi' => 'Kamera, alat dokumentasi, tools minor.'
            ],
        ];

        // Total = 18 + 12 + 5 + 7 + 8 = 50 juta

        foreach ($items as $item) {
            Modal::updateOrCreate(
                [
                    'nama_barang' => $item['nama_barang'],
                    'tanggal' => $item['tanggal'],
                ],
                [
                    'harga' => $item['harga'],
                    'deskripsi' => $item['deskripsi'],
                ]
            );
        }
    }
}
