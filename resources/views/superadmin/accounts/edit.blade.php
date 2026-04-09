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
.acc-form-group-row { display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px; }
@media (max-width: 640px) {
    .acc-form-group-row { grid-template-columns:1fr; }
}

.acc-form-label { display:block;font-size:0.82rem;font-weight:700;color:#111827;margin-bottom:8px;text-transform:capitalize; }
.acc-form-label .required { color:#c8292a; }

.acc-form-input, .acc-form-select, .acc-form-textarea {
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

.acc-form-input:focus, .acc-form-select:focus, .acc-form-textarea:focus {
    border-color:#c8292a;
    background:#fff;
    box-shadow:0 0 0 3px rgba(200,41,42,0.08);
}

.acc-form-textarea {
    resize:vertical;
    min-height:80px;
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
</style>
@endpush

@section('content')
<div class="acc-form-page">

    {{-- Topbar --}}
    <div class="acc-form-topbar">
        <a href="{{ route('superadmin.accounts.index') }}" class="acc-form-back" title="Go back">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="acc-form-title">Edit Account</h1>
    </div>

    {{-- Error alert --}}
    @if ($errors->any())
        <div class="acc-alert error">
            <strong>Please fix the following errors:</strong>
            <ul style="margin:8px 0 0;padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form card --}}
    <div class="acc-form-card">
        <form action="{{ route('superadmin.accounts.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Basic Information Section --}}
            <div class="acc-form-section">
                <h3 class="acc-form-section-title">Basic Information</h3>

                <div class="acc-form-group-row">
                    <div class="acc-form-group">
                        <label class="acc-form-label">First Name <span class="required">*</span></label>
                        <input type="text" name="first_name" class="acc-form-input @error('first_name') is-invalid @enderror" value="{{ old('first_name', $user->first_name) }}" required>
                        @error('first_name')
                            <span class="acc-form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="acc-form-group">
                        <label class="acc-form-label">Last Name <span class="required">*</span></label>
                        <input type="text" name="last_name" class="acc-form-input @error('last_name') is-invalid @enderror" value="{{ old('last_name', $user->last_name) }}" required>
                        @error('last_name')
                            <span class="acc-form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="acc-form-group-row">
                    <div class="acc-form-group">
                        <label class="acc-form-label">Email <span class="required">*</span></label>
                        <input type="email" name="email" class="acc-form-input @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <span class="acc-form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="acc-form-group">
                        <label class="acc-form-label">Phone</label>
                        <input type="tel" name="phone" class="acc-form-input @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                        @error('phone')
                            <span class="acc-form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Account Information Section --}}
            <div class="acc-form-section">
                <h3 class="acc-form-section-title">Account Information</h3>

                <div class="acc-form-group">
                    <label class="acc-form-label">Username</label>
                    <input type="text" class="acc-form-input" value="{{ $user->username }}" disabled>
                    <small style="color:#9ca3af;margin-top:6px;display:block;">Username cannot be changed</small>
                </div>

                <div class="acc-form-group">
                    <label class="acc-form-label">Role <span class="required">*</span></label>
                    <select name="role" class="acc-form-select @error('role') is-invalid @enderror" required>
                        <option value="">-- Select role --</option>
                        <option value="superadmin" {{ old('role', $user->role) === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="hr" {{ old('role', $user->role) === 'hr' ? 'selected' : '' }}>HR</option>
                        <option value="accountant" {{ old('role', $user->role) === 'accountant' ? 'selected' : '' }}>Accountant</option>
                        <option value="remittance_clerk" {{ old('role', $user->role) === 'remittance_clerk' ? 'selected' : '' }}>Remittance Clerk</option>
                        <option value="qr_admin" {{ old('role', $user->role) === 'qr_admin' ? 'selected' : '' }}>QR Admin</option>
                        <option value="employee" {{ old('role', $user->role) === 'employee' ? 'selected' : '' }}>Employee</option>
                    </select>
                    @error('role')
                        <span class="acc-form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Employment Information Section --}}
            <div class="acc-form-section">
                <h3 class="acc-form-section-title">Employment Information</h3>

                <div class="acc-form-group-row">
                    <div class="acc-form-group">
                        <label class="acc-form-label">Position</label>
                        <input type="text" name="position" class="acc-form-input @error('position') is-invalid @enderror" value="{{ old('position', $user->position) }}">
                        @error('position')
                            <span class="acc-form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="acc-form-group">
                        <label class="acc-form-label">Department</label>
                        <input type="text" name="department" class="acc-form-input @error('department') is-invalid @enderror" value="{{ old('department', $user->department) }}">
                        @error('department')
                            <span class="acc-form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="acc-form-group-row">
                    <div class="acc-form-group">
                        <label class="acc-form-label">Date of Hire</label>
                        <input type="date" name="date_of_hire" class="acc-form-input @error('date_of_hire') is-invalid @enderror" value="{{ old('date_of_hire', $user->date_of_hire?->format('Y-m-d')) }}">
                        @error('date_of_hire')
                            <span class="acc-form-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="acc-form-group">
                        <label class="acc-form-label">Salary Rate</label>
                        <input type="number" name="salary_rate" class="acc-form-input @error('salary_rate') is-invalid @enderror" value="{{ old('salary_rate', $user->salary_rate) }}" step="0.01" min="0">
                        @error('salary_rate')
                            <span class="acc-form-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Form Footer --}}
            <div class="acc-form-footer">
                <a href="{{ route('superadmin.accounts.index') }}" class="acc-btn-cancel">Cancel</a>
                <button type="submit" class="acc-btn-submit">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Update Account
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
