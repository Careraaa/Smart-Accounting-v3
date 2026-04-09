@extends('layouts.layout')

@push('styles')
    @include('partials.profile._styles')
@endpush

@section('content')
@php
    $u = auth()->user();
@endphp

<div class="pf2-page">
    <div class="pf2-backdrop"><div class="pf2-grid"></div></div>
    <div class="pf2-content">

        <div class="pf2-topbar">
            <div>
                <h1 class="pf2-title">Account Settings</h1>
                <p class="pf2-sub">Password & security preferences</p>
            </div>
            <div class="pf2-actions">
                <a href="{{ route('profile.details') }}" class="pf2-btn">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Profile
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="prl-flash error" style="margin-bottom:18px;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01" />
                </svg>
                Please fix the errors below.
            </div>
        @endif

        @if (session('success'))
            <div class="prl-flash success" style="margin-bottom:18px;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="pf2-two-col">
            <div class="pf2-card">
                <div class="pf2-card-head">
                    <p class="pf2-card-title"><span class="pf2-dot"></span> Change Password</p>
                </div>
                <div class="pf2-card-body">
                    <form action="{{ route('settings.update-password') }}" method="POST">
                        @csrf

                        <div style="margin-bottom:14px;">
                            <label class="pf2-field-label" for="current_password">Current Password *</label>
                            <input type="password" class="pf2-input @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                            @error('current_password')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>

                        <div style="margin-bottom:14px;">
                            <label class="pf2-field-label" for="password">New Password *</label>
                            <input type="password" class="pf2-input @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>

                        <div style="margin-bottom:14px;">
                            <label class="pf2-field-label" for="password_confirmation">Confirm Password *</label>
                            <input type="password" class="pf2-input @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
                            @error('password_confirmation')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                        </div>

                        <button type="submit" class="pf2-btn-primary">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Update Password
                        </button>
                    </form>
                </div>
            </div>

            <div class="pf2-card">
                <div class="pf2-card-head">
                    <p class="pf2-card-title"><span class="pf2-dot"></span> Security Summary</p>
                </div>
                <div class="pf2-card-body">
                    <div class="pf2-kv">
                        <div class="pf2-k">Account</div>
                        <div>
                            <div class="pf2-v">{{ $u->name ?? '—' }}</div>
                            <div class="pf2-v-sub">Role: {{ ucfirst(str_replace('_', ' ', $u->role ?? 'user')) }}</div>
                        </div>

                        <div class="pf2-k">Email</div>
                        <div>
                            <div class="pf2-v pf2-mono">{{ $u->email ?? '—' }}</div>
                            <div class="pf2-v-sub">Used for notifications</div>
                        </div>

                        <div class="pf2-k">Last activity</div>
                        <div>
                            <div class="pf2-v">{{ $u->updated_at?->format('M d, Y') ?? '—' }}</div>
                            <div class="pf2-v-sub">{{ $u->updated_at?->format('h:i A') ?? '' }}</div>
                        </div>
                    </div>

                    <div class="pf2-divider"></div>

                    <div style="font-size:0.78rem;color:#9ca3af;line-height:1.6;">
                        Tip: Use a strong password (8+ chars) and avoid reusing old passwords.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
