<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Item; // Pastikan import Model Item
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Kasir
        User::create([
            'name' => 'Admin Kasir',
            'email' => 'kasir@kantinjaya.com',
            'password' => Hash::make('password'),
            'role' => 'kasir',
        ]);

        // 2. Akun Dapur
        User::create([
            'name' => 'Chef Juna',
            'email' => 'dapur@kantinjaya.com',
            'password' => Hash::make('password'),
            'role' => 'dapur',
        ]);

        // 3. Akun Pembeli (Contoh)
        User::create([
            'name' => 'Mahasiswa Lapar',
            'email' => 'pembeli@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'pembeli',
        ]);

        // 4. Data Dummy Makanan (Agar tidak kosong pas demo)
        Item::create(['nama' => 'Nasi Goreng Spesial', 'harga' => 15000, 'stok' => 50]);
        Item::create(['nama' => 'Ayam Geprek', 'harga' => 12000, 'stok' => 30]);
        Item::create(['nama' => 'Es Teh Manis', 'harga' => 3000, 'stok' => 100]);
    }
}