@extends('layouts.layout')

@push('styles')
<style>
@keyframes pfUp { 0%{opacity:0;transform:translateY(16px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes pfBounce { 0%{opacity:0;transform:scale(0.5)} 50%{transform:scale(1.12)} 100%{opacity:1;transform:scale(1)} }
@keyframes pfBadge { 0%{opacity:0;transform:translateX(-8px)} 100%{opacity:1;transform:translateX(0)} }
@keyframes pfIconPop { 0%{opacity:0;transform:scale(0.6)} 60%{transform:scale(1.08)} 100%{opacity:1;transform:scale(1)} }
@keyframes pfStagger { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
.pf-h { animation:pfUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.pf-avatar { animation:pfBounce 0.55s cubic-bezier(0.34,1.56,0.64,1) 0.15s both; }
.pf-badge { animation:pfBadge 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.pf-badge:nth-child(1){animation-delay:.28s}
.pf-badge:nth-child(2){animation-delay:.34s}
.pf-badge:nth-child(3){animation-delay:.4s}
.pf-stat-icon { animation:pfIconPop 0.45s cubic-bezier(0.34,1.56,0.64,1) both; }
.pf-stat:nth-child(1) .pf-stat-icon{animation-delay:.1s}
.pf-stat:nth-child(2) .pf-stat-icon{animation-delay:.14s}
.pf-stat:nth-child(3) .pf-stat-icon{animation-delay:.18s}
.pf-stat:nth-child(4) .pf-stat-icon{animation-delay:.22s}
.pf-card { animation:pfUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.pf-stagger > * { opacity:0; animation:pfStagger 0.4s cubic-bezier(0.16,1,0.3,1) forwards; }
.pf-stagger > *:nth-child(1){animation-delay:0.05s}
.pf-stagger > *:nth-child(2){animation-delay:0.10s}
.pf-stagger > *:nth-child(3){animation-delay:0.15s}
.pf-stagger > *:nth-child(4){animation-delay:0.20s}
.pf-stagger > *:nth-child(5){animation-delay:0.25s}
.pf-stagger > *:nth-child(6){animation-delay:0.30s}
</style>
@endpush

@section('content')
@php
    $u = auth()->user();
    $name = $u->first_name ? trim($u->first_name . ' ' . $u->last_name) : ($u->name ?? 'User');
    $role = ucfirst(str_replace('_', ' ', $u->role ?? 'user'));
    $initials = strtoupper(
        substr($u->first_name ?? $u->name ?? 'U', 0, 1) .
        substr($u->last_name ?? '', 0, 1)
    );
@endphp

<div class="py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        {{-- Flash --}}
        @if(session('success'))
            <div class="pf-h mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <div class="text-sm font-semibold text-emerald-800">{{ session('success') }}</div>
            </div>
        @endif

        {{-- Floating card --}}
        <div class="pf-card bg-white rounded-2xl shadow-sm border border-gray-200/80 overflow-hidden">
            {{-- Card Header: Avatar + Name --}}
            <div class="flex items-start gap-5 px-6 pt-6 pb-5 border-b border-gray-100">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="photo-upload-form" class="relative shrink-0">
                    @csrf
                    @method('PUT')
                    <label tabindex="0" class="pf-avatar block w-16 h-16 rounded-2xl overflow-hidden border-2 border-gray-200 shadow-sm cursor-pointer group relative" id="photo-label" role="button" aria-label="Upload profile photo">
                        @if($u->photo_url)
                            <img src="{{ $u->photo_url }}" alt="Photo" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-50 flex items-center justify-center text-gray-900 font-bold text-xl">
                                {{ $initials ?: 'U' }}
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 transition-opacity duration-200 rounded-2xl">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <input type="file" name="photo" accept="image/*" class="hidden" onchange="document.getElementById('photo-upload-form').submit();">
                    </label>
                </form>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-extrabold text-gray-900">{{ $name }}</h2>
                            <div class="text-sm text-gray-400 mt-0.5 truncate">{{ $u->email ?? '—' }}</div>
                        </div>
                        <a href="{{ route('settings.account') }}"
                           class="pf-h inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-gray-700 bg-gray-50 border border-gray-200 hover:border-rose-300 hover:text-rose-600 hover:bg-rose-50/50 transition-all shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                            Settings
                        </a>
                    </div>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="pf-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.7rem] font-semibold bg-rose-50 text-rose-700 border border-rose-200/60">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $role }}
                        </span>
                        <span class="pf-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.7rem] font-semibold bg-sky-50 text-sky-700 border border-sky-200/60">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                            Joined {{ $u->created_at?->format('M Y') ?? '—' }}
                        </span>
                        @if($u->status)
                            <span class="pf-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.7rem] font-semibold {{ $u->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-gray-100 text-gray-600 border border-gray-200/60' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $u->status === 'active' ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                {{ ucfirst($u->status) }}
                            </span>
                        @endif
                        @if($u->username)
                            <span class="pf-badge inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.7rem] font-semibold bg-violet-50 text-violet-700 border border-violet-200/60">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M4.501 20.118a7.5 7.5 0 0114.998 0"/></svg>
                                {{ $u->username }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Card Body --}}
            <div class="p-6">
                {{-- Stats --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-7">
                    <div class="pf-stat group bg-gray-50 rounded-xl p-4 flex items-center gap-3 border border-gray-200/60 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-sm">
                        <span class="pf-stat-icon w-10 h-10 rounded-lg bg-rose-50 flex items-center justify-center shrink-0 group-hover:bg-rose-100 transition-colors">
                            <svg class="w-5 h-5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0"/></svg>
                        </span>
                        <div class="min-w-0">
                            <div class="text-[0.65rem] font-bold uppercase tracking-[0.08em] text-gray-400 truncate">Role</div>
                            <div class="text-sm font-extrabold text-gray-900 truncate">{{ $role }}</div>
                        </div>
                    </div>
                    <div class="pf-stat group bg-gray-50 rounded-xl p-4 flex items-center gap-3 border border-gray-200/60 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-sm">
                        <span class="pf-stat-icon w-10 h-10 rounded-lg bg-sky-50 flex items-center justify-center shrink-0 group-hover:bg-sky-100 transition-colors">
                            <svg class="w-5 h-5 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        </span>
                        <div class="min-w-0">
                            <div class="text-[0.65rem] font-bold uppercase tracking-[0.08em] text-gray-400 truncate">Email</div>
                            <div class="text-sm font-extrabold text-gray-900 truncate">{{ $u->email ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="pf-stat group bg-gray-50 rounded-xl p-4 flex items-center gap-3 border border-gray-200/60 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-sm">
                        <span class="pf-stat-icon w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0 group-hover:bg-emerald-100 transition-colors">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M4.501 20.118a7.5 7.5 0 0114.998 0"/></svg>
                        </span>
                        <div class="min-w-0">
                            <div class="text-[0.65rem] font-bold uppercase tracking-[0.08em] text-gray-400 truncate">Username</div>
                            <div class="text-sm font-extrabold text-gray-900 font-mono truncate">{{ $u->username ?? '—' }}</div>
                        </div>
                    </div>
                    <div class="pf-stat group bg-gray-50 rounded-xl p-4 flex items-center gap-3 border border-gray-200/60 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-sm">
                        <span class="pf-stat-icon w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center shrink-0 group-hover:bg-amber-100 transition-colors">
                            <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                        </span>
                        <div class="min-w-0">
                            <div class="text-[0.65rem] font-bold uppercase tracking-[0.08em] text-gray-400 truncate">Member Since</div>
                            <div class="text-sm font-extrabold text-gray-900 truncate">{{ $u->created_at?->format('M Y') ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Account Information --}}
                <div>
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-5 h-5 rounded-md bg-rose-50 flex items-center justify-center">
                            <svg class="w-3 h-3 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0"/></svg>
                        </div>
                        <h2 class="text-sm font-extrabold text-gray-900">Account Information</h2>
                    </div>
                    <div class="pf-stagger grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-5">
                        <div>
                            <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1">First Name</div>
                            <div class="text-sm font-bold text-gray-900">{{ $u->first_name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1">Last Name</div>
                            <div class="text-sm font-bold text-gray-900">{{ $u->last_name ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1">Email</div>
                            <div class="text-sm font-bold text-gray-900 break-all">{{ $u->email ?? '—' }}</div>
                        </div>
                        <div>
                            <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1">Username</div>
                            <div class="text-sm font-bold text-gray-900 font-mono">{{ $u->username ?? '—' }}</div>
                        </div>
                        @if($u->phone)
                        <div>
                            <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1">Phone</div>
                            <div class="text-sm font-bold text-gray-900 font-mono">{{ $u->phone }}</div>
                        </div>
                        @endif
                        @if($u->department)
                        <div>
                            <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1">Department</div>
                            <div class="text-sm font-bold text-gray-900">{{ $u->department }}</div>
                        </div>
                        @endif
                        @if($u->last_login_at)
                        <div>
                            <div class="text-[0.6rem] font-bold uppercase tracking-[0.08em] text-gray-400 mb-1">Last Login</div>
                            <div class="text-sm font-bold text-gray-900">{{ $u->last_login_at?->format('M d, Y h:i A') ?? '—' }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
