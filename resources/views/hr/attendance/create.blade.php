@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.att-page { font-family: 'Sora', sans-serif; }

/* ── Topbar ─────────────────────────────────────────────────── */
.att-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.att-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.att-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

.att-btn-sec {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.att-btn-sec:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

/* ── Form card ──────────────────────────────────────────────── */
.att-form-wrap { max-width:680px;margin:0 auto; }

.att-card { background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden; }

.att-card-header { padding:20px 24px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px; }
.att-card-icon { width:38px;height:38px;border-radius:10px;background:#fff0f0;color:#c8292a;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.att-card-title { font-size:0.95rem;font-weight:800;color:#111827;margin:0 0 2px;letter-spacing:-0.01em; }
.att-card-sub   { font-size:0.75rem;color:#9ca3af;margin:0; }

.att-card-body  { padding:24px; }
.att-card-footer { padding:16px 24px;border-top:1px solid #f3f4f6;background:#fafafa;display:flex;align-items:center;gap:10px; }

/* ── Form field styles ──────────────────────────────────────── */
.att-label {
    display:block;font-size:0.72rem;font-weight:700;text-transform:uppercase;
    letter-spacing:0.09em;color:#6b7280;margin-bottom:6px;
}
.att-label .req { color:#c8292a; }

.att-input, .att-select {
    width:100%;border:1px solid #e5e7eb;border-radius:10px;
    padding:10px 14px;font-size:0.845rem;font-family:'Sora',sans-serif;
    color:#111827;background:#fff;outline:none;
    transition:border-color 0.15s,box-shadow 0.15s;
    appearance:none;-webkit-appearance:none;
}
.att-input:focus, .att-select:focus {
    border-color:#c8292a;
    box-shadow:0 0 0 3px rgba(200,41,42,0.08);
}
.att-input::placeholder { color:#9ca3af; }
.att-input.is-invalid, .att-select.is-invalid { border-color:#ef4444 !important; }
.att-invalid-feedback { display:block;font-size:0.75rem;color:#ef4444;margin-top:4px; }

/* Select wrapper with chevron */
.att-select-wrap { position:relative; }
.att-select-wrap svg { position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.att-select-wrap .att-select { padding-right:36px; }

/* Two-column row */
.att-row { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
@media (max-width:560px) { .att-row { grid-template-columns:1fr; } }

/* Field group */
.att-field { margin-bottom:20px; }
.att-field:last-of-type { margin-bottom:0; }

/* Divider */
.att-divider { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#9ca3af;border-bottom:1px solid #f3f4f6;padding-bottom:10px;margin-bottom:20px; }

/* ── Action buttons ─────────────────────────────────────────── */
.att-btn-submit {
    display:inline-flex;align-items:center;gap:8px;padding:11px 24px;
    background:#111827;color:#fff;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:700;
    cursor:pointer;transition:background 0.15s;
}
.att-btn-submit:hover { background:#000; }

.att-btn-cancel {
    display:inline-flex;align-items:center;gap:7px;padding:11px 18px;
    background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:600;
    text-decoration:none;cursor:pointer;transition:all 0.15s;
}
.att-btn-cancel:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

/* ── Flash messages ─────────────────────────────────────────── */
.att-flash { display:flex;align-items:center;gap:10px;padding:12px 16px;border-radius:10px;font-size:0.82rem;font-weight:500;margin-bottom:20px;animation:flashIn 0.3s ease; }
.att-flash.error { background:#fff0f0;border:1px solid #fecaca;color:#c8292a; }
@keyframes flashIn { from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)} }
</style>
@endpush

@section('content')
<div class="att-page" data-global-datepicker="off">

    {{-- Flash --}}
    @if(session('error'))
    <div class="att-flash error">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Topbar --}}
    <div class="att-topbar">
        <div>
            <h1 class="att-topbar-title">Manual Attendance Log</h1>
            <p class="att-topbar-sub">Record an attendance entry manually for an employee</p>
        </div>
        <a href="{{ url()->previous() }}" class="att-btn-sec">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    {{-- Form card --}}
    <div class="att-form-wrap">
        <div class="att-card">

            <div class="att-card-header">
                <div class="att-card-icon">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg>
                </div>
                <div>
                    <p class="att-card-title">Attendance Entry</p>
                    <p class="att-card-sub">All fields marked <span style="color:#c8292a;">*</span> are required</p>
                </div>
            </div>

            <div class="att-card-body">
                <form action="{{ route('attendance.store') }}" method="POST" id="attForm">
                    @csrf

                    {{-- Employee --}}
                    <div class="att-field">
                        <label for="user_id" class="att-label">Employee <span class="req">*</span></label>
                        <div class="att-select-wrap">
                            <select name="user_id" id="user_id"
                                class="att-select @error('user_id') is-invalid @enderror"
                                >
                                <option value="">— Select an employee —</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" @selected(old('user_id') == $employee->id)>
                                        {{ $employee->first_name }} {{ $employee->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                        @error('user_id')
                            <span class="att-invalid-feedback">{{ $message }}</span>
                        @enderror
                        <p class="att-card-sub" style="margin-top:8px;">You can also type the employee username below.</p>
                    </div>

                    <div class="att-field">
                        <label for="employee_identifier" class="att-label">Employee Username <span style="text-transform:none;letter-spacing:0;color:#9ca3af;">(optional)</span></label>
                        <input type="text" name="employee_identifier" id="employee_identifier"
                            class="att-input @error('employee_identifier') is-invalid @enderror"
                            value="{{ old('employee_identifier') }}"
                            placeholder="e.g. juan.delacruz">
                        @error('employee_identifier')
                            <span class="att-invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Date --}}
                    <div class="att-field">
                        <label for="date" class="att-label">Date <span class="req">*</span></label>
                        <input type="date" name="date" id="date"
                            class="att-input @error('date') is-invalid @enderror"
                            value="{{ old('date', today()->toDateString()) }}"
                            required>
                        @error('date')
                            <span class="att-invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Time In / Out --}}
                    <div class="att-divider">Time</div>
                    <div class="att-row att-field">
                        <div>
                            <label for="time_in" class="att-label">Time In</label>
                            <input type="time" name="time_in" id="time_in"
                                class="att-input @error('time_in') is-invalid @enderror"
                                value="{{ old('time_in') }}">
                            @error('time_in')
                                <span class="att-invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="time_out" class="att-label">Time Out</label>
                            <input type="time" name="time_out" id="time_out"
                                class="att-input @error('time_out') is-invalid @enderror"
                                value="{{ old('time_out') }}">
                            @error('time_out')
                                <span class="att-invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="att-divider" style="margin-top:4px;">Status</div>
                    <div class="att-field">
                        <label for="status" class="att-label">Attendance Status <span class="req">*</span></label>
                        <div class="att-select-wrap">
                            <select name="status" id="status"
                                class="att-select @error('status') is-invalid @enderror"
                                required>
                                <option value="">— Select Status —</option>
                                <option value="present"     @selected(old('status') == 'present')>Present</option>
                                <option value="absent"      @selected(old('status') == 'absent')>Absent</option>
                                <option value="late"        @selected(old('status') == 'late')>Late</option>
                                <option value="early_leave" @selected(old('status') == 'early_leave')>Early Leave</option>
                            </select>
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                        @error('status')
                            <span class="att-invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                </form>
            </div>

            <div class="att-card-footer">
                <button type="submit" form="attForm" class="att-btn-submit">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save Attendance
                </button>
                <a href="{{ route('attendance.index') }}" class="att-btn-cancel">
                    Cancel
                </a>
            </div>

        </div>
    </div>

</div>
@endsection