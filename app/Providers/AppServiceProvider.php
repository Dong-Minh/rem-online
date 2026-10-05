<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        // Tự động ép buộc mọi URL, Form và Chữ ký số về HTTPS trên Render
        if (
            app()->environment('production') ||
            env('APP_ENV') === 'production' ||
            !empty($_SERVER['HTTPS']) ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], 'onrender.com')) ||
            env('RENDER')
        ) {
            URL::forceScheme('https');
            if (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], 'onrender.com')) {
                URL::forceRootUrl('https://' . $_SERVER['HTTP_HOST']);
            } else {
                URL::forceRootUrl('https://rem-online.onrender.com');
            }
        }
    }
}
