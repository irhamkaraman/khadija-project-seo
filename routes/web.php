<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\SiteRedirectController;

Route::get('/', [BlogController::class, 'home'])->name('home');

Route::get('/file/{path}', function (string $path) {
    $filePath = storage_path('app/public/' . $path);

    if (!file_exists($filePath)) {
        abort(404);
    }

    $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

    return response()->file($filePath, [
        'Content-Type'  => $mimeType,
        'Cache-Control' => 'public, max-age=2592000', // cache 30 hari
    ]);
})->where('path', '.*')->name('storage.serve');

Route::prefix('blog')->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('blog.index');
    Route::get('/kategori/{slug}', [BlogController::class, 'category'])->name('blog.category');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('blog.show');
});

/**
 * ============================================================
 * AFFILIATE LINK TRACKER — /go/{slug}
 * ============================================================
 * Route ini mencatat klik iklan afiliasi secara atomic
 * lalu meredirect pengunjung ke affiliate_url.
 * Dipisah dari /{slug} agar tidak bentrok dengan rute Safelink.
 * ============================================================
 */
Route::get('/go/{slug}', function (string $slug) {
    $affiliate = \App\Models\AffiliateLink::where('slug', $slug)
        ->where('is_active', true)
        ->firstOrFail();

    // Atomic increment — aman dari race condition di concurrent requests
    $affiliate->incrementClick();

    // Redirect langsung ke link afiliasi (Shopee, Tokped, dll)
    return redirect()->away($affiliate->affiliate_url);
})->name('affiliate.go');

Route::get('/{slug}', SiteRedirectController::class);
