<?php

namespace Database\Seeders; // 🔥 Tambahkan namespace yang benar

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('testing123'),
            'role' => 'admin', // Role admin
        ]);
    }
}
