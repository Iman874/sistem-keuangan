<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'owner@gmail.com'],
            [
                'name' => 'owner',
                'password' => Hash::make('12345678'),
                'role' => 'owner',
            ]
        );
    }
}