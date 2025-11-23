<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KasirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'kasir@gmail.com'],
            [
                'name' => 'kasir',
                'password' => Hash::make('12345678'),
                'role' => 'kasir',
            ]
        );
    }
}