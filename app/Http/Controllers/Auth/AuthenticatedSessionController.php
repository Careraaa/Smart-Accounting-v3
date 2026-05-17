<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Regenerate session first to establish a new session ID
        $request->session()->regenerate();
        
        $user = auth()->user();

        // Invalidate all other sessions for this user (except the current one)
        $this->invalidateOtherSessions($user, $request->session()->getId());

        // Mark the account as logged in and refresh user data
        $user->update([
            'is_logged_in' => true,
            'last_login_at' => now(),
        ]);

        // Flash notification if user hasn't changed their generated password yet
        if ($user->password_changed === 0 || $user->password_changed === false) {
            session()->flash('info', 'Security reminder: you are using a temporary password. Please change it now in Account Settings.');
        }

        // Reload the user to ensure we have fresh data including role
        $user->refresh();

        // Return role-specific redirect
        return match ($user->role) {
            'superadmin' => redirect()->route('superadmin.dashboard'),
            'remittance_clerk' => redirect()->route('remittance-clerk.index'),
            'accountant' => redirect()->route('accountant.index'),
            'hr' => redirect()->route('hr.index'),
            'qr_admin' => redirect()->route('admin.dashboard'),
            'employee' => redirect()->route('employee.index'),
            default => redirect()->intended(route('dashboard', absolute: false)),
        };
    }

    /**
     * Invalidate all other sessions for a user, except the current one.
     */
    private function invalidateOtherSessions($user, $currentSessionId): void
    {
        \DB::table(config('session.table', 'sessions'))
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->delete();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Free the account slot on logout
        if (auth()->check()) {
            auth()->user()->update(['is_logged_in' => false]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
