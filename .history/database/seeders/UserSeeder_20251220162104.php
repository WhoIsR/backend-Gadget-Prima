<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Owner
        User::create([
            'name' => 'Bapak Owner',
            'email' => 'owner@gadgetprima.com',
            'password' => Hash::make('password'), // passwordnya: password
            'role' => 'owner',
            'avatar' => null,
        ]);

        // 2. Akun Admin
        User::create([
            'name' => 'Admin Toko',
            'email' => 'admin@gadgetprima.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 3. Akun Gudang
        User::create([
            'name' => 'Staf Gudang',
            'email' => 'gudang@gadgetprima.com',
            'password' => Hash::make('password'),
            'role' => 'gudang',
        ]);

        // 4. Akun Kasir
        User::create([
            'name' => 'Mba Kasir',
            'email' => 'kasir@gadgetprima.com',
            'password' => Hash::make('password'),
            'role' => 'kasir',
        ]);
    }
}
