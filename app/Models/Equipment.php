<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $fillable = [
        'court_id', 'name', 'icon', 'description', 'price_per_unit', 'stock', 'is_active',
    ];

    protected $casts = [
        'price_per_unit' => 'integer',
        'stock'          => 'integer',
        'is_active'      => 'boolean',
    ];

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    /**
     * Scope: only active equipment.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
