<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Admin (Platform Owner / Superadmin)
        User::updateOrCreate(
            ['email' => 'admin@mainyuk.id'],
            [
                'name'     => 'Admin MainYuk',
                'username' => 'admin_mainyuk',
                'password' => Hash::make('MainYuk2026!'),
                'phone'    => '08111111111',
                'role'     => 'admin',
            ]
        );

        // 2. Akun Venue Owner (Mitra / Pengelola Lapangan)
        User::updateOrCreate(
            ['email' => 'mitra@mainyuk.id'],
            [   
                'name'     => 'Mitra Arena Futsal',
                'username' => 'mitra_arena',
                'password' => Hash::make('MainYuk2026!'),
                'phone'    => '08222222222',
                'role'     => 'venue_owner',
            ]
        );

        // 3. Akun User (Customer / Penyewa Lapangan)
        User::updateOrCreate(
            ['email' => 'chandratriantomo123@gmail.com'],
            [
                'name'     => 'Chandra Tri Antomo',
                'username' => 'chanzz',
                'password' => Hash::make('MainYuk2026!'),
                'phone' => '085730676143',
                'role'     => 'user',
            ]
        );
    }
}
