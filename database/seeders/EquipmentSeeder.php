<?php

namespace Database\Seeders;

use App\Models\Court;
use App\Models\Equipment;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $courts = Court::all();

        // Peralatan default untuk setiap lapangan berdasarkan tipe olahraga
        $equipmentBySport = [
            'futsal' => [
                ['name' => 'Bola Futsal', 'icon' => 'fa-solid fa-futbol', 'description' => 'Bola Molten Original', 'price_per_unit' => 15000],
                ['name' => 'Rompi Tim', 'icon' => 'fa-solid fa-shirt', 'description' => 'Rompi berwarna untuk identifikasi tim', 'price_per_unit' => 5000],
            ],
            'bulutangkis' => [
                ['name' => 'Raket Badminton', 'icon' => 'fa-solid fa-table-tennis-paddle-ball', 'description' => 'Raket Yonex kondisi baik', 'price_per_unit' => 25000],
                ['name' => 'Shuttlecock (6 pcs)', 'icon' => 'fa-solid fa-feather', 'description' => 'Cock bulu angsa grade A', 'price_per_unit' => 20000],
                ['name' => 'Sepatu Badminton', 'icon' => 'fa-solid fa-shoe-prints', 'description' => 'Berbagai ukuran tersedia', 'price_per_unit' => 30000],
            ],
            'basket' => [
                ['name' => 'Bola Basket', 'icon' => 'fa-solid fa-basketball', 'description' => 'Bola Spalding original', 'price_per_unit' => 20000],
            ],
            'tenis' => [
                ['name' => 'Raket Tenis', 'icon' => 'fa-solid fa-table-tennis-paddle-ball', 'description' => 'Raket Wilson all-around', 'price_per_unit' => 35000],
                ['name' => 'Bola Tenis (3 pcs)', 'icon' => 'fa-solid fa-circle', 'description' => 'Bola tenis tekanan normal', 'price_per_unit' => 15000],
            ],
            'padel' => [
                ['name' => 'Raket Padel', 'icon' => 'fa-solid fa-table-tennis-paddle-ball', 'description' => 'Raket padel khusus', 'price_per_unit' => 40000],
                ['name' => 'Bola Padel (3 pcs)', 'icon' => 'fa-solid fa-circle', 'description' => 'Bola tekanan rendah padel', 'price_per_unit' => 15000],
            ],
        ];

        foreach ($courts as $court) {
            $items = $equipmentBySport[$court->sport_type] ?? [];

            foreach ($items as $item) {
                Equipment::firstOrCreate(
                    ['court_id' => $court->id, 'name' => $item['name']],
                    array_merge($item, ['court_id' => $court->id, 'stock' => 10, 'is_active' => true])
                );
            }
        }
    }
}
