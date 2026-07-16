<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Midtrans\Notification;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('services.midtrans.server_key');
        Config::$clientKey    = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    /**
     * Create Snap transaction and return token + redirect_url.
     */
    public function createTransaction(array $booking, array $user): array
    {
        $params = [
            'transaction_details' => [
                'order_id'     => $booking['order_id'],
                'gross_amount' => (int) $booking['total_price'],
            ],
            'customer_details' => [
                'first_name' => $user['name'],
                'email'      => $user['email'],
                'phone'      => $user['phone'] ?? '',
            ],
            'item_details' => [
                [
                    'id'       => $booking['court_id'],
                    'price'    => (int) $booking['total_price'],
                    'quantity' => 1,
                    'name'     => 'Booking ' . ($booking['court_name'] ?? 'Lapangan'),
                ],
            ],
            'expiry' => [
                'unit'     => 'minutes',
                'duration' => 15,
            ],
            'callbacks' => [
                'finish' => route('history'),
                'error' => route('history'),
                'unfinish' => route('history'),
            ],
        ];

        $snapResponse = Snap::createTransaction($params);

        return [
            'snap_token'   => $snapResponse->token,
            'redirect_url' => $snapResponse->redirect_url,
        ];
    }

    /**
     * Create generic Snap token using provided params
     */
    public function createSnapToken(array $params): string
    {
        return Snap::getSnapToken($params);
    }

    /**
     * Handle Midtrans webhook notification.
     */
    public function handleNotification(): array
    {
        $notification = new Notification();

        return [
            'order_id'           => $notification->order_id,
            'status_code'        => $notification->status_code,
            'gross_amount'       => $notification->gross_amount,
            'transaction_status' => $notification->transaction_status,
            'payment_type'       => $notification->payment_type,
            'fraud_status'       => $notification->fraud_status ?? 'accept',
            'signature_key'      => $notification->signature_key ?? '',
            'transaction_id'     => $notification->transaction_id ?? null,
        ];
    }

    /**
     * Verify Midtrans webhook signature.
     * Formula: SHA512(order_id + status_code + gross_amount + server_key)
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $incomingSignature): bool
    {
        $serverKey         = config('services.midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expectedSignature, $incomingSignature);
    }

    /**
     * Determine booking status from Midtrans transaction status.
     */
    public function resolveStatus(string $transactionStatus, string $fraudStatus): string
    {
        if ($transactionStatus === 'capture') {
            return $fraudStatus === 'challenge' ? 'pending' : 'active';
        }

        return match ($transactionStatus) {
            'settlement' => 'active',
            'pending'    => 'pending',
            'deny', 'expire', 'cancel' => 'cancelled',
            default => 'pending',
        };
    }

    /**
     * Cancel a transaction in Midtrans.
     */
    public function cancelTransaction(string $orderId): bool
    {
        try {
            Transaction::cancel($orderId);
            return true;
        } catch (\Exception $e) {
            // Log error or ignore if already cancelled
            return false;
        }
    }
}
