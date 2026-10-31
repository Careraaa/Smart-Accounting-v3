@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes slideInRight { 0%{opacity:0;transform:translateX(16px)} 100%{opacity:1;transform:translateX(0)} }
@keyframes drawLine { to { stroke-dashoffset: 0; } }
@keyframes fadeInDot { to { opacity: 1; } }
@keyframes fillArea { to { opacity: 1; } }
@keyframes bounceIn { 0%{opacity:0;transform:scale(0.6)} 60%{transform:scale(1.05)} 80%{transform:scale(0.95)} 100%{opacity:1;transform:scale(1)} }
@keyframes pulseGlow { 0%,100%{box-shadow:0 0 0 0 rgba(200,41,42,0.4)} 50%{box-shadow:0 0 0 8px rgba(200,41,42,0)} }
@keyframes floatSlow { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-4px)} }
@keyframes countUp { 0%{opacity:0;transform:translateY(8px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes slideUpBounce { 0%{opacity:0;transform:translateY(24px)} 60%{transform:translateY(-4px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes growBar { 0%{transform:scaleY(0);opacity:0} 100%{transform:scaleY(1);opacity:1} }
@keyframes donutFill { 0%{stroke-dasharray:0 999} 100%{stroke-dasharray:var(--pct) 999} }
@keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes pulseDot { 0%,100%{opacity:0.4;transform:scale(1)} 50%{opacity:1;transform:scale(1.3)} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.slide-right { animation:slideInRight 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.acd-chart-line { stroke-dasharray: 800; stroke-dashoffset: 800; animation: drawLine 1.2s cubic-bezier(0.16,1,0.3,1) 0.2s forwards; }
.acd-chart-line-delay { stroke-dasharray: 600; stroke-dashoffset: 600; animation: drawLine 1s cubic-bezier(0.16,1,0.3,1) 0.6s forwards; }
.acd-chart-dot { opacity: 0; animation: fadeInDot 0.3s ease both; }
.acd-chart-dot:nth-child(1) { animation-delay: 0.4s; }
.acd-chart-dot:nth-child(2) { animation-delay: 0.5s; }
.acd-chart-dot:nth-child(3) { animation-delay: 0.6s; }
.acd-chart-dot:nth-child(4) { animation-delay: 0.7s; }
.acd-chart-dot:nth-child(5) { animation-delay: 0.8s; }
.acd-chart-dot:nth-child(6) { animation-delay: 0.9s; }
.acd-chart-dot:nth-child(7) { animation-delay: 1s; }
.acd-chart-fill { opacity: 0; animation: fillArea 0.6s ease 1.1s forwards; }
.acd-bounce { animation:bounceIn 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.acd-float { animation:floatSlow 3s ease-in-out infinite; }
.acd-pulse { animation:pulseGlow 2s ease-in-out infinite; }
.acd-count-num { animation:countUp 0.6s cubic-bezier(0.16,1,0.3,1) both; }
.acd-slide-bounce { animation:slideUpBounce 0.6s cubic-bezier(0.16,1,0.3,1) both; }
.acd-bar { transform-origin:bottom; animation:growBar 0.7s cubic-bezier(0.16,1,0.3,1) both; }
.acd-donut-ring { fill:none;stroke-width:28;stroke-linecap:round;transform:rotate(-90deg);transform-origin:center; }
.acd-donut-seg { animation:donutFill 1s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
@php
$totalPendingTodo = $processingPayroll + $pendingRemittances + $pendingCashAdvancesCount;
@endphp


<div class="flex flex-col lg:flex-row gap-5 items-start">

    {{-- LEFT COLUMN (2/3) --}}
    <div class="flex-1 min-w-0 w-full space-y-5">
        {{-- Quick Actions / Shortcuts --}}
        <div class="acd-slide-bounce kpi-card overflow-hidden" style="animation-delay:0.2s">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <span class="text-[13px] font-semibold text-gray-900">Quick Actions</span>
                <span class="text-[11px] text-gray-400">Accountant modules</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4">
                <a href="{{ route('payroll-approval.index') }}" class="qa-tile group transition-all hover:bg-emerald-50 hover:border-emerald-200 no-underline">
                    <div class="qa-icon bg-emerald-100 text-emerald-600 transition-colors group-hover:bg-emerald-500 group-hover:text-white">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="qa-label group-hover:text-emerald-700 transition-colors">Payroll Approval</span>
                    @if($processingPayroll > 0)
                        <span class="qa-badge bg-emerald-100 text-emerald-700 group-hover:bg-\w+-500 group-hover:text-white transition-colors">{{ $processingPayroll }}</span>
                    @endif
                </a>
                <a href="{{ route('remittance-approval.index') }}" class="qa-tile group transition-all hover:bg-violet-50 hover:border-violet-200 no-underline">
                    <div class="qa-icon bg-violet-100 text-violet-600 transition-colors group-hover:bg-violet-500 group-hover:text-white">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </div>
                    <span class="qa-label group-hover:text-violet-700 transition-colors">Remittance Approval</span>
                    @if($pendingRemittances > 0)
                        <span class="qa-badge bg-violet-100 text-violet-700 group-hover:bg-\w+-500 group-hover:text-white transition-colors">{{ $pendingRemittances }}</span>
                    @endif
                </a>
                <a href="{{ route('payroll.receivables.index', ['tab' => 'cash_advances']) }}" class="qa-tile group transition-all hover:bg-blue-50 hover:border-blue-200 no-underline">
                    <div class="qa-icon bg-blue-100 text-blue-600 transition-colors group-hover:bg-blue-500 group-hover:text-white">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M2 10h20"/></svg>
                    </div>
                    <span class="qa-label group-hover:text-blue-700 transition-colors">Cash Advances</span>
                    @if($pendingCashAdvancesCount > 0)
                        <span class="qa-badge bg-blue-100 text-blue-700 group-hover:bg-\w+-500 group-hover:text-white transition-colors">{{ $pendingCashAdvancesCount }}</span>
                    @endif
                </a>
            </div>
        </div>




        @include('partials.financial-overview-card')

    </div>

    {{-- RIGHT COLUMN: Calendar + To-Do --}}
    <div class="w-full lg:w-[280px] lg:shrink-0 space-y-5">
        <div class="lg:sticky lg:top-24 space-y-5">

        {{-- Calendar Card --}}
        @include('partials.dashboard-calendar')

        {{-- To-Do Card --}}
        <div class="slide-right kpi-card overflow-hidden" style="animation-delay:0.1s">
            <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span class="text-xs font-semibold text-gray-900">To Do</span>
                </div>
                @if($totalPendingTodo > 0)
                    <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] rounded-full bg-amber-100 text-amber-700 text-[0.5rem] font-bold px-1">{{ $totalPendingTodo }}</span>
                @endif
            </div>
            @if($totalPendingTodo > 0)
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
