<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Court;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    public function index(Request $request)
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with('court:id,name,main_image,sport_type')
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'court_id'     => 'required|exists:courts,id',
            'date'         => 'required|date|after_or_equal:today',
            'time_start'   => 'required|date_format:H:i',
            'time_end'     => 'required|date_format:H:i|after:time_start',
            'slots'        => 'required|array|min:1',
            'slots.*'      => 'string|regex:/^\d{2}:\d{2}-\d{2}:\d{2}$/',
            'promo_code'   => 'nullable|string',
            'addon_total'  => 'nullable|numeric|min:0',
            'addon_items'  => 'nullable|array',
            'court_detail' => 'nullable|string',
        ]);

        try {
            $user = Auth::user();
            $cart = [
                'court_id'     => $validated['court_id'],
                'date'         => $validated['date'],
                'time_start'   => $validated['time_start'],
                'time_end'     => $validated['time_end'],
                'slots'        => $validated['slots'],
                'court_detail' => $validated['court_detail'] ?? null,
            ];

            $data = $this->bookingService->processBooking($cart, $validated, $user);
            return response()->json($data, 201);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'SLOT_TAKEN') {
                return response()->json(['error' => 'Slot waktu sudah dipesan.'], 409);
            }
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
