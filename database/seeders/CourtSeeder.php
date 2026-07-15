<?php

namespace Database\Seeders;

use App\Models\Court;
use Illuminate\Database\Seeder;

class CourtSeeder extends Seeder
{
    public function run(): void
    {
        $courts = [
            [
                'name'          => 'Futsal Arena Premium Surabaya',
                'sport_type'    => 'futsal',
                'location'      => 'surabaya',
                'city'          => 'Surabaya',
                'address'       => 'Jl. Raya Darmo No. 45, Surabaya',
                'price_per_hour'=> 150000,
                'rating'        => 4.9,
                'total_reviews' => 124,
                'description'   => 'Lapangan futsal premium dengan rumput sintetis berkualitas FIFA Standard. Tersedia 8 lapangan berstandar turnamen.',
                'facilities'    => ['WiFi', 'Parkir', 'Toilet', 'AC', 'Kantin', 'Ruang Ganti'],
                'images'        => [
                    'https://images.unsplash.com/photo-1529900748604-07564a03e7a6?w=800&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=800&auto=format&fit=crop',
                ],
                'courts_detail' => [
                    ['id' => 'L1', 'name' => 'Lapangan 1', 'price' => 150000],
                    ['id' => 'L2', 'name' => 'Lapangan 2', 'price' => 150000],
                    ['id' => 'L3', 'name' => 'Lapangan 3', 'price' => 150000],
                    ['id' => 'L4', 'name' => 'Lapangan 4', 'price' => 150000],
                    ['id' => 'L5', 'name' => 'Lapangan 5', 'price' => 150000],
                    ['id' => 'L6', 'name' => 'Lapangan 6', 'price' => 150000],
                    ['id' => 'L7', 'name' => 'Lapangan 7', 'price' => 150000],
                    ['id' => 'L8', 'name' => 'Lapangan 8', 'price' => 150000],
                ],
                'open_time'  => '06:00:00',
                'close_time' => '23:00:00',
            ],
            [
                'name'          => 'Bulu Tangkis Pro Center Surabaya',
                'sport_type'    => 'badminton',
                'location'      => 'surabaya',
                'city'          => 'Surabaya',
                'address'       => 'Jl. Ahmad Yani No. 123, Surabaya',
                'price_per_hour'=> 80000,
                'rating'        => 4.8,
                'total_reviews' => 98,
                'description'   => 'GOR Badminton profesional dengan 4 lapangan standar BWF. Cocok untuk latihan rutin hingga turnamen.',
                'facilities'    => ['WiFi', 'Parkir', 'Toilet', 'Kantin', 'Ruang Ganti', 'Tribun'],
                'images'        => [
                    'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=800&auto=format&fit=crop',
                ],
                'courts_detail' => [
                    ['id' => 'L1', 'name' => 'Lapangan 1', 'price' => 80000],
                    ['id' => 'L2', 'name' => 'Lapangan 2', 'price' => 80000],
                    ['id' => 'L3', 'name' => 'Lapangan 3', 'price' => 80000],
                    ['id' => 'L4', 'name' => 'Lapangan 4', 'price' => 80000],
                ],
                'open_time'  => '06:00:00',
                'close_time' => '23:00:00',
            ],
            [
                'name'          => 'Basket Hall Artemis Surabaya',
                'sport_type'    => 'basket',
                'location'      => 'surabaya',
                'city'          => 'Surabaya',
                'address'       => 'Jl. Pemuda No. 17, Surabaya',
                'price_per_hour'=> 120000,
                'rating'        => 4.8,
                'total_reviews' => 76,
                'description'   => 'Arena basket indoor dengan lantai parket premium. Tersedia papan pantul adjustable dan sistem pencahayaan profesional.',
                'facilities'    => ['WiFi', 'Parkir', 'AC', 'Toilet', 'Loker', 'Tribun'],
                'images'        => [
                    'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=800&auto=format&fit=crop',
                ],
                'courts_detail' => [
                    ['id' => 'L1', 'name' => 'Lapangan 1', 'price' => 120000],
                    ['id' => 'L2', 'name' => 'Lapangan 2', 'price' => 120000],
                    ['id' => 'L3', 'name' => 'Lapangan 3', 'price' => 120000],
                    ['id' => 'L4', 'name' => 'Lapangan 4', 'price' => 120000],
                ],
                'open_time'  => '06:00:00',
                'close_time' => '23:00:00',
            ],
            [
                'name'          => 'Tenis Club Royale Surabaya',
                'sport_type'    => 'tenis',
                'location'      => 'surabaya',
                'city'          => 'Surabaya',
                'address'       => 'Jl. Basuki Rahmat No. 88, Surabaya',
                'price_per_hour'=> 200000,
                'rating'        => 4.9,
                'total_reviews' => 58,
                'description'   => 'Lapangan tenis kelas dunia. Tersedia coach profesional.',
                'facilities'    => ['WiFi', 'Parkir', 'Shower', 'Loker', 'Kantin', 'Pro Shop'],
                'images'        => [
                    'https://images.unsplash.com/photo-1554068865-24cecd4e34b8?w=800&auto=format&fit=crop',
                ],
                'courts_detail' => [
                    ['id' => 'L1', 'name' => 'Clay Court 1', 'price' => 200000],
                    ['id' => 'L2', 'name' => 'Clay Court 2', 'price' => 200000],
                    ['id' => 'L3', 'name' => 'Hard Court 1', 'price' => 180000],
                    ['id' => 'L4', 'name' => 'Hard Court 2', 'price' => 180000],
                ],
                'open_time'  => '06:00:00',
                'close_time' => '23:00:00',
            ],
            [
                'name'          => 'Padel Club Elite Surabaya',
                'sport_type'    => 'padel',
                'location'      => 'surabaya',
                'city'          => 'Surabaya',
                'address'       => 'Jl. Pluit Raya No. 200, Surabaya',
                'price_per_hour'=> 180000,
                'rating'        => 4.7,
                'total_reviews' => 43,
                'description'   => 'Lapangan padel pertama dan terbaik di Surabaya. Dilengkapi tempered glass court internasional.',
                'facilities'    => ['WiFi', 'Parkir', 'AC', 'Shower', 'Loker', 'Kafe'],
                'images'        => [
                    'https://images.unsplash.com/photo-1632863720046-0efd6a0a8cce?w=800&auto=format&fit=crop',
                ],
                'courts_detail' => [
                    ['id' => 'L1', 'name' => 'Court A', 'price' => 180000],
                    ['id' => 'L2', 'name' => 'Court B', 'price' => 180000],
                    ['id' => 'L3', 'name' => 'Court C', 'price' => 180000],
                    ['id' => 'L4', 'name' => 'Court D', 'price' => 180000],
                ],
                'open_time'  => '06:00:00',
                'close_time' => '23:00:00',
            ],
            [
                'name'          => 'Voli Pantai Senggigi Surabaya',
                'sport_type'    => 'voli',
                'location'      => 'surabaya',
                'city'          => 'Surabaya',
                'address'       => 'Pantai Kenjeran, Surabaya',
                'price_per_hour'=> 90000,
                'rating'        => 4.8,
                'total_reviews' => 38,
                'description'   => 'Lapangan voli pantai dengan view laut yang memukau. Tersedia lapangan pasir buatan.',
                'facilities'    => ['Parkir', 'Toilet', 'Kantin', 'Shower'],
                'images'        => [
                    'https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?w=800&auto=format&fit=crop',
                ],
                'courts_detail' => [
                    ['id' => 'L1', 'name' => 'Court Pantai 1', 'price' => 90000],
                    ['id' => 'L2', 'name' => 'Court Pantai 2', 'price' => 90000],
                    ['id' => 'L3', 'name' => 'Court Pantai 3', 'price' => 90000],
                    ['id' => 'L4', 'name' => 'Court Pantai 4', 'price' => 90000],
                ],
                'open_time'  => '07:00:00',
                'close_time' => '23:00:00',
            ],
        ];

        foreach ($courts as $court) {
            Court::create($court);
        }
    }
}
