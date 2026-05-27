@extends('layouts.layout')

@push('styles')
<style>
@keyframes acUp { 0%{opacity:0;transform:translateY(16px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes acIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.ac-h { animation:acUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.ac-card { animation:acUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.ac-card:nth-child(1){animation-delay:.1s}
.ac-card:nth-child(2){animation-delay:.18s}
</style>
@endpush

@section('content')
@php $u = auth()->user(); @endphp

<div class="min-h-screen bg-[#f6f7fb]">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">

        {{-- Header --}}
        <div class="ac-h flex items-start justify-between gap-4 mb-7">
            <div>
                <h1 class="text-lg sm:text-xl font-extrabold text-gray-900 tracking-tight">Account Settings</h1>
                <p class="text-sm text-gray-400 mt-0.5">Password &amp; security preferences</p>
            </div>
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition-all shadow-sm shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
        </div>

        {{-- Flash messages --}}
        @if ($errors->any())
            <div class="ac-h mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
                <div class="text-sm font-semibold text-rose-800">Please fix the errors below.</div>
            </div>
        @endif

        @if (session('success'))
            <div class="ac-h mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <div class="text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
            </div>
        @endif

        {{-- Two-column --}}
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-5 items-start">

            {{-- Change Password --}}
            <div class="ac-card bg-white rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.04)] border border-gray-200/80 overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                    <div class="w-6 h-6 rounded-md bg-rose-50 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/></svg>
                    </div>
                    <h2 class="text-sm font-extrabold text-gray-900">Change Password</h2>
                </div>
                <div class="p-5">
                    <form action="{{ route('settings.update-password') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1.5" for="current_password">Current Password *</label>
                            <input type="password"
                                   class="block w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-bold text-gray-900 bg-white outline-none transition-all focus:border-rose-400 focus:ring-2 focus:ring-rose-500/10 @error('current_password') border-rose-300 bg-rose-50/30 @enderror"
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                                <p class="mt-1 text-[0.7rem] font-semibold text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1.5" for="password">New Password *</label>
                            <input type="password"
                                   class="block w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-bold text-gray-900 bg-white outline-none transition-all focus:border-rose-400 focus:ring-2 focus:ring-rose-500/10 @error('password') border-rose-300 bg-rose-50/30 @enderror"
                                   id="password" name="password" required>
                            @error('password')
                                <p class="mt-1 text-[0.7rem] font-semibold text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="block text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1.5" for="password_confirmation">Confirm Password *</label>
                            <input type="password"
                                   class="block w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-bold text-gray-900 bg-white outline-none transition-all focus:border-rose-400 focus:ring-2 focus:ring-rose-500/10 @error('password_confirmation') border-rose-300 bg-rose-50/30 @enderror"
                                   id="password_confirmation" name="password_confirmation" required>
                            @error('password_confirmation')
                                <p class="mt-1 text-[0.7rem] font-semibold text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            Update Password
                        </button>
                    </form>
                </div>
            </div>

            {{-- Security Summary --}}
            <div class="ac-card bg-white rounded-xl shadow-[0_1px_3px_0_rgba(0,0,0,0.04)] border border-gray-200/80 overflow-hidden">
                <div class="flex items-center gap-2.5 px-5 py-4 border-b border-gray-100">
                    <div class="w-6 h-6 rounded-md bg-sky-50 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                    </div>
                    <h2 class="text-sm font-extrabold text-gray-900">Security Summary</h2>
                </div>
                <div class="p-5 space-y-3.5">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400">Account</div>
                            <div class="text-sm font-bold text-gray-900 mt-0.5">{{ $u->name ?? '—' }}</div>
                        </div>
                        <span class="text-[0.6rem] font-bold text-gray-400 uppercase">{{ ucfirst(str_replace('_', ' ', $u->role ?? 'user')) }}</span>
                    </div>
                    <div class="border-t border-gray-100"></div>
                    <div>
                        <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400">Email</div>
                        <div class="text-sm font-bold text-gray-900 font-mono mt-0.5">{{ $u->email ?? '—' }}</div>
                        <div class="text-[0.65rem] text-gray-400 mt-0.5">Used for notifications</div>
                    </div>
                    <div class="border-t border-gray-100"></div>
                    <div>
                        <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400">Last activity</div>
                        <div class="text-sm font-bold text-gray-900 mt-0.5">{{ $u->updated_at?->format('M d, Y') ?? '—' }}</div>
                        <div class="text-[0.65rem] text-gray-400 mt-0.5">{{ $u->updated_at?->format('h:i A') ?? '' }}</div>
                    </div>
                    <div class="border-t border-gray-100"></div>
                    <div class="text-[0.72rem] text-gray-400 leading-relaxed">
                        Tip: Use a strong password (8+ chars) and avoid reusing old passwords.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
