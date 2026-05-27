@extends('layouts.auth-layout')

@section('title', 'Sign In - Smart Accounting')

@push('styles')
<style>
.input-group {
  position: relative;
  margin-bottom: 1.25rem;
}

.input-group input {
  border: 1.5px solid #e5e7eb;
  border-radius: 0.75rem;
  background: none;
  padding: 1.25rem 1rem 0.5rem;
  font-size: 0.875rem;
  color: #111827;
  width: 100%;
  transition: border-color 150ms cubic-bezier(0.4,0,0.2,1);
  outline: none;
  box-sizing: border-box;
}

.input-group input:focus {
  border-color: #c8292a;
}

.input-group.input-error input {
  border-color: #fca5a5;
}

.input-group label {
  position: absolute;
  left: 1rem;
  top: 0;
  color: #9ca3af;
  pointer-events: none;
  transform: translateY(1rem);
  font-size: 0.875rem;
  transition: 150ms cubic-bezier(0.4,0,0.2,1);
  background: transparent;
  padding: 0;
}

.input-group input:focus ~ label,
.input-group input:not(:placeholder-shown) ~ label {
  transform: translateY(0.35rem) scale(0.8);
  background-color: #fff;
  padding: 0 0.3em;
  color: #c8292a;
}

.input-group.input-error label {
  color: #ef4444;
}

.input-group.input-error input:focus ~ label,
.input-group.input-error input:not(:placeholder-shown) ~ label {
  color: #ef4444;
}

.input-group .error-text {
  color: #ef4444;
  font-size: 0.75rem;
  margin-top: 0.25rem;
  margin-left: 0.25rem;
}
</style>
@endpush

@section('content')
    @if (!Auth::check())

        <div class="text-center mb-6">
            <img src="{{ asset('images/knights_white-bg.png') }}" alt="Knights Transport"
                class="hidden md:block mx-auto w-14 h-14 object-contain rounded-xl mb-5">
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Welcome</h1>
            <p class="text-sm text-gray-400 mt-1">Sign in to your account to continue.</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="input-group @error('username') input-error @enderror">
                <input id="username" type="text" name="username" required autocomplete="username"
                    value="{{ old('username') }}" autofocus placeholder=" ">
                <label for="username">Username</label>
                @error('username')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="input-group @error('password') input-error @enderror">
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder=" ">
                <label for="password">Password</label>
                @error('password')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex items-center justify-between mb-5">
                <label class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer select-none">
                    <input type="checkbox" id="remember" name="remember"
                        class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit"
                class="w-full bg-[#c8292a] text-white font-bold text-sm rounded-xl py-3 px-4 cursor-pointer shadow-[0_8px_24px_rgba(200,41,42,0.3)] hover:bg-[#a81f20] hover:shadow-[0_6px_20px_rgba(200,41,42,0.35)] active:scale-[0.98] focus:outline-none focus:ring-[3px] focus:ring-[rgba(200,41,42,0.2)] transition-all duration-150">
                Sign In
            </button>
        </form>

    @else

        <div class="text-center py-4">
            @php
                $user = Auth::user();
                $initials = strtoupper(
                    substr($user->first_name ?? $user->name ?? $user->username, 0, 1) .
                    substr($user->last_name ?? '', 0, 1)
                );
            @endphp
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gray-100 text-gray-700 font-bold text-lg mb-4">{{ $initials }}</div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Welcome back</h1>
            <p class="text-sm text-gray-500 mt-1 mb-6">
                You're signed in as <strong>{{ Auth::user()->name ?? Auth::user()->username }}</strong>.
            </p>
            <a href="{{ route('dashboard') }}"
                class="inline-block bg-[#c8292a] text-white font-bold text-sm rounded-xl py-2.5 px-8 shadow-[0_8px_24px_rgba(200,41,42,0.3)] hover:bg-[#a81f20] hover:shadow-[0_6px_20px_rgba(200,41,42,0.35)] active:scale-[0.98] focus:outline-none focus:ring-[3px] focus:ring-[rgba(200,41,42,0.2)] transition-all duration-150 no-underline text-center">
                Go to Dashboard
            </a>
        </div>

    @endif
@endsection
