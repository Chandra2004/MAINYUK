<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Court;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_review()
    {
        $user = User::factory()->create();
        $court = Court::create([
            'name' => 'Test Court',
            'sport_type' => 'futsal',
            'location' => 'Test Location',
            'city' => 'Surabaya',
            'address' => 'Test Address',
            'price_per_hour' => 100000,
            'is_active' => true,
        ]);
        
        $booking = Booking::create([
            'user_id' => $user->id,
            'court_id' => $court->id,
            'date' => '2023-10-10',
            'time_start' => '10:00',
            'time_end' => '11:00',
            'duration_hours' => 1,
            'subtotal' => 100000,
            'total_price' => 100000,
            'paid_amount' => 100000,
            'pay_type' => 'lunas',
            'status' => 'completed',
            'items' => ['10:00'],
        ]);
        
        $response = $this->actingAs($user)->post('/reviews', [
            'booking_id' => $booking->id,
            'rating' => 5,
            'comment' => 'Bagus sekali',
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'rating' => 5,
            'comment' => 'Bagus sekali',
        ]);
    }
}
