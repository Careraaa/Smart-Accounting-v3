@extends('layouts.layout')

@push('styles')
<style>
@keyframes sadFadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes sadScaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes sadSlideInRight { 0%{opacity:0;transform:translateX(16px)} 100%{opacity:1;transform:translateX(0)} }
@keyframes sadDrawLine { to { stroke-dashoffset: 0; } }
@keyframes sadFadeInDot { to { opacity: 1; } }
@keyframes sadFillArea { to { opacity: 1; } }
@keyframes sadBounceIn { 0%{opacity:0;transform:scale(0.6)} 60%{transform:scale(1.05)} 80%{transform:scale(0.95)} 100%{opacity:1;transform:scale(1)} }
@keyframes sadFloatSlow { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-4px)} }
@keyframes sadCountUp { 0%{opacity:0;transform:translateY(8px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes sadSlideUpBounce { 0%{opacity:0;transform:translateY(24px)} 60%{transform:translateY(-4px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes sadGrowBar { 0%{transform:scaleY(0);opacity:0} 100%{transform:scaleY(1);opacity:1} }
.sad-stat-card { animation:sadScaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.sad-fade-up { animation:sadFadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.sad-slide-right { animation:sadSlideInRight 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.sad-chart-line { stroke-dasharray:1200;stroke-dashoffset:1200;animation:sadDrawLine 1.2s cubic-bezier(0.16,1,0.3,1) 0.2s forwards; }
.sad-chart-line-delay { stroke-dasharray:800;stroke-dashoffset:800;animation:sadDrawLine 1s cubic-bezier(0.16,1,0.3,1) 0.6s forwards; }
.sad-chart-dot { opacity:0;animation:sadFadeInDot 0.3s ease both; }
.sad-chart-fill { opacity:0;animation:sadFillArea 0.6s ease 1.1s forwards; }
.sad-bounce { animation:sadBounceIn 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.sad-float { animation:sadFloatSlow 3s ease-in-out infinite; }
.sad-count-num { animation:sadCountUp 0.6s cubic-bezier(0.16,1,0.3,1) both; }
.sad-slide-bounce { animation:sadSlideUpBounce 0.6s cubic-bezier(0.16,1,0.3,1) both; }
.sad-bar { transform-origin:bottom;animation:sadGrowBar 0.7s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
@php
$maxV = collect($attendanceTrend)->map(fn($d) => $d['present'] + $d['late'] + $d['absent'])->max() ?: 1;
$td = $attendanceTrend[array_key_last($attendanceTrend)];
$financialRevenue = (float) ($financialOverview['netRevenue'] ?? 0);
$financialNetIncome = (float) ($financialOverview['netIncome'] ?? 0);
$financialExpense = (float) ($financialOverview['netExpenses'] ?? 0);
$financialDateFrom = $financialOverview['dateFrom'] ?? now()->startOfMonth()->toDateString();
$financialDateTo = $financialOverview['dateTo'] ?? now()->toDateString();
$financialTrend = collect($financialTrend ?? []);
$financialChartMax = max(1, (float) $financialTrend->max('revenue'), (float) $financialTrend->max(fn($point) => max($point['netIncome'], 0)));
@endphp

<div class="flex flex-col lg:flex-row gap-5 items-start">

    {{-- LEFT COLUMN (2/3) --}}
    <div class="flex-1 min-w-0 w-full space-y-5">

        {{-- Quick Actions / Shortcuts --}}
        <div class="sad-slide-bounce kpi-card overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <span class="text-[13px] font-semibold text-gray-900">Quick Actions</span>
                <span class="text-[11px] text-gray-400">System modules</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4">
                <a href="{{ route('superadmin.accounts.index') }}" class="qa-tile group transition-all hover:bg-indigo-50 hover:border-indigo-200 no-underline">
                    <div class="qa-icon bg-indigo-100 text-indigo-600 transition-colors group-hover:bg-indigo-500 group-hover:text-white">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <span class="qa-label group-hover:text-indigo-700 transition-colors">Accounts</span>
                    @if($totalEmployees - $activeEmployees > 0)
                        <span class="qa-badge bg-indigo-100 text-indigo-700 group-hover:bg-indigo-500 group-hover:text-white transition-colors">{{ $totalEmployees - $activeEmployees }}</span>
                    @endif
                </a>
                <a href="{{ route('configuration.index') }}" class="qa-tile group transition-all hover:bg-gray-200 hover:border-gray-300 no-underline">
                    <div class="qa-icon bg-gray-200 text-gray-600 transition-colors group-hover:bg-gray-600 group-hover:text-white">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="qa-label group-hover:text-gray-900 transition-colors">Configuration</span>
                </a>
                <a href="{{ route('configuration.backup-history') }}" class="qa-tile group transition-all hover:bg-emerald-50 hover:border-emerald-200 no-underline">
                    <div class="qa-icon bg-emerald-100 text-emerald-600 transition-colors group-hover:bg-emerald-500 group-hover:text-white">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    </div>
                    <span class="qa-label group-hover:text-emerald-700 transition-colors">Backups</span>
                </a>
                <a href="{{ route('superadmin.accounts.create') }}" class="qa-tile group transition-all hover:bg-amber-50 hover:border-amber-200 no-underline">
                    <div class="qa-icon bg-amber-100 text-amber-600 transition-colors group-hover:bg-amber-500 group-hover:text-white">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <span class="qa-label group-hover:text-amber-700 transition-colors">New Account</span>
                </a>
            </div>
        </div>

        {{-- Four dashboard cards in one balanced responsive grid. --}}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 items-stretch">
            @include('partials.financial-overview-card')

            {{-- Attendance Trend (SVG line chart) --}}
            @php
            $cnt = count($attendanceTrend);
            $atH = 132;
            $atPresentH = [];
            $atLateH = [];
            foreach ($attendanceTrend as $i => $d) {
                $atPresentH[$i] = $maxV > 0 ? round(($d['present'] / $maxV) * $atH) : 0;
                $atLateH[$i] = $maxV > 0 ? round(($d['late'] / $maxV) * $atH) : 0;
            }
            @endphp
            <div class="sad-fade-up kpi-card overflow-hidden">
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
                    @if($cnt > 0)
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
                            <strong class="text-emerald-600 font-medium tabular-nums">{{ number_format($attendanceRate, 1) }}%</strong>
                        </span>
                    </div>
                    @else
                    <div class="flex items-center justify-center h-[160px] text-xs text-gray-400">No attendance data yet.</div>
                    @endif
                </div>
            </div>




@include('partials.daily-remittance-card')


    </div>
    </div>

    {{-- RIGHT COLUMN: Calendar + To-Do --}}
    <div class="w-full lg:w-[280px] lg:shrink-0 space-y-5">
        <div class="lg:sticky lg:top-24 space-y-5">

        {{-- Calendar Card --}}
        @include('partials.dashboard-calendar')

        {{-- To-Do Card --}}
        <div class="sad-slide-right kpi-card overflow-hidden" style="animation-delay:0.1s">
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
