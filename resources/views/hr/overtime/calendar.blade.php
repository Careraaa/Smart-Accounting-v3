@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
*, *::before, *::after { box-sizing: border-box; }
.otcal-page { font-family: 'Sora', sans-serif; }

/* ── Topbar ─────────────────────────────────────────────────── */
.otcal-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:28px;flex-wrap:wrap; }
.otcal-topbar-left { display:flex;align-items:center;gap:14px; }
.otcal-avatar { width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#0284c7,#38bdf8);display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:800;color:#fff;flex-shrink:0;letter-spacing:-0.02em; }
.otcal-emp-name { font-size:1.25rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 2px; }
.otcal-emp-meta { font-size:0.78rem;color:#9ca3af;margin:0;display:flex;align-items:center;gap:8px; }
.otcal-emp-dept { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;background:#f3f4f6;border-radius:20px;font-size:0.68rem;font-weight:700;color:#374151; }

.otcal-btn-back {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.otcal-btn-back:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

/* ── Month nav ──────────────────────────────────────────────── */
.otcal-month-nav {
    display:flex;align-items:center;justify-content:space-between;
    background:#fff;border:1px solid #e5e7eb;border-radius:14px;
    padding:14px 20px;margin-bottom:20px;
}
.otcal-month-label { font-size:1.05rem;font-weight:800;color:#111827;letter-spacing:-0.02em; }
.otcal-month-sub   { font-size:0.72rem;color:#9ca3af;margin-top:2px; }
.otcal-nav-btn {
    display:inline-flex;align-items:center;gap:6px;padding:8px 14px;
    background:#f9fafb;border:1px solid #e5e7eb;border-radius:9px;
    font-family:'Sora',sans-serif;font-size:0.78rem;font-weight:600;color:#374151;
    text-decoration:none;transition:all 0.12s;
}
.otcal-nav-btn:hover { background:#f0f9ff;border-color:#bae6fd;color:#0284c7; }

/* ── Legend ─────────────────────────────────────────────────── */
.otcal-legend { display:flex;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:20px; }
.otcal-legend-item { display:flex;align-items:center;gap:6px;font-size:0.72rem;font-weight:600;color:#6b7280; }
.otcal-legend-dot { width:12px;height:12px;border-radius:4px; }
.otcal-legend-dot.ot      { background:#dbeafe;border:1.5px solid #93c5fd; }
.otcal-legend-dot.ut      { background:#fef9c3;border:1.5px solid #fde047; }
.otcal-legend-dot.both    { background:linear-gradient(135deg,#dbeafe 50%,#fef9c3 50%);border:1.5px solid #93c5fd; }
.otcal-legend-dot.nodata  { background:#fff;border:1.5px solid #e5e7eb; }

/* ── Calendar grid ──────────────────────────────────────────── */
.otcal-grid-wrap { background:#fff;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden; }

.otcal-half { padding:20px 20px 16px; }
.otcal-half:first-child { border-bottom:2px dashed #f3f4f6; }

.otcal-half-label {
    font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;
    color:#9ca3af;margin-bottom:14px;display:flex;align-items:center;gap:8px;
}
.otcal-half-label::after { content:'';flex:1;height:1px;background:#f3f4f6; }
.otcal-half-label span { background:#fff;padding-right:8px; }

.otcal-dow-row { display:grid;grid-template-columns:repeat(5,1fr);gap:6px;margin-bottom:8px; }
.otcal-dow { text-align:center;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;padding:4px 0; }

.otcal-days-row { display:grid;grid-template-columns:repeat(5,1fr);gap:6px; }

.otcal-day {
    border-radius:10px;padding:10px 8px 8px;min-height:80px;
    border:1.5px solid #e5e7eb;background:#fff;
    display:flex;flex-direction:column;gap:3px;
    transition:transform 0.1s,box-shadow 0.1s;
    position:relative;
}
.otcal-day:hover { transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,0,0,0.07); }

.otcal-day.is-today { border-color:#0284c7 !important;box-shadow:0 0 0 2px rgba(2,132,199,0.15); }
.otcal-day.is-today .otcal-day-num { color:#0284c7; }
.otcal-day.is-future { background:#fafafa;border-color:#f3f4f6; }
.otcal-day.is-future .otcal-day-num { color:#d1d5db; }

.otcal-day.has-ot   { background:#eff6ff;border-color:#93c5fd; }
.otcal-day.has-ut   { background:#fefce8;border-color:#fde047; }
.otcal-day.has-both { background:linear-gradient(160deg,#eff6ff 50%,#fefce8 50%);border-color:#93c5fd; }

.otcal-day-num { font-size:0.78rem;font-weight:800;color:#374151;line-height:1; }

.otcal-entry {
    display:flex;align-items:center;gap:4px;
    padding:2px 5px;border-radius:5px;
    font-size:0.6rem;font-weight:700;line-height:1.3;
}
.otcal-entry.ot { background:#dbeafe;color:#1d4ed8; }
.otcal-entry.ut { background:#fef9c3;color:#92400e; }

.otcal-entry-hrs { font-family:'DM Mono',monospace;font-size:0.62rem; }

.otcal-day-empty { border-radius:10px;min-height:80px;background:transparent;border:1.5px dashed #f3f4f6; }

/* ── Summary strip ──────────────────────────────────────────── */
.otcal-summary { display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:20px; }
@media (max-width:640px) { .otcal-summary { grid-template-columns:repeat(2,1fr); } }

.otcal-stat { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:12px; }
.otcal-stat-icon { width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.otcal-stat-icon.blue   { background:#eff6ff;color:#0284c7; }
.otcal-stat-icon.amber  { background:#fffbeb;color:#d97706; }
.otcal-stat-icon.green  { background:#f0fdf4;color:#16a34a; }
.otcal-stat-icon.red    { background:#fef2f2;color:#dc2626; }
.otcal-stat-val { font-size:1.35rem;font-weight:800;color:#111827;line-height:1;font-family:'DM Mono',monospace; }
.otcal-stat-lbl { font-size:0.68rem;color:#9ca3af;margin-top:2px; }

/* ── Add record btn ─────────────────────────────────────────── */
.otcal-add-btn {
    display:inline-flex;align-items:center;gap:7px;padding:9px 18px;
    background:#111827;color:#fff;border:none;border-radius:10px;
    font-family:'Sora',sans-serif;font-size:0.82rem;font-weight:600;
    text-decoration:none;cursor:pointer;transition:background 0.15s;
}
.otcal-add-btn:hover { background:#000;color:#fff; }
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

    // Flatten all records for summary
    $allRecords = $records->flatten();
    $totalOtHrs = $allRecords->where('type', 'overtime')->sum('hours');
    $totalUtHrs = $allRecords->where('type', 'undertime')->sum('hours');
    $otCount    = $allRecords->where('type', 'overtime')->count();
    $utCount    = $allRecords->where('type', 'undertime')->count();

    $initials = strtoupper(substr($employee->first_name ?? 'U', 0, 1) . substr($employee->last_name ?? '', 0, 1));
@endphp

<div class="otcal-page">

    {{-- Topbar --}}
    <div class="otcal-topbar">
        <div class="otcal-topbar-left">
            <div class="otcal-avatar">{{ $initials }}</div>
            <div>
                <h1 class="otcal-emp-name">{{ $employee->first_name }} {{ $employee->last_name }}</h1>
                <p class="otcal-emp-meta">
                    <span class="otcal-emp-dept">
                        <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        {{ $employee->department ?? 'No Department' }}
                    </span>
                    <span>{{ $employee->position ?? 'No Position' }}</span>
                </p>
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <a href="{{ route('overtime.create') }}" class="otcal-add-btn">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Record
            </a>
            <a href="{{ route('overtime.index') }}" class="otcal-btn-back">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
        </div>
    </div>

    {{-- Month navigation --}}
    <div class="otcal-month-nav">
        <a href="{{ route('overtime.employee.calendar', [$employee->id, 'month' => $prevMonth]) }}" class="otcal-nav-btn">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Prev
        </a>
        <div class="text-center">
            <div class="otcal-month-label">{{ $month->format('F Y') }}</div>
            <div class="otcal-month-sub">Cutoff: 1–15 &amp; 16–{{ $monthEnd->day }}</div>
        </div>
        <a href="{{ route('overtime.employee.calendar', [$employee->id, 'month' => $nextMonth]) }}" class="otcal-nav-btn">
            Next
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    {{-- Legend --}}
    <div class="otcal-legend">
        <div class="otcal-legend-item"><div class="otcal-legend-dot ot"></div> Overtime</div>
        <div class="otcal-legend-item"><div class="otcal-legend-dot ut"></div> Undertime</div>
        <div class="otcal-legend-item"><div class="otcal-legend-dot both"></div> Both</div>
        <div class="otcal-legend-item"><div class="otcal-legend-dot nodata"></div> No Record</div>
    </div>

    {{-- Calendar --}}
    <div class="otcal-grid-wrap">

        {{-- First half: 1–15 --}}
        <div class="otcal-half">
            <div class="otcal-half-label"><span>1st Cutoff &nbsp;·&nbsp; {{ $monthStart->format('M 1') }} – {{ $monthStart->copy()->setDay(15)->format('M 15') }}</span></div>

            <div class="otcal-dow-row">
                @foreach(['Mon','Tue','Wed','Thu','Fri'] as $dow)
                    <div class="otcal-dow">{{ $dow }}</div>
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
            <div class="otcal-days-row" style="margin-bottom:6px;">
                @foreach($week as $day)
                    @if($day === null)
                        <div class="otcal-day-empty"></div>
                    @else
                        @php
                            $key      = $day->format('Y-m-d');
                            $dayRecs  = $records[$key] ?? collect();
                            $isToday  = $day->isSameDay($today);
                            $isFuture = $day->isAfter($today);
                            $hasOt    = $dayRecs->where('type','overtime')->count() > 0;
                            $hasUt    = $dayRecs->where('type','undertime')->count() > 0;
                            $colorCls = '';
                            if ($hasOt && $hasUt) $colorCls = 'has-both';
                            elseif ($hasOt)        $colorCls = 'has-ot';
                            elseif ($hasUt)        $colorCls = 'has-ut';
                        @endphp
                        <div class="otcal-day {{ $colorCls }} {{ $isToday ? 'is-today' : '' }} {{ $isFuture ? 'is-future' : '' }}">
                            <div class="otcal-day-num">{{ $day->day }}</div>
                            @if(!$isFuture && $dayRecs->count())
                                @foreach($dayRecs as $rec)
                                    <div class="otcal-entry {{ $rec->type === 'overtime' ? 'ot' : 'ut' }}">
                                        <svg width="8" height="8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            @if($rec->type === 'overtime')
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                                            @endif
                                        </svg>
                                        <span class="otcal-entry-hrs">{{ number_format($rec->hours, 1) }}h</span>
                                    </div>
                                @endforeach
                            @elseif(!$isFuture)
                                <div style="font-size:0.6rem;color:#d1d5db;margin-top:2px;">—</div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
            @endforeach
        </div>

        {{-- Second half: 16–end --}}
        <div class="otcal-half">
            <div class="otcal-half-label"><span>2nd Cutoff &nbsp;·&nbsp; {{ $monthStart->copy()->setDay(16)->format('M 16') }} – {{ $monthEnd->format('M j') }}</span></div>

            <div class="otcal-dow-row">
                @foreach(['Mon','Tue','Wed','Thu','Fri'] as $dow)
                    <div class="otcal-dow">{{ $dow }}</div>
                @endforeach
            </div>

            @php
                $secondFirst  = $secondHalfDays->first();
                $padStart2    = $secondFirst ? ($secondFirst->dayOfWeekIso - 1) : 0;
                $secondCells  = [];
                for ($p = 0; $p < $padStart2; $p++) $secondCells[] = null;
                foreach ($secondHalfDays as $d) $secondCells[] = $d;
                $rem2 = count($secondCells) % 5;
                if ($rem2 > 0) for ($p = 0; $p < (5 - $rem2); $p++) $secondCells[] = null;
            @endphp

            @foreach(array_chunk($secondCells, 5) as $week)
            <div class="otcal-days-row" style="margin-bottom:6px;">
                @foreach($week as $day)
                    @if($day === null)
                        <div class="otcal-day-empty"></div>
                    @else
                        @php
                            $key      = $day->format('Y-m-d');
                            $dayRecs  = $records[$key] ?? collect();
                            $isToday  = $day->isSameDay($today);
                            $isFuture = $day->isAfter($today);
                            $hasOt    = $dayRecs->where('type','overtime')->count() > 0;
                            $hasUt    = $dayRecs->where('type','undertime')->count() > 0;
                            $colorCls = '';
                            if ($hasOt && $hasUt) $colorCls = 'has-both';
                            elseif ($hasOt)        $colorCls = 'has-ot';
                            elseif ($hasUt)        $colorCls = 'has-ut';
                        @endphp
                        <div class="otcal-day {{ $colorCls }} {{ $isToday ? 'is-today' : '' }} {{ $isFuture ? 'is-future' : '' }}">
                            <div class="otcal-day-num">{{ $day->day }}</div>
                            @if(!$isFuture && $dayRecs->count())
                                @foreach($dayRecs as $rec)
                                    <div class="otcal-entry {{ $rec->type === 'overtime' ? 'ot' : 'ut' }}">
                                        <svg width="8" height="8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            @if($rec->type === 'overtime')
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                                            @endif
                                        </svg>
                                        <span class="otcal-entry-hrs">{{ number_format($rec->hours, 1) }}h</span>
                                    </div>
                                @endforeach
                            @elseif(!$isFuture)
                                <div style="font-size:0.6rem;color:#d1d5db;margin-top:2px;">—</div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
            @endforeach
        </div>

    </div>{{-- /.otcal-grid-wrap --}}

    {{-- Summary strip --}}
    <div class="otcal-summary">
        <div class="otcal-stat">
            <div class="otcal-stat-icon blue">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg>
            </div>
            <div>
                <div class="otcal-stat-val">{{ number_format($totalOtHrs, 1) }}</div>
                <div class="otcal-stat-lbl">OT hours this month</div>
            </div>
        </div>
        <div class="otcal-stat">
            <div class="otcal-stat-icon amber">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 8v4l-2 2"/></svg>
            </div>
            <div>
                <div class="otcal-stat-val">{{ number_format($totalUtHrs, 1) }}</div>
                <div class="otcal-stat-lbl">UT hours this month</div>
            </div>
        </div>
        <div class="otcal-stat">
            <div class="otcal-stat-icon green">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <div class="otcal-stat-val">{{ $otCount }}</div>
                <div class="otcal-stat-lbl">Overtime entries</div>
            </div>
        </div>
        <div class="otcal-stat">
            <div class="otcal-stat-icon red">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
            </div>
            <div>
                <div class="otcal-stat-val">{{ $utCount }}</div>
                <div class="otcal-stat-lbl">Undertime entries</div>
            </div>
        </div>
    </div>

</div>
@endsection
