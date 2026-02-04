@extends('layouts.auth-layout')

@section('title', 'Forgot Password - Smart Accounting')

@section('content')
    <h2 class="fs-20 fw-bolder mb-4">Forgot Password</h2>
    <h4 class="fs-13 fw-bold mb-2">Reset Your Password</h4>
    <p class="fs-12 fw-medium text-muted">Enter your email address and we'll send you a link to reset your password.</p>

    <form method="POST" action="{{ route('password.email') }}" class="w-100 mt-4 pt-2">
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email') }}" required autofocus autocomplete="email" 
                   placeholder="your@email.com">
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-5">
            <button type="submit" class="btn btn-lg btn-primary w-100">Send Reset Link</button>
        </div>
    </form>

    <div class="mt-5 text-muted text-center">
        <span>Remember your password?</span>
        <a href="{{ route('login') }}" class="fw-bold">Back to Login</a>
    </div>
@endsection
