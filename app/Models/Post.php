<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'content',
        'image_url',
        'share_links',
    ];

    protected $casts = [
        'share_links' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * URL aset gambar publik yang efisien dan stabil untuk web & medsos
     */
    public function getAssetUrlAttribute(): string
    {
        if (empty($this->image_url)) {
            return asset('favicon.ico');
        }

        if (\Illuminate\Support\Str::startsWith($this->image_url, ['http://', 'https://'])) {
            return $this->image_url;
        }

        $clean = ltrim(preg_replace('#^(file/|storage/)#', '', $this->image_url), '/');
        return asset('storage/' . $clean);
    }
}
