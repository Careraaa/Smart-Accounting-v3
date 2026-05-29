@extends('layouts.auth-layout')

@section('title', 'Sign In - Smart Accounting')

@push('styles')
<style>
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
  -webkit-box-shadow: 0 0 0 1000px white inset !important;
  -webkit-text-fill-color: #111827 !important;
}

.form-group { animation: fadeUp 0.6s cubic-bezier(0.16,1,0.3,1) both; }
.form-group:nth-child(1) { animation-delay: 0.1s; }
.form-group:nth-child(2) { animation-delay: 0.2s; }
.form-group:nth-child(3) { animation-delay: 0.3s; }
.form-group:nth-child(4) { animation-delay: 0.4s; }

@keyframes fadeUp {
  0% { opacity: 0; transform: translateY(16px) scale(0.98); }
  100% { opacity: 1; transform: translateY(0) scale(1); }
}

@keyframes btnPulse {
  0%, 100% { box-shadow: 0 8px 24px rgba(200,41,42,0.3); }
  50% { box-shadow: 0 8px 32px rgba(200,41,42,0.45); }
}

.btn-submit { animation: btnPulse 3s ease-in-out infinite; }

@keyframes shakeX {
  0%, 100% { transform: translateX(0); }
  10%, 50%, 90% { transform: translateX(-4px); }
  30%, 70% { transform: translateX(4px); }
}
.input-shake { animation: shakeX 0.5s ease-in-out; }

@keyframes popIn {
    0% { transform: scale(0.3); opacity: 0; }
    60% { transform: scale(1.2); }
    100% { transform: scale(1); opacity: 1; }
}
.eye-icon { transition: opacity 0.15s ease; }
.eye-pop { animation: popIn 0.35s cubic-bezier(0.34,1.56,0.64,1) both; }
</style>
@endpush

@section('content')
@if (!Auth::check())

    <div class="text-center mb-7 animate-[fadeUp_0.5s_cubic-bezier(0.16,1,0.3,1)_both]">
        <img src="{{ asset('images/knights-icon.png') }}" alt="Knights Transport"
            class="hidden md:block mx-auto w-14 h-14 object-contain rounded-xl mb-4">
        <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">Welcome</h1>
        <p class="text-sm text-gray-400 mt-1">Sign in to your account to continue.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        {{-- Username --}}
        <div class="form-group relative mb-6">
            <input type="text" name="username" id="username" required autocomplete="username"
                value="{{ old('username') }}" autofocus
                placeholder=" "
                class="peer w-full h-14 px-4 pt-5 pb-1 text-sm border-2 border-gray-200 rounded-xl bg-white outline-none transition-all duration-200 focus-visible:border-[#c8292a] focus-visible:shadow-[0_0_0_4px_rgba(200,41,42,0.08)] @error('username') border-red-300 focus-visible:border-red-500 @enderror">
            <label for="username"
                class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 pointer-events-none transition-all duration-200 peer-focus-visible:top-2 peer-focus-visible:-translate-y-0 peer-focus-visible:text-xs peer-focus-visible:text-[#c8292a] peer-[:not(:placeholder-shown)]:top-2 peer-[:not(:placeholder-shown)]:-translate-y-0 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:text-gray-500">
                Username
            </label>
            @error('username')
                <p class="text-xs text-red-500 mt-1.5 ml-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group relative mb-6">
            <input type="password" name="password" id="password" required autocomplete="current-password"
                placeholder=" "
                class="peer w-full h-14 px-4 pt-5 pb-1 pr-12 text-sm border-2 border-gray-200 rounded-xl bg-white outline-none transition-all duration-200 focus-visible:border-[#c8292a] focus-visible:shadow-[0_0_0_4px_rgba(200,41,42,0.08)] @error('password') border-red-300 focus-visible:border-red-500 @enderror">
            <label for="password"
                class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-400 pointer-events-none transition-all duration-200 peer-focus-visible:top-2 peer-focus-visible:-translate-y-0 peer-focus-visible:text-xs peer-focus-visible:text-[#c8292a] peer-[:not(:placeholder-shown)]:top-2 peer-[:not(:placeholder-shown)]:-translate-y-0 peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:text-gray-500">
                Password
            </label>
            <button type="button" id="togglePassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer bg-transparent border-none p-1.5 rounded-lg transition-colors focus:outline-none">
                <div class="relative w-5 h-5">
                    <svg id="eyeClosed" class="absolute inset-0 w-5 h-5 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                    </svg>
                    <svg id="eyeOpen" class="absolute inset-0 w-5 h-5 eye-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </button>
            @error('password')
                <p class="text-xs text-red-500 mt-1.5 ml-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember + Forgot --}}
        <div class="form-group flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer select-none group">
                <input type="checkbox" id="remember" name="remember"
                    class="rounded border-gray-300 text-[#c8292a] focus:ring-[#c8292a] transition-shadow duration-150">
                <span class="group-hover:text-gray-600 transition-colors duration-150">Remember me</span>
            </label>
        </div>

        {{-- Submit --}}
        <div class="form-group mt-6">
            <button type="submit"
                class="btn-submit relative w-full bg-[#c8292a] text-white font-bold text-sm rounded-xl py-3.5 px-4 cursor-pointer hover:bg-[#a81f20] active:scale-[0.97] focus:outline-none focus:ring-[3px] focus:ring-[rgba(200,41,42,0.25)] transition-all duration-150 overflow-hidden group">
                <span class="relative z-10 flex items-center justify-center gap-2">
                    Sign In
                    <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </span>
            </button>
        </div>
    </form>

    <script>
    (function () {
        var toggle = document.getElementById('togglePassword');
        var input  = document.getElementById('password');
        var closed = document.getElementById('eyeClosed');
        var open   = document.getElementById('eyeOpen');
        if (toggle && input && closed && open) {
            toggle.addEventListener('click', function () {
                var isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                closed.classList.toggle('hidden', isPassword);
                open.classList.toggle('hidden', !isPassword);
                var shown = isPassword ? open : closed;
                shown.classList.remove('eye-pop');
                void shown.offsetWidth;
                shown.classList.add('eye-pop');
            });
        }
        document.querySelectorAll('.form-group .text-red-500').forEach(function (el) {
            var group = el.closest('.form-group');
            if (group) group.classList.add('input-shake');
        });
    })();
    </script>

@else

    <div class="text-center py-4 animate-[fadeUp_0.5s_cubic-bezier(0.16,1,0.3,1)_both]">
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
