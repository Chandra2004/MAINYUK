<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Promo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class PromoTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_valid_returns_promo_when_valid()
    {
        $promo = Promo::create([
            'code' => 'TESTPROMO',
            'discount_percent' => 10,
            'valid_until' => Carbon::tomorrow(),
            'is_active' => true,
            'description' => 'Test Promo',
        ]);

        $validPromo = Promo::findValid('TESTPROMO');

        $this->assertNotNull($validPromo);
        $this->assertEquals($promo->id, $validPromo->id);
    }

    public function test_find_valid_returns_null_when_expired()
    {
        $promo = Promo::create([
            'code' => 'EXPIRED',
            'discount_percent' => 10,
            'valid_until' => Carbon::yesterday(),
            'is_active' => true,
            'description' => 'Expired Promo',
        ]);

        $validPromo = Promo::findValid('EXPIRED');

        $this->assertNull($validPromo);
    }

}
