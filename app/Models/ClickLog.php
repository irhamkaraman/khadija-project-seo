<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClickLog extends Model
{
    protected $fillable = ['affiliate_link_id', 'clicked_date', 'clicks'];

    public function affiliateLink()
    {
        return $this->belongsTo(AffiliateLink::class);
    }
}
