@extends('layouts.auth-layout')

@section('title', 'Sign In - Smart Accounting')

@section('content')
    @if (!Auth::check())

        <div class="text-center mb-6">
            <img src="{{ asset('images/knights_white-bg.png') }}" alt="Knights Transport"
                class="mx-auto w-14 h-14 md:w-16 md:h-16 object-contain rounded-xl mb-4">
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Welcome</h1>
            <p class="text-sm text-gray-400 mt-1">Sign in to your account to continue.</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="username" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Username</label>
                <input id="username" type="text" name="username" required autocomplete="username"
                    value="{{ old('username') }}" autofocus
                    class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl outline-none transition-colors focus:border-gray-400 focus:bg-gray-50/50 @error('username') border-red-300 bg-red-50/50 @enderror"
                    placeholder="Enter your username">
                @error('username')
                    <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl outline-none transition-colors focus:border-gray-400 focus:bg-gray-50/50 @error('password') border-red-300 bg-red-50/50 @enderror"
                    placeholder="Enter your password">
                @error('password')
                    <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
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
