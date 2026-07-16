<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'court_id', 'user_id', 'avatar', 'name', 'comment', 'rating',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted date.
     */
    public function getCreatedAtFormattedAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }
}
