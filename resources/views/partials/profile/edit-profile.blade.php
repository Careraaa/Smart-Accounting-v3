@extends('layouts.layout')

@push('styles')
    @include('partials.profile._styles')
@endpush

@section('content')
@php
    $u = auth()->user();
    $initials = method_exists($u, 'getFirstLetter')
        ? strtoupper($u->getFirstLetter())
        : strtoupper(substr($u->name ?? 'U', 0, 1));
@endphp

<div class="pf2-page">
    <div class="pf2-backdrop"><div class="pf2-grid"></div></div>
    <div class="pf2-content">

        <div class="pf2-topbar">
            <div>
                <h1 class="pf2-title">Edit Profile</h1>
                <p class="pf2-sub">Update your account identity details</p>
            </div>
            <div class="pf2-actions">
                <a href="{{ route('profile.details') }}" class="pf2-btn">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </a>
            </div>
        </div>

        <div class="pf2-hero">
            <div class="pf2-hero-left">
                <div class="pf2-avatar">{{ $initials }}</div>
                <div>
                    <p class="pf2-hero-name">{{ $u->name ?? '—' }}</p>
                    <p class="pf2-hero-meta"><span class="pf2-mono">{{ $u->email ?? '—' }}</span></p>
                    <div class="pf2-chip-row">
                        <span class="pf2-chip">Tip: Use a valid email for password resets</span>
                    </div>
                </div>
            </div>
            <div class="pf2-hero-right">
                <a href="{{ route('settings.account') }}" class="pf2-btn">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Settings
                </a>
            </div>
        </div>

        <div class="pf2-two-col">
            <div class="pf2-card">
                <div class="pf2-card-head">
                    <p class="pf2-card-title"><span class="pf2-dot"></span> Update Details</p>
                </div>
                <div class="pf2-card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div style="margin-bottom:14px;">
                            <label class="pf2-field-label" for="name">Full Name *</label>
                            <input id="name" type="text" class="pf2-input @error('name') is-invalid @enderror" name="name" value="{{ old('name', $u->name) }}" required>
                            @error('name')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>

                        <div style="margin-bottom:14px;">
                            <label class="pf2-field-label" for="username">Username *</label>
                            <input id="username" type="text" class="pf2-input @error('username') is-invalid @enderror" name="username" value="{{ old('username', $u->username) }}" required>
                            @error('username')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>

                        <div style="margin-bottom:14px;">
                            <label class="pf2-field-label" for="email">Email Address *</label>
                            <input id="email" type="email" class="pf2-input @error('email') is-invalid @enderror" name="email" value="{{ old('email', $u->email) }}" required>
                            @error('email')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                            <div class="pf2-help">We’ll use this email for notifications and account recovery.</div>
                        </div>

                        <div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-top:18px;">
                            <a href="{{ route('profile.details') }}" class="pf2-btn">Cancel</a>
                            <button type="submit" class="pf2-btn-primary">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="pf2-card">
                <div class="pf2-card-head">
                    <p class="pf2-card-title"><span class="pf2-dot"></span> Notes</p>
                </div>
                <div class="pf2-card-body" style="color:#6b7280;font-size:0.82rem;line-height:1.7;">
                    <div style="font-weight:900;color:#111827;margin-bottom:6px;">Keep it consistent</div>
                    Your profile name and username appear across the system (attendance, payroll, reports).
                    <div class="pf2-divider"></div>
                    <div style="font-weight:900;color:#111827;margin-bottom:6px;">Security</div>
                    To change your password, go to Account Settings.
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
