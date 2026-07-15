<?php

namespace Database\Seeders;

use App\Models\Promo;
use Illuminate\Database\Seeder;

class PromoSeeder extends Seeder
{
    public function run(): void
    {
        $promos = [
            [
                'code'             => 'SBYFUTSAL20',
                'description'      => 'Diskon 20% khusus lapangan Futsal di Surabaya. Syarat & Ketentuan: Hanya berlaku 1x per pengguna.',
                'discount_percent' => 20,
                'court_id'         => 1, // Futsal
                'is_active'        => true,
                'valid_until'      => '2026-12-31',
            ],
            [
                'code'             => 'SBYBADMINTON15',
                'description'      => 'Diskon 15% khusus lapangan Bulutangkis di Surabaya. Syarat & Ketentuan: Hanya berlaku 1x per pengguna.',
                'discount_percent' => 15,
                'court_id'         => 2, // Bulutangkis
                'is_active'        => true,
                'valid_until'      => '2026-12-31',
            ],
            [
                'code'             => 'SBYBASKET10',
                'description'      => 'Diskon 10% khusus lapangan Basket di Surabaya. Syarat & Ketentuan: Hanya berlaku 1x per pengguna.',
                'discount_percent' => 10,
                'court_id'         => 3, // Basket
                'is_active'        => true,
                'valid_until'      => '2026-12-31',
            ],
            [
                'code'             => 'SBYTENIS25',
                'description'      => 'Diskon 25% khusus lapangan Tenis di Surabaya. Syarat & Ketentuan: Hanya berlaku 1x per pengguna.',
                'discount_percent' => 25,
                'court_id'         => 4, // Tenis
                'is_active'        => true,
                'valid_until'      => '2026-12-31',
            ],
            [
                'code'             => 'SBYALL10',
                'description'      => 'Diskon 10% untuk semua lapangan di Surabaya. Syarat & Ketentuan: Hanya berlaku 1x per pengguna.',
                'discount_percent' => 10,
                'court_id'         => null, // Semua
                'is_active'        => true,
                'valid_until'      => '2026-12-31',
            ],
        ];

        foreach ($promos as $promo) {
            Promo::updateOrCreate(['code' => $promo['code']], $promo);
        }
    }
}
