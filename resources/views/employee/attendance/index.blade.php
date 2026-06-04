@extends('layouts.layout')

@push('styles')
<style>
@keyframes attFadeUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes attScaleIn { 0%{opacity:0;transform:scale(0.93)} 100%{opacity:1;transform:scale(1)} }
.att-page { animation:attFadeUp 0.3s ease-out; }
.att-stat { animation:attScaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.att-stat:nth-child(1) { animation-delay:0.05s; }
.att-stat:nth-child(2) { animation-delay:0.1s; }
.att-stat:nth-child(3) { animation-delay:0.15s; }
.att-stat:nth-child(4) { animation-delay:0.2s; }
.att-stat:nth-child(5) { animation-delay:0.25s; }
.att-stat:nth-child(6) { animation-delay:0.3s; }
.att-nav { animation:attFadeUp 0.35s ease-out 0.05s both; }
.att-section { animation:attFadeUp 0.4s ease-out 0.1s both; }
.att-tooltip-dynamic {
    position: fixed;
    z-index: 9999;
    pointer-events: none;
}
.att-tooltip-dynamic .att-tip-inner {
    opacity: 0;
    transform: scale(0.95);
    transition: opacity 0.2s cubic-bezier(0.16,1,0.3,1), transform 0.2s cubic-bezier(0.16,1,0.3,1);
    transform-origin: center bottom;
}
.att-tooltip-dynamic.att-tooltip--show .att-tip-inner {
    opacity: 1;
    transform: scale(1);
}
.att-tooltip-dynamic.att-tooltip--below .att-tip-inner {
    transform-origin: center top;
}
.att-tooltip-dynamic.att-tooltip--below .tip-arrow-down { display: none; }
.att-tooltip-dynamic.att-tooltip--below .tip-arrow-up { display: block; }
@media (max-width: 639px) {
    .att-section .grid-cols-7 > div { min-height:60px !important; }
}
</style>
@endpush

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
        $firstHalfDays->push($date);
    }
    for ($d = 16; $d <= $monthEnd->day; $d++) {
        $date = $monthStart->copy()->setDay($d);
        $secondHalfDays->push($date);
    }

    $presentCount2 = $attendances->where('status', 'present')->count();
    $lateCount2    = $attendances->where('status', 'late')->count();
    $absentCount2  = $attendances->where('status', 'absent')->count();
@endphp

