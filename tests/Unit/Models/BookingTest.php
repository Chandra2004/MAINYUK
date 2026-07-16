<?php

namespace Tests\Unit\Models;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_addon_total_attribute()
    {
        $booking = new Booking();
        $booking->addon_items = [
            ['qty' => 2, 'price_per_unit' => 10000],
            ['qty' => 1, 'price' => 20000], // fallback to price
        ];

        // (2 * 10000) + (1 * 20000) = 40000
        $this->assertEquals(40000, $booking->addon_total);
    }

    public function test_is_cancellable_for_pending_booking()
    {
        $booking = new Booking(['status' => 'pending']);
        
        $this->assertTrue($booking->isCancellable());
    }

    public function test_is_cancellable_for_active_booking_in_future()
    {
        $booking = new Booking([
            'status' => 'active',
            'date' => Carbon::tomorrow()->format('Y-m-d'),
            'time_start' => '10:00:00',
        ]);
        
        // Mock the date casting internally
        $booking->setAttribute('date', Carbon::tomorrow());
        
        $this->assertTrue($booking->isCancellable());
    }

    public function test_is_cancellable_for_completed_booking()
    {
        $booking = new Booking(['status' => 'completed']);
        $this->assertFalse($booking->isCancellable());
    }
}
