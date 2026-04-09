<?php

namespace App\Providers;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Helpers\BaxHelper;

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
        URL::forceScheme('https');
        
        // Register global helper functions
        if (!function_exists('statusColor')) {
            function statusColor($status) {
                return BaxHelper::statusColor($status);
            }
        }
    }
}
