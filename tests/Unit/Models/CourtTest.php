<?php

namespace Tests\Unit\Models;

use App\Models\Court;
use PHPUnit\Framework\TestCase;

class CourtTest extends TestCase
{
    public function test_calculate_price_for_slots_weekday_regular_hours()
    {
        $court = new Court(['price_per_hour' => 100000]);

        // Weekday (Senin) jam 10:00 (bukan peak hour)
        $date = '2023-10-02'; // Senin
        $slots = ['10:00', '11:00'];

        $price = $court->calculatePriceForSlots($slots, $date);

        // 2 jam x 100.000 = 200.000
        $this->assertEquals(200000, $price);
    }

    public function test_calculate_price_for_slots_weekday_peak_hours()
    {
        $court = new Court(['price_per_hour' => 100000]);

        // Weekday (Senin) jam 18:00 (peak hour, +20%)
        $date = '2023-10-02'; // Senin
        $slots = ['18:00', '19:00'];

        $price = $court->calculatePriceForSlots($slots, $date);

        // 2 jam x 120.000 = 240.000
        $this->assertEquals(240000, $price);
    }

    public function test_calculate_price_for_slots_weekend()
    {
        $court = new Court(['price_per_hour' => 100000]);

        // Weekend (Sabtu) jam 10:00 (+20%)
        $date = '2023-10-07'; // Sabtu
        $slots = ['10:00', '11:00'];

        $price = $court->calculatePriceForSlots($slots, $date);

        // 2 jam x 120.000 = 240.000
        $this->assertEquals(240000, $price);
    }
}
