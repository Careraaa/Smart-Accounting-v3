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

// To-do total
$totalPending = $pendingLeaves + $pendingOT + $pendingUT + $pendingCashAdvances + $pendingSalaryLoans;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

    {{-- LEFT COLUMN (2/3) --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Quick Actions / Shortcuts --}}
        <div class="hrd-slide-bounce bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" style="animation-delay:0.2s">
            <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-sm hrd-float">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-gray-900">Quick Actions</span>
                </div>
                <span class="text-[0.55rem] font-mono text-gray-400">HR modules</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4">
                <a href="{{ route('payroll.salary-computation.index') }}" class="group flex flex-col items-center gap-2 px-3 py-4 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-indigo-50 hover:border-indigo-200 hover:shadow-sm no-underline">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center transition-all group-hover:bg-indigo-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-indigo-200/50">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M8 21h8M12 17v4"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-700 group-hover:text-indigo-700 transition-colors text-center">Payroll</span>
                    @if($batchSubmitted > 0)
                        <span class="inline-flex items-center justify-center min-w-[18px] h-4 px-1 rounded-full bg-indigo-100 text-indigo-700 text-[0.45rem] font-bold">{{ $batchSubmitted }}</span>
                    @endif
                </a>
                <a href="{{ route('leave.index') }}" class="group flex flex-col items-center gap-2 px-3 py-4 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-amber-50 hover:border-amber-200 hover:shadow-sm no-underline">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center transition-all group-hover:bg-amber-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-amber-200/50">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-700 group-hover:text-amber-700 transition-colors text-center">Leave Mgmt</span>
                    @if($pendingLeaves > 0)
                        <span class="inline-flex items-center justify-center min-w-[18px] h-4 px-1 rounded-full bg-amber-100 text-amber-700 text-[0.45rem] font-bold">{{ $pendingLeaves }}</span>
                    @endif
                </a>
                <a href="{{ route('overtime.index') }}" class="group flex flex-col items-center gap-2 px-3 py-4 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-violet-50 hover:border-violet-200 hover:shadow-sm no-underline">
                    <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center transition-all group-hover:bg-violet-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-violet-200/50">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-700 group-hover:text-violet-700 transition-colors text-center">OT / UT</span>
                    @if($pendingOT + $pendingUT > 0)
                        <span class="inline-flex items-center justify-center min-w-[18px] h-4 px-1 rounded-full bg-violet-100 text-violet-700 text-[0.45rem] font-bold">{{ $pendingOT + $pendingUT }}</span>
                    @endif
                </a>
                <a href="{{ route('payroll.receivables.index') }}" class="group flex flex-col items-center gap-2 px-3 py-4 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-emerald-50 hover:border-emerald-200 hover:shadow-sm no-underline">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center transition-all group-hover:bg-emerald-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-emerald-200/50">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-700 group-hover:text-emerald-700 transition-colors text-center">Receivables</span>
                    @if($pendingCashAdvances + $pendingSalaryLoans > 0)
                        <span class="inline-flex items-center justify-center min-w-[18px] h-4 px-1 rounded-full bg-emerald-100 text-emerald-700 text-[0.45rem] font-bold">{{ $pendingCashAdvances + $pendingSalaryLoans }}</span>
                    @endif
                </a>
            </div>
        </div>

        {{-- STAT CARDS ROW 1: Employees, Leaves, Attendance --}}
        <div class="grid grid-cols-3 gap-3">
            {{-- Employees --}}
            <a href="{{ route('employees.index') }}" class="hrd-bounce hrd-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 no-underline group hover:border-emerald-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.05s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Employees</p>
                        <p class="text-xl font-bold text-gray-900 tabular-nums mt-0.5 hrd-count-num" style="animation-delay:0.15s">{{ $totalEmployees }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center transition-all duration-300 group-hover:bg-emerald-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span>{{ $newHiresThisMonth }} new this month</span>
                </div>
            </a>

            {{-- Leaves --}}
            <a href="{{ route('leave.index') }}" class="hrd-bounce hrd-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 no-underline group hover:border-violet-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.1s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Leaves</p>
                        <p class="text-xl font-bold text-gray-900 tabular-nums mt-0.5 hrd-count-num" style="animation-delay:0.2s">{{ $pendingLeaves }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-violet-50 text-violet-500 flex items-center justify-center transition-all duration-300 group-hover:bg-violet-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>{{ $pendingLeaves }} pending</span>
                    <span class="text-gray-300">·</span>
                    <span>{{ $approvedLeaves }} approved</span>
                </div>
            </a>

            {{-- Attendance --}}
            <a href="{{ route('attendance.index') }}" class="hrd-bounce hrd-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 no-underline group hover:border-cyan-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.15s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Attendance</p>
                        <p class="text-xl font-bold text-gray-900 tabular-nums mt-0.5 hrd-count-num" style="animation-delay:0.25s">{{ $td['present'] }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-cyan-50 text-cyan-500 flex items-center justify-center transition-all duration-300 group-hover:bg-cyan-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>{{ $tdRate }}% present today</span>
                </div>
            </a>
        </div>

        {{-- STAT CARDS ROW 2: Payroll, Holidays, Bonuses --}}
        <div class="grid grid-cols-3 gap-3">
            {{-- Payroll --}}
            <a href="{{ route('payroll.salary-computation.index') }}" class="hrd-bounce hrd-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 no-underline group hover:border-indigo-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.2s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Payroll</p>
                        <p class="text-xl font-bold text-gray-900 tabular-nums mt-0.5 hrd-count-num" style="animation-delay:0.3s">{{ $batchSubmitted }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center transition-all duration-300 group-hover:bg-indigo-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M8 21h8M12 17v4"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>{{ $batchSubmitted }} pending</span>
                    <span class="text-gray-300">·</span>
                    <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>{{ $batchPaid }} paid</span>
                </div>
            </a>

            {{-- Holidays --}}
            <a href="{{ route('holiday.index') }}" class="hrd-bounce hrd-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 no-underline group hover:border-rose-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.25s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Holidays</p>
                        <p class="text-xl font-bold text-gray-900 tabular-nums mt-0.5 hrd-count-num" style="animation-delay:0.35s">{{ $upcomingHolidays }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center transition-all duration-300 group-hover:bg-rose-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 11l7-7 7 7M5 19l7-7 7 7"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span>Upcoming in 30 days</span>
                </div>
            </a>

            {{-- Bonuses --}}
            <a href="{{ route('bonuses.index') }}" class="hrd-bounce hrd-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 no-underline group hover:border-yellow-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.3s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Bonuses</p>
                        <p class="text-xl font-bold text-gray-900 tabular-nums mt-0.5 hrd-count-num" style="animation-delay:0.4s">{{ $activeBonuses }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-yellow-50 text-yellow-500 flex items-center justify-center transition-all duration-300 group-hover:bg-yellow-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span>Active bonuses</span>
                </div>
            </a>
        </div>

        {{-- GRAPH CARDS ROW --}}
        <div class="grid grid-cols-2 gap-5">

            {{-- Attendance Trend (clerk-style line chart) --}}
            @php
            $chartH = 160;
            $chartW = 600;
            $padL = 0; $padR = 0; $padT = 8; $padB = 24;
            $plotW = $chartW - $padL - $padR;
            $plotH = $chartH - $padT - $padB;
            $cnt = count($attendanceTrend);
            $step = $cnt > 1 ? $plotW / ($cnt - 1) : 0;
            $points = [];
            foreach ($attendanceTrend as $i => $d) {
                $x = $i * $step;
                $y = $plotH - ($maxV > 0 ? ($d['present'] / $maxV) * $plotH : 0);
                $points[] = round($x + $padL, 1) . ',' . round($y + $padT, 1);
            }
            @endphp
            <div class="hrd-fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-semibold text-gray-900">Attendance Trend</span>
                        <span class="text-[9px] text-gray-400 font-medium">7 days</span>
                    </div>
                    <div class="flex items-center gap-3 text-[10px]">
                        <span class="flex items-center gap-1 text-gray-400"><span class="w-2 h-2 rounded-full bg-indigo-500 inline-block"></span> Present</span>
                        <span class="flex items-center gap-1 text-gray-400"><span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span> Late</span>
                    </div>
                </div>
                <div class="p-4">
                    @if($cnt > 0)
                    <svg viewBox="0 0 {{ $chartW }} {{ $chartH }}" class="w-full h-auto" style="max-height:180px">
                        {{-- Grid lines --}}
                        @for ($g = 0; $g <= 4; $g++)
                        @php $gy = $padT + ($plotH / 4) * $g; @endphp
                        <line x1="{{ $padL }}" y1="{{ $gy }}" x2="{{ $chartW - $padR }}" y2="{{ $gy }}" stroke="#f0f0f0" stroke-width="1"/>
                        @endfor
                        {{-- Present area fill --}}
                        <path d="M{{ $points[0] }} L{{ implode(' L', $points) }} L{{ $padL + ($cnt - 1) * $step }},{{ $padT + $plotH }} L{{ $padL }},{{ $padT + $plotH }} Z"
                              fill="url(#atGradHr)" opacity="0.15"/>
                        {{-- Late dashed line --}}
                        @php
                        $latePoints = [];
                        foreach ($attendanceTrend as $i => $d) {
                            $x = $i * $step;
                            $y = $plotH - ($maxV > 0 ? ($d['late'] / $maxV) * $plotH : 0);
                            $latePoints[] = round($x + $padL, 1) . ',' . round($y + $padT, 1);
                        }
                        @endphp
                        <polyline points="{{ implode(' ', $latePoints) }}" fill="none" stroke="#fbbf24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="4 3" class="hrd-chart-line-delay"/>
                        {{-- Present line --}}
                        <polyline points="{{ implode(' ', $points) }}" fill="none" stroke="#4f46e5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="hrd-chart-line"/>
                        {{-- Dots --}}
                        @foreach ($attendanceTrend as $i => $d)
                        @php $dx = $i * $step + $padL; $dy = $plotH - ($maxV > 0 ? ($d['present'] / $maxV) * $plotH : 0) + $padT; @endphp
                        <circle cx="{{ $dx }}" cy="{{ $dy }}" r="3.5" fill="#4f46e5" stroke="white" stroke-width="2" class="hrd-chart-dot"
                                style="animation-delay:{{ 0.1 + $i * 0.05 }}s"/>
                        @endforeach
                        {{-- X-axis labels --}}
                        @foreach ($attendanceTrend as $i => $d)
                        @php $lx = $i * $step + $padL; @endphp
                        <text x="{{ $lx }}" y="{{ $chartH - 4 }}" text-anchor="middle" fill="#9ca3af" font-size="9" font-family="monospace">{{ \Carbon\Carbon::parse($d['date_iso'])->format('D') }}</text>
                        @endforeach
                        <defs>
                            <linearGradient id="atGradHr" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#4f46e5"/>
                                <stop offset="100%" stop-color="#4f46e5" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="flex items-center justify-between mt-2 text-[10px] text-gray-400">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-indigo-500 inline-block"></span>
                            Present trend
                        </span>
                        <span>
                            Today: <strong class="text-gray-700 font-mono">{{ $td['present'] }} present</strong>
                            <span class="mx-1">·</span>
                            <strong class="text-emerald-600 font-mono">{{ $tdRate }}%</strong>
                        </span>
                    </div>
                    @else
                    <div class="flex items-center justify-center h-[160px] text-xs text-gray-400">No attendance data yet.</div>
                    @endif
                </div>
            </div>

            {{-- Leave Activity Graph --}}
            <div class="hrd-fade-up bg-white rounded-xl shadow-sm border border-gray-100 p-4" style="animation-delay:0.1s">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <span class="text-[9px] font-semibold text-gray-600 uppercase tracking-widest">Leave Activity</span>
                        <div class="flex items-baseline gap-1.5 mt-0.5">
                            <span class="text-lg font-bold text-gray-900 tabular-nums">{{ collect($leaveTrend)->sum('total') }}</span>
                            <span class="text-[10px] text-gray-400">last 6 months</span>
                        </div>
                    </div>
                    <div class="flex gap-2 text-[9px]">
                        <span class="flex items-center gap-1 text-gray-400"><span class="w-1.5 h-1.5 rounded-full ring-1 ring-emerald-100 bg-emerald-500"></span> Approved</span>
                        <span class="flex items-center gap-1 text-gray-400"><span class="w-1.5 h-1.5 rounded-full ring-1 ring-gray-200 bg-gray-300"></span> Total</span>
                    </div>
                </div>

                @php
                $lvMax = collect($leaveTrend)->map(fn($m) => $m['total'])->max() ?: 1;
                @endphp

                <div class="flex items-end gap-2" style="height:50px">
                    @foreach($leaveTrend as $i => $m)
                    @php
                    $th = max(4, round(($m['total'] / $lvMax) * 40));
                    $ah = max(2, round(($m['approved'] / $lvMax) * 40));
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-0.5 h-full justify-end">
                        <div class="w-full flex flex-col-reverse items-center" style="height:40px">
                            <div class="w-4/5 bg-emerald-500 rounded-t hrd-bar" style="height:{{ $ah }}px;animation-delay:{{ $i * 0.1 + 0.2 }}s" title="Approved: {{ $m['approved'] }}"></div>
                            <div class="w-4/5 bg-gray-200 rounded-t hrd-bar" style="height:{{ max(2, $th - $ah) }}px;animation-delay:{{ $i * 0.1 }}s" title="Total: {{ $m['total'] }}"></div>
                        </div>
                        <span class="text-[7px] text-gray-400 font-medium mt-0.5">{{ $m['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>


    </div>

    {{-- RIGHT COLUMN (1/3): Calendar + To-Do --}}
    <div class="space-y-5 max-w-[280px] ml-auto">
        <div class="sticky top-24 space-y-5">

        {{-- Calendar Card --}}
        @include('partials.dashboard-calendar')

        {{-- To-Do Card --}}
        <div class="hrd-slide-right bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" style="animation-delay:0.1s">
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
                <a href="{{ route('payroll.receivables.index', ['tab' => 'cash_advances']) }}" class="flex items-center gap-3 px-5 py-3 transition-colors hover:bg-gray-50 no-underline">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M2 10h20"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-900">Cash Advances</p>
                        <p class="text-[0.55rem] text-gray-400">Pending approval</p>
                    </div>
                    @if($pendingCashAdvances > 0)
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 rounded-full bg-blue-100 text-blue-700 text-[0.5rem] font-bold px-1.5">{{ $pendingCashAdvances }}</span>
                    @endif
                    <svg class="w-3.5 h-3.5 text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('payroll.receivables.index', ['tab' => 'salary_loans']) }}" class="flex items-center gap-3 px-5 py-3 transition-colors hover:bg-gray-50 no-underline">
                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-900">Salary Loans</p>
                        <p class="text-[0.55rem] text-gray-400">Pending approval</p>
                    </div>
                    @if($pendingSalaryLoans > 0)
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 rounded-full bg-emerald-100 text-emerald-700 text-[0.5rem] font-bold px-1.5">{{ $pendingSalaryLoans }}</span>
                    @endif
                    <svg class="w-3.5 h-3.5 text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('leave.pending') }}" class="flex items-center gap-3 px-5 py-3 transition-colors hover:bg-gray-50 no-underline">
                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-900">Leave Requests</p>
                        <p class="text-[0.55rem] text-gray-400">Awaiting review</p>
                    </div>
                    @if($pendingLeaves > 0)
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 rounded-full bg-amber-100 text-amber-700 text-[0.5rem] font-bold px-1.5">{{ $pendingLeaves }}</span>
                    @endif
                    <svg class="w-3.5 h-3.5 text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('attendance.index', ['tab' => 'otut']) }}" class="flex items-center gap-3 px-5 py-3 transition-colors hover:bg-gray-50 no-underline">
                    <div class="w-7 h-7 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-900">Overtime</p>
                        <p class="text-[0.55rem] text-gray-400">{{ $pendingOT }} pending, {{ $pendingUT }} undertime</p>
                    </div>
                    @if($pendingOT + $pendingUT > 0)
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 rounded-full bg-violet-100 text-violet-700 text-[0.5rem] font-bold px-1.5">{{ $pendingOT + $pendingUT }}</span>
                    @endif
                    <svg class="w-3.5 h-3.5 text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
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
