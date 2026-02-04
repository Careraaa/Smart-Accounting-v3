@extends('layouts.auth-layout')

@section('title', 'Confirm Password - Smart Accounting')

@section('content')
    <h2 class="fs-20 fw-bolder mb-4">Confirm Password</h2>
    <h4 class="fs-13 fw-bold mb-2">Security Check</h4>
    <p class="fs-12 fw-medium text-muted">Please confirm your password to continue with this sensitive operation.</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="w-100 mt-4 pt-2">
        @csrf

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" required autocomplete="current-password" placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mt-5">
            <button type="submit" class="btn btn-lg btn-primary w-100">Confirm Password</button>
        </div>
    </form>

    <div class="mt-5 text-muted text-center">
        <span>Don't have access?</span>
        <a href="{{ route('password.request') }}" class="fw-bold">Reset Password</a>
    </div>
@endsection
