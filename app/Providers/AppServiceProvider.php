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
        if (
            app()->environment('production') ||
            env('APP_ENV') === 'production' ||
            !empty($_SERVER['HTTPS']) ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            str_contains(env('APP_URL', ''), 'https://') ||
            (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], 'onrender.com'))
        ) {
            URL::forceScheme('https');
        }
    }
}
