@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&display=swap');
.acc-form-page { font-family: 'Sora', sans-serif; }

/* ── Topbar ─────────────────────────────────────────────────── */
.acc-form-topbar { display:flex;align-items:center;gap:12px;margin-bottom:24px; }
.acc-form-back { display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:8px;background:#f3f4f6;color:#6b7280;text-decoration:none;cursor:pointer;transition:all 0.15s; }
.acc-form-back:hover { background:#e5e7eb;color:#374151; }
.acc-form-back svg { width:18px;height:18px; }

.acc-form-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0; }

/* ── Form card ─────────────────────────────────────────────── */
.acc-form-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:28px;max-width:600px;margin:0 auto; }

/* ── Form groups ─────────────────────────────────────────────── */
.acc-form-group { margin-bottom:20px; }

.acc-form-label { display:block;font-size:0.82rem;font-weight:700;color:#111827;margin-bottom:8px;text-transform:capitalize; }

.acc-form-input {
    width:100%;
    border:1px solid #e5e7eb;
    border-radius:8px;
    padding:10px 12px;
    font-size:0.82rem;
    font-family:'Sora',sans-serif;
    color:#111827;
    background:#f9fafb;
    outline:none;
    transition:border-color 0.15s,background 0.15s,box-shadow 0.15s;
}

.acc-form-input:focus {
    border-color:#c8292a;
    background:#fff;
    box-shadow:0 0 0 3px rgba(200,41,42,0.08);
}

.acc-form-error {
    font-size:0.75rem;
    color:#c8292a;
    margin-top:6px;
    display:block;
}

/* ── Form footer ─────────────────────────────────────────────── */
.acc-form-footer { display:flex;gap:12px;justify-content:flex-end;margin-top:28px;padding-top:20px;border-top:1px solid #e5e7eb; }

.acc-btn-cancel {
    display:inline-flex;align-items:center;gap:6px;padding:9px 18px;
    background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;
    font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;
    text-decoration:none;cursor:pointer;transition:all 0.15s;
}
.acc-btn-cancel:hover { background:#e5e7eb;border-color:#d1d5db; }

.acc-btn-submit {
    display:inline-flex;align-items:center;gap:6px;padding:9px 18px;
    background:#111827;color:#fff;border:none;border-radius:8px;
    font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;
    cursor:pointer;transition:background 0.15s;white-space:nowrap;
}
.acc-btn-submit:hover { background:#000; }
.acc-btn-submit:disabled { background:#d1d5db;cursor:not-allowed; }

/* ── Section divider ─────────────────────────────────────────── */
.acc-form-section { margin-bottom:28px;padding-bottom:28px;border-bottom:1px solid #e5e7eb; }
.acc-form-section:last-of-type { border-bottom:none; }
.acc-form-section-title { font-size:0.86rem;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:16px; }

/* ── Alerts ────────────────────────────────────────────────── */
.acc-alert { padding:12px 16px;border-radius:8px;font-size:0.82rem;margin-bottom:20px; }
.acc-alert.error { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
.acc-alert.success { background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d; }
.acc-alert.info { background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8; }

/* ── Info box ────────────────────────────────────────────────── */
.acc-info-box { padding:16px;background:#f0f9ff;border:1px solid #bfdbfe;border-radius:8px;margin-bottom:20px; }
.acc-info-box p { margin:0;font-size:0.82rem;color:#1d4ed8;line-height:1.5; }
</style>
@endpush

@section('content')
<div class="acc-form-page">

    {{-- Topbar --}}
    <div class="acc-form-topbar">
        <a href="{{ route('superadmin.accounts.index') }}" class="acc-form-back" title="Go back">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="acc-form-title">Reset Password</h1>
    </div>

    {{-- Form card --}}
    <div class="acc-form-card">
        <div class="acc-form-section">
            <h3 class="acc-form-section-title">Reset Password for {{ $user->first_name }} {{ $user->last_name }}</h3>
            
            <div class="acc-info-box">
                <p>
                    <strong>Email:</strong> {{ $user->email }}<br>
                    <strong>Current Status:</strong> {{ ucfirst($user->status) }}
                </p>
            </div>

            <p style="font-size:0.82rem;color:#6b7280;margin-bottom:20px;">
                Click the button below to generate a new temporary password for this user. The temporary password will be displayed and you can share it with the user.
            </p>

            <form action="{{ route('superadmin.accounts.perform-reset-password', $user->id) }}" method="POST">
                @csrf

                <div class="acc-form-footer">
                    <a href="{{ route('superadmin.accounts.index') }}" class="acc-btn-cancel">Cancel</a>
                    <button type="submit" class="acc-btn-submit">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Generate New Password
                    </button>
                </div>
            </form>
        </div>

        @if(session('reset_password'))
        <div class="acc-form-section">
            <h3 class="acc-form-section-title">New Temporary Password</h3>
            
            <div class="acc-alert success">
                Password reset successful!
            </div>

            <div style="background:#f9fafb;border:2px dashed #16a34a;border-radius:8px;padding:16px;margin-bottom:20px;position:relative;">
                <p style="font-size:0.72rem;font-weight:700;color:#6b7280;text-transform:uppercase;margin:0 0 12px;">Temporary Password</p>
                <div style="display:flex;align-items:center;gap:12px;">
                    <code style="font-size:1.1rem;font-weight:700;color:#111827;letter-spacing:2px;flex:1;font-family:'DM Mono',monospace;">{{ session('reset_password') }}</code>
                    <button type="button" onclick="copyPassword()" class="acc-btn-sm" title="Copy Password" style="width:auto;padding:8px 12px;gap:6px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy
                    </button>
                </div>
            </div>

            <div class="acc-info-box" style="background:#fff0f0;border-color:#fecaca;color:#c8292a;">
                <p>
                    <strong>⚠ Important:</strong> Please share this temporary password with the user immediately. They should change it upon their next login. You cannot retrieve this password again.
                </p>
            </div>

            <div class="acc-form-footer">
                <a href="{{ route('superadmin.accounts.index') }}" class="acc-btn-cancel">Back to Accounts</a>
            </div>
        </div>
        @endif
    </div>

</div>

<script>
function copyPassword() {
    const passwordText = document.querySelector('code').innerText;
    navigator.clipboard.writeText(passwordText).then(() => {
        alert('Password copied to clipboard!');
    }).catch(() => {
        alert('Failed to copy password');
    });
}
</script>
@endsection
