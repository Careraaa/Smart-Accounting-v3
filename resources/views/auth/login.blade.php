@extends('layouts.auth-layout')

@section('title', 'Sign In - Smart Accounting')

@push('styles')
<style>
.input-group.input-error input {
  border-color: #fca5a5 !important;
}
.input-group.input-error input:focus-visible {
  border-color: #ef4444 !important;
  ring-color: rgba(239, 68, 68, 0.08) !important;
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

            <div class="relative flex flex-row items-center mb-8 @error('username') input-group input-error @enderror">
                <input id="username" type="text" name="username" required autocomplete="username"
                    value="{{ old('username') }}" autofocus placeholder=""
                    class="peer w-full h-[40px] px-3 text-sm border border-gray-200 rounded-xl bg-white outline-none focus-visible:border-[#c8292a] focus-visible:ring-4 focus-visible:ring-[rgba(200,41,42,0.08)] transition-colors">
                <label for="username"
                    class="absolute left-3 text-sm text-gray-400 pointer-events-none duration-200 peer-focus-visible:-translate-y-8 peer-focus-visible:text-xs peer-focus-visible:text-[#c8292a] peer-[:not(:placeholder-shown)]:-translate-y-8 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:text-[#c8292a]">
                    Username
                </label>
                @error('username')
                    <p class="text-red-500 text-xs mt-1 ml-1 absolute -bottom-5 left-0">{{ $message }}</p>
                @enderror
            </div>

            <div class="relative flex flex-row items-center mb-5 @error('password') input-group input-error @enderror">
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder=""
                    class="peer w-full h-[40px] px-3 text-sm border border-gray-200 rounded-xl bg-white outline-none focus-visible:border-[#c8292a] focus-visible:ring-4 focus-visible:ring-[rgba(200,41,42,0.08)] transition-colors">
                <label for="password"
                    class="absolute left-3 text-sm text-gray-400 pointer-events-none duration-200 peer-focus-visible:-translate-y-8 peer-focus-visible:text-xs peer-focus-visible:text-[#c8292a] peer-[:not(:placeholder-shown)]:-translate-y-8 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:text-[#c8292a]">
                    Password
                </label>
                @error('password')
                    <p class="text-red-500 text-xs mt-1 ml-1 absolute -bottom-5 left-0">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mb-5 mt-6">
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
