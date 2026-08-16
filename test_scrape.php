<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$url = "https://www.tribunnews.com/regional/7869360/update-korban-gempa-ntt-korban-meninggal-bertambah-jadi-53-jiwa-135-korban-luka-dirawat";
$response = Illuminate\Support\Facades\Http::withOptions(["verify" => false])
    ->withHeaders(["User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"])
    ->get($url);
    
echo "Status: " . $response->status() . "\n";
$html = $response->body();
echo "Length: " . strlen($html) . "\n";

if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
    echo "Title: " . trim($matches[1]) . "\n";
} else {
    echo "No title found\n";
}

if (preg_match('/<meta[^>]*property=["\']og:image["\'][^>]*content=["\'](.*?)["\']/is', $html, $matches)) {
    echo "Image: " . trim($matches[1]) . "\n";
} else {
    echo "No image found\n";
}
