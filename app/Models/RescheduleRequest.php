<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RescheduleRequest extends Model
{
    //
    protected $fillable = ['booking_id', 'requested_date', 'reason', 'status'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
