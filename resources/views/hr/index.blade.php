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
                @include('partials.attendance-trend-card')

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
