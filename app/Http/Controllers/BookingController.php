<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Court;
use App\Models\Equipment;
use App\Models\Notification;
use App\Models\Promo;
use App\Services\MidtransService;
use App\Services\PdfService;
use App\Services\BookingService;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\ProcessPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function __construct(
        private MidtransService $midtrans,
        private PdfService      $pdf,
        private BookingService  $bookingService,
    ) {}

    /**
     * Get weather forecast for a court on a specific date.
     */
    public function weather(Request $request, Court $court, \App\Services\WeatherService $weatherService)
    {
        $date = $request->query('date', now()->format('Y-m-d'));
        
        if (!$court->latitude || !$court->longitude) {
            return response()->json(null);
        }

        $weather = $weatherService->getForecast($court->latitude, $court->longitude, $date);
        
        return response()->json($weather);
    }

    /**
     * Booking Schedule page.
     */
    public function schedule(Court $court)
    {
        $today   = now()->toDateString();
        $maxDate = now()->copy()->addDays(30)->toDateString();

        $bookedSlots = Booking::bookedForCourt($court->id, $today, $maxDate)
            ->get(['date', 'time_start', 'time_end', 'court_detail', 'items'])
            ->toArray();

        return Inertia::render('BookingSchedule', [
            'court'       => [
                'id'            => $court->id,
                'name'          => $court->name,
                'sport_type'    => $court->sport_type,
                'city'          => $court->city,
                'price_per_hour'=> $court->price_per_hour,
                'courts_detail' => $court->courts_detail ?? [],
                'open_time'     => $court->open_time,
                'close_time'    => $court->close_time,
                'main_image'    => $court->main_image,
            ],
            'bookedSlots' => $bookedSlots,
        ]);
    }

    /**
     * Store cart in session.
     */
    public function addToCart(StoreBookingRequest $request)
    {
        $validated = $request->validated();

        $courtId = $validated['court_id'];
        $slots   = $validated['slots'];

        session(['booking_cart' => [
            'court_id'     => $courtId,
            'date'         => $validated['date'],
            'time_start'   => $validated['time_start'],
            'time_end'     => $validated['time_end'],
            'slots'        => $slots,
            'court_detail' => $validated['court_detail'] ?? null,
        ]]);

        return redirect()->route('booking.cart');
    }

    /**
     * Cart page — sertakan equipment dari DB.
     */
    public function cart()
    {
        $cart = session('booking_cart');

        if (!$cart) {
            return redirect()->route('dashboard')->with('error', 'Keranjang kosong.');
        }

        $court    = Court::with('equipment')->findOrFail($cart['court_id']);
        $subtotal = $court->calculatePriceForSlots($cart['slots'], $cart['date'] ?? null);

        $usedPromoCodes = \App\Models\Booking::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->whereNotIn('status', ['cancelled'])
            ->whereNotNull('promo_code')
            ->pluck('promo_code');

        $promos = \App\Models\Promo::active()
            ->where(function($q) use ($court) {
                $q->whereNull('court_id')->orWhere('court_id', $court->id);
            })
            ->whereNotIn('code', $usedPromoCodes)
            ->get();

        return Inertia::render('BookingCart', [
            'cart'  => $cart,
            'court' => [
                'id'         => $court->id,
                'name'       => $court->name,
                'city'       => $court->city,
                'main_image' => $court->main_image,
            ],
            'summary' => [
                'hours'    => count($cart['slots']),
                'subtotal' => $subtotal,
                'discount' => 0,
                'total'    => $subtotal,
            ],
            'available_promos' => $promos,
            // Peralatan dari DB, bukan hard-coded
            'equipment' => $court->equipment->map(fn ($e) => [
                'id'             => $e->id,
                'name'           => $e->name,
                'icon'           => $e->icon,
                'description'    => $e->description,
                'price_per_unit' => $e->price_per_unit,
                'stock'          => $e->stock,
            ])->values(),
        ]);
    }

    /**
     * Validate promo code via AJAX.
     */
    public function validatePromo(Request $request)
    {
        $request->validate([
            'code'     => 'required|string|max:30',
            'court_id' => 'required|exists:courts,id',
        ]);

        $promo = Promo::findValid($request->code, (int) $request->court_id);

        if ($promo) {
            $used = \App\Models\Booking::where('user_id', Auth::id())
                ->where('promo_code', $promo->code)
                ->whereNotIn('status', ['cancelled'])
                ->exists();
                
            if ($used) {
                return response()->json(['valid' => false, 'message' => 'Anda sudah pernah menggunakan kode promo ini.']);
            }
        }

        if (!$promo) {
            return response()->json(['valid' => false, 'message' => 'Kode promo tidak valid atau sudah kadaluarsa.']);
        }

        return response()->json([
            'valid'            => true,
            'discount_percent' => $promo->discount_percent,
            'description'      => $promo->description,
            'message'          => "✅ Promo \"{$promo->code}\" — diskon {$promo->discount_percent}% berhasil diterapkan!",
        ]);
    }

    /**
     * Payment page — kalkulasi dari session cart.
     * Hanya mendukung pembayaran LUNAS (100%).
     */
    public function payment(Request $request)
    {
        $cart = session('booking_cart');
        if (!$cart) {
            return redirect()->route('dashboard');
        }

        $court      = Court::findOrFail($cart['court_id']);
        $hours      = count($cart['slots']);
        $subtotal   = $court->calculatePriceForSlots($cart['slots'], $cart['date'] ?? null);
        $serviceFee = (int) round($subtotal * 0.05);
        $addonTotal = (int) $request->input('addon_total', 0);
        
        $promoCode = $request->input('promo_code');
        $promo = $promoCode ? Promo::findValid($promoCode, $court->id) : null;
        $discountPct = $promo ? $promo->discount_percent : 0;
        
        // Cek Pro User 
        $user = Auth::user();
        $isPro = $user && $user->is_pro && $user->pro_expires_at > now();
        if ($isPro) {
            $serviceFee = 0;
            $discountPct += 10;
        }

        $discount   = (int) round($subtotal * $discountPct / 100);
        $total      = $subtotal + $serviceFee + $addonTotal - $discount;

        $addonItems = $request->input('addon_items', []);
        
        $amountDue = $total;

        return Inertia::render('BookingPayment', [
            'cart'    => array_merge($cart, [
                'promo_code'       => $request->input('promo_code'),
                'addon_total'      => $addonTotal,
                'addon_items'      => $addonItems,
            ]),
            'court'   => [
                'id'         => $court->id,
                'name'       => $court->name,
                'city'       => $court->city,
                'main_image' => $court->main_image,
            ],
            'summary' => [
                'hours'       => $hours,
                'subtotal'    => $subtotal,
                'service_fee' => $serviceFee,
                'addon_total' => $addonTotal,
                'discount'    => $discount,
                'total'       => max(0, $total),
                'amount_due'  => max(0, $amountDue),
            ],
            'clientKey' => config('services.midtrans.client_key'),
        ]);
    }

    /**
     * Process payment — LUNAS only. Validasi promo dari DB.
     */
    public function processPayment(ProcessPaymentRequest $request)
    {
        $validated = $request->validated();

        $cart = session('booking_cart');
        if (!$cart) {
            return response()->json(['error' => 'Session expired. Silakan mulai booking ulang.'], 422);
        }

        try {
            $data = $this->bookingService->processBooking($cart, $validated, Auth::user());
            

            return response()->json([
                'snap_token' => $data['snap_token'],
                'order_id'   => $data['order_id']
            ]);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'SLOT_TAKEN') {
                return response()->json([
                    'error' => 'Maaf, slot waktu ini sudah dipesan orang lain. Silakan pilih waktu lain.',
                ], 409);
            }
            return response()->json(['error' => 'Terjadi kesalahan server: ' . $e->getMessage()], 500);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Gagal membuat transaksi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Payment success — HANYA tampilkan data, tidak ubah status.
     * Status booking diubah oleh webhook Midtrans.
     */
    public function paymentSuccess(Request $request)
    {
        $orderId = $request->query('order_id');

        $booking = $orderId
            ? Booking::where('midtrans_order_id', $orderId)
                ->where('user_id', Auth::id())
                ->with('court')
                ->first()
            : null;

        session()->forget('booking_cart');

        return Inertia::render('PaymentSuccess', [
            'booking' => $booking ? [
                'booking_code' => $booking->booking_code,
                'court_name'   => $booking->court->name,
                'date'         => $booking->date->format('d M Y'),
                'time_start'   => $booking->time_start,
                'time_end'     => $booking->time_end,
                'total_price'  => $booking->total_price,
                'status'       => $booking->status,
            ] : null,
        ]);
    }

    /**
     * Payment failed.
     */
    public function paymentFailed(Request $request)
    {
        $orderId = $request->query('order_id');
        $booking = $orderId
            ? Booking::where('midtrans_order_id', $orderId)->where('user_id', Auth::id())->with('court')->first()
            : null;

        return Inertia::render('PaymentFailed', [
            'booking' => $booking ? [
                'booking_code' => $booking->booking_code,
                'court_name'   => $booking->court->name,
            ] : null,
        ]);
    }

    /**
     * Booking history — dengan pagination.
     */
    public function history(Request $request)
    {
        $userId = Auth::id();
        $bookings = Booking::where('user_id', $userId)
            ->with(['court', 'review'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($b) => [
                'id'             => $b->id,
                'booking_code'   => $b->booking_code,
                'court_id'       => $b->court_id,
                'court_name'     => $b->court->name,
                'court_sport'    => $b->court->sport_type,
                'court_image'    => $b->court->main_image,
                'date'           => $b->date->format('d M Y'),
                'time_start'     => $b->time_start,
                'time_end'       => $b->time_end,
                'total_price'    => $b->total_price,
                'addon_total'    => $b->addon_total,
                'status'         => $b->status,
                'payment_status' => $b->payment_status,
                'can_cancel'     => $b->isCancellable(),
                'is_reviewed'    => $b->review !== null,
                'created_at'     => $b->created_at->diffForHumans(),
                'midtrans_snap_token' => $b->midtrans_snap_token,
            ])
            ->values()
            ->toArray();

        return Inertia::render('History', ['bookings' => $bookings]);
    }

    /**
     * Cancel booking.
     */
    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);
        if (!$booking->isCancellable()) {
            return back()->with('error', 'Booking tidak dapat dibatalkan.');
        }

        if ($booking->midtrans_order_id && $booking->payment_status !== 'settlement') {
            try {
                $this->midtrans->cancelTransaction($booking->midtrans_order_id);
            } catch (\Exception $e) {
                // Ignore if already cancelled or error
            }
        }

        $isRefundable = in_array($booking->status, ['active', 'completed']) || in_array($booking->payment_status, ['settlement', 'capture']);
        $refundStatus = $isRefundable ? 'requested' : 'none';
        
        $booking->update([
            'status' => 'cancelled',
            'refund_status' => $refundStatus,
        ]);

        Notification::create([
            'user_id' => Auth::id(),
            'type'    => 'system',
            'title'   => 'Booking Dibatalkan',
            'message' => "Booking {$booking->booking_code} telah dibatalkan.",
        ]);

        // Kirim notifikasi WhatsApp
        $waMessage = "Halo {$booking->user->name} 👋\n\nBooking Anda untuk lapangan *{$booking->court->name}* dengan kode *{$booking->booking_code}* telah berhasil *dibatalkan*.\n\n" . 
            ($refundStatus === 'requested' ? "Pengajuan refund sedang diproses admin." : "Tidak ada refund untuk pembatalan ini.") . 
            "\n\nTerima kasih,\nMainYuk!";
        app(\App\Services\WhatsAppService::class)->sendMessage($booking->user->phone ?? '', $waMessage);

        return back()->with('success', 'Booking berhasil dibatalkan.' . ($isRefundable ? ' Permintaan refund sedang diproses.' : ''));
    }

    /**
     * Reschedule booking.
     */
    public function reschedule(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);
        
        $validated = $request->validate([
            'requested_date' => 'required|date|after_or_equal:today',
            'reason' => 'nullable|string',
        ]);

        // Buat record RescheduleRequest
        \App\Models\RescheduleRequest::create([
            'booking_id'     => $booking->id,
            'requested_date' => $validated['requested_date'],
            'reason'         => $validated['reason'],
            'status'         => 'pending',
        ]);

        // Kirim notifikasi sistem
        Notification::create([
            'user_id' => Auth::id(),
            'type'    => 'system',
            'title'   => 'Pengajuan Reschedule',
            'message' => "Pengajuan reschedule untuk booking {$booking->booking_code} ke tanggal {$validated['requested_date']} telah diterima.",
        ]);

        // Kirim notifikasi WhatsApp
        $waMessage = "Halo {$booking->user->name} 👋\n\nPengajuan *reschedule* Anda untuk booking lapangan *{$booking->court->name}* (Kode: *{$booking->booking_code}*) ke tanggal *{$validated['requested_date']}* telah kami terima.\n\nAdmin kami akan segera meninjau jadwal dan menghubungi Anda.\n\nTerima kasih,\nMainYuk!";
        app(\App\Services\WhatsAppService::class)->sendMessage($booking->user->phone ?? '', $waMessage);

        return back()->with('success', 'Pengajuan reschedule berhasil dikirim.');
    }

    /**
     * Download invoice PDF.
     */
    public function downloadInvoice(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) abort(403);
        // Menggunakan streamInvoice agar bisa di-preview di new tab, bukan langsung download
        return $this->pdf->streamInvoice($booking);
    }

    /**
     * Generate QR Code for Booking Check-in.
     */
    public function qr($booking_code)
    {
        $booking = Booking::where('booking_code', $booking_code)->firstOrFail();
        
        if ($booking->user_id !== Auth::id() && !$booking->participants()->where('user_id', Auth::id())->exists()) {
            abort(403);
        }

        $checkInUrl = route('admin.bookings.check-in', $booking->booking_code);
        
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)
            ->style('round')
            ->color(0, 98, 65) // #006241
            ->generate($checkInUrl);

        return response($qrCode)->header('Content-type', 'image/svg+xml');
    }

    /**
     * Repay page for pending bookings.
     */
    public function repay(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return redirect()->route('history')->with('error', 'Pemesanan ini tidak dapat dibayar ulang.');
        }

        return Inertia::render('Repay', [
            'booking' => [
                'id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'court_name' => $booking->court->name,
                'date' => $booking->date->format('Y-m-d'),
                'time_start' => $booking->time_start,
                'time_end' => $booking->time_end,
                'total_price' => $booking->total_price,
            ],
            'snap_token' => $booking->midtrans_snap_token,
            'clientKey' => config('services.midtrans.client_key'),
        ]);
    }

    /**
     * Repay remaining balance for Split Bill by Host.
     */
    public function repayBalance(Booking $booking, \App\Services\MidtransService $midtrans)
    {
        if ($booking->user_id !== Auth::id()) abort(403);
        if ($booking->status !== 'pending') abort(403, 'Booking sudah lunas atau dibatalkan.');
        if ($booking->max_participants <= 1) abort(403, 'Bukan mabar.');

        // Hitung sisa yang belum dibayar oleh partisipan
        $paidAmount = $booking->participants()
            ->whereIn('payment_status', ['settlement', 'capture'])
            ->sum('amount_due');
            
        $remainingAmount = $booking->total_price - $paidAmount;
        
        if ($remainingAmount <= 0) {
            $booking->update(['status' => 'active', 'payment_status' => 'settlement']);
            return redirect()->route('history')->with('success', 'Pembayaran sudah lunas sepenuhnya.');
        }

        $user = Auth::user();
        $orderId = 'LUNASI-' . $booking->booking_code . '-' . time();

        $snapData = $midtrans->createTransaction(
            [
                'order_id'    => $orderId,
                'total_price' => $remainingAmount,
                'court_id'    => $booking->court->id,
                'court_name'  => $booking->court->name . ' (Pelunasan Mabar)',
            ],
            [
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ]
        );

        $booking->update([
            'midtrans_snap_token' => $snapData['snap_token'],
            'midtrans_order_id'   => $orderId,
        ]);

        return redirect()->route('booking.repay', $booking->booking_code);
    }
}
