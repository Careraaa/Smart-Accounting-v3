@extends('layouts.auth-layout')

@section('title', 'Sign In - Smart Accounting')

@push('styles')
<style>
.input-group.input-error input {
  border-color: #fca5a5 !important;
}
.input-group.input-error input:focus {
  border-color: #ef4444 !important;
}
.input-group.input-error label {
  color: #ef4444 !important;
}

input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
  -webkit-box-shadow: 0 0 0 1000px white inset !important;
  -webkit-text-fill-color: #111827 !important;
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

            <div class="input-group w-full h-14 relative rounded-xl mb-5 @error('username') input-error @enderror">
                <input id="username" type="text" name="username" required autocomplete="username"
                    value="{{ old('username') }}" autofocus
                    class="peer w-full h-full bg-white outline-none px-4 pt-3 text-sm rounded-xl border border-gray-200 transition-colors focus:border-[#c8292a]">
                <label for="username"
                    class="absolute top-1/2 -translate-y-1/2 bg-white left-4 px-1.5 text-sm text-gray-400 peer-focus:top-2 peer-focus:left-3 peer-focus:text-xs peer-focus:text-[#c8292a] peer-valid:top-2 peer-valid:left-3 peer-valid:text-xs peer-valid:text-[#c8292a] duration-150 pointer-events-none">
                    Username
                </label>
                @error('username')
                    <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="input-group w-full h-14 relative rounded-xl mb-2 @error('password') input-error @enderror">
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="peer w-full h-full bg-white outline-none px-4 pt-3 text-sm rounded-xl border border-gray-200 transition-colors focus:border-[#c8292a]">
                <label for="password"
                    class="absolute top-1/2 -translate-y-1/2 bg-white left-4 px-1.5 text-sm text-gray-400 peer-focus:top-2 peer-focus:left-3 peer-focus:text-xs peer-focus:text-[#c8292a] peer-valid:top-2 peer-valid:left-3 peer-valid:text-xs peer-valid:text-[#c8292a] duration-150 pointer-events-none">
                    Password
                </label>
                @error('password')
                    <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mb-5 mt-5">
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
