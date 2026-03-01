@extends('layouts.auth-layout')

@section('title', 'Forgot Password - Smart Accounting')

@section('content')
    <div class="mb-5">
        <h1 class="auth-page-title">Password Reset</h1>
        <p class="auth-page-sub">Contact your administrator to reset your password.</p>
    </div>

    <div class="alert alert-info" role="alert">
        <p>For security reasons, password resets must be handled by your system administrator. Please contact them for assistance with resetting your password.</p>
    </div>

    <div class="auth-footer-text">
        Remember your password? <a href="{{ route('login') }}">Back to Sign In</a>
    </div>
@endsection