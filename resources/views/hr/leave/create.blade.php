@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.lv-page { font-family: 'Sora', sans-serif; }

.lv-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.lv-topbar-title { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.lv-topbar-sub   { font-size:0.78rem;color:#9ca3af;margin:0; }

.lv-btn-sec {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.lv-btn-sec:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

.lv-form-wrap { max-width:680px;margin:0 auto; }
.lv-card { background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden; }
.lv-card-header { padding:20px 24px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:12px; }
.lv-card-icon { width:38px;height:38px;border-radius:10px;background:#fff0f0;color:#c8292a;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.lv-card-title { font-size:0.95rem;font-weight:800;color:#111827;margin:0 0 2px; }
.lv-card-sub   { font-size:0.75rem;color:#9ca3af;margin:0; }
.lv-card-body  { padding:24px; }
.lv-card-footer { padding:16px 24px;border-top:1px solid #f3f4f6;background:#fafafa;display:flex;align-items:center;gap:10px; }

.lv-label { display:block;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280;margin-bottom:6px; }
.lv-label .req { color:#c8292a; }

.lv-input, .lv-select, .lv-textarea {
    width:100%;border:1px solid #e5e7eb;border-radius:10px;
    padding:10px 14px;font-size:0.845rem;font-family:'Sora',sans-serif;
    color:#111827;background:#fff;outline:none;
    transition:border-color 0.15s,box-shadow 0.15s;
    appearance:none;-webkit-appearance:none;
}
.lv-input:focus, .lv-select:focus, .lv-textarea:focus { border-color:#c8292a;box-shadow:0 0 0 3px rgba(200,41,42,0.08); }
.lv-input::placeholder, .lv-textarea::placeholder { color:#9ca3af; }
.lv-input.is-invalid, .lv-select.is-invalid, .lv-textarea.is-invalid { border-color:#ef4444 !important; }
.lv-invalid-feedback { display:block;font-size:0.75rem;color:#ef4444;margin-top:4px; }

.lv-select-wrap { position:relative; }
.lv-select-wrap svg { position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#9ca3af;pointer-events:none; }
.lv-select-wrap .lv-select { padding-right:36px; }

.lv-textarea { resize:vertical;min-height:110px; }

.lv-row { display:grid;grid-template-columns:1fr 1fr;gap:16px; }
@media(max-width:560px){ .lv-row{grid-template-columns:1fr;} }

.lv-field { margin-bottom:20px; }
.lv-field:last-of-type { margin-bottom:0; }

.lv-divider { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#9ca3af;border-bottom:1px solid #f3f4f6;padding-bottom:10px;margin-bottom:20px; }

.lv-duration-hint { font-size:0.75rem;color:#9ca3af;margin-top:5px;font-family:'DM Mono',monospace; }
.lv-duration-hint span { color:#c8292a;font-weight:700; }

.lv-btn-submit { display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:#111827;color:#fff;border:none;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:700;cursor:pointer;transition:background 0.15s; }
.lv-btn-submit:hover { background:#000; }
.lv-btn-cancel { display:inline-flex;align-items:center;gap:7px;padding:11px 18px;background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.845rem;font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s; }
.lv-btn-cancel:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }
</style>
@endpush

@section('content')
<div class="lv-page">

    <div class="lv-topbar">
        <div>
            <h1 class="lv-topbar-title">Create Leave Request</h1>
            <p class="lv-topbar-sub">Submit a new leave request for an employee</p>
        </div>
        <a href="{{ route('leave.pending') }}" class="lv-btn-sec">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Leaves
        </a>
    </div>

    <div class="lv-form-wrap">
        <div class="lv-card">

            <div class="lv-card-header">
                <div class="lv-card-icon">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="lv-card-title">Leave Details</p>
                    <p class="lv-card-sub">All fields marked <span style="color:#c8292a;">*</span> are required</p>
                </div>
            </div>

            <div class="lv-card-body">
                <form action="{{ route('leave.store') }}" method="POST" id="lvForm">
                    @csrf

                    {{-- Employee & Leave Type --}}
                    <div class="lv-row lv-field">
                        <div>
                            <label for="user_id" class="lv-label">Employee <span class="req">*</span></label>
                            <div class="lv-select-wrap">
                                <select name="user_id" id="user_id" class="lv-select @error('user_id') is-invalid @enderror" required>
                                    <option value="">— Select Employee —</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" @selected(old('user_id') == $employee->id)>
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            @error('user_id')<span class="lv-invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="leave_type" class="lv-label">Leave Type <span class="req">*</span></label>
                            <div class="lv-select-wrap">
                                <select name="leave_type" id="leave_type" class="lv-select @error('leave_type') is-invalid @enderror" required>
                                    <option value="">— Select Leave Type —</option>
                                    @foreach($leaveTypes as $type)
                                        <option value="{{ $type }}" @selected(old('leave_type') == $type)>{{ $type }}</option>
                                    @endforeach
                                </select>
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                            @error('leave_type')<span class="lv-invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Dates --}}
                    <div class="lv-divider">Duration</div>
                    <div class="lv-row lv-field">
                        <div>
                            <label for="start_date" class="lv-label">Start Date <span class="req">*</span></label>
                            <input type="date" name="start_date" id="start_date"
                                class="lv-input @error('start_date') is-invalid @enderror"
                                value="{{ old('start_date') }}" required>
                            @error('start_date')<span class="lv-invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div>
                            <label for="end_date" class="lv-label">End Date <span class="req">*</span></label>
                            <input type="date" name="end_date" id="end_date"
                                class="lv-input @error('end_date') is-invalid @enderror"
                                value="{{ old('end_date') }}" required>
                            <p class="lv-duration-hint">Duration: <span id="durationDays">0</span> day(s)</p>
                            @error('end_date')<span class="lv-invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Reason --}}
                    <div class="lv-divider">Reason</div>
                    <div class="lv-field">
                        <label for="reason" class="lv-label">Reason / Remarks <span class="req">*</span></label>
                        <textarea name="reason" id="reason"
                            class="lv-textarea @error('reason') is-invalid @enderror"
                            placeholder="Provide a reason for the leave request…" required>{{ old('reason') }}</textarea>
                        @error('reason')<span class="lv-invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                </form>
            </div>

            <div class="lv-card-footer">
                <button type="submit" form="lvForm" class="lv-btn-submit">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Create Leave Request
                </button>
                <a href="{{ route('leave.pending') }}" class="lv-btn-cancel">Cancel</a>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function(){
    const s = document.getElementById('start_date');
    const e = document.getElementById('end_date');
    const d = document.getElementById('durationDays');
    function calc(){
        if(s.value && e.value){
            const diff = Math.ceil(Math.abs(new Date(e.value) - new Date(s.value)) / 86400000) + 1;
            d.textContent = diff > 0 ? diff : 0;
        } else { d.textContent = 0; }
    }
    s.addEventListener('change', calc);
    e.addEventListener('change', calc);
    calc();
})();
</script>
@endpush