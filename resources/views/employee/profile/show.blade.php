@extends('layouts.layout')

@push('styles')
    @include('employee.profile._styles')
@endpush

@section('content')
<div class="pf-page pf-wrap">
    <div class="pf-backdrop"><div class="pf-grid"></div></div>
    <div class="pf-content">

    <div class="pf-topbar">
        <div>
            <h1 class="pf-topbar-title">My Profile</h1>
            <p class="pf-topbar-sub">View and manage your personal information</p>
        </div>
    </div>

    @if (session('success'))
        <div class="prl-flash success" style="margin-bottom:18px;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @php
        $u = auth()->user();
        $initials = strtoupper(substr($u->first_name ?? $u->name ?? 'U', 0, 1) . substr($u->last_name ?? '', 0, 1));
        $dept = $u->department ?? '—';
        $role = ucfirst(str_replace('_', ' ', $u->role ?? 'user'));
    @endphp

    <div class="pf-hero">
        <div class="pf-hero-left">
            <div class="pf-avatar">{{ $initials }}</div>
            <div>
                <p class="pf-hero-name">{{ $u->first_name ? ($u->first_name . ' ' . $u->last_name) : $u->name }}</p>
                <p class="pf-hero-meta">
                    <span class="pf-mono">{{ $u->email ?? '—' }}</span>
                    <span style="margin:0 8px;color:#37415122;">•</span>
                    <span>{{ $dept }}</span>
                </p>
                <div class="pf-chip-row">
                    <span class="pf-chip">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M5 11h14M5 7h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2z"/>
                        </svg>
                        Joined {{ $u->created_at?->format('M Y') ?? '—' }}
                    </span>
                    <span class="pf-chip">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5s-3 1.343-3 3 1.343 3 3 3z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21a7 7 0 10-14 0"/>
                        </svg>
                        {{ $role }}
                    </span>
                    @if($u->status ?? null)
                        <span class="pf-chip">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ ucfirst($u->status) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="pf-hero-right">
            <a href="{{ route('employee.profile.edit') }}" class="pf-btn-primary">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125L16.862 4.487" />
                </svg>
                Update Details
            </a>
        </div>
    </div>

    <div class="pf-stats">
        <div class="pf-stat s-blue">
            <div class="pf-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 21a8 8 0 10-16 0" />
                </svg>
            </div>
            <div>
                <div class="pf-stat-label">Role</div>
                <div class="pf-stat-value">{{ $role }}</div>
                <div class="pf-stat-sub">account type</div>
            </div>
        </div>
        <div class="pf-stat s-amber">
            <div class="pf-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" />
                </svg>
            </div>
            <div>
                <div class="pf-stat-label">Department</div>
                <div class="pf-stat-value">{{ $dept }}</div>
                <div class="pf-stat-sub">assignment</div>
            </div>
        </div>
        <div class="pf-stat s-green">
            <div class="pf-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2 8.5C2 6.567 3.567 5 5.5 5h13C20.433 5 22 6.567 22 8.5v7c0 1.933-1.567 3.5-3.5 3.5h-13C3.567 19 2 17.433 2 15.5v-7z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12h.01M10 12h.01M14 12h.01M18 12h.01" />
                </svg>
            </div>
            <div>
                <div class="pf-stat-label">Phone</div>
                <div class="pf-stat-value pf-mono" style="font-size:1.05rem;">{{ $u->phone ?? '—' }}</div>
                <div class="pf-stat-sub">contact</div>
            </div>
        </div>
        <div class="pf-stat s-red">
            <div class="pf-stat-icon">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" />
                    <path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18" />
                </svg>
            </div>
            <div>
                <div class="pf-stat-label">Member Since</div>
                <div class="pf-stat-value" style="font-size:1.0rem;">{{ $u->created_at?->format('M d, Y') ?? '—' }}</div>
                <div class="pf-stat-sub">{{ $u->created_at?->format('l') ?? '' }}</div>
            </div>
        </div>
    </div>

    <div class="pf-two-col">
        <div class="pf-card">
            <div class="pf-card-head">
                <p class="pf-card-title"><span class="pf-dot"></span> Personal Information</p>
                <a href="{{ route('employee.profile.edit') }}" class="pf-btn-sec" style="padding:7px 12px;border-radius:9px;">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                    </svg>
                    Edit
                </a>
            </div>
            <div class="pf-card-body">
                @include('partials.employee.personal', ['readOnly' => true])
            </div>
        </div>

    </div>

</div></div>
@endsection
