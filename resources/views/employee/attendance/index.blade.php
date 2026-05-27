@extends('layouts.layout')

@section('content')
@php
    use Carbon\Carbon;

    $today      = Carbon::today();
    $monthStart = $month->copy()->startOfMonth();
    $monthEnd   = $month->copy()->endOfMonth();

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

<div class="min-h-screen bg-gray-50/60">
    <div class="max-w-screen-xl mx-auto px-4 sm:px-6 py-8">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-7">
            <div>
                <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-1">Employee Portal</p>
                <h1 class="text-2xl font-extrabold tracking-tight text-gray-900 leading-tight">Time &amp; Attendance</h1>
                <p class="text-sm text-gray-500 mt-1">Your monthly attendance calendar — present, late, absent, and OT/UT at a glance.</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                    {{ $month->format('F Y') }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 shadow-sm">
                    <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ $presentCount + $lateCount }} days in
                </span>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-xl p-1 shadow-sm mb-6 w-fit">
            <a href="{{ route('employee.attendance.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all bg-gray-900 text-white shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                Attendance Calendar
            </a>
            <a href="{{ route('employee.overtime-undertime.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all text-gray-500 hover:text-gray-800 hover:bg-gray-50">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                OT / UT Requests
            </a>
        </div>

        {{-- Month Navigation --}}
        <div class="flex items-center justify-between bg-white border border-gray-200 rounded-2xl px-5 py-4 mb-5 shadow-sm">
            <a href="{{ route('employee.attendance.index', ['month' => $prevMonth]) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl text-xs font-semibold text-gray-600 hover:text-gray-900 transition-all">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Prev
            </a>
            <div class="text-center">
                <div class="text-base font-extrabold tracking-tight text-gray-900">{{ $month->format('F Y') }}</div>
                <div class="text-xs text-gray-400 mt-0.5">Cutoff: 1–15 &amp; 16–{{ $monthEnd->day }}</div>
            </div>
            <a href="{{ route('employee.attendance.index', ['month' => $nextMonth]) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl text-xs font-semibold text-gray-600 hover:text-gray-900 transition-all">
                Next
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        {{-- Summary Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
            <div class="bg-white border border-gray-200 rounded-xl p-3.5 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <div class="font-mono text-lg font-bold text-gray-900 leading-none">{{ $presentCount }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">Present</div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-3.5 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                </div>
                <div>
                    <div class="font-mono text-lg font-bold text-gray-900 leading-none">{{ $lateCount }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">Late</div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-3.5 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="font-mono text-lg font-bold text-gray-900 leading-none">{{ $absentCount }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">Absent</div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-3.5 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="font-mono text-lg font-bold text-gray-900 leading-none">{{ number_format($otHours, 1) }}h</div>
                    <div class="text-xs text-gray-400 mt-0.5">OT Hours</div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-3.5 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-yellow-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="font-mono text-lg font-bold text-gray-900 leading-none">{{ number_format($utHours, 1) }}h</div>
                    <div class="text-xs text-gray-400 mt-0.5">UT Hours</div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-3.5 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div>
                    <div class="font-mono text-lg font-bold text-gray-900 leading-none">{{ $firstHalfDays->count() + $secondHalfDays->count() }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">Work Days</div>
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap items-center gap-4 mb-5 px-1">
            @foreach([
                ['label' => 'Present',     'bg' => 'bg-emerald-100', 'border' => 'border-emerald-300'],
                ['label' => 'Late',        'bg' => 'bg-amber-100',   'border' => 'border-amber-300'],
                ['label' => 'Absent',      'bg' => 'bg-rose-100',    'border' => 'border-rose-300'],
                ['label' => 'Overtime',    'bg' => 'bg-blue-100',    'border' => 'border-blue-300'],
                ['label' => 'Undertime',   'bg' => 'bg-yellow-100',  'border' => 'border-yellow-400'],
                ['label' => 'No Record',   'bg' => 'bg-white',       'border' => 'border-gray-300'],
            ] as $item)
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-sm {{ $item['bg'] }} border {{ $item['border'] }}"></div>
                <span class="text-xs font-semibold text-gray-500">{{ $item['label'] }}</span>
            </div>
            @endforeach
        </div>

        {{-- Calendar Grid --}}
        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm mb-6">

            {{-- First half: 1–15 --}}
            <div class="p-5 border-b-2 border-dashed border-gray-100">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-400">1st Cutoff</span>
                    <span class="text-xs font-semibold text-gray-500">{{ $monthStart->format('M 1') }} – {{ $monthStart->copy()->setDay(15)->format('M 15') }}</span>
                    <div class="flex-1 h-px bg-gray-100"></div>
                </div>

                {{-- Day-of-week header --}}
                <div class="overflow-x-auto -mx-5 px-5">
                    <div class="min-w-[500px]">
                        <div class="grid grid-cols-5 gap-2 mb-2">
                            @foreach(['Mon','Tue','Wed','Thu','Fri'] as $dow)
                                <div class="text-center text-xs font-bold uppercase tracking-widest text-gray-400 py-1">{{ $dow }}</div>
                            @endforeach
                        </div>

                        @php
                            $firstDay   = $firstHalfDays->first();
                            $padStart   = $firstDay ? ($firstDay->dayOfWeekIso - 1) : 0;
                            $firstCells = [];
                            for ($p = 0; $p < $padStart; $p++) $firstCells[] = null;
                            foreach ($firstHalfDays as $d) $firstCells[] = $d;
                            $rem = count($firstCells) % 5;
                            if ($rem > 0) for ($p = 0; $p < (5 - $rem); $p++) $firstCells[] = null;
                        @endphp

                        @foreach(array_chunk($firstCells, 5) as $week)
                        <div class="grid grid-cols-5 gap-2 mb-2">
                            @foreach($week as $day)
                                @if($day === null)
                                    <div class="min-h-[88px] rounded-xl border-2 border-dashed border-gray-100 bg-transparent"></div>
                                @else
                                    @php
                                        $key      = $day->format('Y-m-d');
                                        $att      = $attendances[$key] ?? null;
                                        $dayOtut  = $otutRecords[$key] ?? collect();
                                        $isToday  = $day->isSameDay($today);
                                        $isFuture = $day->isAfter($today);
                                        $hasOt    = $dayOtut->where('type','overtime')->count() > 0;
                                        $hasUt    = $dayOtut->where('type','undertime')->count() > 0;

                                        $cellBg = 'bg-white';
                                        $cellBorder = 'border-gray-200';
                                        $statusLabel = '';
                                        $statusClasses = '';

                                        if ($att && !$isFuture) {
                                            switch ($att->status) {
                                                case 'present':
                                                    $cellBg = 'bg-emerald-50';
                                                    $cellBorder = 'border-emerald-200';
                                                    $statusLabel = 'Present';
                                                    $statusClasses = 'bg-emerald-100 text-emerald-700';
                                                    break;
                                                case 'late':
                                                    $cellBg = 'bg-amber-50';
                                                    $cellBorder = 'border-amber-200';
                                                    $statusLabel = 'Late';
                                                    $statusClasses = 'bg-amber-100 text-amber-700';
                                                    break;
                                                case 'absent':
                                                    $cellBg = 'bg-rose-50';
                                                    $cellBorder = 'border-rose-200';
                                                    $statusLabel = 'Absent';
                                                    $statusClasses = 'bg-rose-100 text-rose-700';
                                                    break;
                                                default:
                                                    $statusLabel = ucfirst($att->status);
                                                    $statusClasses = 'bg-gray-100 text-gray-600';
                                            }
                                        }
                                    @endphp
                                    <div class="relative min-h-[88px] rounded-xl border-2 {{ $cellBorder }} {{ $cellBg }} p-2 flex flex-col gap-1 transition-all duration-100 hover:-translate-y-0.5 hover:shadow-md {{ $isToday ? 'ring-2 ring-gray-900 ring-offset-1' : '' }} {{ $isFuture ? 'opacity-50' : '' }}">
                                        <div class="flex items-start justify-between">
                                            <span class="text-xs font-extrabold {{ $isToday ? 'text-gray-900' : 'text-gray-500' }} leading-none">{{ $day->day }}</span>
                                            @if($hasOt && $hasUt)
                                                <span class="text-[0.52rem] font-bold bg-gradient-to-r from-blue-100 to-yellow-100 text-gray-700 px-1.5 py-0.5 rounded leading-none">OT·UT</span>
                                            @elseif($hasOt)
                                                <span class="text-[0.52rem] font-bold bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded leading-none">OT</span>
                                            @elseif($hasUt)
                                                <span class="text-[0.52rem] font-bold bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded leading-none">UT</span>
                                            @endif
                                        </div>
                                        @if($att && !$isFuture)
                                            <span class="text-[0.58rem] font-bold {{ $statusClasses }} px-1.5 py-0.5 rounded w-fit leading-none">{{ $statusLabel }}</span>
                                            @if($att->time_in || $att->time_out)
                                            <div class="font-mono text-[0.55rem] text-gray-500 leading-relaxed mt-auto">
                                                @if($att->time_in)
                                                    <div>▶ {{ \Carbon\Carbon::createFromFormat('H:i:s', $att->time_in)->format('g:i A') }}</div>
                                                @endif
                                                @if($att->time_out)
                                                    <div>◀ {{ \Carbon\Carbon::createFromFormat('H:i:s', $att->time_out)->format('g:i A') }}</div>
                                                @endif
                                            </div>
                                            @endif
                                            @if($dayOtut->count())
                                            <div class="flex gap-1 flex-wrap">
                                                @foreach($dayOtut as $rec)
                                                    <span class="text-[0.52rem] font-bold px-1.5 py-0.5 rounded leading-none {{ $rec->type === 'overtime' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                        {{ $rec->type === 'overtime' ? 'OT' : 'UT' }} {{ number_format($rec->hours, 1) }}h
                                                    </span>
                                                @endforeach
                                            </div>
                                            @endif
                                        @elseif(!$isFuture)
                                            <span class="text-[0.58rem] text-gray-300 mt-auto">No record</span>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Second half: 16--end --}}
            <div class="p-5">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-400">2nd Cutoff</span>
                    <span class="text-xs font-semibold text-gray-500">{{ $monthStart->copy()->setDay(16)->format('M 16') }} – {{ $monthEnd->format('M j') }}</span>
                    <div class="flex-1 h-px bg-gray-100"></div>
                </div>

                <div class="overflow-x-auto -mx-5 px-5">
                    <div class="min-w-[500px]">
                        <div class="grid grid-cols-5 gap-2 mb-2">
                            @foreach(['Mon','Tue','Wed','Thu','Fri'] as $dow)
                                <div class="text-center text-xs font-bold uppercase tracking-widest text-gray-400 py-1">{{ $dow }}</div>
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
                        <div class="grid grid-cols-5 gap-2 mb-2">
                            @foreach($week as $day)
                                @if($day === null)
                                    <div class="min-h-[88px] rounded-xl border-2 border-dashed border-gray-100 bg-transparent"></div>
                                @else
                                    @php
                                        $key      = $day->format('Y-m-d');
                                        $att      = $attendances[$key] ?? null;
                                        $dayOtut  = $otutRecords[$key] ?? collect();
                                        $isToday  = $day->isSameDay($today);
                                        $isFuture = $day->isAfter($today);
                                        $hasOt    = $dayOtut->where('type','overtime')->count() > 0;
                                        $hasUt    = $dayOtut->where('type','undertime')->count() > 0;

                                        $cellBg = 'bg-white';
                                        $cellBorder = 'border-gray-200';
                                        $statusLabel = '';
                                        $statusClasses = '';

                                        if ($att && !$isFuture) {
                                            switch ($att->status) {
                                                case 'present':
                                                    $cellBg = 'bg-emerald-50';
                                                    $cellBorder = 'border-emerald-200';
                                                    $statusLabel = 'Present';
                                                    $statusClasses = 'bg-emerald-100 text-emerald-700';
                                                    break;
                                                case 'late':
                                                    $cellBg = 'bg-amber-50';
                                                    $cellBorder = 'border-amber-200';
                                                    $statusLabel = 'Late';
                                                    $statusClasses = 'bg-amber-100 text-amber-700';
                                                    break;
                                                case 'absent':
                                                    $cellBg = 'bg-rose-50';
                                                    $cellBorder = 'border-rose-200';
                                                    $statusLabel = 'Absent';
                                                    $statusClasses = 'bg-rose-100 text-rose-700';
                                                    break;
                                                default:
                                                    $statusLabel = ucfirst($att->status);
                                                    $statusClasses = 'bg-gray-100 text-gray-600';
                                            }
                                        }
                                    @endphp
                                    <div class="relative min-h-[88px] rounded-xl border-2 {{ $cellBorder }} {{ $cellBg }} p-2 flex flex-col gap-1 transition-all duration-100 hover:-translate-y-0.5 hover:shadow-md {{ $isToday ? 'ring-2 ring-gray-900 ring-offset-1' : '' }} {{ $isFuture ? 'opacity-50' : '' }}">
                                        <div class="flex items-start justify-between">
                                            <span class="text-xs font-extrabold {{ $isToday ? 'text-gray-900' : 'text-gray-500' }} leading-none">{{ $day->day }}</span>
                                            @if($hasOt && $hasUt)
                                                <span class="text-[0.52rem] font-bold bg-gradient-to-r from-blue-100 to-yellow-100 text-gray-700 px-1.5 py-0.5 rounded leading-none">OT·UT</span>
                                            @elseif($hasOt)
                                                <span class="text-[0.52rem] font-bold bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded leading-none">OT</span>
                                            @elseif($hasUt)
                                                <span class="text-[0.52rem] font-bold bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded leading-none">UT</span>
                                            @endif
                                        </div>
                                        @if($att && !$isFuture)
                                            <span class="text-[0.58rem] font-bold {{ $statusClasses }} px-1.5 py-0.5 rounded w-fit leading-none">{{ $statusLabel }}</span>
                                            @if($att->time_in || $att->time_out)
                                            <div class="font-mono text-[0.55rem] text-gray-500 leading-relaxed mt-auto">
                                                @if($att->time_in)
                                                    <div>▶ {{ \Carbon\Carbon::createFromFormat('H:i:s', $att->time_in)->format('g:i A') }}</div>
                                                @endif
                                                @if($att->time_out)
                                                    <div>◀ {{ \Carbon\Carbon::createFromFormat('H:i:s', $att->time_out)->format('g:i A') }}</div>
                                                @endif
                                            </div>
                                            @endif
                                            @if($dayOtut->count())
                                            <div class="flex gap-1 flex-wrap">
                                                @foreach($dayOtut as $rec)
                                                    <span class="text-[0.52rem] font-bold px-1.5 py-0.5 rounded leading-none {{ $rec->type === 'overtime' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                        {{ $rec->type === 'overtime' ? 'OT' : 'UT' }} {{ number_format($rec->hours, 1) }}h
                                                    </span>
                                                @endforeach
                                            </div>
                                            @endif
                                        @elseif(!$isFuture)
                                            <span class="text-[0.58rem] text-gray-300 mt-auto">No record</span>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
                </div>

                @php
                    $firstDay   = $firstHalfDays->first();
                    $padStart   = $firstDay ? ($firstDay->dayOfWeekIso - 1) : 0;
                    $firstCells = [];
                    for ($p = 0; $p < $padStart; $p++) $firstCells[] = null;
                    foreach ($firstHalfDays as $d) $firstCells[] = $d;
                    $rem = count($firstCells) % 5;
                    if ($rem > 0) for ($p = 0; $p < (5 - $rem); $p++) $firstCells[] = null;
                @endphp

                @foreach(array_chunk($firstCells, 5) as $week)
                <div class="grid grid-cols-5 gap-2 mb-2">
                    @foreach($week as $day)
                        @if($day === null)
                            <div class="min-h-[88px] rounded-xl border-2 border-dashed border-gray-100 bg-transparent"></div>
                        @else
                            @php
                                $key      = $day->format('Y-m-d');
                                $att      = $attendances[$key] ?? null;
                                $dayOtut  = $otutRecords[$key] ?? collect();
                                $isToday  = $day->isSameDay($today);
                                $isFuture = $day->isAfter($today);
                                $hasOt    = $dayOtut->where('type','overtime')->count() > 0;
                                $hasUt    = $dayOtut->where('type','undertime')->count() > 0;

                                $cellBg = 'bg-white';
                                $cellBorder = 'border-gray-200';
                                $statusLabel = '';
                                $statusClasses = '';

                                if ($att && !$isFuture) {
                                    switch ($att->status) {
                                        case 'present':
                                            $cellBg = 'bg-emerald-50';
                                            $cellBorder = 'border-emerald-200';
                                            $statusLabel = 'Present';
                                            $statusClasses = 'bg-emerald-100 text-emerald-700';
                                            break;
                                        case 'late':
                                            $cellBg = 'bg-amber-50';
                                            $cellBorder = 'border-amber-200';
                                            $statusLabel = 'Late';
                                            $statusClasses = 'bg-amber-100 text-amber-700';
                                            break;
                                        case 'absent':
                                            $cellBg = 'bg-rose-50';
                                            $cellBorder = 'border-rose-200';
                                            $statusLabel = 'Absent';
                                            $statusClasses = 'bg-rose-100 text-rose-700';
                                            break;
                                        default:
                                            $statusLabel = ucfirst($att->status);
                                            $statusClasses = 'bg-gray-100 text-gray-600';
                                    }
                                }
                            @endphp
                            <div class="relative min-h-[88px] rounded-xl border-2 {{ $cellBorder }} {{ $cellBg }} p-2 flex flex-col gap-1 transition-all duration-100 hover:-translate-y-0.5 hover:shadow-md {{ $isToday ? 'ring-2 ring-gray-900 ring-offset-1' : '' }} {{ $isFuture ? 'opacity-50' : '' }}">
                                <div class="flex items-start justify-between">
                                    <span class="text-xs font-extrabold {{ $isToday ? 'text-gray-900' : 'text-gray-500' }} leading-none">{{ $day->day }}</span>
                                    @if($hasOt && $hasUt)
                                        <span class="text-[0.52rem] font-bold bg-gradient-to-r from-blue-100 to-yellow-100 text-gray-700 px-1.5 py-0.5 rounded leading-none">OT·UT</span>
                                    @elseif($hasOt)
                                        <span class="text-[0.52rem] font-bold bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded leading-none">OT</span>
                                    @elseif($hasUt)
                                        <span class="text-[0.52rem] font-bold bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded leading-none">UT</span>
                                    @endif
                                </div>
                                @if($att && !$isFuture)
                                    <span class="text-[0.58rem] font-bold {{ $statusClasses }} px-1.5 py-0.5 rounded w-fit leading-none">{{ $statusLabel }}</span>
                                    @if($att->time_in || $att->time_out)
                                    <div class="font-mono text-[0.55rem] text-gray-500 leading-relaxed mt-auto">
                                        @if($att->time_in)
                                            <div>▶ {{ \Carbon\Carbon::createFromFormat('H:i:s', $att->time_in)->format('g:i A') }}</div>
                                        @endif
                                        @if($att->time_out)
                                            <div>◀ {{ \Carbon\Carbon::createFromFormat('H:i:s', $att->time_out)->format('g:i A') }}</div>
                                        @endif
                                    </div>
                                    @endif
                                    @if($dayOtut->count())
                                    <div class="flex gap-1 flex-wrap">
                                        @foreach($dayOtut as $rec)
                                            <span class="text-[0.52rem] font-bold px-1.5 py-0.5 rounded leading-none {{ $rec->type === 'overtime' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                {{ $rec->type === 'overtime' ? 'OT' : 'UT' }} {{ number_format($rec->hours, 1) }}h
                                            </span>
                                        @endforeach
                                    </div>
                                    @endif
                                @elseif(!$isFuture)
                                    <span class="text-[0.58rem] text-gray-300 mt-auto">No record</span>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
                @endforeach
            </div>

            {{-- Second half: 16–end --}}
            <div class="p-5">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-400">2nd Cutoff</span>
                    <span class="text-xs font-semibold text-gray-500">{{ $monthStart->copy()->setDay(16)->format('M 16') }} – {{ $monthEnd->format('M j') }}</span>
                    <div class="flex-1 h-px bg-gray-100"></div>
                </div>

                <div class="grid grid-cols-5 gap-2 mb-2">
                    @foreach(['Mon','Tue','Wed','Thu','Fri'] as $dow)
                        <div class="text-center text-xs font-bold uppercase tracking-widest text-gray-400 py-1">{{ $dow }}</div>
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
                <div class="grid grid-cols-5 gap-2 mb-2">
                    @foreach($week as $day)
                        @if($day === null)
                            <div class="min-h-[88px] rounded-xl border-2 border-dashed border-gray-100 bg-transparent"></div>
                        @else
                            @php
                                $key      = $day->format('Y-m-d');
                                $att      = $attendances[$key] ?? null;
                                $dayOtut  = $otutRecords[$key] ?? collect();
                                $isToday  = $day->isSameDay($today);
                                $isFuture = $day->isAfter($today);
                                $hasOt    = $dayOtut->where('type','overtime')->count() > 0;
                                $hasUt    = $dayOtut->where('type','undertime')->count() > 0;

                                $cellBg = 'bg-white';
                                $cellBorder = 'border-gray-200';
                                $statusLabel = '';
                                $statusClasses = '';

                                if ($att && !$isFuture) {
                                    switch ($att->status) {
                                        case 'present':
                                            $cellBg = 'bg-emerald-50';
                                            $cellBorder = 'border-emerald-200';
                                            $statusLabel = 'Present';
                                            $statusClasses = 'bg-emerald-100 text-emerald-700';
                                            break;
                                        case 'late':
                                            $cellBg = 'bg-amber-50';
                                            $cellBorder = 'border-amber-200';
                                            $statusLabel = 'Late';
                                            $statusClasses = 'bg-amber-100 text-amber-700';
                                            break;
                                        case 'absent':
                                            $cellBg = 'bg-rose-50';
                                            $cellBorder = 'border-rose-200';
                                            $statusLabel = 'Absent';
                                            $statusClasses = 'bg-rose-100 text-rose-700';
                                            break;
                                        default:
                                            $statusLabel = ucfirst($att->status);
                                            $statusClasses = 'bg-gray-100 text-gray-600';
                                    }
                                }
                            @endphp
                            <div class="relative min-h-[88px] rounded-xl border-2 {{ $cellBorder }} {{ $cellBg }} p-2 flex flex-col gap-1 transition-all duration-100 hover:-translate-y-0.5 hover:shadow-md {{ $isToday ? 'ring-2 ring-gray-900 ring-offset-1' : '' }} {{ $isFuture ? 'opacity-50' : '' }}">
                                <div class="flex items-start justify-between">
                                    <span class="text-xs font-extrabold {{ $isToday ? 'text-gray-900' : 'text-gray-500' }} leading-none">{{ $day->day }}</span>
                                    @if($hasOt && $hasUt)
                                        <span class="text-[0.52rem] font-bold bg-gradient-to-r from-blue-100 to-yellow-100 text-gray-700 px-1.5 py-0.5 rounded leading-none">OT·UT</span>
                                    @elseif($hasOt)
                                        <span class="text-[0.52rem] font-bold bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded leading-none">OT</span>
                                    @elseif($hasUt)
                                        <span class="text-[0.52rem] font-bold bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded leading-none">UT</span>
                                    @endif
                                </div>
                                @if($att && !$isFuture)
                                    <span class="text-[0.58rem] font-bold {{ $statusClasses }} px-1.5 py-0.5 rounded w-fit leading-none">{{ $statusLabel }}</span>
                                    @if($att->time_in || $att->time_out)
                                    <div class="font-mono text-[0.55rem] text-gray-500 leading-relaxed mt-auto">
                                        @if($att->time_in)
                                            <div>▶ {{ \Carbon\Carbon::createFromFormat('H:i:s', $att->time_in)->format('g:i A') }}</div>
                                        @endif
                                        @if($att->time_out)
                                            <div>◀ {{ \Carbon\Carbon::createFromFormat('H:i:s', $att->time_out)->format('g:i A') }}</div>
                                        @endif
                                    </div>
                                    @endif
                                    @if($dayOtut->count())
                                    <div class="flex gap-1 flex-wrap">
                                        @foreach($dayOtut as $rec)
                                            <span class="text-[0.52rem] font-bold px-1.5 py-0.5 rounded leading-none {{ $rec->type === 'overtime' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                {{ $rec->type === 'overtime' ? 'OT' : 'UT' }} {{ number_format($rec->hours, 1) }}h
                                            </span>
                                        @endforeach
                                    </div>
                                    @endif
                                @elseif(!$isFuture)
                                    <span class="text-[0.58rem] text-gray-300 mt-auto">No record</span>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
