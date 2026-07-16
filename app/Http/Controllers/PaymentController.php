<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Notification;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private MidtransService $midtrans) {}

    /**
     * Midtrans Webhook — called by Midtrans server, NOT by user.
     * Must be excluded from CSRF.
     */
    public function notification(Request $request)
    {
        try {
            $data = $this->midtrans->handleNotification();

            // ─── Verifikasi Signature Midtrans ───────────────────
            $isValid = $this->midtrans->verifySignature(
                $data['order_id'],
                (string) $data['status_code'],
                (string) $data['gross_amount'],
                $data['signature_key']
            );

            if (!$isValid) {
                Log::warning('Midtrans webhook: signature verification failed for order_id ' . $data['order_id']);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            $isMabar = str_starts_with($data['order_id'], 'MABAR-');
            $isPro = str_starts_with($data['order_id'], 'PRO-');

            $newStatus = $this->midtrans->resolveStatus(
                $data['transaction_status'],
                $data['fraud_status']
            );

            // ─── Handling Langganan PRO ───────────────────────────
            if ($isPro) {
                if (in_array($newStatus, ['active', 'settlement', 'capture'])) {
                    // Extract user ID dari PRO-{user_id}-{time}-{random}
                    $parts = explode('-', $data['order_id']);
                    if (isset($parts[1])) {
                        $userId = $parts[1];
                        $user = \App\Models\User::find($userId);
                        if ($user) {
                            $user->update([
                                'is_pro' => true,
                                'pro_expires_at' => now()->addDays(30)
                            ]);
                            
                            Notification::create([
                                'user_id' => $user->id,
                                'type'    => 'system',
                                'title'   => 'MainYuk! PRO Aktif 🌟',
                                'message' => "Terima kasih! Langganan PRO Anda aktif hingga " . now()->addDays(30)->format('d M Y') . ".",
                            ]);
                        }
                    }
                }
                return response()->json(['message' => 'PRO subscription handled']);
            }

            // ─── Handling Booking Lapangan / Split Bill ───────────
            $booking = null;
            $participant = null;

            if ($isMabar) {
                $participant = \App\Models\BookingParticipant::where('midtrans_order_id', $data['order_id'])->first();
                if ($participant) $booking = $participant->booking;
            } else {
                $booking = Booking::where('midtrans_order_id', $data['order_id'])->first();
            }

            if (!$booking) {
                Log::warning("Midtrans webhook: booking not found for order_id {$data['order_id']}");
                return response()->json(['message' => 'Order not found'], 404);
            }

            if ($isMabar && $participant) {
                $participant->update([
                    'payment_status' => $data['transaction_status'],
                ]);
                
                if (in_array($newStatus, ['active', 'settlement', 'capture'])) {
                    $booking->increment('paid_amount', $participant->amount_due);
                    $newStatus = 'active'; // Once a participant pays, secure the booking
                }
            }

            $booking->update([
                'status'                 => in_array($booking->status, ['active']) ? 'active' : $newStatus,
                'payment_status'         => $isMabar ? $booking->payment_status : $data['transaction_status'],
                'payment_method'         => $data['payment_type'] ?? $booking->payment_method,
                'midtrans_transaction_id'=> $data['transaction_id'] ?? $booking->midtrans_transaction_id,
                'paid_at'                => in_array($newStatus, ['active']) && !$booking->paid_at ? now() : $booking->paid_at,
            ]);

            // ─── Kirim notifikasi ke user ─────────────────────────
            if ($newStatus === 'active') {
                Notification::create([
                    'user_id' => $booking->user_id,
                    'type'    => 'payment',
                    'title'   => 'Pembayaran Berhasil ✅',
                    'message' => "Booking {$booking->booking_code} telah dikonfirmasi!",
                ]);
                
                \App\Jobs\SendMailJob::dispatch('booking', [
                    'user'    => $booking->user->only(['name', 'email']),
                    'booking' => [
                        'booking_code' => $booking->booking_code,
                        'court_name'   => $booking->court->name,
                        'date'         => $booking->date,
                        'time_start'   => $booking->time_start,
                        'time_end'     => $booking->time_end,
                        'total_price'  => number_format($booking->total_price, 0, ',', '.'),
                    ],
                ]);

                // Kirim notifikasi WhatsApp
                $waMessage = "Halo {$booking->user->name} 👋\n\nBooking Anda untuk lapangan *{$booking->court->name}* telah kami konfirmasi.\n\n📅 {$booking->date}\n⏰ {$booking->time_start} - {$booking->time_end}\nKode Booking: *{$booking->booking_code}*\n\nTunjukkan kode booking atau QR Code pada halaman detail booking Anda saat check-in.\n\nTerima kasih,\nMainYuk!";
                app(\App\Services\WhatsAppService::class)->sendMessage($booking->user->phone ?? '', $waMessage);

            } elseif ($newStatus === 'cancelled') {
                Notification::create([
                    'user_id' => $booking->user_id,
                    'type'    => 'payment',
                    'title'   => 'Pembayaran Gagal',
                    'message' => "Booking {$booking->booking_code} dibatalkan karena pembayaran tidak diterima.",
                ]);
            }

            return response()->json(['message' => 'OK']);
        } catch (\Throwable $e) {
            Log::error("Midtrans webhook error: " . $e->getMessage());
            return response()->json(['message' => 'Error'], 500);
        }
    }
}
