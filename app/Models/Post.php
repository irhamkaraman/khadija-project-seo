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
}
