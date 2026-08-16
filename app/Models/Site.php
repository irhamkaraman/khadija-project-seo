<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'url',
        'title',
        'slug',
        'description',
        'image_url',
        'share_link',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
