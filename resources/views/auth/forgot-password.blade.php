@extends('layouts.auth-layout')

@section('title', 'Forgot Password - Smart Accounting')

@section('content')
    <div class="mb-5">
        <h1 class="auth-page-title">Forgot Password</h1>
        <p class="auth-page-sub">Enter your email and we'll send you a reset link.</p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="w-100">
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email"
                class="form-control @error('email') is-invalid @enderror"
                name="email" value="{{ old('email') }}" required autofocus
                autocomplete="email" placeholder="your@email.com">
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-auth">Send Reset Link</button>
    </form>

    <div class="auth-footer-text">
        Remember your password? <a href="{{ route('login') }}">Back to Sign In</a>
    </div>
@endsection