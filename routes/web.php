<?php

use App\Models\AffiliateLink;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\SiteRedirectController;
use App\Http\Controllers\AffiliateRedirectController;

Route::get('/', [BlogController::class, 'home'])->name('home');
Route::get('/ajax/ads', [BlogController::class, 'ajaxAds'])->name('ajax.ads');

Route::get('/og-image/{slug}.jpg', [BlogController::class, 'ogImage'])
    ->name('blog.og-image')
    ->withoutMiddleware([
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    ]);

Route::get('/file/{path}', function (string $path) {
    $filePath = storage_path('app/public/' . $path);

    if (!file_exists($filePath)) {
        abort(404);
    }

    $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
    $fileSize = filesize($filePath);

    return response()->file($filePath, [
        'Content-Type'                => $mimeType,
        'Content-Length'              => $fileSize,
        'Cache-Control'               => 'public, max-age=2592000, immutable',
        'Access-Control-Allow-Origin' => '*',
        'Accept-Ranges'               => 'bytes',
    ]);
})->where('path', '.*')->name('storage.serve')
  ->withoutMiddleware([
      \Illuminate\Session\Middleware\StartSession::class,
      \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
  ]);

Route::prefix('blog')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/kategori/{slug}', [BlogController::class, 'category'])->name('blog.category');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('blog.show');
});

Route::get('/go/{slug}', AffiliateRedirectController::class)->name('affiliate.go');

Route::get('/{slug}', SiteRedirectController::class);
