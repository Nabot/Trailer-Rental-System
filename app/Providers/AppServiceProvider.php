<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $helpers = $this->app->basePath('app/Helpers/helpers.php');
        if (file_exists($helpers)) {
            require_once $helpers;
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // When document root = app root (e.g. cPanel workaround), use base path as public path
        // so Vite manifest at build/manifest.json and assets at /build/ resolve correctly.
        if (config('app.public_path_is_app_root')) {
            $this->app->usePublicPath($this->app->basePath());
        }
    }
}