<div class="att-page min-h-screen bg-gray-50/60">
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
        <div class="border-b border-gray-200 mb-6">
            <nav class="flex gap-1 -mb-px" role="tablist">
                <a href="{{ route('employee.attendance.index') }}" role="tab"
                   class="relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group inline-flex items-center gap-2
                   {{ request()->routeIs('employee.attendance.index')
                       ? 'border-violet-600 text-violet-700 bg-violet-50/60'
                       : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                    <svg class="w-4 h-4 transition-colors {{ request()->routeIs('employee.attendance.index') ? 'text-violet-600' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                    Attendance Calendar
                </a>
                <a href="{{ route('employee.overtime-undertime.index') }}" role="tab"
                   class="relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 group inline-flex items-center gap-2
                   {{ request()->routeIs('employee.overtime-undertime.index')
                       ? 'border-amber-500 text-amber-700 bg-amber-50/60'
                       : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                    <svg class="w-4 h-4 transition-colors {{ request()->routeIs('employee.overtime-undertime.index') ? 'text-amber-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                    OT / UT Requests
                </a>
            </nav>
        </div>

        {{-- Month Navigation --}}
        <div class="att-nav flex items-center justify-between bg-white border border-gray-200 rounded-2xl px-5 py-4 mb-5 shadow-sm">
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
            <div class="att-stat bg-white border border-gray-200 rounded-xl p-3.5 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <div class="font-mono text-lg font-bold text-gray-900 leading-none">{{ $presentCount2 }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">Present</div>
                </div>
            </div>
            <div class="att-stat bg-white border border-gray-200 rounded-xl p-3.5 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                </div>
                <div>
                    <div class="font-mono text-lg font-bold text-gray-900 leading-none">{{ $lateCount2 }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">Late</div>
                </div>
            </div>
            <div class="att-stat bg-white border border-gray-200 rounded-xl p-3.5 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="font-mono text-lg font-bold text-gray-900 leading-none">{{ $absentCount2 }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">Absent</div>
                </div>
            </div>
            <div class="att-stat bg-white border border-gray-200 rounded-xl p-3.5 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="font-mono text-lg font-bold text-gray-900 leading-none">{{ number_format($otHours, 1) }}h</div>
                    <div class="text-xs text-gray-400 mt-0.5">OT Hours</div>
                </div>
            </div>
            <div class="att-stat bg-white border border-gray-200 rounded-xl p-3.5 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-yellow-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="font-mono text-lg font-bold text-gray-900 leading-none">{{ number_format($utHours, 1) }}h</div>
                    <div class="text-xs text-gray-400 mt-0.5">UT Hours</div>
                </div>
            </div>
            <div class="att-stat bg-white border border-gray-200 rounded-xl p-3.5 flex items-center gap-3 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4.5 h-4.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div>
                    <div class="font-mono text-lg font-bold text-gray-900 leading-none">{{ $firstHalfDays->count() + $secondHalfDays->count() }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">Total Days</div>
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap items-center gap-4 mb-5 px-1">
            @foreach([
                ['label' => 'Present',       'bg' => 'bg-emerald-100', 'border' => 'border-emerald-300'],
                ['label' => 'Late',          'bg' => 'bg-yellow-100',  'border' => 'border-yellow-300'],
                ['label' => 'Absent',        'bg' => 'bg-red-100',     'border' => 'border-red-300'],
                ['label' => 'OT Approved',   'bg' => 'bg-blue-100',    'border' => 'border-blue-300'],
                ['label' => 'UT Approved',   'bg' => 'bg-violet-100',  'border' => 'border-violet-300'],
                ['label' => 'No Record',     'bg' => 'bg-white',       'border' => 'border-gray-200'],
                ['label' => 'Weekend',       'bg' => 'bg-gray-50',     'border' => 'border-gray-200'],
            ] as $item)
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-sm {{ $item['bg'] }} border {{ $item['border'] }}"></div>
                <span class="text-xs font-semibold text-gray-500">{{ $item['label'] }}</span>
            </div>
            @endforeach
        </div>

        {{-- Calendar Grid --}}
        <div class="att-section bg-white border border-gray-200 rounded-2xl shadow-sm mb-6">

            @php
                function renderHalfEmp($days, $label) {
                    $firstDay = $days->first();
                    $dayOfWeek = $firstDay ? $firstDay->dayOfWeekIso : 1;
                    $padStart = $dayOfWeek === 7 ? 0 : $dayOfWeek;
                    $cells = [];
                    if ($firstDay) {
                        for ($p = 0; $p < $padStart; $p++) $cells[] = null;
                    }
                    foreach ($days as $d) $cells[] = $d;
                    $rem = count($cells) % 7;
                    if ($rem > 0) for ($p = 0; $p < (7 - $rem); $p++) $cells[] = null;
                    return compact('cells', 'label');
                }

                $firstHalf7  = renderHalfEmp($firstHalfDays, '1st Cutoff · ' . $monthStart->format('M 1') . ' – ' . $monthStart->copy()->setDay(15)->format('M 15'));
                $secondHalf7 = renderHalfEmp($secondHalfDays, '2nd Cutoff · ' . $monthStart->copy()->setDay(16)->format('M 16') . ' – ' . $monthEnd->format('M j'));
            @endphp

            @foreach([$firstHalf7, $secondHalf7] as $i => $half)
            <div class="{{ $i === 0 ? 'border-b-2 border-dashed border-gray-100' : '' }} p-5">
                <div class="flex items-center gap-2 mb-3.5">
                    <span class="text-[11px] font-bold uppercase tracking-widest text-gray-400 shrink-0 bg-white pr-2">{{ $half['label'] }}</span>
                    <span class="flex-1 h-px bg-gray-100"></span>
                </div>

                <div class="overflow-x-auto -mx-5 px-5">
                    <div class="min-w-[490px]">
                        <div class="grid grid-cols-7 gap-1.5 mb-2">
                            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $dow)
                                <div class="text-center text-[10px] font-bold uppercase tracking-wider text-gray-400 py-1">{{ $dow }}</div>
                            @endforeach
                        </div>

                        @foreach(array_chunk($half['cells'], 7) as $week)
                        <div class="grid grid-cols-7 gap-1.5 mb-1.5">
                            @foreach($week as $day)
                                @if($day === null)
                                    <div class="rounded-xl min-h-[82px] border border-dashed border-gray-100 bg-transparent"></div>
                                @else
                                    @php
                                        $key = $day->format('Y-m-d');
                                        $att = $attendances[$key] ?? null;
                                        $dayOtut = $otutRecords[$key] ?? collect();
                                        $otHours = $dayOtut->where('type', 'overtime')->sum('hours');
                                        $utHours = $dayOtut->where('type', 'undertime')->sum('hours');
                                        $isToday = $day->isSameDay($today);
                                        $isFuture = $day->isAfter($today);
                                        $isWeekend = $day->isSaturday() || $day->isSunday();
                                        $hasOtut = $otHours > 0 || $utHours > 0;
                                        $timeIn = $att && $att->time_in ? \Carbon\Carbon::createFromFormat('H:i:s', $att->time_in)->format('g:ia') : null;
                                        $timeOut = $att && $att->time_out ? \Carbon\Carbon::createFromFormat('H:i:s', $att->time_out)->format('g:ia') : null;
                                        $hrsWorked = $att ? $att->hours_worked : null;

                                        if ($att) {
                                            $bgClass = match($att->status) {
                                                'present' => 'bg-emerald-50/60 border-emerald-200',
                                                'late' => 'bg-yellow-50/60 border-yellow-200',
                                                'absent' => 'bg-red-50/60 border-red-200',
                                                default => 'bg-white border-gray-200',
                                            };
                                            $dotClass = match($att->status) {
                                                'present' => 'bg-emerald-500',
                                                'late' => 'bg-yellow-500',
                                                'absent' => 'bg-red-500',
                                                default => 'bg-gray-300',
                                            };
                                            $statusText = match($att->status) {
                                                'present' => 'Present',
                                                'late' => 'Late',
                                                'absent' => 'Absent',
                                                default => ucfirst($att->status),
                                            };
                                            $statusColor = match($att->status) {
                                                'present' => 'text-emerald-700',
                                                'late' => 'text-yellow-700',
                                                'absent' => 'text-red-700',
                                                default => 'text-gray-500',
                                            };
                                        } elseif ($hasOtut) {
                                            $bgClass = 'bg-emerald-50/60 border-emerald-200';
                                            $dotClass = 'bg-emerald-500';
                                            $statusText = 'Present (OT)';
                                            $statusColor = 'text-emerald-600';
                                        } elseif ($isWeekend || $isFuture) {
                                            $bgClass = 'bg-gray-50 border-gray-200';
                                            $dotClass = '';
                                            $statusText = '';
                                            $statusColor = '';
                                        } else {
                                            $bgClass = 'bg-white border-gray-200';
                                            $dotClass = '';
                                            $statusText = '';
                                            $statusColor = '';
                                        }
                                    @endphp
                                    <div class="rounded-xl min-h-[82px] p-2 border-2 flex flex-col gap-0.5 transition-all duration-150 hover:-translate-y-0.5 hover:shadow-md group relative {{ $bgClass }} {{ $isToday ? '!border-rose-500 shadow-md shadow-rose-100' : '' }}">
                                        {{-- Top row: day number + status dot/label --}}
                                        <div class="flex items-center justify-between gap-0.5">
                                            <span class="text-sm font-extrabold text-gray-700 leading-none {{ $isToday ? '!text-rose-600' : '' }}">{{ $day->day }}</span>
                                            @if(($att || $hasOtut) && !$isFuture)
                                            <span class="inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full {{ $dotClass }}"></span>
                                                <span class="hidden sm:inline text-[10px] font-bold uppercase tracking-wider {{ $statusColor }}">{{ $statusText }}</span>
                                            </span>
                                            @endif
                                        </div>

                                        {{-- Time range (only when attendance record exists) --}}
                                        @if($att && !$isFuture)
                                            <div class="text-[11px] font-mono text-gray-500 leading-tight -mt-0.5">
                                                @if($timeIn && $timeOut)
                                                    <span>{{ $timeIn }} → {{ $timeOut }}</span>
                                                @elseif($timeIn)
                                                    <span>{{ $timeIn }} → —</span>
                                                @elseif($timeOut)
                                                    <span>— → {{ $timeOut }}</span>
                                                @else
                                                    <span class="text-gray-300">No times</span>
                                                @endif
                                            </div>
                                        @elseif(!$att && !$hasOtut && !$isFuture && !$isWeekend)
                                            <div class="text-[10px] text-gray-300 leading-tight -mt-0.5">No record</div>
                                        @endif

                                        {{-- OT/UT pills --}}
                                        @if($hasOtut && !$isFuture)
                                        <div class="hidden sm:flex items-center gap-1 mt-auto pt-0.5">
                                            @if($otHours > 0)
                                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-[3px] text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 leading-none">
                                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                                {{ number_format($otHours, 1) }}h
                                            </span>
                                            @endif
                                            @if($utHours > 0)
                                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-[3px] text-[10px] font-bold bg-violet-50 text-violet-700 border border-violet-200 leading-none">
                                                <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                                                {{ number_format($utHours, 1) }}h
                                            </span>
                                            @endif
                                        </div>
                                        @endif

                                        {{-- Hover tooltip template (cloned to body on hover) --}}
                                        @if($att && !$isFuture)
                                        <template class="att-tip-tpl">
                                            <div class="att-tip-inner bg-white border border-gray-200 rounded-xl shadow-xl p-4 min-w-[250px]">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="w-3 h-3 rounded-full {{ $dotClass }} shrink-0"></span>
                                                    <span class="text-sm font-bold uppercase tracking-wider {{ $statusColor }}">{{ $statusText }}</span>
                                                    <span class="text-sm text-gray-400 ml-auto font-medium">{{ $day->format('D, M j') }}</span>
                                                </div>
                                                <div class="space-y-1.5">
                                                    @if($timeIn || $timeOut)
                                                    <div class="flex items-center gap-1.5 text-[15px] text-gray-600">
                                                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <span class="font-mono">{{ $timeIn ?? '—' }} → {{ $timeOut ?? '—' }}</span>
                                                    </div>
                                                    @endif
                                                    @if($hrsWorked !== null)
                                                    <div class="flex items-center gap-1.5 text-[15px] text-gray-600">
                                                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                        <span>{{ number_format($hrsWorked, 1) }} hrs worked</span>
                                                    </div>
                                                    @endif
                                                    @if($hasOtut)
                                                    <div class="flex items-center gap-1.5 pt-1.5 mt-1.5 border-t border-gray-100">
                                                        @if($otHours > 0)
                                                        <span class="inline-flex items-center gap-0.5 text-sm font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">+{{ number_format($otHours, 1) }}h OT</span>
                                                        @endif
                                                        @if($utHours > 0)
                                                        <span class="inline-flex items-center gap-0.5 text-sm font-bold text-violet-700 bg-violet-50 px-2 py-0.5 rounded border border-violet-200">{{ number_format($utHours, 1) }}h UT</span>
                                                        @endif
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="tip-arrow-down absolute -bottom-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-white border-r border-b border-gray-200 rotate-45"></div>
                                                <div class="tip-arrow-up absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-white border-l border-t border-gray-200 rotate-45 hidden"></div>
                                            </div>
                                        </template>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach

        </div>

    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var activeTip = null;
    document.querySelectorAll('.group').forEach(function(cell) {
        var tpl = cell.querySelector('.att-tip-tpl');
        if (!tpl) return;
        cell.addEventListener('mouseenter', function() {
            if (activeTip) { activeTip.remove(); activeTip = null; }
            var cr = cell.getBoundingClientRect();
            var wrapper = document.createElement('div');
            wrapper.className = 'att-tooltip-dynamic';
            wrapper.innerHTML = tpl.innerHTML;
            document.body.appendChild(wrapper);
            var inner = wrapper.querySelector('.att-tip-inner');
            var th = inner.offsetHeight || 180;
            var above = cr.top - th - 10;
            wrapper.style.left = (cr.left + cr.width / 2) + 'px';
            wrapper.style.transform = 'translateX(-50%)';
            if (above < 6) {
                wrapper.classList.add('att-tooltip--below');
                wrapper.style.top = (cr.bottom + 10) + 'px';
            } else {
                wrapper.style.top = (above) + 'px';
            }
            activeTip = wrapper;
            requestAnimationFrame(function() { wrapper.classList.add('att-tooltip--show'); });
        });
        cell.addEventListener('mouseleave', function() {
            if (!activeTip) return;
            var el = activeTip;
            el.classList.remove('att-tooltip--show');
            setTimeout(function() {
                if (el.parentNode) el.remove();
                if (activeTip === el) activeTip = null;
            }, 250);
        });
    });
});
</script>
@endpush
@endsection
