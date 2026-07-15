<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $fillable = [
        'booking_code', 'user_id', 'court_id',
        'date', 'time_start', 'time_end', 'duration_hours', 'court_detail',
        'subtotal', 'discount', 'total_price', 'pay_type',
        'status', 'payment_status', 'payment_method', 'payment_channel',
        'midtrans_order_id', 'midtrans_snap_token', 'midtrans_transaction_id',
        'paid_at', 'paid_amount', 'payment_expired_at', 'items',
        'check_in_time',
    ];

    protected $casts = [
        'date'                => 'date',
        'paid_at'             => 'datetime',
        'payment_expired_at'  => 'datetime',
        'check_in_time'       => 'datetime',
        'items'               => 'array',
        'addon_items'         => 'array',
        'subtotal'            => 'integer',
        'discount'            => 'integer',
        'total_price'         => 'integer',
        'paid_amount'         => 'integer',
        'duration_hours'      => 'integer',
    ];

    /**
     * Auto-generate booking code before creating.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_code)) {
                $booking->booking_code = 'MY-' . strtoupper(Str::random(6));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }



    public function scopePending($query) { return $query->where('status', 'pending'); }
    public function scopeActive($query)  { return $query->where('status', 'active'); }
    public function scopeCompleted($query) { return $query->where('status', 'completed'); }
    public function scopeCancelled($query) { return $query->where('status', 'cancelled'); }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getAddonTotalAttribute(): int
    {
        if (empty($this->addon_items)) return 0;
        
        $total = 0;
        foreach ($this->addon_items as $item) {
            $total += ($item['qty'] ?? 0) * ($item['price'] ?? $item['price_per_unit'] ?? 0);
        }
        return $total;
    }

    public function isActive(): bool   { return $this->status === 'active'; }

    /**
     * Scope: get active/pending booked slots for a specific court within a date range.
     */
    public function scopeBookedForCourt($query, int $courtId, string $startDate, string $endDate)
    {
        return $query->where('court_id', $courtId)
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->whereIn('status', ['active', 'pending']);
    }

    public function isCancellable(): bool
    {
        if (!in_array($this->status, ['pending', 'active'])) {
            return false;
        }
        
        if ($this->status === 'pending') {
            return true;
        }
        // Harus lebih dari hari ini, ATAU hari ini tapi belum mulai
        $bookingStart = \Carbon\Carbon::parse(
            $this->date->format('Y-m-d') . ' ' . $this->time_start
        );
        return $bookingStart->isFuture();
    }
}
