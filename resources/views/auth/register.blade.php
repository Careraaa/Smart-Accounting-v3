@extends('layouts.auth-layout')

@section('title', 'Register - Smart Accounting')

@section('content')
    <div class="mb-5">
        <h1 class="auth-page-title">Create Account</h1>
        <p class="auth-page-sub">Set up your account to get started.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="w-100">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input id="name" type="text"
                class="form-control @error('name') is-invalid @enderror"
                name="name" value="{{ old('name') }}" required autofocus
                autocomplete="name" placeholder="John Doe">
            @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email"
                class="form-control @error('email') is-invalid @enderror"
                name="email" value="{{ old('email') }}" required
                autocomplete="email" placeholder="your@email.com">
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password"
                class="form-control @error('password') is-invalid @enderror"
                name="password" required autocomplete="new-password"
                placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input id="password_confirmation" type="password"
                class="form-control @error('password_confirmation') is-invalid @enderror"
                name="password_confirmation" required autocomplete="new-password"
                placeholder="••••••••">
            @error('password_confirmation')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="role" class="form-label">Role</label>
            <select id="role"
                class="form-control @error('role') is-invalid @enderror"
                name="role" required>
                <option value="">Select a role</option>
                <option value="remittance_clerk" {{ old('role') === 'remittance_clerk' ? 'selected' : '' }}>Remittance Clerk</option>
                <option value="hr"               {{ old('role') === 'hr'               ? 'selected' : '' }}>HR</option>
                <option value="accountant"        {{ old('role') === 'accountant'        ? 'selected' : '' }}>Accountant</option>
            </select>
            @error('role')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check mb-4">
            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
            <label class="form-check-label" for="terms"
                style="font-size:0.815rem; color:#9898a8;">
                I agree to the <a href="#">Terms &amp; Conditions</a>
            </label>
        </div>

        <button type="submit" class="btn-auth">Create Account</button>
    </form>

    <div class="auth-footer-text">
        Already have an account? <a href="{{ route('login') }}">Sign in</a>
    </div>
@endsection