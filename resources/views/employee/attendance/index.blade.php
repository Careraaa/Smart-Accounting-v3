@extends('layouts.layout')

@push('styles')
    @include('employee._ui-styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&display=swap');
*, *::before, *::after { box-sizing: border-box; }

/* ── Month nav ──────────────────────────────────────────────── */
.empcal-month-nav {
    display:flex;align-items:center;justify-content:space-between;
    background:#fff;border:1px solid #e5e7eb;border-radius:14px;
    padding:14px 20px;margin-bottom:20px;
}
.empcal-month-label { font-size:1.05rem;font-weight:800;color:#111827;letter-spacing:-0.02em; }
.empcal-month-sub   { font-size:0.72rem;color:#9ca3af;margin-top:2px; }
.empcal-nav-btn {
    display:inline-flex;align-items:center;gap:6px;padding:8px 14px;
    background:#f9fafb;border:1px solid #e5e7eb;border-radius:9px;
    font-family:'Sora',sans-serif;font-size:0.78rem;font-weight:600;color:#374151;
    text-decoration:none;transition:all 0.12s;
}
.empcal-nav-btn:hover { background:#fff0f0;border-color:#fecaca;color:#c8292a; }

/* ── Legend ─────────────────────────────────────────────────── */
.empcal-legend { display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-bottom:20px; }
.empcal-legend-item { display:flex;align-items:center;gap:6px;font-size:0.72rem;font-weight:600;color:#6b7280; }
.empcal-legend-dot { width:12px;height:12px;border-radius:4px; }
.empcal-legend-dot.present { background:#dcfce7;border:1.5px solid #86efac; }
.empcal-legend-dot.late    { background:#fef9c3;border:1.5px solid #fde047; }
.empcal-legend-dot.absent  { background:#fee2e2;border:1.5px solid #fca5a5; }
.empcal-legend-dot.early   { background:#ede9fe;border:1.5px solid #c4b5fd; }
.empcal-legend-dot.ot      { background:#dbeafe;border:1.5px solid #93c5fd; }
.empcal-legend-dot.ut      { background:#fef9c3;border:1.5px solid #fde047; }
.empcal-legend-dot.nodata  { background:#fff;border:1.5px solid #e5e7eb; }

/* ── Calendar grid ──────────────────────────────────────────── */
.empcal-grid-wrap { background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;margin-bottom:20px; }

.empcal-half { padding:20px 20px 16px; }
.empcal-half:first-child { border-bottom:2px dashed #f3f4f6; }

.empcal-half-label {
    font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;
    color:#9ca3af;margin-bottom:14px;display:flex;align-items:center;gap:8px;
}
.empcal-half-label::after { content:'';flex:1;height:1px;background:#f3f4f6; }
.empcal-half-label span { background:#fff;padding-right:8px; }

.empcal-dow-row { display:grid;grid-template-columns:repeat(5,1fr);gap:6px;margin-bottom:8px; }
.empcal-dow { text-align:center;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;padding:4px 0; }

.empcal-days-row { display:grid;grid-template-columns:repeat(5,1fr);gap:6px; }

.empcal-day {
    border-radius:10px;padding:9px 8px 7px;min-height:82px;
    border:1.5px solid #e5e7eb;background:#fff;
    display:flex;flex-direction:column;gap:3px;
    transition:transform 0.1s,box-shadow 0.1s;
    position:relative;
}
.empcal-day:hover { transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,0.07); }

.empcal-day.is-today { border-color:#c8292a !important;box-shadow:0 0 0 2px rgba(200,41,42,0.12); }
.empcal-day.is-today .empcal-day-num { color:#c8292a; }
.empcal-day.is-future { background:#fafafa;border-color:#f3f4f6; }
.empcal-day.is-future .empcal-day-num { color:#d1d5db; }

/* Attendance status fills */
.empcal-day.s-present { background:#f0fdf4;border-color:#86efac; }
.empcal-day.s-late    { background:#fefce8;border-color:#fde047; }
.empcal-day.s-absent  { background:#fef2f2;border-color:#fca5a5; }
.empcal-day.s-early   { background:#f5f3ff;border-color:#c4b5fd; }

/* OT/UT overlay stripe on top-right corner */
.empcal-day.has-ot::after  { content:'OT';position:absolute;top:5px;right:5px;font-size:0.52rem;font-weight:800;background:#dbeafe;color:#1d4ed8;padding:1px 4px;border-radius:3px;line-height:1.4; }
.empcal-day.has-ut::after  { content:'UT';position:absolute;top:5px;right:5px;font-size:0.52rem;font-weight:800;background:#fef9c3;color:#92400e;padding:1px 4px;border-radius:3px;line-height:1.4; }
.empcal-day.has-ot.has-ut::after { content:'OT·UT';position:absolute;top:5px;right:5px;font-size:0.48rem;font-weight:800;background:linear-gradient(90deg,#dbeafe,#fef9c3);color:#374151;padding:1px 4px;border-radius:3px;line-height:1.4; }

.empcal-day-num { font-size:0.78rem;font-weight:800;color:#374151;line-height:1; }

.empcal-day-status {
    font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;
    padding:2px 5px;border-radius:4px;display:inline-block;width:fit-content;
}
.empcal-day-status.s-present { background:#dcfce7;color:#16a34a; }
.empcal-day-status.s-late    { background:#fef9c3;color:#ca8a04; }
.empcal-day-status.s-absent  { background:#fee2e2;color:#dc2626; }
.empcal-day-status.s-early   { background:#ede9fe;color:#7c3aed; }

.empcal-day-times { font-family:'DM Mono',monospace;font-size:0.58rem;color:#6b7280;line-height:1.5; }
.empcal-day-times span { display:block; }

.empcal-otut-row { display:flex;gap:3px;flex-wrap:wrap;margin-top:1px; }
.empcal-otut-tag {
    font-size:0.55rem;font-weight:700;padding:1px 4px;border-radius:3px;line-height:1.4;
}
.empcal-otut-tag.ot { background:#dbeafe;color:#1d4ed8; }
.empcal-otut-tag.ut { background:#fef9c3;color:#92400e; }

.empcal-day-empty { border-radius:10px;min-height:82px;background:transparent;border:1.5px dashed #f3f4f6; }

/* ── Summary strip ──────────────────────────────────────────── */
.empcal-summary { display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:20px; }
@media (max-width:700px) { .empcal-summary { grid-template-columns:repeat(2,1fr); } }
@media (max-width:480px) { .empcal-summary { grid-template-columns:1fr; } }

.empcal-stat { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:12px; }
.empcal-stat-icon { width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.empcal-stat-icon.green  { background:#f0fdf4;color:#16a34a; }
.empcal-stat-icon.yellow { background:#fefce8;color:#ca8a04; }
.empcal-stat-icon.red    { background:#fef2f2;color:#dc2626; }
.empcal-stat-icon.purple { background:#f5f3ff;color:#7c3aed; }
.empcal-stat-icon.blue   { background:#eff6ff;color:#0284c7; }
.empcal-stat-icon.amber  { background:#fffbeb;color:#d97706; }
.empcal-stat-val { font-size:1.3rem;font-weight:800;color:#111827;line-height:1;font-family:'DM Mono',monospace; }
.empcal-stat-lbl { font-size:0.68rem;color:#9ca3af;margin-top:2px; }
</style>
@endpush

@section('content')
@php
    use Carbon\Carbon;

    $today      = Carbon::today();
    $monthStart = $month->copy()->startOfMonth();
    $monthEnd   = $month->copy()->endOfMonth();

    // Build weekday-only day lists for each half
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
@endphp

<div class="container-fluid empui-page empui-wrap">
    <div class="empui-backdrop"><div class="empui-grid"></div></div>
    <div class="empui-content">

        {{-- Hero --}}
        <div class="empui-hero">
            <div class="empui-hero-left">
                <h1 class="empui-title">My Attendance</h1>
                <p class="empui-sub">Your monthly attendance calendar — present, late, absent, and OT/UT at a glance.</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="empui-chip">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                        {{ $month->format('F Y') }}
                    </span>
                    <span class="empui-chip">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ $presentCount + $lateCount }} days in
                    </span>
                </div>
            </div>
        </div>

        {{-- Month navigation --}}
        <div class="empcal-month-nav">
            <a href="{{ route('employee.attendance.index', ['month' => $prevMonth]) }}" class="empcal-nav-btn">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Prev
            </a>
            <div class="text-center">
                <div class="empcal-month-label">{{ $month->format('F Y') }}</div>
                <div class="empcal-month-sub">Cutoff: 1–15 &amp; 16–{{ $monthEnd->day }}</div>
            </div>
            <a href="{{ route('employee.attendance.index', ['month' => $nextMonth]) }}" class="empcal-nav-btn">
                Next
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        {{-- Legend --}}
        <div class="empcal-legend">
            <div class="empcal-legend-item"><div class="empcal-legend-dot present"></div> Present</div>
            <div class="empcal-legend-item"><div class="empcal-legend-dot late"></div> Late</div>
            <div class="empcal-legend-item"><div class="empcal-legend-dot absent"></div> Absent</div>
            <div class="empcal-legend-item"><div class="empcal-legend-dot early"></div> Early Leave</div>
            <div class="empcal-legend-item"><div class="empcal-legend-dot ot"></div> Overtime</div>
            <div class="empcal-legend-item"><div class="empcal-legend-dot ut"></div> Undertime</div>
            <div class="empcal-legend-item"><div class="empcal-legend-dot nodata"></div> No Record</div>
        </div>

        {{-- Calendar --}}
        <div class="empcal-grid-wrap">

            {{-- First half: 1–15 --}}
            <div class="empcal-half">
                <div class="empcal-half-label"><span>1st Cutoff &nbsp;·&nbsp; {{ $monthStart->format('M 1') }} – {{ $monthStart->copy()->setDay(15)->format('M 15') }}</span></div>

                <div class="empcal-dow-row">
                    @foreach(['Mon','Tue','Wed','Thu','Fri'] as $dow)
                        <div class="empcal-dow">{{ $dow }}</div>
                    @endforeach
                </div>

                @php
                    $firstDay  = $firstHalfDays->first();
                    $padStart  = $firstDay ? ($firstDay->dayOfWeekIso - 1) : 0;
                    $firstCells = [];
                    for ($p = 0; $p < $padStart; $p++) $firstCells[] = null;
                    foreach ($firstHalfDays as $d) $firstCells[] = $d;
                    $rem = count($firstCells) % 5;
                    if ($rem > 0) for ($p = 0; $p < (5 - $rem); $p++) $firstCells[] = null;
                @endphp

                @foreach(array_chunk($firstCells, 5) as $week)
                <div class="empcal-days-row" style="margin-bottom:6px;">
                    @foreach($week as $day)
                        @if($day === null)
                            <div class="empcal-day-empty"></div>
                        @else
                            @php
                                $key      = $day->format('Y-m-d');
                                $att      = $attendances[$key] ?? null;
                                $dayOtut  = $otutRecords[$key] ?? collect();
                                $isToday  = $day->isSameDay($today);
                                $isFuture = $day->isAfter($today);
                                $hasOt    = $dayOtut->where('type','overtime')->count() > 0;
                                $hasUt    = $dayOtut->where('type','undertime')->count() > 0;

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
                                    $statusBadge = $dayClass;
                                }
                                $otutClass = ($hasOt && $hasUt) ? 'has-ot has-ut' : ($hasOt ? 'has-ot' : ($hasUt ? 'has-ut' : ''));
                            @endphp
                            <div class="empcal-day {{ $dayClass }} {{ $otutClass }} {{ $isToday ? 'is-today' : '' }} {{ $isFuture ? 'is-future' : '' }}">
                                <div class="empcal-day-num">{{ $day->day }}</div>
                                @if($att && !$isFuture)
                                    <div class="empcal-day-status {{ $statusBadge }}">{{ $statusLabel }}</div>
                                    @if($att->time_in || $att->time_out)
                                    <div class="empcal-day-times">
                                        @if($att->time_in)
                                            <span>▶ {{ \Carbon\Carbon::createFromFormat('H:i:s', $att->time_in)->format('g:i A') }}</span>
                                        @endif
                                        @if($att->time_out)
                                            <span>◀ {{ \Carbon\Carbon::createFromFormat('H:i:s', $att->time_out)->format('g:i A') }}</span>
                                        @endif
                                    </div>
                                    @endif
                                    @if($dayOtut->count())
                                    <div class="empcal-otut-row">
                                        @foreach($dayOtut as $rec)
                                            <span class="empcal-otut-tag {{ $rec->type === 'overtime' ? 'ot' : 'ut' }}">
                                                {{ $rec->type === 'overtime' ? 'OT' : 'UT' }} {{ number_format($rec->hours, 1) }}h
                                            </span>
                                        @endforeach
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
            <div class="empcal-half">
                <div class="empcal-half-label"><span>2nd Cutoff &nbsp;·&nbsp; {{ $monthStart->copy()->setDay(16)->format('M 16') }} – {{ $monthEnd->format('M j') }}</span></div>

                <div class="empcal-dow-row">
                    @foreach(['Mon','Tue','Wed','Thu','Fri'] as $dow)
                        <div class="empcal-dow">{{ $dow }}</div>
                    @endforeach
                </div>

                @php
                    $secondFirst = $secondHalfDays->first();
                    $padStart2   = $secondFirst ? ($secondFirst->dayOfWeekIso - 1) : 0;
                    $secondCells = [];
                    for ($p = 0; $p < $padStart2; $p++) $secondCells[] = null;
                    foreach ($secondHalfDays as $d) $secondCells[] = $d;
                    $rem2 = count($secondCells) % 5;
                    if ($rem2 > 0) for ($p = 0; $p < (5 - $rem2); $p++) $secondCells[] = null;
                @endphp

                @foreach(array_chunk($secondCells, 5) as $week)
                <div class="empcal-days-row" style="margin-bottom:6px;">
                    @foreach($week as $day)
                        @if($day === null)
                            <div class="empcal-day-empty"></div>
                        @else
                            @php
                                $key      = $day->format('Y-m-d');
                                $att      = $attendances[$key] ?? null;
                                $dayOtut  = $otutRecords[$key] ?? collect();
                                $isToday  = $day->isSameDay($today);
                                $isFuture = $day->isAfter($today);
                                $hasOt    = $dayOtut->where('type','overtime')->count() > 0;
                                $hasUt    = $dayOtut->where('type','undertime')->count() > 0;

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
                                    $statusBadge = $dayClass;
                                }
                                $otutClass = ($hasOt && $hasUt) ? 'has-ot has-ut' : ($hasOt ? 'has-ot' : ($hasUt ? 'has-ut' : ''));
                            @endphp
                            <div class="empcal-day {{ $dayClass }} {{ $otutClass }} {{ $isToday ? 'is-today' : '' }} {{ $isFuture ? 'is-future' : '' }}">
                                <div class="empcal-day-num">{{ $day->day }}</div>
                                @if($att && !$isFuture)
                                    <div class="empcal-day-status {{ $statusBadge }}">{{ $statusLabel }}</div>
                                    @if($att->time_in || $att->time_out)
                                    <div class="empcal-day-times">
                                        @if($att->time_in)
                                            <span>▶ {{ \Carbon\Carbon::createFromFormat('H:i:s', $att->time_in)->format('g:i A') }}</span>
                                        @endif
                                        @if($att->time_out)
                                            <span>◀ {{ \Carbon\Carbon::createFromFormat('H:i:s', $att->time_out)->format('g:i A') }}</span>
                                        @endif
                                    </div>
                                    @endif
                                    @if($dayOtut->count())
                                    <div class="empcal-otut-row">
                                        @foreach($dayOtut as $rec)
                                            <span class="empcal-otut-tag {{ $rec->type === 'overtime' ? 'ot' : 'ut' }}">
                                                {{ $rec->type === 'overtime' ? 'OT' : 'UT' }} {{ number_format($rec->hours, 1) }}h
                                            </span>
                                        @endforeach
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

        </div>{{-- /.empcal-grid-wrap --}}

    </div>
</div>
@endsection
