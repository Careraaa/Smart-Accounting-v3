@extends('layouts.auth-layout')

@section('title', 'Confirm Password - Smart Accounting')

@section('content')
    <div class="mb-5">
        <h1 class="auth-page-title">Confirm Password</h1>
        <p class="auth-page-sub">This area requires your password to continue.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="w-100">
        @csrf

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password"
                class="form-control @error('password') is-invalid @enderror"
                name="password" required autocomplete="current-password"
                placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-auth">Confirm &amp; Continue</button>
    </form>

    <div class="auth-footer-text">
        Need to reset? <a href="{{ route('password.request') }}">Reset Password</a>
    </div>
@endsection