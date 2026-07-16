<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Models\User;
use App\Models\Court;
use App\Models\Booking;
use App\Services\BookingService;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_booking_success()
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

        $cart = [
            'court_id' => $court->id,
            'date' => '2023-10-10',
            'time_start' => '10:00',
            'time_end' => '12:00',
            'slots' => ['10:00', '11:00'],
        ];

        $validated = [];

        // Mock MidtransService
        $midtransMock = Mockery::mock(MidtransService::class);
        $midtransMock->shouldReceive('createTransaction')
            ->once()
            ->andReturn(['snap_token' => 'mocked-snap-token', 'redirect_url' => 'http://mocked']);

        $bookingService = new BookingService($midtransMock);

        $result = $bookingService->processBooking($cart, $validated, $user);

        $this->assertEquals('mocked-snap-token', $result['snap_token']);
        
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'court_id' => $court->id,
            'status' => 'pending',
        ]);
    }

    public function test_process_booking_overlap_throws_exception()
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

        // Create existing booking
        Booking::create([
            'user_id' => $user->id,
            'court_id' => $court->id,
            'date' => '2023-10-10',
            'time_start' => '10:00',
            'time_end' => '11:00',
            'duration_hours' => 1,
            'subtotal' => 100000,
            'total_price' => 100000,
            'paid_amount' => 0,
            'pay_type' => 'lunas',
            'status' => 'active',
            'items' => ['10:00'],
        ]);

        $cart = [
            'court_id' => $court->id,
            'date' => '2023-10-10',
            'time_start' => '10:00',
            'time_end' => '12:00',
            'slots' => ['10:00', '11:00'],
        ];

        $midtransMock = Mockery::mock(MidtransService::class);
        $bookingService = new BookingService($midtransMock);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('SLOT_TAKEN');

        $bookingService->processBooking($cart, [], $user);
    }
}
