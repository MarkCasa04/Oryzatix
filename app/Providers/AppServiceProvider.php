<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use App\Models\SystemSetting;

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
        if (config('app.env') === 'production' || env('APP_ENV') === 'production' || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            URL::forceScheme('https');
        }

        // Dynamically load Google OAuth credentials from database if env is not provided
        try {
            if (!config('services.google.client_id')) {
                $clientId = SystemSetting::get('google_client_id');
                $clientSecret = SystemSetting::get('google_client_secret');
                $redirectUri = SystemSetting::get('google_redirect_uri');

                if ($clientId) {
                    Config::set('services.google.client_id', $clientId);
                }
                if ($clientSecret) {
                    Config::set('services.google.client_secret', $clientSecret);
                }
                if ($redirectUri) {
                    Config::set('services.google.redirect', $redirectUri);
                }
            }
        } catch (\Throwable $e) {
            // Silently catch in case of early bootstrap or migrations
        }
    }
}
