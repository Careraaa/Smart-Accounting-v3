@extends('layouts.auth-layout')

@section('title', 'Reset Password - Smart Accounting')

@section('content')
    <div class="mb-5">
        <h1 class="auth-page-title">Reset Password</h1>
        <p class="auth-page-sub">Enter your new password below.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="w-100">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email"
                class="form-control @error('email') is-invalid @enderror"
                name="email" value="{{ old('email', $request->email) }}" required autofocus
                autocomplete="email" placeholder="your@email.com">
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input id="password" type="password"
                class="form-control @error('password') is-invalid @enderror"
                name="password" required autocomplete="new-password"
                placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <input id="password_confirmation" type="password"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                name="password_confirmation" required autocomplete="new-password"
                placeholder="••••••••">
            @error('password_confirmation')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-auth">Reset Password</button>
    </form>

    <div class="auth-footer-text">
        Remember your password? <a href="{{ route('login') }}">Back to Sign In</a>
    </div>
@endsection