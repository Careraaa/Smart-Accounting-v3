@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

.prl-page { font-family: 'Sora', sans-serif; }

/* ── Wrapper ──────────────────────────────────────────────────── */
.prl-wrap { max-width: 760px; margin: 0 auto; padding-bottom: 56px; }

.prl-back-link {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: 0.84rem; font-weight: 700; color: #374151;
    text-decoration: none; margin-bottom: 16px; transition: all 0.13s;
    padding: 9px 16px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; cursor: pointer;
}
.prl-back-link:hover { border-color: #c8292a; color: #c8292a; background: #fff5f5; }

.prl-page-title {
    font-size: 1.25rem; font-weight: 800; color: #111827;
    letter-spacing: -0.02em; margin: 0 0 4px;
}
.prl-page-sub { font-size: 0.78rem; color: #9ca3af; margin: 0 0 24px; }

/* ── Prefill notice ───────────────────────────────────────────── */
.prl-prefill-notice {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fff9f9;
    border: 1.5px solid #fecaca;
    color: #c8292a;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 20px;
}
.prl-prefill-notice svg { flex-shrink: 0; }

/* ── Card ──────────────────────────────────────────────────────── */
.prl-card {
    background: #fff; border: 1px solid #e5e7eb;
    border-radius: 14px; overflow: hidden; margin-bottom: 16px;
}
.prl-card-head {
    padding: 16px 22px; border-bottom: 1px solid #f3f4f6;
    display: flex; align-items: center; gap: 10px;
}
.prl-card-head-icon {
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.prl-card-head-icon.red   { background: #fff0f0; color: #c8292a; }
.prl-card-head-icon.amber { background: #fffbeb; color: #d97706; }
.prl-card-head-icon.green { background: #f0fdf4; color: #16a34a; }
.prl-card-head-icon.blue  { background: #eff6ff; color: #2563eb; }
.prl-card-head-title { font-size: 0.88rem; font-weight: 700; color: #111827; margin: 0; }
.prl-card-head-sub   { font-size: 0.72rem; color: #9ca3af; margin: 0; }
.prl-card-body { padding: 20px 22px; }

/* ── Form fields ──────────────────────────────────────────────── */
.prl-grid   { display: grid; gap: 14px; }
.prl-col-1  { grid-template-columns: 1fr; }
.prl-col-2  { grid-template-columns: 1fr 1fr; }

.prl-lbl {
    display: block; font-size: 0.775rem; font-weight: 600;
    color: #374151; margin-bottom: 5px;
}
.prl-lbl .req { color: #ef4444; margin-left: 2px; }

.prl-ctrl {
    display: block; width: 100%;
    border: 1px solid #d1d5db; border-radius: 8px;
    font-size: 0.855rem; padding: 9px 12px;
    color: #111827; background: #fff;
    transition: border-color .15s, box-shadow .15s;
    appearance: auto; font-family: 'Sora', sans-serif;
}
.prl-ctrl:focus {
    border-color: #c8292a;
    box-shadow: 0 0 0 3px rgba(200,41,42,0.1);
    outline: none;
}
.prl-ctrl.is-invalid { border-color: #ef4444; }
.prl-err { font-size: 0.74rem; color: #ef4444; margin-top: 4px; }

/* ── Stat chips ───────────────────────────────────────────────── */
.prl-chips { display: flex; gap: 10px; }
.prl-chip {
    flex: 1; background: #f9fafb; border: 1px solid #e5e7eb;
    border-radius: 10px; padding: 13px 14px; text-align: center;
}
.prl-chip-lbl {
    display: block; font-size: 0.68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .07em; color: #9ca3af; margin-bottom: 5px;
}
.prl-chip-val {
    font-size: 1.3rem; font-weight: 700; color: #111827;
    font-variant-numeric: tabular-nums; font-family: 'DM Mono', monospace;
}

/* ── Computation breakdown ────────────────────────────────────── */
.prl-breakdown { display: flex; flex-direction: column; }
.prl-brow {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 0; border-bottom: 1px solid #f3f4f6; gap: 10px;
}
.prl-brow:last-child { border-bottom: none; padding-bottom: 0; }
.prl-brow-lbl {
    font-size: 0.845rem; color: #6b7280;
    display: flex; align-items: center; gap: 7px; flex: 1;
}
.prl-brow-lbl.c-green { color: #16a34a; }
.prl-brow-lbl.c-red   { color: #dc2626; }
.prl-brow-lbl.c-bold  { color: #111827; font-weight: 700; font-size: .9rem; }
.prl-badge {
    font-size: 0.67rem; font-weight: 600;
    background: #f3f4f6; color: #6b7280;
    border-radius: 4px; padding: 2px 6px; white-space: nowrap;
}
.prl-brow-val {
    font-size: .9rem; font-weight: 700;
    font-variant-numeric: tabular-nums; font-family: 'DM Mono', monospace;
    color: #111827; white-space: nowrap;
}
.prl-brow-val.c-green { color: #16a34a; }
.prl-brow-val.c-red   { color: #dc2626; }
.prl-loading-hint { font-size: .75rem; color: #9ca3af; font-style: italic; }

/* ── Inline add row ───────────────────────────────────────────── */
.prl-add-row {
    display: flex; gap: 8px; align-items: stretch; margin-bottom: 12px;
}
.prl-add-row .prl-fi {
    border: 1px solid #d1d5db; border-radius: 8px;
    padding: 9px 12px; font-size: .845rem;
    color: #111827; background: #fff;
    transition: border-color .15s, box-shadow .15s;
    font-family: 'Sora', sans-serif;
}
.prl-add-row .prl-fi:focus {
    border-color: #c8292a;
    box-shadow: 0 0 0 3px rgba(200,41,42,0.1);
    outline: none;
}
.prl-add-row .prl-fi-name   { flex: 1 1 0; min-width: 0; }
.prl-add-row .prl-fi-amount { width: 120px; flex-shrink: 0; }

.prl-add-btn-green, .prl-add-btn-red {
    flex-shrink: 0; padding: 0 16px; border: none; border-radius: 8px;
    font-size: .8rem; font-weight: 700; cursor: pointer;
    transition: background .15s; white-space: nowrap; color: #fff;
    font-family: 'Sora', sans-serif; display: inline-flex; align-items: center; gap: 5px;
}
.prl-add-btn-green       { background: #16a34a; }
.prl-add-btn-green:hover { background: #15803d; }
.prl-add-btn-red         { background: #dc2626; }
.prl-add-btn-red:hover   { background: #b91c1c; }

/* ── Tag pills ────────────────────────────────────────────────── */
.prl-pill-area {
    display: flex; flex-wrap: wrap; gap: 7px;
    min-height: 34px; padding: 2px 0 8px;
}
.prl-pill-empty { font-size: .78rem; color: #d1d5db; font-style: italic; align-self: center; }
.prl-pill {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 8px 5px 11px; border-radius: 999px;
    font-size: .775rem; font-weight: 600;
    animation: pillIn .15s ease;
}
@keyframes pillIn {
    from { transform: scale(.75); opacity: 0; }
    to   { transform: scale(1);   opacity: 1; }
}
.prl-pill.p-green { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.prl-pill.p-red   { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
.prl-pill-text { max-width: 110px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.prl-pill-amt  { font-variant-numeric: tabular-nums; font-family: 'DM Mono', monospace; opacity: .85; }
.prl-pill-rm {
    width: 15px; height: 15px; border-radius: 50%; border: none;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; line-height: 1; cursor: pointer; padding: 0;
    flex-shrink: 0; opacity: .55; transition: opacity .12s;
}
.prl-pill-rm:hover { opacity: 1; }
.prl-pill.p-green .prl-pill-rm { background: #bbf7d0; color: #15803d; }
.prl-pill.p-red   .prl-pill-rm { background: #fecaca; color: #b91c1c; }

.prl-sub {
    font-size: .795rem; font-weight: 600;
    display: flex; justify-content: flex-end; gap: 6px;
    padding-top: 2px; font-variant-numeric: tabular-nums;
    font-family: 'DM Mono', monospace;
}
.prl-sub.p-green { color: #16a34a; }
.prl-sub.p-red   { color: #dc2626; }

/* ── Net box ──────────────────────────────────────────────────── */
.prl-net {
    background: #111827; border-radius: 12px; padding: 18px 22px;
    display: flex; align-items: center; justify-content: space-between;
    margin-top: 16px;
}
.prl-net-lbl {
    font-size: .72rem; font-weight: 700; letter-spacing: .09em;
    text-transform: uppercase; color: #6b7280;
}
.prl-net-val {
    font-size: 1.55rem; font-weight: 800; color: #fff;
    font-variant-numeric: tabular-nums; letter-spacing: -.02em;
    font-family: 'DM Mono', monospace;
}

/* ── Footer ───────────────────────────────────────────────────── */
.prl-footer {
    display: flex; gap: 10px; margin-top: 20px;
    padding-top: 20px; border-top: 1px solid #f3f4f6; flex-wrap: wrap;
}
.prl-btn-submit {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 24px; background: #c8292a; color: #fff;
    border: none; border-radius: 10px; font-family: 'Sora', sans-serif;
    font-size: .855rem; font-weight: 700; cursor: pointer;
    transition: background .15s, box-shadow .15s;
    box-shadow: 0 4px 14px rgba(200,41,42,0.25);
}
.prl-btn-submit:hover { background: #a81f20; box-shadow: 0 6px 20px rgba(200,41,42,0.35); color:#fff; }
.prl-btn-cancel {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 18px; background: #fff; color: #374151;
    border: 1px solid #e5e7eb; border-radius: 10px;
    font-family: 'Sora', sans-serif; font-size: .845rem; font-weight: 600;
    text-decoration: none; transition: background .13s, border-color .13s;
}
.prl-btn-cancel:hover { background: #f9fafb; border-color: #d1d5db; color:#374151; }

@media (max-width: 600px) {
    .prl-col-2 { grid-template-columns: 1fr; }
    .prl-chips { flex-wrap: wrap; }
    .prl-add-row { flex-wrap: wrap; }
    .prl-add-row .prl-fi-amount { width: 100%; }
    .prl-add-btn-green, .prl-add-btn-red { width: 100%; padding: 10px; justify-content: center; }
}
</style>
@endpush

@section('content')
<div class="prl-page">
<div class="prl-wrap">

    <button type="button" onclick="history.back()" class="prl-back-link">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back
    </button>

    <h1 class="prl-page-title">Individual Payroll</h1>
    <p class="prl-page-sub">Create a fully customized payroll record for a single employee.</p>

    @if(request('prefill_user'))
    @php $prefillEmp = \App\Models\User::find(request('prefill_user')); @endphp
    @if($prefillEmp)
    <div class="prl-prefill-notice">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        Override mode — pre-filled for {{ $prefillEmp->first_name }} {{ $prefillEmp->last_name }}. Adjust any values below before saving.
    </div>
    @endif
    @endif

    <form action="{{ route('payroll.salary-computation.store') }}" method="POST">
    @csrf

    {{-- ① Employee & Period ──────────────────────────────────── --}}
    <div class="prl-card">
        <div class="prl-card-head">
            <div class="prl-card-head-icon red">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <p class="prl-card-head-title">Employee & Period</p>
                <p class="prl-card-head-sub">Who is this payroll for and what period does it cover?</p>
            </div>
        </div>
        <div class="prl-card-body">
            <div class="prl-grid prl-col-1" style="margin-bottom:14px;">
                <div>
                    <label class="prl-lbl" for="prl_user_id">Employee <span class="req">*</span></label>
                    <select name="user_id" id="prl_user_id"
                        class="prl-ctrl @error('user_id') is-invalid @enderror" required>
                        <option value="">— Select an employee —</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}"
                                data-salary-rate="{{ $emp->salary_rate }}"
                                data-has-sss="{{ $emp->has_sss ? 1 : 0 }}"
                                data-has-pagibig="{{ $emp->has_pagibig ? 1 : 0 }}"
                                {{ (old('user_id', request('prefill_user')) == $emp->id) ? 'selected' : '' }}>
                                {{ $emp->last_name }}, {{ $emp->first_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<p class="prl-err">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="prl-grid prl-col-2">
                <div>
                    <label class="prl-lbl" for="prl_period_start">Period Start <span class="req">*</span></label>
                    <input type="date" name="payroll_period_start" id="prl_period_start"
                        class="prl-ctrl @error('payroll_period_start') is-invalid @enderror"
                        value="{{ old('payroll_period_start', request('period_start')) }}" required>
                    @error('payroll_period_start')<p class="prl-err">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="prl-lbl" for="prl_period_end">Period End <span class="req">*</span></label>
                    <input type="date" name="payroll_period_end" id="prl_period_end"
                        class="prl-ctrl @error('payroll_period_end') is-invalid @enderror"
                        value="{{ old('payroll_period_end', request('period_end')) }}" required>
                    @error('payroll_period_end')<p class="prl-err">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    {{-- ② Attendance Summary ───────────────────────────────────── --}}
    <div class="prl-card">
        <div class="prl-card-head">
            <div class="prl-card-head-icon amber">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
            </div>
            <div>
                <p class="prl-card-head-title">Attendance Summary</p>
                <p class="prl-card-head-sub">Auto-fetched from attendance records for the selected period</p>
            </div>
        </div>
        <div class="prl-card-body">
            <div class="prl-chips">
                <div class="prl-chip">
                    <span class="prl-chip-lbl">Days Worked</span>
                    <span class="prl-chip-val" id="prl_days_worked">—</span>
                </div>
                <div class="prl-chip">
                    <span class="prl-chip-lbl">Hours Worked</span>
                    <span class="prl-chip-val" id="prl_hours_worked">—</span>
                </div>
                <div class="prl-chip">
                    <span class="prl-chip-lbl">Days Absent</span>
                    <span class="prl-chip-val" id="prl_days_absent" style="color:#dc2626;">—</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ③ Salary Computation ─────────────────────────────────── --}}
    <div class="prl-card">
        <div class="prl-card-head">
            <div class="prl-card-head-icon green">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="prl-card-head-title">Salary Computation</p>
                <p class="prl-card-head-sub">All values computed server-side from attendance data</p>
            </div>
        </div>
        <div class="prl-card-body">
            <div class="prl-breakdown">
                <div class="prl-brow">
                    <span class="prl-brow-lbl">Basic Salary <span class="prl-badge" id="prl_basic_badge">rate × days</span></span>
                    <span class="prl-brow-val" id="prl_basic_display">₱0.00</span>
                </div>
                <input type="hidden" name="basic_salary" id="prl_basic_input" value="0">

                {{-- Holiday pay breakdown rows (injected by JS from preview response) --}}
                <div id="prl_holiday_rows"></div>

                <div id="prl_leave_pay_row" style="display:none;"></div>

                <div class="prl-brow" id="prl_ot_row" style="display:none;">
                    <span class="prl-brow-lbl c-green">
                        + Overtime Pay <span class="prl-badge" id="prl_ot_hrs"></span>
                    </span>
                    <span class="prl-brow-val c-green" id="prl_ot_pay">₱0.00</span>
                </div>

                <div class="prl-brow" id="prl_ut_row" style="display:none;">
                    <span class="prl-brow-lbl c-red">
                        − Undertime Deduction <span class="prl-badge" id="prl_ut_hrs"></span>
                    </span>
                    <span class="prl-brow-val c-red" id="prl_ut_deduct">₱0.00</span>
                </div>

                <div class="prl-brow" id="prl_sss_row" style="display:none;">
                    <span class="prl-brow-lbl c-red">
                        − SSS Contribution <span class="prl-badge">statutory · ½ of monthly</span>
                    </span>
                    <span class="prl-brow-val c-red" id="prl_sss_val">₱0.00</span>
                </div>

                <div class="prl-brow" id="prl_pagibig_row" style="display:none;">
                    <span class="prl-brow-lbl c-red">
                        − Pag-IBIG Contribution <span class="prl-badge">statutory · ½ of monthly</span>
                    </span>
                    <span class="prl-brow-val c-red" id="prl_pagibig_val">₱0.00</span>
                </div>

                <div class="prl-brow" id="prl_ca_deduct_row" style="display:none;">
                    <span class="prl-brow-lbl c-red">
                        − Cash Advance <span class="prl-badge">loan deduction</span>
                    </span>
                    <span class="prl-brow-val c-red" id="prl_ca_deduct_val">₱0.00</span>
                </div>

                <div class="prl-brow" id="prl_loading_row" style="display:none;">
                    <span class="prl-loading-hint">Computing from attendance records…</span>
                    <span></span>
                </div>

                <div class="prl-brow">
                    <span class="prl-brow-lbl c-bold">Adjusted Gross</span>
                    <span class="prl-brow-val" id="prl_adjusted" style="font-size:1rem;">₱0.00</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ④ Allowances ─────────────────────────────────────────── --}}
    <div class="prl-card">
        <div class="prl-card-head">
            <div class="prl-card-head-icon green">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <p class="prl-card-head-title">Allowances <span style="font-weight:400; color:#9ca3af; font-size:0.78rem; margin-left:6px;">optional</span></p>
                <p class="prl-card-head-sub">Transportation, meal, housing, and other extras on top of salary</p>
            </div>
        </div>
        <div class="prl-card-body">
            <div class="prl-add-row">
                <input type="text"   id="prl_allow_name"   class="prl-fi prl-fi-name"   placeholder="Label — e.g. Transportation">
                <input type="number" id="prl_allow_amount" class="prl-fi prl-fi-amount" placeholder="0.00" min="0.01" step="0.01">
                <button type="button" class="prl-add-btn-green" id="prl_allow_btn">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add
                </button>
            </div>
            <div class="prl-pill-area" id="prl_allow_pills">
                <span class="prl-pill-empty" id="prl_allow_empty">No allowances added yet.</span>
            </div>
            <div class="prl-sub p-green" id="prl_allow_subtotal" style="display:none;">
                Total allowances: ₱<span id="prl_allow_total">0.00</span>
            </div>
            <div id="prl_allow_hidden"></div>
        </div>
    </div>

    {{-- ⑤ Deductions ──────────────────────────────────────────── --}}
    <div class="prl-card">
        <div class="prl-card-head">
            <div class="prl-card-head-icon red">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
            </div>
            <div>
                <p class="prl-card-head-title">Additional Deductions <span style="font-weight:400; color:#9ca3af; font-size:0.78rem; margin-left:6px;">optional</span></p>
                <p class="prl-card-head-sub">Cash advances, loans, and other custom deductions</p>
            </div>
        </div>
        <div class="prl-card-body">
            <div class="prl-add-row">
                <input type="text"   id="prl_deduct_name"   class="prl-fi prl-fi-name"   placeholder="Label — e.g. Cash Advance">
                <input type="number" id="prl_deduct_amount" class="prl-fi prl-fi-amount" placeholder="0.00" min="0.01" step="0.01">
                <button type="button" class="prl-add-btn-red" id="prl_deduct_btn">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add
                </button>
            </div>
            <div class="prl-pill-area" id="prl_deduct_pills">
                <span class="prl-pill-empty" id="prl_deduct_empty">No deductions added yet.</span>
            </div>
            <div class="prl-sub p-red" id="prl_deduct_subtotal" style="display:none;">
                Total deductions: ₱<span id="prl_deduct_total">0.00</span>
            </div>
            <div id="prl_deduct_hidden"></div>
        </div>
    </div>

    {{-- ⑥ Net Salary ───────────────────────────────────────────── --}}
    <div class="prl-net">
        <span class="prl-net-lbl">Net Salary</span>
        <span class="prl-net-val" id="prl_net_salary">₱0.00</span>
    </div>

    {{-- Actions ──────────────────────────────────────────────── --}}
    <div class="prl-footer">
        <button type="submit" class="prl-btn-submit">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Create Payroll
        </button>
        <a href="{{ route('payroll.salary-computation.batch-generate') }}" class="prl-btn-cancel">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            Cancel
        </a>
    </div>

    </form>
</div>
</div>
@endsection

@push('head_scripts')
    <meta name="page-id" content="payroll-create">
    <script>
        window._prl = {
            previewUrl:     "{{ route('payroll.preview') }}",
            initAllowances: @json(old('allowances', [])),
            initDeductions: @json(old('deductions', [])),
        };
    </script>
@endpush