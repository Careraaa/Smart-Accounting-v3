@extends('layouts.layout')

@push('styles')
@include('superadmin.partials.prl-theme')
@endpush

@section('content')
<div class="prl-wrap">
    <a href="{{ route('superadmin.accounts.index') }}" class="prl-back-link">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back to accounts
    </a>
    <h1 class="prl-page-title">Reset password</h1>
    <p class="prl-page-sub">Generate a temporary password for {{ $user->first_name }} {{ $user->last_name }}.</p>

    <div class="prl-card">
        <div class="prl-card-head">
            <div class="prl-card-head-icon amber" aria-hidden="true">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
            </div>
            <div>
                <p class="prl-card-head-title">Confirm reset</p>
                <p class="prl-card-head-sub">Share the new password securely with the user</p>
            </div>
        </div>
        <div class="prl-card-body">
            <div class="prl-info-panel" style="margin-bottom:20px;">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <strong>Email:</strong> <span class="prl-mono">{{ $user->email }}</span><br>
                    <strong>Status:</strong> {{ ucfirst($user->status) }}
                </div>
            </div>

            <p style="font-size:0.84rem;color:#6b7280;margin:0 0 20px;line-height:1.55;">
                This generates a new temporary password and shows it once. Ask the user to change it after login.
            </p>

            <form action="{{ route('superadmin.accounts.perform-reset-password', $user->id) }}" method="POST">
                @csrf
                <div class="prl-form-footer" style="border-top:none;padding-top:0;margin-top:0;">
                    <a href="{{ route('superadmin.accounts.index') }}" class="prl-btn-cancel">Cancel</a>
                    <button type="submit" class="prl-btn-generate">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Generate password
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(session('reset_password'))
        <div class="prl-card" style="margin-top:20px;">
            <div class="prl-card-head">
                <div class="prl-card-head-icon green" aria-hidden="true">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="prl-card-head-title">Temporary password</p>
                    <p class="prl-card-head-sub">Copy and share securely — it will not be shown again</p>
                </div>
            </div>
            <div class="prl-card-body">
                <div class="prl-alert success" style="margin-bottom:16px;">Password reset successful.</div>

                <div class="prl-code-box">
                    <p class="prl-code-label">One-time password</p>
                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                        <code id="sa-temp-password" style="flex:1;min-width:0;word-break:break-all;">{{ session('reset_password') }}</code>
                        <button type="button" onclick="copyPassword()" class="prl-btn-sec" style="flex-shrink:0;">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Copy
                        </button>
                    </div>
                </div>

                <div class="prl-warn-panel">
                    <strong>Important:</strong> Share this password immediately. The user should change it on next login.
                </div>

                <div class="prl-form-footer" style="border-top:none;padding-top:0;margin-top:8px;">
                    <a href="{{ route('superadmin.accounts.index') }}" class="prl-btn-generate">Back to accounts</a>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function copyPassword() {
    const el = document.getElementById('sa-temp-password');
    if (!el) return;
    const text = el.textContent.trim();
    navigator.clipboard.writeText(text).then(function () {
        alert('Copied to clipboard.');
    }).catch(function () {
        alert('Could not copy.');
    });
}
</script>
@endsection
