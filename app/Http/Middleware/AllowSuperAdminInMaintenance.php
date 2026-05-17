<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowSuperAdminInMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $maintenanceFile = storage_path('maintenance.json');

        // Check if maintenance mode is active
        if (file_exists($maintenanceFile)) {
            // Allow logout route to work even in maintenance mode
            if ($request->routeIs('logout')) {
                return $next($request);
            }

            // Allow unauthenticated users to access login page
            if (!auth()->check()) {
                return $next($request);
            }

            // Check if authenticated user is a superadmin
            if (auth()->user()->role === 'superadmin') {
                // Superadmin can bypass maintenance mode
                return $next($request);
            }

            // For authenticated non-superadmin users, show maintenance page
            return response()->view('errors.maintenance', [], 503);
        }

        return $next($request);
    }
}
