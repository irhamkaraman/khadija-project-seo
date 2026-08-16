<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/storage-link', function () {
    $targetFolder = base_path() . '/storage/app/public';
    $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage';

    if (!file_exists($linkFolder)) {
        symlink($targetFolder, $linkFolder);
        return redirect()->back()->with('success', 'Penyimpanan di server sudah diaktifkan!');
    }
    else {
        return redirect()->back()->with('error', 'Penyimpanan di server telah tersedia!');
    }
})->name('storage-link');

Route::prefix('blog')->group(function () {
    Route::get('/', [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
    Route::get('/kategori/{slug}', [\App\Http\Controllers\BlogController::class, 'category'])->name('blog.category');
    Route::get('/{slug}', [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');
});
Route::get('/{slug}', \App\Http\Controllers\SiteRedirectController::class);
