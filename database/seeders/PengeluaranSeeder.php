<?php

namespace Database\Seeders;

use App\Models\Expend;
use App\Models\ExpenseCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PengeluaranSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user kasir pertama; fallback owner
        $kasir = User::where('role', 'kasir')->first() ?? User::where('role', 'owner')->first();
        if (!$kasir) {
            return;
        }

        // Pastikan ada kategori pengeluaran harian default
        $category = ExpenseCategory::firstOrCreate([
            'name' => 'ATK',
            'type' => 'harian',
        ], [
            'created_by' => 'system',
            'is_active' => true,
        ]);

        $start = Carbon::today()->subDays(6);
        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);
            // Total 500.000 per hari, bagi jadi 2 sesi
            $half = 250_000;

            // Sesi pagi
            Expend::create([
                'user_id' => $kasir->id,
                'session' => 'pagi',
                'amount' => $half,
                'date' => $date->toDateString(),
                'time' => $date->copy()->setTime(10, 0, 0),
                'type' => 'harian',
                'category_id' => $category->id,
                'description' => 'Pengeluaran ATK (pagi)',
            ]);

            // Sesi sore
            Expend::create([
                'user_id' => $kasir->id,
                'session' => 'sore',
                'amount' => $half,
                'date' => $date->toDateString(),
                'time' => $date->copy()->setTime(15, 30, 0),
                'type' => 'harian',
                'category_id' => $category->id,
                'description' => 'Pengeluaran ATK (sore)',
            ]);
        }
    }
}
