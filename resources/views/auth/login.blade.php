@extends('layouts.auth-layout')

@section('title', 'Login - Smart Accounting')

@section('content')
    @if(!Auth::check())
        <h2 class="fs-20 fw-bolder mb-4">Login</h2>
        <h4 class="fs-13 fw-bold mb-2">Sign In to your account</h4>
        <p class="fs-12 fw-medium text-muted">Welcome back to Smart Accounting System. Please enter your credentials to continue.</p>

        <form method="POST" action="{{ route('login') }}" class="w-100 mt-4 pt-2">
            @csrf

            <div class="mb-4">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                       placeholder="your@email.com">
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                       name="password" required autocomplete="current-password" placeholder="••••••••">
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex align-items-center justify-content-between">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>
                @if (Route::has('password.request'))
                    <div>
                        <a href="{{ route('password.request') }}" class="fs-11 text-primary">Forgot password?</a>
                    </div>
                @endif
            </div>

            <div class="mt-5">
                <button type="submit" class="btn btn-lg btn-primary w-100">Sign In</button>
            </div>
        </form>

        <div class="mt-5 text-muted text-center">
            <span>Don't have an account?</span>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="fw-bold">Create one now</a>
            @endif
        </div>
    @else
        <div class="text-center">
            <h3 class="mb-3">You are already logged in</h3>
            <p class="text-muted mb-4">Redirecting to dashboard...</p>
            <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
        </div>
    @endif
@endsection
