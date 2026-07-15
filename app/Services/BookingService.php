<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Court;
use App\Models\Promo;
use Illuminate\Support\Facades\DB;
use App\Services\MidtransService;

class BookingService
{
    public function __construct(private MidtransService $midtrans) {}

    /**
     * Process the payment and create the booking.
     * Returns an array with midtrans snapshot data.
     * Throws \RuntimeException on overlap.
     */
    public function processBooking(array $cart, array $validated, $user)
    {
        $court      = Court::findOrFail($cart['court_id']);
        $hours      = count($cart['slots']);
        $subtotal   = $court->calculatePriceForSlots($cart['slots'], $cart['date'] ?? null);
        $addonTotal = (int) ($validated['addon_total'] ?? 0);
        $serviceFee = (int) round($subtotal * 0.05);

        // Jika user PRO, service fee 0, dan tambahan diskon 10%
        $isPro = $user->is_pro && $user->pro_expires_at && $user->pro_expires_at->isFuture();
        if ($isPro) {
            $serviceFee = 0;
        }

        // Validasi promo
        $discountPct    = 0;
        $promoCodeInput = strtoupper(trim($validated['promo_code'] ?? ''));
        if ($promoCodeInput) {
            $promo = Promo::findValid($promoCodeInput, $court->id);
            if ($promo) {
                // Pastikan promo belum dipakai
                $used = Booking::where('user_id', $user->id)
                    ->where('promo_code', $promo->code)
                    ->whereNotIn('status', ['cancelled'])
                    ->exists();
                if (!$used) {
                    $discountPct = $promo->discount_percent;
                } else {
                    $promoCodeInput = null; // Batalkan promo jika sudah dipakai
                }
            }
        }

        // Jika PRO, tambahkan 10% ke persentase diskon
        if ($isPro) {
            $discountPct += 10;
        }

        $discount = (int) round($subtotal * $discountPct / 100);
        $total    = $subtotal + $serviceFee + $addonTotal - $discount;
        $total    = max(0, $total);

        // Update session cart
        session(['booking_cart' => array_merge($cart, [
            'discount'    => $discount,
            'addon_total' => $addonTotal,
        ])]);

        // Cek slot dan buat booking dalam transaksi
        $result = DB::transaction(function () use (
            $user, $court, $cart, $hours, $subtotal, $discount, $addonTotal, $total, $validated, $promoCodeInput
        ) {
            $existingBookings = Booking::where('court_id', $court->id)
                ->whereDate('date', $cart['date'])
                ->where('court_detail', $cart['court_detail'] ?? null)
                ->whereIn('status', ['active', 'pending'])
                ->lockForUpdate()
                ->get(['items']);

            $overlap = false;
            foreach ($existingBookings as $b) {
                if (!empty(array_intersect($b->items ?? [], $cart['slots']))) {
                    $overlap = true;
                    break;
                }
            }

            if ($overlap) {
                throw new \RuntimeException('SLOT_TAKEN');
            }

            $booking = Booking::create([
                'user_id'        => $user->id,
                'court_id'       => $court->id,
                'date'           => $cart['date'],
                'time_start'     => $cart['time_start'],
                'time_end'       => $cart['time_end'],
                'duration_hours' => $hours,
                'court_detail'   => $cart['court_detail'] ?? null,
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'total_price'    => $total,
                'paid_amount'    => 0,
                'pay_type'       => 'lunas',
                'status'         => 'pending',
                'items'          => $cart['slots'],
                'addon_items'    => $validated['addon_items'] ?? null,
                'promo_code'     => $promoCodeInput ?: null,
            ]);

            $orderId = 'ORDER-' . $booking->booking_code;

            $booking->update([
                'midtrans_order_id' => $orderId,
            ]);

            return ['booking' => $booking, 'orderId' => $orderId];
        });

        $booking = $result['booking'];
        $orderId = $result['orderId'];

        // Buat Snap token Midtrans
        $snapData = $this->midtrans->createTransaction(
            [
                'order_id'    => $orderId,
                'total_price' => $total,
                'court_id'    => $court->id,
                'court_name'  => $court->name,
            ],
            [
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ]
        );

        $booking->update(['midtrans_snap_token' => $snapData['snap_token']]);

        return [
            'snap_token'   => $snapData['snap_token'],
            'booking_code' => $booking->booking_code,
            'order_id'     => $orderId,
        ];
    }
}
