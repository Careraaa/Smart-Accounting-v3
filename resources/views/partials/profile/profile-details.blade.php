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
    $role = ucfirst(str_replace('_', ' ', $u->role ?? 'user'));
@endphp

<div class="pf2-page">
    <div class="pf2-backdrop"><div class="pf2-grid"></div></div>
    <div class="pf2-content">

        <div class="pf2-topbar">
            <div>
                <h1 class="pf2-title">Profile</h1>
                <p class="pf2-sub">Your account details</p>
            </div>
        </div>

        <div class="pf2-hero">
            <div class="pf2-hero-left">
                <div class="pf2-avatar">{{ $initials }}</div>
                <div>
                    <p class="pf2-hero-name">{{ $u->name ?? '—' }}</p>
                    <p class="pf2-hero-meta">
                        <span class="pf2-mono">{{ $u->email ?? '—' }}</span>
                        <span style="margin:0 8px;color:#37415122;">•</span>
                        <span>{{ $role }}</span>
                    </p>
                    <div class="pf2-chip-row">
                        <span class="pf2-chip">
                            Joined {{ $u->created_at?->format('M Y') ?? '—' }}
                        </span>
                        <span class="pf2-chip">
                            Status: Active
                        </span>
                        <span class="pf2-chip">
                            @if(!empty($u->username))
                                {{ '@' . $u->username }}
                            @else
                                Username: —
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            <div class="pf2-hero-right">
                <a href="{{ route('profile.edit') }}" class="pf2-btn-primary">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Update Details
                </a>
            </div>
        </div>

        <div class="pf2-stats">
            <div class="pf2-stat s-blue">
                <div class="pf2-stat-icon">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 21a8 8 0 10-16 0" />
                    </svg>
                </div>
                <div>
                    <div class="pf2-stat-label">Role</div>
                    <div class="pf2-stat-value">{{ $role }}</div>
                    <div class="pf2-stat-sub">account type</div>
                </div>
            </div>
            <div class="pf2-stat s-amber">
                <div class="pf2-stat-icon">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" />
                    </svg>
                </div>
                <div>
                    <div class="pf2-stat-label">Username</div>
                    <div class="pf2-stat-value pf2-mono" style="font-size:1.05rem;">{{ $u->username ?? '—' }}</div>
                    <div class="pf2-stat-sub">login handle</div>
                </div>
            </div>
            <div class="pf2-stat s-green">
                <div class="pf2-stat-icon">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18" />
                    </svg>
                </div>
                <div>
                    <div class="pf2-stat-label">Member Since</div>
                    <div class="pf2-stat-value" style="font-size:1.0rem;">{{ $u->created_at?->format('M d, Y') ?? '—' }}</div>
                    <div class="pf2-stat-sub">{{ $u->created_at?->format('l') ?? '' }}</div>
                </div>
            </div>
            <div class="pf2-stat s-red">
                <div class="pf2-stat-icon">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                        <circle cx="12" cy="12" r="9" />
                    </svg>
                </div>
                <div>
                    <div class="pf2-stat-label">Last Updated</div>
                    <div class="pf2-stat-value" style="font-size:1.0rem;">{{ $u->updated_at?->format('M d, Y') ?? '—' }}</div>
                    <div class="pf2-stat-sub">{{ $u->updated_at?->format('h:i A') ?? '' }}</div>
                </div>
            </div>
        </div>

        <div class="pf2-two-col">
            <div class="pf2-card">
                <div class="pf2-card-head">
                    <p class="pf2-card-title"><span class="pf2-dot"></span> Profile Details</p>
                </div>
                <div class="pf2-card-body">
                    <div class="pf2-kv">
                        <div class="pf2-k">Full Name</div>
                        <div>
                            <div class="pf2-v">{{ $u->name ?? '—' }}</div>
                            <div class="pf2-v-sub">Shown in records and reports</div>
                        </div>

                        <div class="pf2-k">Email</div>
                        <div>
                            <div class="pf2-v pf2-mono">{{ $u->email ?? '—' }}</div>
                            <div class="pf2-v-sub">Used for login and notifications</div>
                        </div>

                        <div class="pf2-k">Username</div>
                        <div>
                            <div class="pf2-v pf2-mono">{{ $u->username ?? '—' }}</div>
                            <div class="pf2-v-sub">Unique login identifier</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
