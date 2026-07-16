<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
        ]);

        $booking = Booking::with('court')->findOrFail($validated['booking_id']);

        // Pastikan booking milik user yang sedang login
        if ($booking->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak berhak mengulas pesanan ini.');
        }

        // Hanya pesanan yang sudah selesai yang bisa diulas
        if ($booking->status !== 'completed') {
            return back()->with('error', 'Ulasan hanya bisa diberikan untuk pesanan yang sudah selesai dimainkan.');
        }

        // Satu pesanan hanya bisa diulas satu kali
        if ($booking->review()->exists()) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk pesanan ini.');
        }

        Review::create([
            'user_id'    => Auth::id(),
            'court_id'   => $booking->court_id,
            'booking_id' => $booking->id,
            'rating'     => $validated['rating'],
            'comment'    => $validated['comment'],
        ]);

        // Hitung ulang rating lapangan
        $booking->court->recalculateRating();

        return back()->with('success', 'Ulasan berhasil dikirim. Terima kasih!');
    }
}
