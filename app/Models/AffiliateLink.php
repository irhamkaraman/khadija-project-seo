<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateLink extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'affiliate_url',
        'source_url',
        'image_url',
        'description',
        'badge',
        'cta_text',
        'click_count',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'click_count' => 'integer',
    ];

    /**
     * Atomic click counter increment.
     * Gunakan increment() bukan update() agar tidak terjadi race condition di concurrent requests.
     */
    public function incrementClick(): void
    {
        $this->increment('click_count');
    }

    /**
     * Scope untuk mengambil iklan yang aktif saja.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk mengambil iklan secara acak sejumlah $count.
     */
    public function scopeRandom($query, int $count = 6)
    {
        return $query->inRandomOrder()->limit($count);
    }
}
