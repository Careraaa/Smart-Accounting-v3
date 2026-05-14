@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

.hld-page {
    font-family: 'Sora', sans-serif;
}

/* HEADER */
.hld-cal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 12px;
}

.hld-cal-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: #111827;
    letter-spacing: -0.02em;
    margin: 0;
}

.hld-cal-nav {
    display: flex;
    align-items: center;
    gap: 10px;
}

.hld-nav-btn {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
    color: #111827;
}

.hld-nav-btn:hover {
    border-color: #c8292a;
    color: #c8292a;
    background: #fff5f5;
}

.hld-nav-text {
    font-size: 0.92rem;
    font-weight: 700;
    color: #111827;
    min-width: 140px;
    text-align: center;
}

/* LAYOUT */
.hld-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 300px;
    gap: 20px;
    align-items: start;
}

@media(max-width: 1000px) {
    .hld-layout {
        grid-template-columns: 1fr;
    }
}

/* CALENDAR */
.hld-cal-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    overflow-x: auto;
    overflow-y: hidden;
    width: 100%;
}

.hld-cal-table {
    width: 100%;
    min-width: 900px;
    border-collapse: collapse;
    table-layout: fixed;
}

.hld-weekday {
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
    padding: 14px 10px;
    text-align: center;
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #6b7280;
}

.hld-day {
    height: 130px;
    min-height: 130px;
    border: 1px solid #f1f5f9;
    padding: 8px;
    vertical-align: top;
    position: relative;
    transition: background 0.15s ease;
    background: #fff;
}

.hld-day:hover {
    background: #fff7f7;
}

.hld-day.empty {
    background: #fafafa;
}

.hld-day.empty:hover {
    background: #fafafa;
}

.hld-day.today {
    background: #fff5f5;
}

.hld-day.today::after {
    content: '';
    position: absolute;
    inset: 0;
    border: 2px solid #c8292a;
    pointer-events: none;
}

.hld-day.other-month {
    background: #fcfcfc;
}

.hld-day-num {
    font-size: 0.78rem;
    font-weight: 800;
    color: #111827;
    margin-bottom: 8px;
}

.hld-day.other-month .hld-day-num {
    color: #cbd5e1;
}

.hld-day.today .hld-day-num {
    color: #c8292a;
}

/* HOLIDAY BADGES */
.hld-holiday-wrap {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.hld-holiday-badge {
    display: block;
    width: 100%;
    padding: 5px 7px;
    border-radius: 8px;
    font-size: 0.66rem;
    font-weight: 700;
    line-height: 1.25;
    overflow: hidden;
    text-overflow: ellipsis;
    word-break: break-word;
}

.hld-badge-regular {
    background: #f0fdf4;
    color: #15803d;
    border: 1px solid #bbf7d0;
}

.hld-badge-special {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
}

/* SIDEBAR */
.hld-sidebar {
    position: sticky;
    top: 20px;
}

.hld-side-card,
.hld-legend {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 18px;
    margin-bottom: 16px;
}

.hld-side-title,
.hld-legend-title {
    font-size: 0.84rem;
    font-weight: 800;
    color: #111827;
    margin: 0 0 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.hld-side-stat {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
}

.hld-side-stat:last-child {
    border-bottom: none;
}

.hld-side-icon {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 800;
    flex-shrink: 0;
}

.hld-side-icon.regular {
    background: #f0fdf4;
    color: #16a34a;
}

.hld-side-icon.special {
    background: #fffbeb;
    color: #d97706;
}

.hld-side-label {
    font-size: 0.78rem;
    color: #9ca3af;
}

.hld-side-value {
    font-size: 1.2rem;
    font-weight: 800;
    color: #111827;
    font-family: 'DM Mono', monospace;
}

/* LEGEND */
.hld-legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.78rem;
    color: #6b7280;
    margin-bottom: 10px;
}

.hld-legend-item:last-child {
    margin-bottom: 0;
}

.hld-legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 3px;
}

.hld-legend-dot.regular {
    background: #16a34a;
}

.hld-legend-dot.special {
    background: #d97706;
}

/* BUTTONS */
.hld-action-btns {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.hld-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 14px;
    border: 1px solid #e5e7eb;
    background: #fff;
    border-radius: 10px;
    font-size: 0.78rem;
    font-weight: 700;
    color: #374151;
    text-decoration: none;
    transition: all 0.15s ease;
}

.hld-btn:hover {
    border-color: #c8292a;
    color: #c8292a;
    background: #fff5f5;
}

.hld-btn-primary {
    background: #c8292a;
    color: #fff;
    border-color: #c8292a;
}

