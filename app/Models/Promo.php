<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Promo extends Model
{
    protected $fillable = [
        'code', 'description', 'discount_percent', 'court_id', 'is_active', 'valid_until',
    ];

    protected $casts = [
        'discount_percent' => 'integer',
        'is_active'        => 'boolean',
        'valid_until'      => 'date',
    ];

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    /**
     * Scope: only active and not expired promos.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(fn ($q) =>
                         $q->whereNull('valid_until')
                           ->orWhere('valid_until', '>=', today())
                     );
    }

    /**
     * Validate if this promo is applicable for a given court_id.
     * Promo with court_id = null applies to all courts.
     */
    public function isApplicableFor(?int $courtId): bool
    {
        return $this->court_id === null || $this->court_id === $courtId;
    }

    /**
     * Find an active promo by code (case-insensitive), optionally scoped to a court.
     */
    public static function findValid(string $code, ?int $courtId = null): ?self
    {
        return self::active()
            ->where('code', strtoupper(trim($code)))
            ->where(function ($q) use ($courtId) {
                $q->whereNull('court_id')
                  ->orWhere('court_id', $courtId);
            })
            ->first();
    }
}
