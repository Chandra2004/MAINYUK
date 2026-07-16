<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Court;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_schedule_page_loads_for_authenticated_users()
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

        $response = $this->actingAs($user)->get('/booking/' . $court->id);
        
        $response->assertStatus(200);
    }

    public function test_add_to_cart_requires_auth()
    {
        $court = Court::create([
            'name' => 'Test Court',
            'sport_type' => 'futsal',
            'location' => 'Test Location',
            'city' => 'Surabaya',
            'address' => 'Test Address',
            'price_per_hour' => 100000,
            'is_active' => true,
        ]);

        $response = $this->post('/booking/cart', [
            'court_id' => $court->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'time_start' => '10:00',
            'time_end' => '11:00',
            'slots' => ['10:00'],
        ]);

        $response->assertRedirect('/login');
    }

    public function test_add_to_cart_success()
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

        $response = $this->actingAs($user)->post('/booking/cart', [
            'court_id' => $court->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'time_start' => '10:00',
            'time_end' => '11:00',
            'slots' => ['10:00'],
        ]);

        $response->assertRedirect(route('booking.cart'));
        $this->assertTrue(session()->has('booking_cart'));
    }
}
