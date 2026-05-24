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
.att-card-footer { padding:16px 24px;border-top:1px solid #f3f4f6;background:#fafafa;display:flex;align-items:center;justify-content:flex-end;gap:10px; }

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

                    {{-- OT/UT preview hint --}}
                    <div id="otut-hint" style="display:none;margin-top:4px;padding:12px 14px;border-radius:10px;font-size:0.8rem;font-weight:600;line-height:1.5;"></div>

                </form>
            </div>

            <div class="att-card-footer">
                <button type="submit" form="attForm" class="att-btn-submit">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Save Attendance
                </button>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    // Standard 8 working hours
    const STANDARD_MIN  = 480; // 8h × 60

    const timeIn  = document.getElementById('time_in');
    const timeOut = document.getElementById('time_out');
    const hint    = document.getElementById('otut-hint');

    // Shift break times and settings (fetched via AJAX)
    let breakStart = null;      // minutes from midnight
    let breakEnd = null;        // minutes from midnight
    let gracePeriodMin = {{ $gracePeriodMinutes }};     // Grace period in minutes from server
    let shiftStart = null;      // Shift start time in minutes from midnight

    function toMinutes(hhmm) {
        if (!hhmm) return null;
        const [h, m] = hhmm.split(':').map(Number);
        return h * 60 + m;
    }

    function timeStringToMinutes(hhmmss) {
        if (!hhmmss) return null;
        const [h, m, s] = hhmmss.split(':').map(Number);
        return h * 60 + m;
    }

    function fmt(totalMin) {
        const h = Math.floor(totalMin / 60);
        const m = totalMin % 60;
        if (h > 0 && m > 0) return `${h}h ${m}m`;
        if (h > 0) return `${h}h`;
        return `${m}m`;
    }

    // Convert minutes to 0.5-hour increments (matches backend conversion)
    // floor(minutes / 30) * 0.5
    function convertMinutesToHourIncrement(minutes) {
        return Math.floor(minutes / 30) * 0.5;
    }

    // Convert hours (0.5 increments) back to minutes for display
    function hoursToMinutes(hours) {
        return Math.round(hours * 60);
    }

    function calculateBreakOverlap(inMin, outMin) {
        if (breakStart === null || breakEnd === null) return 0;

        // Calculate overlap between [inMin, outMin] and [breakStart, breakEnd]
        // overlapStart = max(inMin, breakStart)
        // overlapEnd = min(outMin, breakEnd)
        // overlap = max(0, overlapEnd - overlapStart)

        const overlapStart = Math.max(inMin, breakStart);
        const overlapEnd = Math.min(outMin, breakEnd);

        return Math.max(0, overlapEnd - overlapStart);
    }

    function update() {
        let inMin  = toMinutes(timeIn.value);
        const outMin = toMinutes(timeOut.value);

        if (inMin === null || outMin === null || outMin <= inMin) {
            hint.style.display = 'none';
            return;
        }

        // Apply grace period logic: if time in is within grace period, use shift start time
        if (shiftStart !== null) {
            const minutesLate = inMin - shiftStart;
            if (minutesLate >= 0 && minutesLate <= gracePeriodMin) {
                // Employee is within grace period; use scheduled start time for OT/UT computation
                inMin = shiftStart;
            }
        }

        // Calculate break overlap instead of using fixed break
        const breakOverlapMin = calculateBreakOverlap(inMin, outMin);
        const workedMin = Math.max(0, (outMin - inMin) - breakOverlapMin);
        const diff      = workedMin - STANDARD_MIN;

        // Convert to 0.5-hour increments (matching backend logic)
        const diffHours = convertMinutesToHourIncrement(Math.abs(diff));
        
        // Only show OT/UT if it converts to at least 0.5 hours (30 minutes)
        if (diffHours === 0) {
            hint.style.display = 'none';
            return;
        }

        const isOT = diff > 0;
        const displayMin = hoursToMinutes(diffHours);

        hint.style.display = 'block';

        if (isOT) {
            hint.style.background  = '#f0fdf4';
            hint.style.border      = '1px solid #bbf7d0';
            hint.style.color       = '#15803d';
            hint.innerHTML =
                `<svg style="display:inline;vertical-align:-3px;margin-right:6px;" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/></svg>` +
                `<strong>Overtime detected:</strong> ${fmt(displayMin)} beyond the 8h schedule — an OT record will be auto-created and approved on save.`;
        } else {
            hint.style.background  = '#fffbeb';
            hint.style.border      = '1px solid #fde68a';
            hint.style.color       = '#b45309';
            hint.innerHTML =
                `<svg style="display:inline;vertical-align:-3px;margin-right:6px;" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>` +
                `<strong>Undertime detected:</strong> ${fmt(displayMin)} short of the 8h schedule — a UT record will be auto-created and approved on save.`;
        }
    }

    // Fetch shift break times and grace period from API
    fetch('{{ route("api.shift.break-times") }}')
        .then(res => res.json())
        .then(data => {
            breakStart = timeStringToMinutes(data.break_start);
            breakEnd = timeStringToMinutes(data.break_end);
            shiftStart = timeStringToMinutes(data.start_time);
            gracePeriodMin = data.grace_period_minutes || 5;
            // Re-calculate preview with fetched break times and grace period
            update();
        })
        .catch(err => {
            console.warn('Failed to fetch shift break times:', err);
            // Fallback: use default 1 hour break (12:00-13:00 = 720-780 minutes)
            breakStart = 12 * 60;
            breakEnd = 13 * 60;
            shiftStart = 8 * 60;  // Default 8:00 AM
            gracePeriodMin = 5;   // Default grace period
        });

    timeIn.addEventListener('change', update);
    timeOut.addEventListener('change', update);
})();
</script>
@endpush
