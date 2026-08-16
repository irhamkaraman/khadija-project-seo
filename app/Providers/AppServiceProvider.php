<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Auto-buat symlink storage jika belum ada
        // Mengatasi masalah 403 Forbidden pada cPanel hosting
        $link   = public_path('storage');
        $target = storage_path('app/public');

        if (! file_exists($link) && file_exists($target)) {
            try {
                symlink($target, $link);
            } catch (\Exception $e) {
                // Abaikan jika gagal (misalnya permission di shared hosting)
                // Jalankan manual: php artisan storage:link
            }
        }
    }
}
