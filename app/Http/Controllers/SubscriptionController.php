<?php

namespace App\Http\Controllers;

use App\Services\MidtransService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function __construct(private MidtransService $midtrans) {}

    public function index()
    {
        $user = Auth::user();
        return Inertia::render('Pro/Index', [
            'is_pro' => $user->is_pro,
            'pro_expires_at' => $user->pro_expires_at ? $user->pro_expires_at->format('d M Y') : null,
        ]);
    }

    public function subscribe(Request $request)
    {
        $user = Auth::user();
        
        if ($user->is_pro && $user->pro_expires_at && $user->pro_expires_at->isFuture()) {
            return back()->with('error', 'Anda sudah berlangganan MainYuk! PRO.');
        }

        $orderId = 'PRO-' . $user->id . '-' . time() . '-' . Str::random(5);
        $amount = 50000;

        \App\Models\MembershipTransaction::create([
            'user_id' => $user->id,
            'order_id' => $orderId,
            'amount' => $amount,
            'status' => 'pending',
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'item_details' => [
                [
                    'id' => 'MAINYUK-PRO-1M',
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Langganan MainYuk! PRO 1 Bulan',
                ]
            ],
            // Gunakan custom expiry jika perlu, misalnya 1 jam
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => 'hours',
                'duration' => 1,
            ],
            'callbacks' => [
                'finish' => route('membership.success', ['order_id' => $orderId]),
                'error' => route('membership.failed', ['order_id' => $orderId]),
                'unfinish' => route('membership.failed', ['order_id' => $orderId]),
            ],
        ];

        try {
            $snapToken = $this->midtrans->createSnapToken($params);
            
            return Inertia::render('Pro/Payment', [
                'snap_token' => $snapToken,
                'amount' => $amount,
                'order_id' => $orderId,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function unsubscribe(Request $request)
    {
        $user = Auth::user();

        if ($user->is_pro) {
            $user->update([
                'is_pro' => false,
                'pro_expires_at' => null,
            ]);

            return back()->with('success', 'Berhasil berhenti berlangganan MainYuk! PRO.');
        }

        return back()->with('error', 'Anda tidak sedang berlangganan.');
    }
}
