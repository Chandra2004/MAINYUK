<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\MembershipTransaction;
use App\Services\MidtransService;

class MembershipController extends Controller
{
    protected MidtransService $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    public function subscribe(Request $request)
    {
        $user = Auth::user();
        
        // Cek kalau udah langganan aktif
        if ($user->is_pro && $user->pro_expires_at && $user->pro_expires_at > now()) {
            return back()->with('error', 'Anda sudah berlangganan PRO.');
        }

        $orderId = 'PRO-' . $user->id . '-' . time();
        $amount = 50000; // Rp 50.000

        $transaction = MembershipTransaction::create([
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
                'phone' => $user->phone ?? '',
            ],
            'item_details' => [
                [
                    'id' => 'PRO-MONTHLY',
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Membership PRO (1 Bulan)',
                ]
            ],
            'callbacks' => [
                'finish' => route('membership.success', ['order_id' => $orderId]),
                'error' => route('membership.failed', ['order_id' => $orderId]),
                'unfinish' => route('membership.failed', ['order_id' => $orderId]),
            ],
        ];

        $snapToken = $this->midtrans->createSnapToken($params);
        $transaction->update(['snap_token' => $snapToken]);

        return response()->json([
            'snap_token' => $snapToken,
            'order_id' => $orderId,
            'client_key' => config('services.midtrans.client_key'),
        ]);
    }

    public function success(Request $request)
    {
        $orderId = $request->query('order_id');
        
        $transaction = MembershipTransaction::where('order_id', $orderId)->first();
        if ($transaction && $transaction->status !== 'success') {
            $transaction->update(['status' => 'success']);
            
            // Aktivasi sebulan
            $user = $transaction->user;
            $user->update([
                'is_pro' => true,
                'pro_expires_at' => now()->addMonth(),
            ]);
        }

        return Inertia::render('PaymentSuccess', [
            'booking' => null,
            'membership' => true,
        ]);
    }

    public function failed(Request $request)
    {
        $orderId = $request->query('order_id');
        
        $transaction = MembershipTransaction::where('order_id', $orderId)->first();
        if ($transaction && $transaction->status === 'pending') {
            $transaction->update(['status' => 'failed']);
        }

        return Inertia::render('PaymentFailed', [
            'membership' => true,
        ]);
    }
}
