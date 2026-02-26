@extends('layouts.auth-layout')

@section('title', 'Sign In - Smart Accounting')

@section('content')
    @if (!Auth::check())

        <div class="mb-5">
            <h1 class="auth-page-title">Sign In</h1>
            <p class="auth-page-sub">Enter your credentials to access your account.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="w-100">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email') }}" required autofocus
                    autocomplete="username" placeholder="your@email.com">
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password"
                    class="form-control @error('password') is-invalid @enderror"
                    name="password" required autocomplete="current-password"
                    placeholder="••••••••">
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check mb-0">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember"
                        style="font-size:0.815rem; color:#9898a8;">Remember me</label>
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="link-muted"
                        style="font-size:0.815rem;">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="btn-auth">Sign In</button>
        </form>

        @if (Route::has('register'))
            <div class="auth-footer-text">
                Don't have an account?
                <a href="{{ route('register') }}" class="ms-1">Create one</a>
            </div>
        @endif

    @else

        <div class="text-center py-4">
            <div style="width:56px; height:56px; background:#f4f5f7; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
                <i class="feather-check" style="color:#c8292a; font-size:24px;"></i>
            </div>
            <h5 class="fw-bold mb-1" style="color:#1c1c1e;">Already Signed In</h5>
            <p style="font-size:0.845rem; color:#9898a8;" class="mb-4">You're already logged in.</p>
            <a href="{{ route('dashboard') }}" class="btn-auth" style="display:inline-block; padding:10px 32px; width:auto;">
                Go to Dashboard
            </a>
        </div>

    @endif
@endsection