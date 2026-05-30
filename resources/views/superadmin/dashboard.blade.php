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
@endphp

<div class="flex flex-col lg:flex-row gap-5 items-start">

    {{-- LEFT COLUMN (2/3) --}}
    <div class="flex-1 min-w-0 space-y-5">

        {{-- Quick Actions / Shortcuts --}}
        <div class="sad-slide-bounce bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-sm sad-float">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-gray-900">Quick Actions</span>
                </div>
                <span class="text-[0.55rem] font-mono text-gray-400">System modules</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4">
                <a href="{{ route('superadmin.accounts.index') }}" class="group flex flex-col items-center gap-2 px-3 py-4 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-indigo-50 hover:border-indigo-200 hover:shadow-sm no-underline">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center transition-all group-hover:bg-indigo-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-indigo-200/50">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-700 group-hover:text-indigo-700 transition-colors text-center">Accounts</span>
                    @if($totalEmployees - $activeEmployees > 0)
                        <span class="inline-flex items-center justify-center min-w-[18px] h-4 px-1 rounded-full bg-indigo-100 text-indigo-700 text-[0.45rem] font-bold">{{ $totalEmployees - $activeEmployees }} inactive</span>
                    @endif
                </a>
                <a href="{{ route('configuration.index') }}" class="group flex flex-col items-center gap-2 px-3 py-4 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-gray-200 hover:border-gray-300 hover:shadow-sm no-underline">
                    <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center transition-all group-hover:bg-gray-600 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-gray-200/50">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-700 group-hover:text-gray-700 transition-colors text-center">Configuration</span>
                </a>
                <a href="{{ route('configuration.backup-history') }}" class="group flex flex-col items-center gap-2 px-3 py-4 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-emerald-50 hover:border-emerald-200 hover:shadow-sm no-underline">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center transition-all group-hover:bg-emerald-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-emerald-200/50">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-700 group-hover:text-emerald-700 transition-colors text-center">Backups</span>
                </a>
                <a href="{{ route('superadmin.accounts.create') }}" class="group flex flex-col items-center gap-2 px-3 py-4 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-amber-50 hover:border-amber-200 hover:shadow-sm no-underline">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center transition-all group-hover:bg-amber-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-amber-200/50">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-700 group-hover:text-amber-700 transition-colors text-center">New Account</span>
                </a>
            </div>
        </div>

        {{-- STAT CARDS ROW 1: Workforce, Today, Leaves, Payroll --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="sad-bounce sad-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-emerald-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.05s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Workforce</p>
                        <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5 sad-count-num" style="animation-delay:0.15s">{{ $activeEmployees }}<span class="text-xs text-gray-400 font-medium">/{{ $totalEmployees }}</span></p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center transition-all duration-300 group-hover:bg-emerald-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span>{{ $inactiveEmployees }} inactive</span>
                    <span class="text-gray-300">·</span>
                    <span>{{ $onLeaveEmployees }} on leave</span>
                </div>
            </div>
            <div class="sad-bounce sad-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-cyan-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.1s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Today</p>
                        <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5 sad-count-num" style="animation-delay:0.2s">{{ $presentToday + $lateToday }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-cyan-50 text-cyan-500 flex items-center justify-center transition-all duration-300 group-hover:bg-cyan-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>{{ $td['present'] }} present</span>
                    <span class="text-gray-300">·</span>
                    <span>{{ $absentToday }} absent</span>
                </div>
            </div>
            <div class="sad-bounce sad-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-violet-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.15s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Leaves</p>
                        <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5 sad-count-num" style="animation-delay:0.25s">{{ $pendingLeaves }}</p>
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
            </div>
            <div class="sad-bounce sad-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-indigo-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.2s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Net Payroll</p>
                        <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5 sad-count-num" style="animation-delay:0.3s">₱{{ number_format($totalPayroll, 0) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-500 flex items-center justify-center transition-all duration-300 group-hover:bg-indigo-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span>₱{{ number_format($averageBasicSalary ?? 0, 0) }} avg basic</span>
                </div>
            </div>
        </div>

        {{-- STAT CARDS ROW 2: Pipeline, OT/UT, Loans, Advances --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="sad-bounce sad-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-amber-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.25s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Pipeline</p>
                        <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5 sad-count-num" style="animation-delay:0.35s">{{ $releasedPayroll }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center transition-all duration-300 group-hover:bg-amber-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>{{ $processingPayroll }} processing</span>
                    <span class="text-gray-300">·</span>
                    <span>{{ $approvedPayroll }} approved</span>
                </div>
            </div>
            <div class="sad-bounce sad-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-rose-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.3s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">OT / UT</p>
                        <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5 sad-count-num" style="animation-delay:0.4s">{{ number_format($totalOvertimeHours, 1) }}h</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center transition-all duration-300 group-hover:bg-rose-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span>{{ $totalOvertimeRecords }} records</span>
                    <span class="text-gray-300">·</span>
                    <span>{{ number_format($totalUndertimeHours, 1) }}h UT</span>
                </div>
            </div>
            <div class="sad-bounce sad-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-blue-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.35s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Salary Loans</p>
                        <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5 sad-count-num" style="animation-delay:0.45s">₱{{ number_format($totalOutstandingLoans, 0) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center transition-all duration-300 group-hover:bg-blue-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span>{{ $activeSalaryLoans }} active</span>
                </div>
            </div>
            <div class="sad-bounce sad-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-teal-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.4s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Cash Advances</p>
                        <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5 sad-count-num" style="animation-delay:0.5s">₱{{ number_format($totalCashAdvances, 0) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-teal-50 text-teal-500 flex items-center justify-center transition-all duration-300 group-hover:bg-teal-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M2 10h20"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span>₱{{ number_format($pendingCashAdvances, 0) }} pending</span>
                </div>
            </div>
        </div>

        {{-- GRAPH CARDS ROW --}}
        <div class="grid grid-cols-2 gap-5">

            {{-- Attendance Trend (SVG line chart) --}}
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
            <div class="sad-fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
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
                        @for ($g = 0; $g <= 4; $g++)
                        @php $gy = $padT + ($plotH / 4) * $g; @endphp
                        <line x1="{{ $padL }}" y1="{{ $gy }}" x2="{{ $chartW - $padR }}" y2="{{ $gy }}" stroke="#f0f0f0" stroke-width="1"/>
                        @endfor
                        <path d="M{{ $points[0] }} L{{ implode(' L', $points) }} L{{ $padL + ($cnt - 1) * $step }},{{ $padT + $plotH }} L{{ $padL }},{{ $padT + $plotH }} Z"
                              fill="url(#sadAtGrad)" opacity="0.15"/>
                        @php
                        $latePoints = [];
                        foreach ($attendanceTrend as $i => $d) {
                            $x = $i * $step;
                            $y = $plotH - ($maxV > 0 ? ($d['late'] / $maxV) * $plotH : 0);
                            $latePoints[] = round($x + $padL, 1) . ',' . round($y + $padT, 1);
                        }
                        @endphp
                        <polyline points="{{ implode(' ', $latePoints) }}" fill="none" stroke="#fbbf24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="4 3" class="sad-chart-line-delay"/>
                        <polyline points="{{ implode(' ', $points) }}" fill="none" stroke="#4f46e5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="sad-chart-line"/>
                        @foreach ($attendanceTrend as $i => $d)
                        @php $dx = $i * $step + $padL; $dy = $plotH - ($maxV > 0 ? ($d['present'] / $maxV) * $plotH : 0) + $padT; @endphp
                        <circle cx="{{ $dx }}" cy="{{ $dy }}" r="3.5" fill="#4f46e5" stroke="white" stroke-width="2" class="sad-chart-dot"
                                style="animation-delay:{{ 0.1 + $i * 0.05 }}s"/>
                        @endforeach
                        @foreach ($attendanceTrend as $i => $d)
                        @php $lx = $i * $step + $padL; @endphp
                        <text x="{{ $lx }}" y="{{ $chartH - 4 }}" text-anchor="middle" fill="#9ca3af" font-size="9" font-family="monospace">{{ \Carbon\Carbon::parse($d['date_iso'])->format('D') }}</text>
                        @endforeach
                        <defs>
                            <linearGradient id="sadAtGrad" x1="0" y1="0" x2="0" y2="1">
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
                            <strong class="text-emerald-600 font-mono">{{ number_format($attendanceRate, 1) }}%</strong>
                        </span>
                    </div>
                    @else
                    <div class="flex items-center justify-center h-[160px] text-xs text-gray-400">No attendance data yet.</div>
                    @endif
                </div>
            </div>

            {{-- Leave Activity Graph --}}
            <div class="sad-fade-up bg-white rounded-xl shadow-sm border border-gray-100 p-4" style="animation-delay:0.1s">
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
                @php $lvMax = collect($leaveTrend)->map(fn($m) => $m['total'])->max() ?: 1; @endphp
                <div class="flex items-end gap-2" style="height:50px">
                    @foreach($leaveTrend as $i => $m)
                    @php
                    $th = max(4, round(($m['total'] / $lvMax) * 40));
                    $ah = max(2, round(($m['approved'] / $lvMax) * 40));
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-0.5 h-full justify-end">
                        <div class="w-full flex flex-col-reverse items-center" style="height:40px">
                            <div class="w-4/5 bg-emerald-500 rounded-t sad-bar" style="height:{{ $ah }}px;animation-delay:{{ $i * 0.1 + 0.2 }}s" title="Approved: {{ $m['approved'] }}"></div>
                            <div class="w-4/5 bg-gray-200 rounded-t sad-bar" style="height:{{ max(2, $th - $ah) }}px;animation-delay:{{ $i * 0.1 }}s" title="Total: {{ $m['total'] }}"></div>
                        </div>
                        <span class="text-[7px] text-gray-400 font-medium mt-0.5">{{ $m['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- REMITTANCE OVERVIEW --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="sad-bounce sad-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-emerald-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.05s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Collections</p>
                        <p class="text-lg font-bold text-emerald-600 tabular-nums mt-0.5 sad-count-num" style="animation-delay:0.15s">₱{{ number_format($totalCollections, 0) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center transition-all duration-300 group-hover:bg-emerald-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span>Finalized remittances</span>
                </div>
            </div>
            <div class="sad-bounce sad-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-red-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.1s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Expenses</p>
                        <p class="text-lg font-bold text-red-500 tabular-nums mt-0.5 sad-count-num" style="animation-delay:0.2s">₱{{ number_format($totalExpenses, 0) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-red-50 text-red-500 flex items-center justify-center transition-all duration-300 group-hover:bg-red-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span>{{ $totalExpenses > 0 ? round(($totalExpenses / max($totalCollections, 1)) * 100, 1) : 0 }}% of collections</span>
                </div>
            </div>
            <div class="sad-bounce sad-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-blue-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.15s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Net Remittance</p>
                        <p class="text-lg font-bold text-blue-600 tabular-nums mt-0.5 sad-count-num" style="animation-delay:0.25s">₱{{ number_format($totalNetRemittance, 0) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center transition-all duration-300 group-hover:bg-blue-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span>{{ $totalCollections > 0 ? round(($totalNetRemittance / $totalCollections) * 100, 1) : 0 }}% margin</span>
                </div>
            </div>
            <div class="sad-bounce sad-stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-amber-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.2s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Pending</p>
                        <p class="text-lg font-bold text-amber-600 tabular-nums mt-0.5 sad-count-num" style="animation-delay:0.3s">{{ $pendingRemittancesCount }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center transition-all duration-300 group-hover:bg-amber-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span>{{ $completedRemittances }} finalized</span>
                </div>
            </div>
        </div>

        {{-- Payroll breakdown + Payroll status row --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sad-fade-up bg-white border border-gray-200 rounded-xl overflow-hidden" style="animation-delay:0.1s">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-900">Payroll breakdown</span>
                    <span class="text-[9px] text-gray-400">Avg ₱{{ number_format($averageBasicSalary ?? 0, 0) }}</span>
                </div>
                <div class="px-5 py-4 space-y-4">
                    <div>
                        <div class="flex items-center justify-between text-[10px] mb-1.5">
                            <span class="font-semibold text-gray-700">Allowances</span>
                            <span class="font-mono tabular-nums text-gray-500">₱{{ number_format($totalAllowances, 0) }} · {{ number_format($allowancePercentage, 1) }}%</span>
                        </div>
                        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-500 transition-all duration-700" style="width: {{ $allowancePercentage }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-[10px] mb-1.5">
                            <span class="font-semibold text-gray-700">Deductions</span>
                            <span class="font-mono tabular-nums text-gray-500">₱{{ number_format($totalDeductions, 0) }} · {{ number_format($deductionPercentage, 1) }}%</span>
                        </div>
                        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-rose-500 transition-all duration-700" style="width: {{ $deductionPercentage }}%"></div>
                        </div>
                    </div>
                    <div class="pt-2 border-t border-gray-100 grid grid-cols-2 gap-4 text-center">
                        <div>
                            <div class="text-base font-extrabold text-gray-900 tabular-nums">₱{{ number_format($totalPayroll, 0) }}</div>
                            <div class="text-[0.55rem] font-semibold uppercase tracking-wider text-gray-400 mt-0.5">Total Net Pay</div>
                        </div>
                        <div>
                            <div class="text-base font-extrabold text-gray-900 tabular-nums">{{ number_format($totalPayroll > 0 ? $totalDeductions / $totalPayroll * 100 : 0, 1) }}%</div>
                            <div class="text-[0.55rem] font-semibold uppercase tracking-wider text-gray-400 mt-0.5">Deduction Rate</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sad-fade-up bg-white border border-gray-200 rounded-xl overflow-hidden" style="animation-delay:0.15s">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-900">Payroll status</span>
                    <span class="text-[9px] text-gray-400">{{ $releasedPayroll + $approvedPayroll + $processingPayroll + $rejectedPayroll }} total</span>
                </div>
                <div class="px-5 py-4 space-y-3">
                    @php $pipelineTotal = max(1, $releasedPayroll + $approvedPayroll + $processingPayroll + $rejectedPayroll); @endphp
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-900">Released</span>
                                <span class="text-xs font-extrabold text-gray-900 tabular-nums">{{ $releasedPayroll }}</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                <div class="h-full rounded-full bg-emerald-500" style="width: {{ ($releasedPayroll / $pipelineTotal) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-900">Approved</span>
                                <span class="text-xs font-extrabold text-gray-900 tabular-nums">{{ $approvedPayroll }}</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                <div class="h-full rounded-full bg-blue-500" style="width: {{ ($approvedPayroll / $pipelineTotal) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-900">Processing</span>
                                <span class="text-xs font-extrabold text-gray-900 tabular-nums">{{ $processingPayroll }}</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                <div class="h-full rounded-full bg-amber-500" style="width: {{ ($processingPayroll / $pipelineTotal) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold text-gray-900">Rejected</span>
                                <span class="text-xs font-extrabold text-gray-900 tabular-nums">{{ $rejectedPayroll }}</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                <div class="h-full rounded-full bg-rose-500" style="width: {{ ($rejectedPayroll / $pipelineTotal) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- RIGHT COLUMN: Calendar + To-Do --}}
    <div class="w-full lg:w-[280px] lg:shrink-0 space-y-5">
        <div class="lg:sticky lg:top-24 space-y-5">

        {{-- Calendar Card --}}
        @include('partials.dashboard-calendar')

        {{-- To-Do Card --}}
        <div class="sad-slide-right bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" style="animation-delay:0.1s">
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
