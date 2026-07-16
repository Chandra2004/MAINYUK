<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    protected $fillable = [
        'name', 'sport_type', 'location', 'city', 'address',
        'price_per_hour', 'rating', 'total_reviews',
        'description', 'facilities', 'images', 'courts_detail',
        'open_time', 'close_time', 'is_active', 'longitude', 
        'latitude', 'peak_hour_multiplier', 'peak_hour_start', 'peak_hour_end',
    ];

    protected $casts = [
        'facilities'    => 'array',
        'images'        => 'array',
        'courts_detail' => 'array',
        'is_active'     => 'boolean',
        'rating'        => 'float',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function testimonials()
    {
        return $this->hasMany(Testimonial::class)->latest();
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class)->where('is_active', true);
    }

    /**
     * Calculate total price for given slots considering peak hours and weekends.
     */
    public function calculatePriceForSlots(array $slots, ?string $date = null): int
    {
        $total = 0;
        
        $isWeekend = false;
        if ($date) {
            $isWeekend = \Carbon\Carbon::parse($date)->isWeekend();
        }

        foreach ($slots as $slot) {
            $slotTime = \Carbon\Carbon::createFromFormat('H:i', $slot);
            
            $multiplier = 1.0;
            
            if ($isWeekend) {
                // Weekend (Sabtu & Minggu) = Harga + 20% (seluruh jam)
                $multiplier = 1.2;
            } else {
                // Weekday 17:00 - 23:00 = Harga + 20%
                $start = \Carbon\Carbon::createFromFormat('H:i:s', '17:00:00');
                $end   = \Carbon\Carbon::createFromFormat('H:i:s', '23:00:00');
                
                if ($slotTime->between($start, $end)) {
                    $multiplier = 1.2;
                }
            }

            // Also check old peak hour logic if still needed, but user requirement specifically overrides this:
            // "17:00-23:00 di Weekday = Harga + 20%. Weekend (Sabtu & Minggu) = Harga + 20% (seluruh jam)."
            // So we just use the new multiplier.

            $total += (int) ($this->price_per_hour * $multiplier);
        }
        
        return $total;
    }

    public function promos()
    {
        return $this->hasMany(Promo::class);
    }

    /**
     * Hitung ulang rating rata-rata dari seluruh ulasan (reviews).
     * Dipanggil setiap kali review baru ditambahkan/dihapus.
     */
    public function recalculateRating(): void
    {
        $avg   = $this->reviews()->avg('rating') ?? 0;
        $total = $this->reviews()->count();

        $this->update([
            'rating'        => round($avg, 1),
            'total_reviews' => $total,
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('location', 'like', "%{$city}%")
                     ->orWhere('city', 'like', "%{$city}%");
    }

    public function scopeBySport($query, $sport)
    {
        return $query->where('sport_type', $sport);
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price_per_hour, 0, ',', '.');
    }

    /**
     * Get first image URL.
     */
    public function getMainImageAttribute(): ?string
    {
        $images = $this->images ?? [];
        return is_array($images) && count($images) > 0 ? $images[0] : 'https://images.unsplash.com/photo-1541534741688-6078c6bfb5c5?w=600&auto=format&fit=crop';
    }
}
