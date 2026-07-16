<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Users (Admin, Venue Owner, Customer)
        $this->call(UserSeeder::class);

        // Seed courts
        $this->call(CourtSeeder::class);

        // Seed promos & equipment setelah courts ada
        $this->call(PromoSeeder::class);
        $this->call(EquipmentSeeder::class);
    }
}

