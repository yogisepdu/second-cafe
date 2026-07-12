<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@secondcafe.com',
            ],
            [
                'name' => 'Admin Second Cafe',
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
                'is_active' => true,
            ],
        );

        User::updateOrCreate(
            [
                'email' => 'kasir@secondcafe.com',
            ],
            [
                'name' => 'Kasir Second Cafe',
                'password' => Hash::make('kasir12345'),
                'role' => 'cashier',
                'is_active' => true,
            ],
        );
    }
}
