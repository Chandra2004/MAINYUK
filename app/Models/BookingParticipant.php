<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingParticipant extends Model
{
    protected $fillable = [
        'booking_id',
        'user_id',
        'amount_due',
        'payment_status',
        'midtrans_order_id',
        'midtrans_snap_token',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
