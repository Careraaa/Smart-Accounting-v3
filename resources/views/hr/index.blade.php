@extends('layouts.layout')

@push('styles')
<style>
@keyframes hrdFadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes hrdScaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes hrdSlideInRight { 0%{opacity:0;transform:translateX(16px)} 100%{opacity:1;transform:translateX(0)} }
@keyframes hrdDrawLine { to { stroke-dashoffset: 0; } }
@keyframes hrdFadeInDot { to { opacity: 1; } }
@keyframes hrdFillArea { to { opacity: 1; } }
@keyframes hrdBounceIn { 0%{opacity:0;transform:scale(0.6)} 60%{transform:scale(1.05)} 80%{transform:scale(0.95)} 100%{opacity:1;transform:scale(1)} }
@keyframes hrdPulseGlow { 0%,100%{box-shadow:0 0 0 0 rgba(99,102,241,0.4)} 50%{box-shadow:0 0 0 8px rgba(99,102,241,0)} }
@keyframes hrdFloatSlow { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-4px)} }
@keyframes hrdCountUp { 0%{opacity:0;transform:translateY(8px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes hrdSlideUpBounce { 0%{opacity:0;transform:translateY(24px)} 60%{transform:translateY(-4px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes hrdGrowBar { 0%{transform:scaleY(0);opacity:0} 100%{transform:scaleY(1);opacity:1} }

.hrd-stat-card { animation:hrdScaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.hrd-fade-up { animation:hrdFadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.hrd-slide-right { animation:hrdSlideInRight 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.hrd-chart-line { stroke-dasharray: 1200; stroke-dashoffset: 1200; animation: hrdDrawLine 1.2s cubic-bezier(0.16,1,0.3,1) 0.2s forwards; }
.hrd-chart-line-delay { stroke-dasharray: 800; stroke-dashoffset: 800; animation: hrdDrawLine 1s cubic-bezier(0.16,1,0.3,1) 0.6s forwards; }
.hrd-chart-dot { opacity: 0; animation: hrdFadeInDot 0.3s ease both; }
.hrd-chart-fill { opacity: 0; animation: hrdFillArea 0.6s ease 1.1s forwards; }
.hrd-bounce { animation:hrdBounceIn 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.hrd-float { animation:hrdFloatSlow 3s ease-in-out infinite; }
.hrd-pulse { animation:hrdPulseGlow 2s ease-in-out infinite; }
.hrd-count-num { animation:hrdCountUp 0.6s cubic-bezier(0.16,1,0.3,1) both; }
.hrd-slide-bounce { animation:hrdSlideUpBounce 0.6s cubic-bezier(0.16,1,0.3,1) both; }
.hrd-bar { transform-origin:bottom; animation:hrdGrowBar 0.7s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
@php
$maxV = collect($attendanceTrend)->map(fn($d) => $d['present'] + $d['late'] + $d['absent'])->max() ?: 1;
$td = $attendanceTrend[array_key_last($attendanceTrend)];
@endphp

<div class="flex flex-col lg:flex-row gap-5 items-start">

    {{-- LEFT COLUMN --}}
    <div class="flex-1 min-w-0 w-full space-y-5">

        {{-- Quick Actions --}}
        <div class="hrd-slide-bounce kpi-card overflow-hidden" style="animation-delay:0.2s">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                    <span class="text-[13px] font-semibold text-gray-900">Quick Actions</span>
                    <span class="text-[11px] text-gray-400">HR modules</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4">
                    <a href="{{ route('payroll.salary-computation.index') }}" class="qa-tile group transition-all hover:bg-indigo-50 hover:border-indigo-200 no-underline">
                        <div class="qa-icon bg-indigo-100 text-indigo-600 transition-colors group-hover:bg-indigo-500 group-hover:text-white">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M8 21h8M12 17v4"/></svg>
                        </div>
                        <span class="qa-label group-hover:text-indigo-700 transition-colors">Payroll</span>
                        @if($batchSubmitted > 0)
                            <span class="qa-badge bg-indigo-100 text-indigo-700 group-hover:bg-\2-500 group-hover:text-white transition-colors">{{ $batchSubmitted }}</span>
                        @endif
                    </a>
                    <a href="{{ route('leave.index') }}" class="qa-tile group transition-all hover:bg-amber-50 hover:border-amber-200 no-underline">
                        <div class="qa-icon bg-amber-100 text-amber-600 transition-colors group-hover:bg-amber-500 group-hover:text-white">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="qa-label group-hover:text-amber-700 transition-colors">Leave Mgmt</span>
                        @if($pendingLeaves > 0)
                            <span class="qa-badge bg-amber-100 text-amber-700 group-hover:bg-\2-500 group-hover:text-white transition-colors">{{ $pendingLeaves }}</span>
                        @endif
                    </a>
                    <a href="{{ route('overtime.index') }}" class="qa-tile group transition-all hover:bg-violet-50 hover:border-violet-200 no-underline">
                        <div class="qa-icon bg-violet-100 text-violet-600 transition-colors group-hover:bg-violet-500 group-hover:text-white">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>
                        </div>
                        <span class="qa-label group-hover:text-violet-700 transition-colors">OT / UT</span>
                        @if($pendingOT + $pendingUT > 0)
                            <span class="qa-badge bg-violet-100 text-violet-700 group-hover:bg-\2-500 group-hover:text-white transition-colors">{{ $pendingOT + $pendingUT }}</span>
                        @endif
                    </a>
                    <a href="{{ route('payroll.receivables.index') }}" class="qa-tile group transition-all hover:bg-emerald-50 hover:border-emerald-200 no-underline">
                        <div class="qa-icon bg-emerald-100 text-emerald-600 transition-colors group-hover:bg-emerald-500 group-hover:text-white">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                        </div>
                        <span class="qa-label group-hover:text-emerald-700 transition-colors">Receivables</span>
                        @if($pendingCashAdvances > 0)
                            <span class="qa-badge bg-emerald-100 text-emerald-700 group-hover:bg-\2-500 group-hover:text-white transition-colors">{{ $pendingCashAdvances }}</span>
                        @endif
                    </a>
                </div>
            </div>

            <div class="flex flex-col gap-4">
                {{-- Attendance Trend --}}
                @php
                $atCnt = count($attendanceTrend);
                $atH = 132;
                $atPresentH = [];
                $atLateH = [];
                foreach ($attendanceTrend as $i => $d) {
                    $atPresentH[$i] = $maxV > 0 ? round(($d['present'] / $maxV) * $atH) : 0;
                    $atLateH[$i] = $maxV > 0 ? round(($d['late'] / $maxV) * $atH) : 0;
                }
                @endphp
                <div class="hrd-fade-up kpi-card overflow-hidden">
                    <div class="px-4 pt-4 pb-3 flex items-center justify-between">
                        <div class="flex items-baseline gap-2">
                            <span class="text-[13px] font-semibold text-gray-900">Attendance Trend</span>
                            <span class="text-[11px] text-gray-400">last 7 days</span>
                        </div>
                        <div class="flex items-center gap-3 text-[11px] text-gray-400">
                            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-[3px] bg-indigo-500 inline-block"></span> Present</span>
                            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-[3px] bg-amber-400 inline-block"></span> Late</span>
                        </div>
                    </div>
                    <div class="px-4 pb-4">
                        @if($atCnt > 0)
                        <div class="at-plot">
                            <div class="at-grid" style="bottom:0"></div>
                            <div class="at-grid" style="bottom:50%"></div>
                            <div class="at-grid at-grid-top" style="bottom:100%"></div>
                            <span class="at-max">{{ $maxV }}</span>
                            <div class="at-cols">
                                @foreach ($attendanceTrend as $i => $d)
                                <div class="at-col" title="{{ \Carbon\Carbon::parse($d['date_iso'])->format('D, M j') }} — {{ $d['present'] }} present, {{ $d['late'] }} late">
                                    <div class="at-bar" style="height:{{ $atPresentH[$i] + $atLateH[$i] }}px;animation-delay:{{ 0.05 + $i * 0.04 }}s">
                                        <div class="at-seg at-late" style="height:{{ $atLateH[$i] }}px"></div>
                                        <div class="at-seg at-present" style="height:{{ $atPresentH[$i] }}px"></div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="at-days">
                            @foreach ($attendanceTrend as $d)
                            <span>{{ \Carbon\Carbon::parse($d['date_iso'])->format('D') }}</span>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100 text-[11px] text-gray-400">
                            <span>Daily headcount, 7-day window</span>
                            <span>
                                Today:
                                <strong class="text-gray-700 font-medium tabular-nums">{{ $td['present'] }}</strong> present
                                <span class="mx-1 text-gray-300">·</span>
                                <strong class="text-emerald-600 font-medium tabular-nums">{{ number_format($tdRate, 1) }}%</strong>
                            </span>
                        </div>
                        @else
                        <div class="flex items-center justify-center h-[160px] text-xs text-gray-400">No attendance data yet.</div>
                        @endif
                    </div>
                </div>

            </div>

        {{-- STAT CARDS: Employees, Attendance, Holidays --}}
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3">
            <a href="{{ route('employees.index') }}" class="hrd-bounce hrd-stat-card kpi-card p-4 no-underline group transition-all hover:border-emerald-300 hover:shadow-md" style="animation-delay:0.05s">
                <div class="flex items-start justify-between gap-3">
                    <p class="kcard-label">Employees</p>
                    <div class="kcard-icon bg-emerald-50 text-emerald-600 transition-colors group-hover:bg-emerald-500 group-hover:text-white">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>
                <p class="kcard-value mt-2 hrd-count-num" style="animation-delay:0.15s">{{ $totalEmployees }}</p>
                <div class="kcard-foot mt-2.5 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0"></span>
                    <span>{{ $newHiresThisMonth }} new this month</span>
                </div>
            </a>

            <a href="{{ route('attendance.index') }}" class="hrd-bounce hrd-stat-card kpi-card p-4 no-underline group transition-all hover:border-cyan-300 hover:shadow-md" style="animation-delay:0.15s">
                <div class="flex items-start justify-between gap-3">
                    <p class="kcard-label">Attendance</p>
                    <div class="kcard-icon bg-cyan-50 text-cyan-600 transition-colors group-hover:bg-cyan-500 group-hover:text-white">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                    </div>
                </div>
                <p class="kcard-value mt-2 hrd-count-num" style="animation-delay:0.25s">{{ $td['present'] }}</p>
                <div class="kcard-foot mt-2.5 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0"></span>
                    <span>{{ $tdRate }}% present today</span>
                </div>
            </a>

            <a href="{{ route('holiday.index') }}" class="hrd-bounce hrd-stat-card kpi-card p-4 no-underline group transition-all hover:border-rose-300 hover:shadow-md" style="animation-delay:0.25s">
                <div class="flex items-start justify-between gap-3">
                    <p class="kcard-label">Holidays</p>
                    <div class="kcard-icon bg-rose-50 text-rose-600 transition-colors group-hover:bg-rose-500 group-hover:text-white">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 11l7-7 7 7M5 19l7-7 7 7"/></svg>
                    </div>
                </div>
                <p class="kcard-value mt-2 hrd-count-num" style="animation-delay:0.35s">{{ $upcomingHolidays }}</p>
                <div class="kcard-foot mt-2.5 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400 shrink-0"></span>
                    <span>Upcoming in 30 days</span>
                </div>
            </a>

        </div>

    </div>

    {{-- RIGHT COLUMN: Calendar + To-Do --}}
    <div class="w-full lg:w-[280px] lg:shrink-0 space-y-5">
        <div class="lg:sticky lg:top-24 space-y-5">

        {{-- Calendar Card --}}
        @include('partials.dashboard-calendar')

        {{-- To-Do Card --}}
        <div class="hrd-slide-right kpi-card overflow-hidden" style="animation-delay:0.1s">
            <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span class="text-xs font-semibold text-gray-900">To Do</span>
                </div>
                @if($totalPending > 0)
                    <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] rounded-full bg-amber-100 text-amber-700 text-[0.5rem] font-bold px-1">{{ $totalPending }}</span>
                @endif
            </div>
            @if($totalPending > 0)
            <div class="divide-y divide-gray-50">
                @foreach($pendingItems as $item)
                <a href="{{ $item['url'] }}" class="flex items-center gap-3 px-5 py-3 transition-colors hover:bg-gray-50 no-underline">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0" style="background:{{ $item['color'] }};color:{{ $item['iconColor'] }}">
                        <i class="{{ $item['icon'] }}" style="font-size:14px"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-900">{{ $item['text'] }}</p>
                    </div>
                    @if($item['count'] > 0)
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 rounded-full text-[0.5rem] font-bold px-1.5" style="background:{{ $item['color'] }};color:{{ $item['iconColor'] }}">{{ $item['count'] }}</span>
                    @endif
                    <svg class="w-3.5 h-3.5 text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endforeach
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm font-semibold text-gray-400">No to do</p>
                <p class="text-xs text-gray-400 mt-0.5">All caught up!</p>
            </div>
            @endif
        </div>
        </div>
    </div>

</div>
@endsection
