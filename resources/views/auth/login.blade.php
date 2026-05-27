@extends('layouts.auth-layout')

@section('title', 'Sign In - Smart Accounting')

@section('content')
    @if (!Auth::check())

        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Welcome</h1>
            <p class="text-sm text-gray-500 mt-1">Sign in to your account to continue.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="w-full">
            @csrf

            <div class="relative mb-6">
                <input id="username" type="text" required autocomplete="username"
                    value="{{ old('username') }}" autofocus
                    class="peer w-full text-sm px-4 py-[0.8em] outline-none border-2 border-gray-300 bg-transparent rounded-2xl transition-colors @error('username') border-red-500 @enderror peer-focus:border-[rgb(150,150,200)] peer-valid:border-[rgb(150,150,200)]"
                    name="username">
                <label for="username"
                    class="absolute left-0 text-sm text-gray-500 pointer-events-none transition-all duration-300 px-4 py-[0.8em] ml-2 peer-focus:-translate-y-1/2 peer-focus:scale-90 peer-focus:m-0 peer-focus:ml-[1.3em] peer-focus:px-[0.4em] peer-focus:py-[0.4em] peer-focus:bg-white peer-valid:-translate-y-1/2 peer-valid:scale-90 peer-valid:m-0 peer-valid:ml-[1.3em] peer-valid:px-[0.4em] peer-valid:py-[0.4em] peer-valid:bg-white">
                    Username
                </label>
                @error('username')
                    <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="relative mb-6">
                <input id="password" type="password" required autocomplete="current-password"
                    class="peer w-full text-sm px-4 py-[0.8em] outline-none border-2 border-gray-300 bg-transparent rounded-2xl transition-colors @error('password') border-red-500 @enderror peer-focus:border-[rgb(150,150,200)] peer-valid:border-[rgb(150,150,200)]"
                    name="password">
                <label for="password"
                    class="absolute left-0 text-sm text-gray-500 pointer-events-none transition-all duration-300 px-4 py-[0.8em] ml-2 peer-focus:-translate-y-1/2 peer-focus:scale-90 peer-focus:m-0 peer-focus:ml-[1.3em] peer-focus:px-[0.4em] peer-focus:py-[0.4em] peer-focus:bg-white peer-valid:-translate-y-1/2 peer-valid:scale-90 peer-valid:m-0 peer-valid:ml-[1.3em] peer-valid:px-[0.4em] peer-valid:py-[0.4em] peer-valid:bg-white">
                    Password
                </label>
                @error('password')
                    <p class="text-red-500 text-xs mt-1 ml-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mb-4">
                <label class="flex items-center gap-2 text-sm text-gray-500 cursor-pointer">
                    <input type="checkbox"
                        class="rounded border-gray-300 text-gray-900 focus:ring-gray-900 checked:bg-[#1c1c1e] checked:border-[#1c1c1e]"
                        id="remember" name="remember">
                    Remember me
                </label>
            </div>

            <button type="submit"
                class="w-full bg-[#c8292a] text-white font-bold text-sm rounded-lg py-3 px-4 cursor-pointer shadow-[0_10px_28px_rgba(200,41,42,0.34)] hover:bg-[#a81f20] hover:-translate-y-0.5 active:translate-y-0 focus:outline-none focus:ring-[3px] focus:ring-[rgba(200,41,42,0.2)] transition-all duration-150">
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
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Welcome back</h1>
            <p class="text-sm text-gray-500 mt-1 mb-6">
                You're signed in as <strong>{{ Auth::user()->name ?? Auth::user()->username }}</strong>.
            </p>
            <a href="{{ route('dashboard') }}"
                class="inline-block bg-[#c8292a] text-white font-bold text-sm rounded-lg py-2.5 px-8 shadow-[0_10px_28px_rgba(200,41,42,0.34)] hover:bg-[#a81f20] hover:-translate-y-0.5 active:translate-y-0 focus:outline-none focus:ring-[3px] focus:ring-[rgba(200,41,42,0.2)] transition-all duration-150 no-underline text-center">
                Go to Dashboard
            </a>
        </div>

    @endif
@endsection