.hld-btn-primary:hover {
    background: #a81f20;
    border-color: #a81f20;
    color: #fff;
}

/* MOBILE */
@media(max-width: 768px) {

    .hld-cal-card {
        border-radius: 12px;
    }

    .hld-day {
        height: 110px;
        min-height: 110px;
        padding: 6px;
    }

    .hld-holiday-badge {
        font-size: 0.62rem;
        padding: 4px 6px;
    }
}
</style>
@endpush

@section('content')
<div class="hld-page">

    {{-- HEADER --}}
    <div class="hld-cal-header">
        <h1 class="hld-cal-title">Holiday Calendar</h1>

        <div class="hld-cal-nav">
            <a href="{{ route('holiday.calendar', ['month' => $prevMonth]) }}"
               class="hld-nav-btn"
               title="Previous Month">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>

            <div class="hld-nav-text">
                {{ $currentMonth->format('F Y') }}
            </div>

            <a href="{{ route('holiday.calendar', ['month' => $nextMonth]) }}"
               class="hld-nav-btn"
               title="Next Month">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>

    <div class="hld-layout">

        {{-- CALENDAR --}}
        <div>
            <div class="hld-cal-card">

                <table class="hld-cal-table">
                    <thead>
                        <tr>
                            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                                <th class="hld-weekday">{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $month = $currentMonth->copy();

                            $firstDay = $month->copy()->startOfMonth();
                            $lastDay = $month->copy()->endOfMonth();

                            $startDate = $firstDay->copy()->startOfWeek(Carbon\Carbon::SUNDAY);
                            $endDate = $lastDay->copy()->endOfWeek(Carbon\Carbon::SATURDAY);

                            $date = $startDate->copy();
                        @endphp

                        @while($date <= $endDate)
                            <tr>

                                @for($i = 0; $i < 7; $i++)

                                    @php
                                        $current = $date->copy();

                                        $isToday = $current->isToday();
                                        $isOtherMonth = !$current->isSameMonth($month);

                                        $dayHolidays = $holidays->filter(function($holiday) use ($current) {
                                            return \Carbon\Carbon::parse($holiday->date)->toDateString() === $current->toDateString();
                                        });
                                    @endphp

                                    <td class="hld-day {{ $isToday ? 'today' : '' }} {{ $isOtherMonth ? 'other-month' : '' }}">

                                        <div class="hld-day-num">
                                            {{ $current->day }}
                                        </div>

                                        @if($dayHolidays->count())
                                            <div class="hld-holiday-wrap">
                                                @foreach($dayHolidays as $holiday)
                                                    <div class="hld-holiday-badge hld-badge-{{ $holiday->type }}"
                                                         title="{{ $holiday->name }}">
                                                        {{ $holiday->name }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                    </td>

                                    @php
                                        $date->addDay();
                                    @endphp

                                @endfor

                            </tr>
                        @endwhile
                    </tbody>
                </table>

            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="hld-sidebar">

            {{-- SUMMARY --}}
            <div class="hld-side-card">
                <p class="hld-side-title">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/>
                    </svg>

                    {{ $currentMonth->format('Y') }} Summary
                </p>

                <div class="hld-side-stat">
                    <div class="hld-side-icon regular">R</div>

                    <div>
                        <div class="hld-side-label">Regular Holidays</div>
                        <div class="hld-side-value">{{ $regularCount }}</div>
                    </div>
                </div>

                <div class="hld-side-stat">
                    <div class="hld-side-icon special">S</div>

                    <div>
                        <div class="hld-side-label">Special Holidays</div>
                        <div class="hld-side-value">{{ $specialCount }}</div>
                    </div>
                </div>
            </div>

            {{-- LEGEND --}}
            <div class="hld-legend">

                <p class="hld-legend-title">
                    Holiday Types
                </p>

                <div class="hld-legend-item">
                    <div class="hld-legend-dot regular"></div>
                    <span>Regular Holiday</span>
                </div>

                <div class="hld-legend-item">
                    <div class="hld-legend-dot special"></div>
                    <span>Special Non-Working</span>
                </div>

            </div>

            {{-- ACTIONS --}}
            <div class="hld-action-btns">

                <a href="{{ route('holiday.create') }}"
                   class="hld-btn hld-btn-primary">

                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 4v16m8-8H4"/>
                    </svg>

                    Add Holiday
                </a>

                <a href="{{ route('holiday.index') }}"
                   class="hld-btn">

                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>

                    List View
                </a>

            </div>

        </div>

    </div>

</div>
@endsection