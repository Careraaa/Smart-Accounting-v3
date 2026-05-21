<?php

namespace App\Providers;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use App\Models\User;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Trust all proxies (Cloudflare tunnel, reverse proxies, etc.)
        // so Laravel reads X-Forwarded-Proto and generates the correct http/https URLs.
        Request::setTrustedProxies(
            ['127.0.0.1', '::1', 'REMOTE_ADDR'],
            Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_PREFIX
        );

        // Also force https so asset URLs are always generated with https
        // (required for Cloudflare tunnel which only exposes HTTPS externally)
        URL::forceScheme('https');

        // Register observer for automatic leave balance management when new employees are created
        User::observe(UserObserver::class);
    }
}
