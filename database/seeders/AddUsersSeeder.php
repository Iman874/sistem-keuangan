<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AddUsersSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'owner@monoframe.com'],
            [
                'name' => 'Owner',
                'password' => Hash::make('password'),
                'role' => 'owner',
            ]
        );

        User::firstOrCreate(
            ['email' => 'kasir@monoframe.com'],
            [
                'name' => 'Kasir',
                'password' => Hash::make('password'),
                'role' => 'kasir',
            ]
        );
    }
}
