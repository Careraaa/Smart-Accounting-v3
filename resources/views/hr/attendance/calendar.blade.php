@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
*, *::before, *::after { box-sizing: border-box; }
.cal-page { font-family: 'Sora', sans-serif; }

/* ── Topbar ─────────────────────────────────────────────────── */
.cal-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:28px;flex-wrap:wrap; }
.cal-topbar-left { display:flex;align-items:center;gap:14px; }
.cal-avatar { width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#c8292a,#e85d5e);display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:800;color:#fff;flex-shrink:0;letter-spacing:-0.02em; }
.cal-emp-name { font-size:1.25rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.cal-emp-meta { font-size:0.78rem;color:#9ca3af;margin:0;display:flex;align-items:center;gap:8px; }
.cal-emp-dept { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;background:#f3f4f6;border-radius:20px;font-size:0.68rem;font-weight:700;color:#374151; }

.cal-btn-back {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.cal-btn-back:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

/* ── Month nav ──────────────────────────────────────────────── */
.cal-month-nav {
    display:flex;align-items:center;justify-content:space-between;
    background:#fff;border:1px solid #e5e7eb;border-radius:14px;
    padding:14px 20px;margin-bottom:20px;
}
.cal-month-label { font-size:1.05rem;font-weight:800;color:#111827;letter-spacing:-0.02em; }
.cal-month-sub   { font-size:0.72rem;color:#9ca3af;margin-top:2px; }
.cal-nav-btn {
    display:inline-flex;align-items:center;gap:6px;padding:8px 14px;
    background:#f9fafb;border:1px solid #e5e7eb;border-radius:9px;
    font-family:'Sora',sans-serif;font-size:0.78rem;font-weight:600;color:#374151;
    text-decoration:none;transition:all 0.12s;
}
.cal-nav-btn:hover { background:#fff0f0;border-color:#fecaca;color:#c8292a; }

/* ── Legend ─────────────────────────────────────────────────── */
.cal-legend { display:flex;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:20px; }
.cal-legend-item { display:flex;align-items:center;gap:6px;font-size:0.72rem;font-weight:600;color:#6b7280; }
.cal-legend-dot { width:12px;height:12px;border-radius:4px; }
.cal-legend-dot.present  { background:#dcfce7;border:1.5px solid #86efac; }
.cal-legend-dot.late     { background:#fef9c3;border:1.5px solid #fde047; }
.cal-legend-dot.absent   { background:#fee2e2;border:1.5px solid #fca5a5; }
.cal-legend-dot.early    { background:#ede9fe;border:1.5px solid #c4b5fd; }
.cal-legend-dot.weekend  { background:#f9fafb;border:1.5px solid #e5e7eb; }
.cal-legend-dot.nodata   { background:#fff;border:1.5px solid #e5e7eb; }

/* ── Calendar grid ──────────────────────────────────────────── */
.cal-grid-wrap { background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden; }

/* Two half-month sections */
.cal-half { padding:20px 20px 16px; }
.cal-half:first-child { border-bottom:2px dashed #f3f4f6; }

.cal-half-label {
    font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;
    color:#9ca3af;margin-bottom:14px;display:flex;align-items:center;gap:8px;
}
.cal-half-label::after { content:'';flex:1;height:1px;background:#f3f4f6; }
.cal-half-label span { background:#fff;padding-right:8px; }

/* Day-of-week header */
.cal-dow-row { display:grid;grid-template-columns:repeat(5,1fr);gap:6px;margin-bottom:8px; }
.cal-dow { text-align:center;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;padding:4px 0; }

/* Day cells row */
.cal-days-row { display:grid;grid-template-columns:repeat(5,1fr);gap:6px; }

.cal-day {
    border-radius:10px;padding:10px 8px 8px;min-height:72px;
    border:1.5px solid #e5e7eb;background:#fff;
    display:flex;flex-direction:column;gap:4px;
    transition:transform 0.1s,box-shadow 0.1s;
    position:relative;
}
.cal-day:hover { transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,0.07); }

.cal-day.is-today { border-color:#c8292a !important;box-shadow:0 0 0 2px rgba(200,41,42,0.12); }
.cal-day.is-today .cal-day-num { color:#c8292a; }

.cal-day.is-weekend { background:#f9fafb;border-color:#f3f4f6; }
.cal-day.is-weekend .cal-day-num { color:#d1d5db; }

.cal-day.is-future { background:#fafafa;border-color:#f3f4f6; }
.cal-day.is-future .cal-day-num { color:#d1d5db; }

/* Status color fills */
.cal-day.s-present  { background:#f0fdf4;border-color:#86efac; }
.cal-day.s-late     { background:#fefce8;border-color:#fde047; }
.cal-day.s-absent   { background:#fef2f2;border-color:#fca5a5; }
.cal-day.s-early    { background:#f5f3ff;border-color:#c4b5fd; }

.cal-day-num { font-size:0.78rem;font-weight:800;color:#374151;line-height:1; }

.cal-day-status {
    font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;
    padding:2px 5px;border-radius:4px;display:inline-block;width:fit-content;
}
.cal-day-status.s-present { background:#dcfce7;color:#16a34a; }
.cal-day-status.s-late    { background:#fef9c3;color:#ca8a04; }
.cal-day-status.s-absent  { background:#fee2e2;color:#dc2626; }
.cal-day-status.s-early   { background:#ede9fe;color:#7c3aed; }

.cal-day-times { font-family:'DM Mono',monospace;font-size:0.6rem;color:#6b7280;line-height:1.5; }
.cal-day-times span { display:block; }

/* Empty placeholder (days before month start or after end) */
.cal-day-empty { border-radius:10px;min-height:72px;background:transparent;border:1.5px dashed #f3f4f6; }

/* ── Summary strip ──────────────────────────────────────────── */
.cal-summary {
    display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:20px;
}
@media (max-width:640px) { .cal-summary { grid-template-columns:repeat(2,1fr); } }

.cal-stat {
    background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;
    display:flex;align-items:center;gap:12px;
}
.cal-stat-icon { width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.cal-stat-icon.green  { background:#f0fdf4;color:#16a34a; }
.cal-stat-icon.yellow { background:#fefce8;color:#ca8a04; }
.cal-stat-icon.red    { background:#fef2f2;color:#dc2626; }
.cal-stat-icon.purple { background:#f5f3ff;color:#7c3aed; }
.cal-stat-val  { font-size:1.35rem;font-weight:800;color:#111827;line-height:1; }
.cal-stat-lbl  { font-size:0.68rem;color:#9ca3af;margin-top:2px; }
</style>
@endpush

@section('content')
@php
    use Carbon\Carbon;

    $today      = Carbon::today();
    $monthStart = $month->copy()->startOfMonth();
    $monthEnd   = $month->copy()->endOfMonth();

    // Build the two halves: 1–15 and 16–end
    $firstHalfDays  = collect();
    $secondHalfDays = collect();

    for ($d = 1; $d <= 15 && $d <= $monthEnd->day; $d++) {
        $date = $monthStart->copy()->setDay($d);
        if ($date->isWeekday()) $firstHalfDays->push($date);
    }
    for ($d = 16; $d <= $monthEnd->day; $d++) {
        $date = $monthStart->copy()->setDay($d);
        if ($date->isWeekday()) $secondHalfDays->push($date);
    }

    // Summary counts
    $presentCount = $attendances->where('status', 'present')->count();
    $lateCount    = $attendances->where('status', 'late')->count();
    $absentCount  = $attendances->where('status', 'absent')->count();
    $earlyCount   = $attendances->where('status', 'early_leave')->count();

    $initials = strtoupper(substr($employee->first_name ?? 'U', 0, 1) . substr($employee->last_name ?? '', 0, 1));
@endphp

<div class="cal-page">

    {{-- Topbar --}}
    <div class="cal-topbar">
        <div class="cal-topbar-left">
            <div class="cal-avatar">{{ $initials }}</div>
            <div>
                <h1 class="cal-emp-name">{{ $employee->first_name }} {{ $employee->last_name }}</h1>
                <p class="cal-emp-meta">
                    <span class="cal-emp-dept">
                        <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        {{ $employee->department ?? 'No Department' }}
                    </span>
                    <span>{{ $employee->position ?? 'No Position' }}</span>
                </p>
            </div>
        </div>
        <a href="{{ route('attendance.index') }}" class="cal-btn-back">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Employees
        </a>
    </div>

    {{-- Month navigation --}}
    <div class="cal-month-nav">
        <a href="{{ route('attendance.employee.calendar', [$employee->id, 'month' => $prevMonth]) }}" class="cal-nav-btn">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Prev
        </a>
        <div class="text-center">
            <div class="cal-month-label">{{ $month->format('F Y') }}</div>
            <div class="cal-month-sub">Cutoff: 1–15 &amp; 16–{{ $monthEnd->day }}</div>
        </div>
        <a href="{{ route('attendance.employee.calendar', [$employee->id, 'month' => $nextMonth]) }}" class="cal-nav-btn">
            Next
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    {{-- Legend --}}
    <div class="cal-legend">
        <div class="cal-legend-item"><div class="cal-legend-dot present"></div> Present</div>
        <div class="cal-legend-item"><div class="cal-legend-dot late"></div> Late</div>
        <div class="cal-legend-item"><div class="cal-legend-dot absent"></div> Absent</div>
        <div class="cal-legend-item"><div class="cal-legend-dot early"></div> Early Leave</div>
        <div class="cal-legend-item"><div class="cal-legend-dot weekend"></div> Weekend / Holiday</div>
        <div class="cal-legend-item"><div class="cal-legend-dot nodata"></div> No Record</div>
    </div>

    {{-- Calendar --}}
    <div class="cal-grid-wrap">

        {{-- First half: 1–15 --}}
        <div class="cal-half">
            <div class="cal-half-label"><span>1st Cutoff &nbsp;·&nbsp; {{ $monthStart->format('M 1') }} – {{ $monthStart->copy()->setDay(15)->format('M 15') }}</span></div>

            <div class="cal-dow-row">
                @foreach(['Mon','Tue','Wed','Thu','Fri'] as $dow)
                    <div class="cal-dow">{{ $dow }}</div>
                @endforeach
            </div>

            @php
                // Pad first half to start on correct weekday (Mon=0)
                $firstDay = $firstHalfDays->first();
                $padStart = $firstDay ? ($firstDay->dayOfWeekIso - 1) : 0; // Mon=1 → 0 pads
                // But we only show Mon–Fri, so we need to fill the grid row by row
                // Build a flat array of cells: null = empty, Carbon = day
                $firstCells = [];
                if ($firstDay) {
                    for ($p = 0; $p < $padStart; $p++) $firstCells[] = null;
                }
                foreach ($firstHalfDays as $d) $firstCells[] = $d;
                // Pad end to complete last row
                $rem = count($firstCells) % 5;
                if ($rem > 0) for ($p = 0; $p < (5 - $rem); $p++) $firstCells[] = null;
            @endphp

            @foreach(array_chunk($firstCells, 5) as $week)
            <div class="cal-days-row" style="margin-bottom:6px;">
                @foreach($week as $day)
                    @if($day === null)
                        <div class="cal-day-empty"></div>
                    @else
                        @php
                            $key = $day->format('Y-m-d');
                            $att = $attendances[$key] ?? null;
                            $isToday = $day->isSameDay($today);
                            $isFuture = $day->isAfter($today);

                            $dayClass = '';
                            $statusLabel = '';
                            $statusBadge = '';
                            if ($att) {
                                $dayClass = match($att->status) {
                                    'present'     => 's-present',
                                    'late'        => 's-late',
                                    'absent'      => 's-absent',
                                    'early_leave' => 's-early',
                                    default       => '',
                                };
                                $statusLabel = match($att->status) {
                                    'present'     => 'Present',
                                    'late'        => 'Late',
                                    'absent'      => 'Absent',
                                    'early_leave' => 'Early',
                                    default       => ucfirst($att->status),
                                };
                                $statusBadge = match($att->status) {
                                    'present'     => 's-present',
                                    'late'        => 's-late',
                                    'absent'      => 's-absent',
                                    'early_leave' => 's-early',
                                    default       => '',
                                };
                            }
                        @endphp
                        <div class="cal-day {{ $dayClass }} {{ $isToday ? 'is-today' : '' }} {{ $isFuture ? 'is-future' : '' }}">
                            <div class="cal-day-num">{{ $day->day }}</div>
                            @if($att && !$isFuture)
                                <div class="cal-day-status {{ $statusBadge }}">{{ $statusLabel }}</div>
                                @if($att->time_in || $att->time_out)
                                <div class="cal-day-times">
                                    @if($att->time_in)
                                        <span>▶ {{ \Carbon\Carbon::createFromFormat('H:i:s', $att->time_in)->format('g:i A') }}</span>
                                    @endif
                                    @if($att->time_out)
                                        <span>◀ {{ \Carbon\Carbon::createFromFormat('H:i:s', $att->time_out)->format('g:i A') }}</span>
                                    @endif
                                </div>
                                @endif
                            @elseif(!$isFuture)
                                <div style="font-size:0.6rem;color:#d1d5db;margin-top:2px;">No record</div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
            @endforeach
        </div>

        {{-- Second half: 16–end --}}
        <div class="cal-half">
            <div class="cal-half-label"><span>2nd Cutoff &nbsp;·&nbsp; {{ $monthStart->copy()->setDay(16)->format('M 16') }} – {{ $monthEnd->format('M j') }}</span></div>

            <div class="cal-dow-row">
                @foreach(['Mon','Tue','Wed','Thu','Fri'] as $dow)
                    <div class="cal-dow">{{ $dow }}</div>
                @endforeach
            </div>

            @php
                $secondFirst = $secondHalfDays->first();
                $padStart2 = $secondFirst ? ($secondFirst->dayOfWeekIso - 1) : 0;
                $secondCells = [];
                if ($secondFirst) {
                    for ($p = 0; $p < $padStart2; $p++) $secondCells[] = null;
                }
                foreach ($secondHalfDays as $d) $secondCells[] = $d;
                $rem2 = count($secondCells) % 5;
                if ($rem2 > 0) for ($p = 0; $p < (5 - $rem2); $p++) $secondCells[] = null;
            @endphp

            @foreach(array_chunk($secondCells, 5) as $week)
            <div class="cal-days-row" style="margin-bottom:6px;">
                @foreach($week as $day)
                    @if($day === null)
                        <div class="cal-day-empty"></div>
                    @else
                        @php
                            $key = $day->format('Y-m-d');
                            $att = $attendances[$key] ?? null;
                            $isToday = $day->isSameDay($today);
                            $isFuture = $day->isAfter($today);

                            $dayClass = '';
                            $statusLabel = '';
                            $statusBadge = '';
                            if ($att) {
                                $dayClass = match($att->status) {
                                    'present'     => 's-present',
                                    'late'        => 's-late',
                                    'absent'      => 's-absent',
                                    'early_leave' => 's-early',
                                    default       => '',
                                };
                                $statusLabel = match($att->status) {
                                    'present'     => 'Present',
                                    'late'        => 'Late',
                                    'absent'      => 'Absent',
                                    'early_leave' => 'Early',
                                    default       => ucfirst($att->status),
                                };
                                $statusBadge = match($att->status) {
                                    'present'     => 's-present',
                                    'late'        => 's-late',
                                    'absent'      => 's-absent',
                                    'early_leave' => 's-early',
                                    default       => '',
                                };
                            }
                        @endphp
                        <div class="cal-day {{ $dayClass }} {{ $isToday ? 'is-today' : '' }} {{ $isFuture ? 'is-future' : '' }}">
                            <div class="cal-day-num">{{ $day->day }}</div>
                            @if($att && !$isFuture)
                                <div class="cal-day-status {{ $statusBadge }}">{{ $statusLabel }}</div>
                                @if($att->time_in || $att->time_out)
                                <div class="cal-day-times">
                                    @if($att->time_in)
                                        <span>▶ {{ \Carbon\Carbon::createFromFormat('H:i:s', $att->time_in)->format('g:i A') }}</span>
                                    @endif
                                    @if($att->time_out)
                                        <span>◀ {{ \Carbon\Carbon::createFromFormat('H:i:s', $att->time_out)->format('g:i A') }}</span>
                                    @endif
                                </div>
                                @endif
                            @elseif(!$isFuture)
                                <div style="font-size:0.6rem;color:#d1d5db;margin-top:2px;">No record</div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
            @endforeach
        </div>

    </div>{{-- /.cal-grid-wrap --}}

    {{-- Summary strip --}}
    <div class="cal-summary">
        <div class="cal-stat">
            <div class="cal-stat-icon green">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <div class="cal-stat-val">{{ $presentCount }}</div>
                <div class="cal-stat-lbl">Present days</div>
            </div>
        </div>
        <div class="cal-stat">
            <div class="cal-stat-icon yellow">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 6v6l3 3"/></svg>
            </div>
            <div>
                <div class="cal-stat-val">{{ $lateCount }}</div>
                <div class="cal-stat-lbl">Late arrivals</div>
            </div>
        </div>
        <div class="cal-stat">
            <div class="cal-stat-icon red">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div>
                <div class="cal-stat-val">{{ $absentCount }}</div>
                <div class="cal-stat-lbl">Absent days</div>
            </div>
        </div>
        <div class="cal-stat">
            <div class="cal-stat-icon purple">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </div>
            <div>
                <div class="cal-stat-val">{{ $earlyCount }}</div>
                <div class="cal-stat-lbl">Early leaves</div>
            </div>
        </div>
    </div>

</div>
@endsection
