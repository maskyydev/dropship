<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // ✅ Tambahkan ini
use Illuminate\Support\Facades\Hash; // ✅ Dan ini

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@dropship.com',
            'password' => Hash::make('admin1234'),
            'role' => 'admin',
            'is_subscribed' => true,
            'subscription_expiry' => now()->addYear(),
        ]);
    }
}
