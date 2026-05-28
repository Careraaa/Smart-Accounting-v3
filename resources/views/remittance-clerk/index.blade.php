@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes slideInRight { 0%{opacity:0;transform:translateX(16px)} 100%{opacity:1;transform:translateX(0)} }
@keyframes bounceIn { 0%{opacity:0;transform:scale(0.6)} 60%{transform:scale(1.05)} 80%{transform:scale(0.95)} 100%{opacity:1;transform:scale(1)} }
@keyframes countUp { 0%{opacity:0;transform:translateY(8px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes slideUpBounce { 0%{opacity:0;transform:translateY(24px)} 60%{transform:translateY(-4px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes pulseGlow { 0%,100%{box-shadow:0 0 0 0 rgba(200,41,42,0.4)} 50%{box-shadow:0 0 0 8px rgba(200,41,42,0)} }
@keyframes floatSlow { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-4px)} }
@keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes pulseDot { 0%,100%{opacity:0.4;transform:scale(1)} 50%{opacity:1;transform:scale(1.3)} }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.scale-in { animation:scaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.slide-right { animation:slideInRight 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.rc-bounce { animation:bounceIn 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.rc-count-num { animation:countUp 0.6s cubic-bezier(0.16,1,0.3,1) both; }
.rc-slide-bounce { animation:slideUpBounce 0.6s cubic-bezier(0.16,1,0.3,1) both; }
.rc-float { animation:floatSlow 3s ease-in-out infinite; }
.rc-pulse { animation:pulseGlow 2s ease-in-out infinite; }
@keyframes drawLine { to { stroke-dashoffset: 0; } }
@keyframes fadeInDot { to { opacity: 1; } }
.rc-chart-line { stroke-dasharray: 800; stroke-dashoffset: 800; animation: drawLine 1s cubic-bezier(0.16,1,0.3,1) 0.15s forwards; }
.rc-chart-dot { opacity: 0; animation: fadeInDot 0.25s ease both; }
</style>
@endpush

@section('content')
@php
$margin = $totalCollections > 0 ? round(($totalNetRemittance / $totalCollections) * 100, 1) : 0;
$mctMax = collect($monthlyCollectionTrend)->max('total_collection') ?: 1;
$mctActive = count($monthlyCollectionTrend) - 1;
$calMonth = now()->month;
$calYear  = now()->year;
$firstDay = \Carbon\Carbon::createFromDate($calYear, $calMonth, 1);
$today = now()->format('Y-m-d');
@endphp

    {{-- Header --}}
    <div class="flex items-start justify-between flex-wrap gap-4 mb-5 fade-up">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-[pulseDot_2s_ease-in-out_infinite]"></span>
                <span class="text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Remittance Operations</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Remittance Dashboard</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ now()->format('l, F d, Y') }}</p>
        </div>
        <a href="{{ route('remittances.create') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 shadow-sm hover:shadow transition-all no-underline">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            New Remittance
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Stat cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="rc-bounce bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-300" style="animation-delay:0.05s">
                    <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Collections</p>
                    <p class="text-lg font-bold text-emerald-600 tabular-nums mt-1 rc-count-num" style="animation-delay:0.15s">₱{{ number_format($totalCollections, 0) }}</p>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="inline-flex items-center gap-0.5 text-[9px] font-bold {{ $collectionGrowth > 0 ? 'text-emerald-600' : ($collectionGrowth < 0 ? 'text-red-500' : 'text-gray-400') }}">
                            @if($collectionGrowth > 0)
                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>
                            @elseif($collectionGrowth < 0)
                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                            @else
                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/></svg>
                            @endif
                            {{ number_format(abs($collectionGrowth), 1) }}%
                        </span>
                        <span class="text-[9px] text-gray-400">vs prev 30d</span>
                    </div>
                </div>
                <div class="rc-bounce bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-red-200 transition-all duration-300" style="animation-delay:0.1s">
                    <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Expenses</p>
                    <p class="text-lg font-bold text-red-500 tabular-nums mt-1 rc-count-num" style="animation-delay:0.2s">₱{{ number_format($totalExpenses, 0) }}</p>
                    <p class="text-[9px] text-gray-400 mt-1">Avg ₱{{ number_format($averageExpenses, 0) }} / record</p>
                </div>
                <div class="rc-bounce bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-300" style="animation-delay:0.15s">
                    <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Net Remittance</p>
                    <p class="text-lg font-bold text-blue-600 tabular-nums mt-1 rc-count-num" style="animation-delay:0.25s">₱{{ number_format($totalNetRemittance, 0) }}</p>
                    <p class="text-[9px] text-gray-400 mt-1">{{ $margin }}% margin</p>
                </div>
                <div class="rc-bounce bg-white rounded-xl border border-gray-100 p-3.5 shadow-sm hover:shadow-md hover:border-amber-200 transition-all duration-300" style="animation-delay:0.2s">
                    <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Pending</p>
                    <p class="text-lg font-bold text-amber-600 tabular-nums mt-1 rc-count-num" style="animation-delay:0.3s">{{ $pendingRemittances }}</p>
                    <p class="text-[9px] text-gray-400 mt-1">{{ $completedRemittances }} finalized</p>
                </div>
            </div>

            {{-- Chart cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Collections vs Expenses --}}
                <div class="scale-in bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                            <span class="text-xs font-semibold text-gray-900">Collections vs Expenses</span>
                        </div>
                        <span class="text-[9px] text-gray-400">Finalized only</span>
                    </div>
                    @php $cveSum = $totalCollections + $totalExpenses; $cvePct = fn($v) => $cveSum > 0 ? round(($v / $cveSum) * 100) : 50; @endphp
                    <div class="p-4 space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-lg bg-gray-900 px-3.5 py-2.5">
                                <p class="text-[9px] font-bold uppercase tracking-wider text-gray-400">Collections</p>
                                <p class="text-sm font-bold text-white tabular-nums mt-0.5">₱{{ number_format($totalCollections, 0) }}</p>
                                <p class="text-[9px] text-gray-500 mt-0.5">{{ $cvePct($totalCollections) }}% of total</p>
                            </div>
                            <div class="rounded-lg bg-gray-50 border border-gray-200 px-3.5 py-2.5">
                                <p class="text-[9px] font-bold uppercase tracking-wider text-gray-400">Expenses</p>
                                <p class="text-sm font-bold text-gray-900 tabular-nums mt-0.5">₱{{ number_format($totalExpenses, 0) }}</p>
                                <p class="text-[9px] text-gray-400 mt-0.5">{{ $cvePct($totalExpenses) }}% of total</p>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-[9px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">
                                <span>Collections</span>
                                <span>Expenses</span>
                            </div>
                            <div class="h-2.5 rounded-full bg-gray-100 overflow-hidden flex gap-0.5">
                                <div class="h-full rounded-full bg-gray-900 transition-all duration-700" style="width:{{ $cvePct($totalCollections) }}%"></div>
                                <div class="h-full rounded-full bg-red-500 transition-all duration-700" style="width:{{ $cvePct($totalExpenses) }}%"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3.5 py-2.5">
                            <div>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Net Remittance</p>
                                <p class="text-xs font-bold text-gray-900 tabular-nums mt-0.5">₱{{ number_format($totalNetRemittance, 0) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Margin</p>
                                <p class="text-xs font-bold text-emerald-600 tabular-nums mt-0.5">{{ $margin }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Monthly Collection Trend --}}
                <div class="scale-in bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <span class="text-xs font-semibold text-gray-900">Monthly Collection Trend</span>
                        </div>
                        <span class="text-[9px] text-gray-400">Last 6 months</span>
                    </div>
                    <div class="p-4">
                        @if(count($monthlyCollectionTrend))
                        <div class="flex items-end gap-2 h-[120px] mb-3" id="mct-bars">
                            @foreach($monthlyCollectionTrend as $i => $m)
                            @php $barH = $mctMax > 0 ? max(8, round(($m['total_collection'] / $mctMax) * 110)) : 8; @endphp
                            <div class="flex-1 flex flex-col items-center gap-1 cursor-pointer group {{ $i === $mctActive ? '' : '' }}"
                                 data-val="{{ $m['total_collection'] }}" data-month="{{ $m['month'] }}" data-idx="{{ $i }}">
                                <div class="w-full max-w-[32px] rounded-t-md transition-all duration-500 group-hover:brightness-110 group-hover:scale-x-105 {{ $i === $mctActive ? 'bg-gray-900' : 'bg-gray-200' }}"
                                     style="height:{{ $barH }}px;"></div>
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider {{ $i === $mctActive ? 'text-gray-900' : '' }}">{{ $m['month'] }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3.5 py-2.5 transition-opacity" id="mct-detail">
                            <div>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider" id="mct-detail-month">{{ $monthlyCollectionTrend[$mctActive]['month'] ?? '—' }}</p>
                                <p class="text-xs font-bold text-gray-900 tabular-nums mt-0.5" id="mct-detail-val">₱{{ number_format($monthlyCollectionTrend[$mctActive]['total_collection'] ?? 0, 0) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] text-gray-400">Total collection</p>
                            </div>
                        </div>
                        @else
                        <div class="flex items-center justify-center h-[160px] text-xs text-gray-400">No data yet.</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Module resource cards --}}
            <div class="grid grid-cols-3 gap-3">
                <a href="{{ route('drivers.index') }}" class="scale-in bg-white rounded-xl border border-gray-100 shadow-sm p-3.5 hover:shadow-md hover:border-indigo-200 transition-all no-underline group">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Drivers</p>
                            <p class="text-sm font-bold text-gray-900 tabular-nums mt-0.5">{{ $activeDrivers }}</p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-gray-300 ml-auto group-hover:text-gray-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
                <a href="{{ route('paos.index') }}" class="scale-in bg-white rounded-xl border border-gray-100 shadow-sm p-3.5 hover:shadow-md hover:border-purple-200 transition-all no-underline group">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">PAOs</p>
                            <p class="text-sm font-bold text-gray-900 tabular-nums mt-0.5">{{ $activePAOs }}</p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-gray-300 ml-auto group-hover:text-gray-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
                <a href="{{ route('vehicles.index') }}" class="scale-in bg-white rounded-xl border border-gray-100 shadow-sm p-3.5 hover:shadow-md hover:border-cyan-200 transition-all no-underline group">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-cyan-100 text-cyan-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Vehicles</p>
                            <p class="text-sm font-bold text-gray-900 tabular-nums mt-0.5">{{ $activeVehicles }}</p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-gray-300 ml-auto group-hover:text-gray-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
            </div>

            {{-- Quick Actions --}}
            <div class="fade-up bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-gray-900">Quick Actions</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                    <a href="{{ route('remittances.create') }}" class="flex items-center gap-2.5 rounded-lg border border-gray-100 bg-white px-3 py-2.5 hover:border-emerald-200 hover:bg-emerald-50/30 transition-all no-underline group">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span class="text-[11px] font-bold text-gray-700 group-hover:text-emerald-700 transition-colors">Record Remittance</span>
                    </a>
                    <a href="{{ route('short-remittances.index') }}" class="flex items-center gap-2.5 rounded-lg border border-gray-100 bg-white px-3 py-2.5 hover:border-amber-200 hover:bg-amber-50/30 transition-all no-underline group">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[11px] font-bold text-gray-700 group-hover:text-amber-700 transition-colors">Short Remittance</span>
                    </a>
                    <a href="{{ route('drivers.index') }}" class="flex items-center gap-2.5 rounded-lg border border-gray-100 bg-white px-3 py-2.5 hover:border-indigo-200 hover:bg-indigo-50/30 transition-all no-underline group">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <span class="text-[11px] font-bold text-gray-700 group-hover:text-indigo-700 transition-colors">Drivers</span>
                    </a>
                    <a href="{{ route('paos.index') }}" class="flex items-center gap-2.5 rounded-lg border border-gray-100 bg-white px-3 py-2.5 hover:border-purple-200 hover:bg-purple-50/30 transition-all no-underline group">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="text-[11px] font-bold text-gray-700 group-hover:text-purple-700 transition-colors">PAOs</span>
                    </a>
                    <a href="{{ route('routes.index') }}" class="flex items-center gap-2.5 rounded-lg border border-gray-100 bg-white px-3 py-2.5 hover:border-cyan-200 hover:bg-cyan-50/30 transition-all no-underline group">
                        <div class="w-8 h-8 rounded-lg bg-cyan-100 text-cyan-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        </div>
                        <span class="text-[11px] font-bold text-gray-700 group-hover:text-cyan-700 transition-colors">Routes</span>
                    </a>
                    <a href="{{ route('vehicles.index') }}" class="flex items-center gap-2.5 rounded-lg border border-gray-100 bg-white px-3 py-2.5 hover:border-blue-200 hover:bg-blue-50/30 transition-all no-underline group">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[11px] font-bold text-gray-700 group-hover:text-blue-700 transition-colors">Vehicles</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="flex items-center gap-2.5 rounded-lg border border-gray-100 bg-white px-3 py-2.5 hover:border-gray-300 hover:bg-gray-50 transition-all no-underline group">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <span class="text-[11px] font-bold text-gray-700 group-hover:text-gray-900 transition-colors">Reports</span>
                    </a>
                </div>
            </div>

            {{-- Recent remittances chart --}}
            <div class="fade-up bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-900">Daily Remittance Trend</span>
                    </div>
                    <a href="{{ route('remittances.index') }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-md text-[9px] font-bold no-underline transition-all hover:bg-emerald-100 hover:text-emerald-800 active:scale-[0.95]">View all &rarr;</a>
                </div>
                @php
                $dailyData = collect();
                if (!$recentRemittances->isEmpty()) {
                    $grouped = $recentRemittances->groupBy(fn($r) => $r->remittance_date?->format('Y-m-d'));
                    $dailyData = $grouped->map(fn($items, $date) => [
                        'date' => $date,
                        'label' => \Carbon\Carbon::parse($date)->format('M d'),
                        'net' => $items->sum('net_remittance'),
                        'count' => $items->count(),
                    ])->sortBy('date')->values();
                }
                $chartMax = $dailyData->max('net') ?: 1;
                $chartH = 160;
                $chartW = 600;
                $padL = 0; $padR = 0; $padT = 8; $padB = 24;
                $plotW = $chartW - $padL - $padR;
                $plotH = $chartH - $padT - $padB;
                $cnt = $dailyData->count();
                @endphp
                @if($dailyData->isNotEmpty())
                <div class="p-4">
                    <svg viewBox="0 0 {{ $chartW }} {{ $chartH }}" class="w-full h-auto" style="max-height:180px">
                        @php
                        $step = $cnt > 1 ? $plotW / ($cnt - 1) : 0;
                        $points = [];
                        $barW = $cnt > 1 ? min(40, ($plotW / $cnt) * 0.6) : 40;
                        foreach ($dailyData as $i => $d) {
                            $x = $i * $step;
                            $y = $plotH - ($chartMax > 0 ? ($d['net'] / $chartMax) * $plotH : 0);
                            $points[] = round($x + $padL, 1) . ',' . round($y + $padT, 1);
                        }
                        @endphp
                        {{-- Grid lines --}}
                        @for ($g = 0; $g <= 4; $g++)
                        @php $gy = $padT + ($plotH / 4) * $g; @endphp
                        <line x1="{{ $padL }}" y1="{{ $gy }}" x2="{{ $chartW - $padR }}" y2="{{ $gy }}" stroke="#f0f0f0" stroke-width="1"/>
                        @endfor
                        {{-- Area fill --}}
                        <path d="M{{ $points[0] }} L{{ implode(' L', $points) }} L{{ $padL + ($cnt - 1) * $step }},{{ $padT + $plotH }} L{{ $padL }},{{ $padT + $plotH }} Z"
                              fill="url(#rcChartGrad)" opacity="0.15"/>
                        {{-- Line --}}
                        <polyline points="{{ implode(' ', $points) }}" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                  class="rc-chart-line"/>
                        {{-- Dots --}}
                        @foreach ($dailyData as $i => $d)
                        @php $dx = $i * $step + $padL; $dy = $plotH - ($chartMax > 0 ? ($d['net'] / $chartMax) * $plotH : 0) + $padT; @endphp
                        <circle cx="{{ $dx }}" cy="{{ $dy }}" r="3.5" fill="#059669" stroke="white" stroke-width="2" class="rc-chart-dot"
                                style="animation-delay:{{ 0.1 + $i * 0.05 }}s"/>
                        @endforeach
                        {{-- X-axis labels --}}
                        @foreach ($dailyData as $i => $d)
                        @php $lx = $i * $step + $padL; @endphp
                        <text x="{{ $lx }}" y="{{ $chartH - 4 }}" text-anchor="middle" fill="#9ca3af" font-size="9" font-family="monospace">{{ $d['label'] }}</text>
                        @endforeach
                        <defs>
                            <linearGradient id="rcChartGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#059669"/>
                                <stop offset="100%" stop-color="#059669" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="flex items-center justify-between mt-2 text-[10px] text-gray-400">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                            Net remittance
                        </span>
                        <span>
                            Total: <strong class="text-gray-700 font-mono">₱{{ number_format($dailyData->sum('net'), 2) }}</strong>
                        </span>
                    </div>
                </div>
                @else
                <div class="text-center py-10 text-xs text-gray-400">No remittance data yet.</div>
                @endif
            </div>

        </div>

        {{-- Right sidebar --}}
        <div class="space-y-5">

            {{-- Calendar Card --}}
            <div class="slide-right bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" id="cal-card">
                <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                        <button class="p-0.5 rounded hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600" id="cal-prev" title="Previous month">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <span class="text-xs font-semibold text-gray-900 min-w-[100px] text-center" id="cal-label">{{ $firstDay->format('F Y') }}</span>
                        <button class="p-0.5 rounded hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600" id="cal-next" title="Next month">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                    <span class="text-[0.55rem] font-mono text-gray-400">{{ now()->format('D, M j') }}</span>
                </div>
                <div class="px-4 py-3">
                    <div class="grid grid-cols-7 gap-0" id="cal-grid"></div>
                </div>
            </div>

            {{-- To-Do Card --}}
            @php $totalPendingTodo = $pendingRemittances; @endphp
            <div class="slide-right bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" style="animation-delay:0.1s">
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
                    <a href="{{ route('remittances.index') }}" class="flex items-center gap-3 px-5 py-3 transition-colors hover:bg-gray-50 no-underline">
                        <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-gray-900">Pending Remittances</p>
                            <p class="text-[0.55rem] text-gray-400">Awaiting approval</p>
                        </div>
                        @if($pendingRemittances > 0)
                            <span class="inline-flex items-center justify-center min-w-[20px] h-5 rounded-full bg-amber-100 text-amber-700 text-[0.5rem] font-bold px-1.5">{{ $pendingRemittances }}</span>
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

@endsection

@push('scripts')
<script>
(function () {
    /* ── Monthly trend interaction ── */
    const cols = document.querySelectorAll('#mct-bars > div');
    const detMonth = document.getElementById('mct-detail-month');
    const detVal = document.getElementById('mct-detail-val');
    if (cols.length && detMonth) {
        function fmt(n) { return '₱' + Number(n).toLocaleString('en-PH', { maximumFractionDigits: 0 }); }
        function activate(el) {
            cols.forEach(c => {
                c.querySelector('div:first-child')?.classList.remove('bg-gray-900');
                c.querySelector('div:first-child')?.classList.add('bg-gray-200');
                c.querySelector('span')?.classList.remove('text-gray-900');
            });
            const bar = el.querySelector('div:first-child');
            if (bar) { bar.classList.remove('bg-gray-200'); bar.classList.add('bg-gray-900'); }
            const lbl = el.querySelector('span');
            if (lbl) lbl.classList.add('text-gray-900');
            const raw = parseFloat(el.dataset.val) || 0;
            detMonth.textContent = el.dataset.month;
            const prev = parseFloat(detVal.textContent.replace(/[^0-9.]/g, '')) || 0;
            if (prev === raw) { detVal.textContent = fmt(raw); return; }
            const steps = 18, dur = 200;
            let cur = 0;
            const inc = (raw - prev) / steps;
            const t = setInterval(() => {
                cur++;
                detVal.textContent = fmt(Math.round(prev + inc * cur));
                if (cur >= steps) { detVal.textContent = fmt(raw); clearInterval(t); }
            }, dur / steps);
        }
        cols.forEach(col => {
            col.addEventListener('mouseenter', () => activate(col));
            col.addEventListener('click', () => activate(col));
        });
    }

    /* ── Count-up animation for stat values ── */
    document.querySelectorAll('.rc-count-num').forEach(el => {
        const text = el.textContent.trim();
        const prefix = text.startsWith('₱') ? '₱' : '';
        const target = parseInt(text.replace(/[₱,]/g, '')) || 0;
        if (target === 0) return;
        const steps = 24, dur = 500;
        let cur = 0;
        const inc = target / steps;
        const t = setInterval(() => {
            cur++;
            el.textContent = prefix + Math.round(inc * cur).toLocaleString('en-PH');
            if (cur >= steps) { el.textContent = text; clearInterval(t); }
        }, dur / steps);
    });

    /* ── Interactive calendar ── */
    const calState = { month: {{ $calMonth }}, year: {{ $calYear }}, selected: '{{ $today }}' };
    const todayStr = '{{ $today }}';
    const calGrid = document.getElementById('cal-grid');
    const calLabel = document.getElementById('cal-label');
    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];

    function renderCalendar() {
        const firstDow = new Date(calState.year, calState.month - 1, 1).getDay();
        const daysIn = new Date(calState.year, calState.month, 0).getDate();
        calLabel.textContent = monthNames[calState.month - 1] + ' ' + calState.year;
        let html = '';
        ['S','M','T','W','T','F','S'].forEach(d => {
            html += '<span class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 text-center py-1.5">' + d + '</span>';
        });
        for (let i = 0; i < firstDow; i++) {
            html += '<span class="text-center py-1.5"></span>';
        }
        for (let d = 1; d <= daysIn; d++) {
            const ds = calState.year + '-' + String(calState.month).padStart(2,'0') + '-' + String(d).padStart(2,'0');
            let cls = 'text-center py-1.5 text-xs font-semibold rounded-lg transition-all cursor-pointer ';
            if (ds === todayStr) {
                cls += 'bg-gray-900 text-white ';
            } else if (ds === calState.selected) {
                cls += 'bg-indigo-100 text-indigo-700 ring-1 ring-indigo-300 ';
            } else {
                cls += 'text-gray-600 hover:bg-gray-100 ';
            }
            html += '<span class="' + cls + '" data-date="' + ds + '">' + d + '</span>';
        }
        calGrid.innerHTML = html;
        calGrid.querySelectorAll('[data-date]').forEach(el => {
            el.addEventListener('click', function () {
                calState.selected = this.dataset.date;
                renderCalendar();
            });
        });
    }
    renderCalendar();

    document.getElementById('cal-prev').addEventListener('click', function () {
        if (--calState.month < 1) { calState.month = 12; calState.year--; }
        renderCalendar();
    });
    document.getElementById('cal-next').addEventListener('click', function () {
        if (++calState.month > 12) { calState.month = 1; calState.year++; }
        renderCalendar();
    });
})();
</script>
@endpush
