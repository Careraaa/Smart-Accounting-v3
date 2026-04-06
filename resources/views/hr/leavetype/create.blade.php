@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.lt-page { font-family: 'Sora', sans-serif; }

.lt-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.lt-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.lt-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

.lt-btn-sec {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.lt-btn-sec:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

.lt-form-wrap { max-width:680px;margin:0 auto; }

.lt-card { background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden; }

.lt-card-header { padding:20px 24px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px; }
.lt-card-icon { width:38px;height:38px;border-radius:10px;background:#fff0f0;color:#c8292a;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.lt-card-title { font-size:0.95rem;font-weight:800;color:#111827;margin:0 0 2px;letter-spacing:-0.01em; }
.lt-card-sub   { font-size:0.75rem;color:#9ca3af;margin:0; }

.lt-card-body   { padding:24px; }
.lt-card-footer { padding:16px 24px;border-top:1px solid #f3f4f6;background:#fafafa;display:flex;align-items:center;gap:10px; }

.lt-label {
    display:block;font-size:0.72rem;font-weight:700;text-transform:uppercase;
    letter-spacing:0.09em;color:#6b7280;margin-bottom:6px;
}
.lt-label .req { color:#c8292a; }

.lt-input, .lt-select, .lt-textarea {
    width:100%;border:1px solid #e5e7eb;border-radius:10px;
    padding:10px 14px;font-size:0.845rem;font-family:'Sora',sans-serif;
    color:#111827;background:#fff;outline:none;
    transition:border-color 0.15s,box-shadow 0.15s;
    appearance:none;-webkit-appearance:none;
}
.lt-input:focus, .lt-select:focus, .lt-textarea:focus {
    border-color:#c8292a;
    box-shadow:0 0 0 3px rgba(200,41,42,0.08);
}
.lt-input::placeholder, .lt-textarea::placeholder { color:#9ca3af; }
.lt-input.is-invalid, .lt-select.is-invalid, .lt-textarea.is-invalid { border-color:#ef4444 !important; }
.lt-invalid-feedback { display:block;font-size:0.75rem;color:#ef4444;margin-top:4px; }

.lt-select-wrap { position:relative; }
.lt-select-wrap svg { position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.lt-select-wrap .lt-select { padding-right:36px; }

.lt-textarea { resize:vertical;min-height:100px; }

.lt-row { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
@media (max-width:560px) { .lt-row { grid-template-columns:1fr; } }

.lt-field { margin-bottom:20px; }
.lt-field:last-of-type { margin-bottom:0; }

.lt-divider { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#9ca3af;border-bottom:1px solid #f3f4f6;padding-bottom:10px;margin-bottom:20px; }

/* Toggle switch */
.lt-toggle-wrap { display:flex;align-items:center;gap:12px;padding:12px 14px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px; }
.lt-toggle-label { font-size:0.845rem;color:#374151;font-weight:500; }
.lt-toggle-sub   { font-size:0.72rem;color:#9ca3af;margin-top:2px; }

.lt-btn-submit {
    display:inline-flex;align-items:center;gap:8px;padding:11px 24px;
    background:#111827;color:#fff;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:700;
    cursor:pointer;transition:background 0.15s;
}
.lt-btn-submit:hover { background:#000; }

.lt-btn-cancel {
    display:inline-flex;align-items:center;gap:7px;padding:11px 18px;
    background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:600;
    text-decoration:none;cursor:pointer;transition:all 0.15s;
}
.lt-btn-cancel:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

.lt-flash { display:flex;align-items:flex-start;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px;animation:ltFlashIn 0.3s ease; }
.lt-flash.error { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
.lt-flash ul { margin:6px 0 0 16px;padding:0; }
.lt-flash ul li { font-size:0.78rem;margin-bottom:2px; }
@keyframes ltFlashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')
<div class="lt-page">

    {{-- Flash --}}
    @if($errors->any())
    <div class="lt-flash error">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:2px;"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        <div>
            <strong>Please fix the errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Topbar --}}
    <div class="lt-topbar">
        <div>
            <h1 class="lt-topbar-title">Add Leave Type</h1>
            <p class="lt-topbar-sub">Define a new leave category and its policy</p>
        </div>
        <a href="{{ route('leave-type.index') }}" class="lt-btn-sec">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Leave Types
        </a>
    </div>

    {{-- Form card --}}
    <div class="lt-form-wrap">
        <div class="lt-card">

            <div class="lt-card-header">
                <div class="lt-card-icon">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="lt-card-title">Leave Type Details</p>
                    <p class="lt-card-sub">All fields marked <span style="color:#c8292a;">*</span> are required</p>
                </div>
            </div>

            <div class="lt-card-body">
                <form method="POST" action="{{ route('leave-type.store') }}" id="ltForm" novalidate>
                    @csrf

                    {{-- Name & Abbreviation --}}
                    <div class="lt-row lt-field">
                        <div>
                            <label for="name" class="lt-label">Leave Type Name <span class="req">*</span></label>
                            <input type="text" name="name" id="name"
                                class="lt-input @error('name') is-invalid @enderror"
                                placeholder="e.g., Vacation Leave"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <span class="lt-invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="abbreviation" class="lt-label">Abbreviation</label>
                            <input type="text" name="abbreviation" id="abbreviation"
                                class="lt-input @error('abbreviation') is-invalid @enderror"
                                placeholder="e.g., VL"
                                value="{{ old('abbreviation') }}" maxlength="5">
                            @error('abbreviation')
                                <span class="lt-invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Days & Status --}}
                    <div class="lt-divider">Policy</div>
                    <div class="lt-row lt-field">
                        <div>
                            <label for="days_allowed" class="lt-label">Days Allowed <span class="req">*</span></label>
                            <input type="number" name="days_allowed" id="days_allowed"
                                class="lt-input @error('days_allowed') is-invalid @enderror"
                                placeholder="0"
                                value="{{ old('days_allowed') }}" min="0" required>
                            @error('days_allowed')
                                <span class="lt-invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="status" class="lt-label">Status <span class="req">*</span></label>
                            <div class="lt-select-wrap">
                                <select name="status" id="status"
                                    class="lt-select @error('status') is-invalid @enderror"
                                    required>
                                    <option value="">— Select Status —</option>
                                    <option value="active"   @selected(old('status') === 'active')>Active</option>
                                    <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                                </select>
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            @error('status')
                                <span class="lt-invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Carry over --}}
                    <div class="lt-field">
                        <label class="lt-label">Carry Over Settings</label>
                        <label class="lt-toggle-wrap" style="cursor:pointer;">
                            <input type="checkbox" name="carry_over" id="carry_over" value="1"
                                {{ old('carry_over') ? 'checked' : '' }}
                                style="width:16px;height:16px;accent-color:#c8292a;flex-shrink:0;">
                            <div>
                                <div class="lt-toggle-label">Allow carry over</div>
                                <div class="lt-toggle-sub">Unused days roll over to the next period</div>
                            </div>
                        </label>
                        @error('carry_over')
                            <span class="lt-invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="lt-divider">Additional Info</div>
                    <div class="lt-field">
                        <label for="description" class="lt-label">Description</label>
                        <textarea name="description" id="description"
                            class="lt-textarea @error('description') is-invalid @enderror"
                            placeholder="Enter a brief description…">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="lt-invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                </form>
            </div>

            <div class="lt-card-footer">
                <button type="submit" form="ltForm" class="lt-btn-submit">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save Leave Type
                </button>
                <a href="{{ route('leave-type.index') }}" class="lt-btn-cancel">Cancel</a>
            </div>

        </div>
    </div>

</div>
@endsection