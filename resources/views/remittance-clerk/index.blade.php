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
@endphp

<div class="flex flex-col lg:flex-row gap-5 items-start">

    {{-- LEFT COLUMN --}}
    <div class="flex-1 min-w-0 space-y-5">
        <div class="flex gap-4 items-start">
            {{-- Quick Actions --}}
            <div class="w-[260px] shrink-0 rc-slide-bounce bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" style="animation-delay:0.2s">
                <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-sm rc-float">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-900">Quick Actions</span>
                    </div>
                    <span class="text-[0.55rem] font-mono text-gray-400">Remittance modules</span>
                </div>
                <div class="grid grid-cols-2 gap-3 p-4">
                    <a href="{{ route('remittances.create') }}" class="group flex flex-col items-center gap-2 px-3 py-4 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-emerald-50 hover:border-emerald-200 hover:shadow-sm no-underline">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center transition-all group-hover:bg-emerald-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-emerald-200/50">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-700 group-hover:text-emerald-700 transition-colors text-center">Record Remittance</span>
                    </a>
                    <a href="{{ route('short-remittances.index') }}" class="group flex flex-col items-center gap-2 px-3 py-4 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-amber-50 hover:border-amber-200 hover:shadow-sm no-underline">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center transition-all group-hover:bg-amber-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-amber-200/50">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-700 group-hover:text-amber-700 transition-colors text-center">Short Remittance</span>
                    </a>
                    <a href="{{ route('drivers.index') }}" class="group flex flex-col items-center gap-2 px-3 py-4 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-indigo-50 hover:border-indigo-200 hover:shadow-sm no-underline">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center transition-all group-hover:bg-indigo-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-indigo-200/50">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-700 group-hover:text-indigo-700 transition-colors text-center">Drivers</span>
                    </a>
                    <a href="{{ route('paos.index') }}" class="group flex flex-col items-center gap-2 px-3 py-4 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-purple-50 hover:border-purple-200 hover:shadow-sm no-underline">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center transition-all group-hover:bg-purple-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-purple-200/50">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-700 group-hover:text-purple-700 transition-colors text-center">PAOs</span>
                    </a>
                </div>
            </div>

            {{-- Daily Remittance Trend --}}
            <div class="flex-1 min-w-0 fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-gray-900">Daily Remittance Trend</span>
                </div>
                <a href="{{ route('remittances.index') }}" class="btn-uv-pill">View all &rarr;</a>
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
                    foreach ($dailyData as $i => $d) {
                        $x = $i * $step;
                        $y = $plotH - ($chartMax > 0 ? ($d['net'] / $chartMax) * $plotH : 0);
                        $points[] = round($x + $padL, 1) . ',' . round($y + $padT, 1);
                    }
                    @endphp
                    @for ($g = 0; $g <= 4; $g++)
                    @php $gy = $padT + ($plotH / 4) * $g; @endphp
                    <line x1="{{ $padL }}" y1="{{ $gy }}" x2="{{ $chartW - $padR }}" y2="{{ $gy }}" stroke="#f0f0f0" stroke-width="1"/>
                    @endfor
                    <path d="M{{ $points[0] }} L{{ implode(' L', $points) }} L{{ $padL + ($cnt - 1) * $step }},{{ $padT + $plotH }} L{{ $padL }},{{ $padT + $plotH }} Z"
                          fill="url(#rcChartGrad)" opacity="0.15"/>
                    <polyline points="{{ implode(' ', $points) }}" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                              class="rc-chart-line"/>
                    @foreach ($dailyData as $i => $d)
                    @php $dx = $i * $step + $padL; $dy = $plotH - ($chartMax > 0 ? ($d['net'] / $chartMax) * $plotH : 0) + $padT; @endphp
                    <circle cx="{{ $dx }}" cy="{{ $dy }}" r="3.5" fill="#059669" stroke="white" stroke-width="2" class="rc-chart-dot"
                            style="animation-delay:{{ 0.1 + $i * 0.05 }}s"/>
                    @endforeach
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

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="rc-bounce bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-300" style="animation-delay:0.05s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Collections</p>
                        <p class="text-lg font-bold text-emerald-600 tabular-nums mt-0.5 rc-count-num" style="animation-delay:0.15s">₱{{ number_format($totalCollections, 0) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white group-hover:scale-110 transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span class="inline-flex items-center gap-1 text-{{ $collectionGrowth > 0 ? 'emerald' : ($collectionGrowth < 0 ? 'red' : 'gray') }}-600">
                        @if($collectionGrowth > 0)<svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/></svg>@elseif($collectionGrowth < 0)<svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>@else<svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/></svg>@endif
                        {{ number_format(abs($collectionGrowth), 1) }}% vs prev 30d
                    </span>
                </div>
            </div>
            <div class="rc-bounce bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md hover:border-red-200 transition-all duration-300" style="animation-delay:0.1s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Expenses</p>
                        <p class="text-lg font-bold text-red-500 tabular-nums mt-0.5 rc-count-num" style="animation-delay:0.2s">₱{{ number_format($totalExpenses, 0) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-red-50 text-red-500 flex items-center justify-center group-hover:bg-red-500 group-hover:text-white group-hover:scale-110 transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                    </div>
                </div>
                <div class="mt-2 text-[9px] text-gray-400">Avg ₱{{ number_format($averageExpenses, 0) }} / record</div>
            </div>
            <div class="rc-bounce bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-300" style="animation-delay:0.15s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Net Remittance</p>
                        <p class="text-lg font-bold text-blue-600 tabular-nums mt-0.5 rc-count-num" style="animation-delay:0.25s">₱{{ number_format($totalNetRemittance, 0) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white group-hover:scale-110 transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </div>
                </div>
                <div class="mt-2 text-[9px] text-gray-400">{{ $margin }}% margin</div>
            </div>
            <div class="rc-bounce bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md hover:border-amber-200 transition-all duration-300" style="animation-delay:0.2s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Pending</p>
                        <p class="text-lg font-bold text-amber-600 tabular-nums mt-0.5 rc-count-num" style="animation-delay:0.3s">{{ $pendingRemittances }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white group-hover:scale-110 transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-2 text-[9px] text-gray-400">{{ $completedRemittances }} finalized</div>
            </div>
        </div>

        {{-- Charts row 1 --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Collections vs Expenses --}}
            <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
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
            <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
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
                        <div class="flex-1 flex flex-col items-center gap-1 cursor-pointer group"
                             data-val="{{ $m['total_collection'] }}" data-month="{{ $m['month'] }}" data-idx="{{ $i }}">
                            <div class="w-full max-w-[32px] rounded-t-md transition-all duration-500 group-hover:brightness-110 group-hover:scale-x-105 {{ $i === $mctActive ? 'bg-gray-900' : 'bg-gray-200' }}"
                                 style="height:{{ $barH }}px;"></div>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-wider {{ $i === $mctActive ? 'text-gray-900' : '' }}">{{ $m['month'] }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3.5 py-2.5 transition-opacity" id="mct-detail">
                        <div>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider" id="mct-detail-month">{{ $monthlyCollectionTrend[$mctActive]['month'] ?? '–' }}</p>
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



    </div>

    {{-- RIGHT COLUMN: Calendar + To-Do --}}
    <div class="w-[280px] shrink-0 space-y-5">
        <div class="sticky top-24 space-y-5">

        @include('partials.dashboard-calendar')
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
(function () {
    /* -- Monthly trend interaction -- */
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

    /* -- Count-up animation for stat values -- */
    document.querySelectorAll('.rc-count-num').forEach(el => {
        const text = el.textContent.trim();
        const prefix = text.startsWith('₱') ? '₱' : '';
        const target = parseInt(text.replace(/[₱?,]/g, '')) || 0;
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
})();
</script>
@endpush
