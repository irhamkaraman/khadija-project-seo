<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

/**
 * ============================================================
 * STORAGE FILE SERVER — tanpa butuh symlink
 * ============================================================
 * Melayani file dari storage/app/public langsung via PHP.
 * Solusi untuk cPanel yang memblokir symlink (403 Forbidden).
 * URL menggunakan prefix /file untuk menghindari konflik dengan symlink /storage
 * ============================================================
 */
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
    Route::get('/', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
    Route::get('/kategori/{slug}', [\App\Http\Controllers\BlogController::class, 'category'])->name('blog.category');
    Route::get('/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');
});

Route::get('/{slug}', \App\Http\Controllers\SiteRedirectController::class);

