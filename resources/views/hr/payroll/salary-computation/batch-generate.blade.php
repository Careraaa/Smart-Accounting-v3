@extends('layouts.layout')

@push('styles')
<style>
@keyframes countUp { 0%{opacity:0;transform:translateY(8px)} 100%{opacity:1;transform:translateY(0)} }
.bat-count { animation:countUp 0.6s cubic-bezier(0.16,1,0.3,1) both; }
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

.prl-page { font-family: 'Sora', sans-serif; }

/* ── Wrapper ──────────────────────────────────────────────────── */
.prl-wrap { max-width: 860px; margin: 0 auto; padding-bottom: 56px; }

/* ── Page header ──────────────────────────────────────────────── */
.prl-back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.84rem;
    font-weight: 700;
    color: #374151;
    text-decoration: none;
    margin-bottom: 16px;
    padding: 9px 16px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.13s;
}
.prl-back-link:hover { border-color: #c8292a; color: #c8292a; background: #fff5f5; }

.prl-page-title {
    font-size: 1.25rem;
    font-weight: 800;
    color: #111827;
    letter-spacing: -0.02em;
    margin: 0 0 4px;
}
.prl-page-sub {
    font-size: 0.78rem;
    color: #9ca3af;
    margin: 0 0 24px;
}

/* ── Card ──────────────────────────────────────────────────────── */
.prl-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 16px;
}
.prl-card-head {
    padding: 16px 22px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    gap: 10px;
}
.prl-card-head-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.prl-card-head-icon.red   { background: #fff0f0; color: #c8292a; }
.prl-card-head-icon.amber { background: #fffbeb; color: #d97706; }
.prl-card-head-icon.blue  { background: #eff6ff; color: #2563eb; }
.prl-card-head-icon.green { background: #f0fdf4; color: #16a34a; }

.prl-card-head-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: #111827;
    margin: 0;
}
.prl-card-head-sub {
    font-size: 0.72rem;
    color: #9ca3af;
    margin: 0;
}
.prl-card-body { padding: 20px 22px; }

/* ── Active batch banner ───────────────────────────────────────── */
.prl-batch-banner {
    display: flex;
    align-items: center;
    gap: 14px;
    background: linear-gradient(135deg, #fff9f9 0%, #fff5f0 100%);
    border: 1.5px solid #fecaca;
    border-radius: 12px;
    padding: 16px 20px;
}
.prl-batch-banner-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    background: #c8292a;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    color: #fff;
}
.prl-batch-banner-label {
    font-size: 0.67rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    color: #c8292a;
    margin-bottom: 3px;
}
.prl-batch-banner-name {
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
}
.prl-batch-banner-period {
    font-size: 0.78rem;
    color: #6b7280;
    font-family: 'DM Mono', monospace;
}

/* ── Employee list ─────────────────────────────────────────────── */
.prl-emp-list {
    display: flex;
    flex-direction: column;
    gap: 0;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
}

.prl-emp-row {
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: center;
    gap: 14px;
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
    background: #fff;
    transition: background 0.1s;
}
.prl-emp-row:last-child { border-bottom: none; }
.prl-emp-row:hover { background: #fafafa; }
.prl-emp-row.is-checked { background: #fff9f9; }

.prl-emp-check-wrap { display: flex; align-items: center; }
.prl-emp-check {
    width: 18px; height: 18px;
    border-radius: 5px;
    border: 1.5px solid #d1d5db;
    accent-color: #c8292a;
    cursor: pointer;
}

.prl-emp-info { display: flex; align-items: center; gap: 10px; min-width: 0; }
.prl-emp-avatar {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: #f3f4f6;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.72rem;
    font-weight: 700;
    color: #6b7280;
    flex-shrink: 0;
    border: 1.5px solid #e5e7eb;
    text-transform: uppercase;
}
.prl-emp-name { font-size: 0.845rem; font-weight: 600; color: #111827; }
.prl-emp-meta { font-size: 0.72rem; color: #9ca3af; margin-top: 1px; }

.prl-emp-override {
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Override link — opens the individual payroll create for this employee */
.prl-override-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 10px;
    border: 1px solid #e5e7eb;
    border-radius: 7px;
    background: #f9fafb;
    color: #6b7280;
    font-size: 0.72rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.13s;
    white-space: nowrap;
}
.prl-override-btn:hover {
    background: #fff0f0;
    border-color: #fecaca;
    color: #c8292a;
}

/* ── Search within employee list ───────────────────────────────── */
.prl-emp-search-wrap {
    position: relative;
    margin-bottom: 12px;
}
.prl-emp-search-wrap svg {
    position: absolute;
    left: 11px; top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    pointer-events: none;
}
.prl-emp-search {
    width: 100%;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 9px 12px 9px 34px;
    font-size: 0.82rem;
    font-family: 'Sora', sans-serif;
    color: #111827;
    background: #f9fafb;
    outline: none;
    transition: border-color 0.15s, background 0.15s;
}
.prl-emp-search:focus { border-color: #c8292a; background: #fff; box-shadow: 0 0 0 3px rgba(200,41,42,0.08); }

/* Select all bar */
.prl-select-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
    flex-wrap: wrap;
    gap: 8px;
}
.prl-select-all-btn {
    font-size: 0.78rem;
    font-weight: 600;
    color: #c8292a;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    text-decoration: underline;
    transition: color 0.13s;
}
.prl-select-all-btn:hover { color: #a81f20; }
.prl-selected-count {
    font-size: 0.78rem;
    color: #6b7280;
}
.prl-selected-count strong { color: #111827; }

/* ── Summary chips ─────────────────────────────────────────────── */
.prl-chips { display: flex; gap: 10px; margin-top: 16px; }
.prl-chip {
    flex: 1;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 12px 14px;
    text-align: center;
}
.prl-chip-lbl {
    display: block;
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #9ca3af;
    margin-bottom: 4px;
}
.prl-chip-val {
    font-size: 1.3rem;
    font-weight: 800;
    color: #111827;
    font-variant-numeric: tabular-nums;
    font-family: 'DM Mono', monospace;
}

/* ── Info note ──────────────────────────────────────────────────── */
.prl-note {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1d4ed8;
    font-size: 0.79rem;
    padding: 12px 14px;
    border-radius: 8px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    line-height: 1.5;
}
.prl-note svg { flex-shrink: 0; margin-top: 1px; }

/* ── Footer actions ─────────────────────────────────────────────── */
.prl-footer {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #f3f4f6;
    flex-wrap: wrap;
}

.prl-btn-generate {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 24px;
    background: #c8292a;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-family: 'Sora', sans-serif;
    font-size: 0.855rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s, box-shadow 0.15s;
    box-shadow: 0 4px 14px rgba(200,41,42,0.25);
    text-decoration: none;
}
.prl-btn-generate:hover { background: #a81f20; box-shadow: 0 6px 20px rgba(200,41,42,0.35); color: #fff; }

.prl-btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 10px 18px;
    background: #fff;
    color: #374151;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    font-family: 'Sora', sans-serif;
    font-size: 0.845rem;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.13s, border-color 0.13s;
}
.prl-btn-cancel:hover { background: #f9fafb; border-color: #d1d5db; color: #374151; }

/* ── Override modal overlay ─────────────────────────────────────── */
/* We redirect to create page with ?user_id= prefill, no modal needed */

@media (max-width: 600px) {
    .prl-chips { flex-wrap: wrap; }
    .prl-emp-row { grid-template-columns: auto 1fr; }
    .prl-emp-override { grid-column: 2; }
}

/* ── Dark mode overrides ─────────────────────────────────────────── */
html.dark .prl-card,
html.dark .prl-btn-cancel { background: #0a0a14; border-color: #18182a; }
html.dark .prl-card-head { border-bottom-color: #18182a; }
html.dark .prl-card-head-title,
html.dark .prl-page-title,
html.dark .prl-batch-banner-name,
html.dark .prl-chip-val,
html.dark .prl-emp-name,
html.dark .prl-selected-count strong { color: #e0e0f0; }
html.dark .prl-card-head-sub,
html.dark .prl-page-sub,
html.dark .prl-batch-banner-period,
html.dark .prl-emp-meta,
html.dark .prl-selected-count { color: #78789a; }
html.dark .prl-card-head-icon.red   { background: rgba(69,10,10,0.4); color: #fca5a5; }
html.dark .prl-card-head-icon.amber { background: rgba(69,26,3,0.4);  color: #fcd34d; }
html.dark .prl-card-head-icon.blue  { background: rgba(12,35,102,0.4); color: #93c5fd; }
html.dark .prl-card-head-icon.green { background: rgba(2,44,34,0.4);  color: #6ee7b7; }
html.dark .prl-batch-banner {
    background: linear-gradient(135deg, rgba(69,10,10,0.3) 0%, rgba(69,26,3,0.3) 100%);
    border-color: #450a0a;
}
html.dark .prl-batch-banner-label { color: #fca5a5; }
html.dark .prl-emp-list { border-color: #18182a; }
html.dark .prl-emp-row { border-bottom-color: #18182a; background: transparent; }
html.dark .prl-emp-row:hover { background: rgba(255,255,255,.015); }
html.dark .prl-emp-row.is-checked { background: rgba(255,255,255,.02); }
html.dark .prl-emp-check { border-color: #383850; }
html.dark .prl-emp-avatar { background: #14142a; border-color: #24243a; color: #78789a; }
html.dark .prl-override-btn { background: #0a0a14; border-color: #18182a; color: #78789a; }
html.dark .prl-override-btn:hover { background: rgba(69,10,10,0.4); border-color: #450a0a; color: #fca5a5; }
html.dark .prl-emp-search { border-color: #18182a; background: #0a0a14; color: #e0e0f0; }
html.dark .prl-emp-search::placeholder { color: #4e4e6a; }
html.dark .prl-emp-search:focus { border-color: #c8292a; background: #0a0a14; box-shadow: 0 0 0 3px rgba(200,41,42,0.15); }
html.dark .prl-emp-search-wrap svg { color: #4e4e6a; }
html.dark .prl-chip { background: #0a0a14; border-color: #18182a; }
html.dark .prl-chip-lbl { color: #4e4e6a; }
html.dark .prl-note { background: rgba(12,35,102,0.25); border-color: #1d4ed8; color: #93c5fd; }
html.dark .prl-footer { border-top-color: #18182a; }
html.dark .prl-btn-cancel { color: #b0b0cc; }
html.dark .prl-btn-cancel:hover { background: rgba(255,255,255,.04); border-color: #383850; color: #e0e0f0; }
html.dark .prl-back-link { background: #0a0a14; border-color: #18182a; color: #b0b0cc; }
html.dark .prl-back-link:hover { background: rgba(69,10,10,0.3); border-color: #c8292a; color: #fca5a5; }
html.dark .prl-select-all-btn { color: #f87171; }
html.dark .prl-select-all-btn:hover { color: #fca5a5; }
</style>
@endpush

@section('content')
<div class="prl-page">
<div class="prl-wrap">

    <button type="button" onclick="history.back()" class="prl-back-link">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Back
    </button>

    <h1 class="prl-page-title">Batch Payroll Generator</h1>
    <p class="prl-page-sub">Generate payroll for all or selected active employees in one run.</p>

    <form action="{{ route('payroll.generate-batch') }}" method="POST">
    @csrf

    @php
        $today = \Carbon\Carbon::now();
        $dayOfMonth = $today->day;
        $selectedSchedule = \App\Models\PayrollCutoffSchedule::where('is_active', true)
            ->where('cutoff_day', $dayOfMonth <= 27 ? 5 : 28)
            ->first();
        $monthName = $today->format('F');
        if ($selectedSchedule && $selectedSchedule->cutoff_day == 5) {
            $periodStart = $today->copy()->startOfMonth();
            $periodEnd   = $today->copy()->setDay(15);
        } else {
            $periodStart = $today->copy()->setDay(16);
            $periodEnd   = $today->copy()->endOfMonth();
        }

        $activeEmployees = \App\Models\User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();
    @endphp

    {{-- Hidden period inputs --}}
    <input type="hidden" name="cutoff_schedule_id" value="{{ $selectedSchedule->id ?? '' }}">
    <input type="hidden" name="period_start" value="{{ $periodStart->format('Y-m-d') }}">
    <input type="hidden" name="period_end"   value="{{ $periodEnd->format('Y-m-d') }}">

    {{-- Active batch --}}
    <div class="prl-card">
        <div class="prl-card-head">
            <div class="prl-card-head-icon red">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
            </div>
            <div>
                <p class="prl-card-head-title">Active Cutoff Batch</p>
                <p class="prl-card-head-sub">Automatically determined from today's date</p>
            </div>
        </div>
        <div class="prl-card-body">
            <div class="prl-batch-banner">
                <div class="prl-batch-banner-icon">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <div class="prl-batch-banner-label">{{ $monthName }}</div>
                    <div class="prl-batch-banner-name">{{ $selectedSchedule->label ?? 'N/A' }}</div>
                    <div class="prl-batch-banner-period">
                        {{ $periodStart->format('M d, Y') }} — {{ $periodEnd->format('M d, Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Employee selection --}}
    <div class="prl-card">
        <div class="prl-card-head">
            <div class="prl-card-head-icon blue">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="prl-card-head-title">Select Employees</p>
                <p class="prl-card-head-sub">Leave all unchecked to run for every active employee</p>
            </div>
        </div>
        <div class="prl-card-body">

            <div class="prl-emp-search-wrap">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                <input type="text" id="batchEmpSearch" class="prl-emp-search" placeholder="Filter employees…">
            </div>

            <div class="prl-select-bar">
                <div>
                    <button type="button" class="prl-select-all-btn" id="selectAllBtn">Select all</button>
                    &nbsp;·&nbsp;
                    <button type="button" class="prl-select-all-btn" id="clearAllBtn" style="color:#6b7280;">Clear</button>
                </div>
                <span class="prl-selected-count">
                    <strong id="selCount">0</strong> selected
                    <span id="modeText" style="color:#9ca3af; margin-left:4px;">(will run for all)</span>
                </span>
            </div>

            <div class="prl-emp-list" id="empList">
                @forelse($activeEmployees as $emp)
                @php
                    $initials = strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name, 0, 1));
                    $createUrl = route('payroll.salary-computation.create') . '?prefill_user=' . $emp->id
                        . '&period_start=' . $periodStart->format('Y-m-d')
                        . '&period_end=' . $periodEnd->format('Y-m-d');
                @endphp
                <div class="prl-emp-row" data-name="{{ strtolower($emp->first_name . ' ' . $emp->last_name) }}">
                    <div class="prl-emp-check-wrap">
                        <input type="checkbox" class="prl-emp-check emp-checkbox"
                               name="employees[]" value="{{ $emp->id }}"
                               id="emp_{{ $emp->id }}"
                               {{ in_array($emp->id, old('employees', [])) ? 'checked' : '' }}>
                    </div>
                    <label for="emp_{{ $emp->id }}" class="prl-emp-info" style="cursor:pointer; margin:0;">
                        <div class="prl-emp-avatar">{{ $initials }}</div>
                        <div>
                            <div class="prl-emp-name">{{ $emp->first_name }} {{ $emp->last_name }}</div>
                            <div class="prl-emp-meta">
                                {{ $emp->department ?? 'No dept.' }}
                                @if($emp->position) · {{ $emp->position }} @endif
                            </div>
                        </div>
                    </label>
                    <div class="prl-emp-override">
                        <a href="{{ $createUrl }}" class="prl-override-btn" title="Create individual payroll for this employee">
                            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Override
                        </a>
                    </div>
                </div>
                @empty
                <div style="padding:32px; text-align:center; color:#9ca3af; font-size:0.82rem;">
                    No active employees found.
                </div>
                @endforelse
            </div>

            <div class="prl-chips">
                <div class="prl-chip">
                    <span class="prl-chip-lbl">Total Employees</span>
                    <span class="prl-chip-val bat-count" style="animation-delay:0.05s">{{ $activeEmployees->count() }}</span>
                </div>
                <div class="prl-chip">
                    <span class="prl-chip-lbl">Will Process</span>
                    <span class="prl-chip-val bat-count" style="animation-delay:0.1s" id="willProcess">{{ $activeEmployees->count() }}</span>
                </div>
                <div class="prl-chip">
                    <span class="prl-chip-lbl">Mode</span>
                    <span class="prl-chip-val" id="modeChip" style="font-size:0.9rem; font-family:'Sora',sans-serif; font-weight:700;">All</span>
                </div>
            </div>

        </div>
    </div>

    {{-- Info note --}}
    <div class="prl-note">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
        <span>Salaries are auto-computed from attendance records. Existing payroll records for this period will be skipped. Use <strong>Override</strong> on any employee to create a fully customized individual payroll instead.</span>
    </div>

    {{-- Actions --}}
    <div class="prl-footer">
        <button type="submit" class="prl-btn-generate">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Generate Batch Payroll
        </button>
        <a href="{{ route('payroll.salary-computation.index') }}" class="prl-btn-cancel">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            Cancel
        </a>
    </div>

    </form>
</div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const checkboxes  = document.querySelectorAll('.emp-checkbox');
    const selCount    = document.getElementById('selCount');
    const modeText    = document.getElementById('modeText');
    const willProcess = document.getElementById('willProcess');
    const modeChip    = document.getElementById('modeChip');
    const selectAll   = document.getElementById('selectAllBtn');
    const clearAll    = document.getElementById('clearAllBtn');
    const searchInput = document.getElementById('batchEmpSearch');
    const rows        = document.querySelectorAll('.prl-emp-row');
    const total       = checkboxes.length;

    function updateCounts() {
        const selected = [...checkboxes].filter(c => c.checked).length;
        selCount.textContent = selected;
        const isAll = selected === 0;
        willProcess.textContent = isAll ? total : selected;
        modeChip.textContent    = isAll ? 'All' : 'Custom';
        modeText.textContent    = isAll ? '(will run for all)' : `(${selected} selected)`;
        // Highlight checked rows
        checkboxes.forEach(cb => {
            cb.closest('.prl-emp-row')?.classList.toggle('is-checked', cb.checked);
        });
    }

    checkboxes.forEach(cb => cb.addEventListener('change', updateCounts));

    selectAll.addEventListener('click', () => {
        checkboxes.forEach(cb => {
            if (cb.closest('.prl-emp-row')?.style.display !== 'none') cb.checked = true;
        });
        updateCounts();
    });

    clearAll.addEventListener('click', () => {
        checkboxes.forEach(cb => { cb.checked = false; });
        updateCounts();
    });

    searchInput.addEventListener('input', () => {
        const q = searchInput.value.toLowerCase().trim();
        rows.forEach(row => {
            const name = row.dataset.name || '';
            row.style.display = (!q || name.includes(q)) ? '' : 'none';
        });
    });

    updateCounts();
})();
</script>
@endpush