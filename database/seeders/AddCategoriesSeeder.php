<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class AddCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $dailyCategories = [
            'ATK', 'Transportasi', 'Konsumsi', 'Kebersihan', 'Lain-lain',
        ];
        foreach ($dailyCategories as $category) {
            ExpenseCategory::firstOrCreate(
                ['name' => $category, 'type' => 'harian'],
                ['created_by' => 'system', 'is_active' => true]
            );
        }

        $monthlyCategories = [
            'Listrik', 'Air', 'Internet', 'Sewa', 'Gaji', 'Wifi', 'Maintenance',
        ];
        foreach ($monthlyCategories as $category) {
            ExpenseCategory::firstOrCreate(
                ['name' => $category, 'type' => 'bulanan'],
                ['created_by' => 'system', 'is_active' => true]
            );
        }
    }
}
