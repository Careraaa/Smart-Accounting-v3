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

        // Block login if this account is already actively logged in elsewhere
        if (auth()->user()->is_logged_in) {
            Auth::logout();
            return response()->view('errors.account-in-use', [], 409);
        }

        $request->session()->regenerate();

        // Mark the account as logged in
        auth()->user()->update(['is_logged_in' => true]);

        // Flash notification if user hasn't changed their generated password yet
        if (!auth()->user()->password_changed) {
            session()->flash('info', 'Security reminder: you are using a temporary password. Please change it now in Account Settings.');
        }

        return redirect()->intended(route('dashboard', absolute: false));
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
