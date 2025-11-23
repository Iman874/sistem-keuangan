<?php

namespace Database\Seeders;

use App\Models\Income;
use App\Models\Pemasukkan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PemasukanSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user kasir pertama sebagai pemilik transaksi; fallback ke owner jika tidak ada
        $kasir = User::where('role', 'kasir')->first() ?? User::where('role', 'owner')->first();
        if (!$kasir) {
            // Jika tidak ada user, hentikan lebih awal
            return;
        }

        // Pastikan ada minimal satu kategori pemasukkan
        $category = Pemasukkan::firstOrCreate(['nama_pemasukkan' => 'Penjualan Barang']);

        // 7 hari terakhir termasuk hari ini
        $start = Carbon::today()->subDays(6);
        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);

            // Bagi pemasukan 1.000.000 per hari menjadi dua sesi agar realistis
            $half = 500_000;

            // Sesi pagi
            Income::create([
                'user_id' => $kasir->id,
                'pemasukkan_id' => $category->id,
                'session' => 'pagi',
                'amount' => $half,
                'date' => $date->toDateString(),
                'time' => $date->copy()->setTime(9, 0, 0),
                'description' => 'Pemasukan penjualan (pagi)',
                'payment_type' => 'cash',
            ]);

            // Sesi sore
            Income::create([
                'user_id' => $kasir->id,
                'pemasukkan_id' => $category->id,
                'session' => 'sore',
                'amount' => $half,
                'date' => $date->toDateString(),
                'time' => $date->copy()->setTime(16, 0, 0),
                'description' => 'Pemasukan penjualan (sore)',
                'payment_type' => 'cash',
            ]);
        }
    }
}
