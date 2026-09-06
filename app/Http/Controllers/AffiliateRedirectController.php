<?php

namespace App\Http\Controllers;

use App\Models\AffiliateLink;
use Illuminate\Http\Request;

class AffiliateRedirectController extends Controller
{
    public function __invoke(string $slug)
    {
        $affiliate = AffiliateLink::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $affiliate->incrementClick();

        $targetUrl = $affiliate->affiliate_url;
        $parsed = parse_url($targetUrl);
        $host = strtolower($parsed['host'] ?? '');
        $cleanUrl = preg_replace('#^https?://#i', '', $targetUrl);
        $encodedTarget = urlencode($targetUrl);

        $androidPackage = null;
        $iosScheme = null;

        // Shopee (Indonesia)
        if (str_contains($host, 'shopee') || str_contains($host, 'shp.ee')) {
            $androidPackage = 'com.shopee.id';
            $iosScheme = 'shopee://open?url=' . $encodedTarget;
        }
        // TikTok
        elseif (str_contains($host, 'tiktok.com')) {
            $androidPackage = 'com.zhiliaoapp.musically';
            $iosScheme = 'snssdk1180://';
        }
        // Tokopedia
        elseif (str_contains($host, 'tokopedia')) {
            $androidPackage = 'com.tokopedia.tkpd';
            $iosScheme = 'tokopedia://';
        }
        // Lazada
        elseif (str_contains($host, 'lazada')) {
            $androidPackage = 'com.lazada.android';
            $iosScheme = 'lazada://';
        }

        $androidIntent = null;
        if ($androidPackage) {
            $androidIntent = 'intent://' . $cleanUrl . '#Intent;scheme=https;package=' . $androidPackage . ';S.browser_fallback_url=' . $encodedTarget . ';end;';
        }

        return view('affiliate.go', compact('affiliate', 'targetUrl', 'androidIntent', 'iosScheme'));
    }
}
