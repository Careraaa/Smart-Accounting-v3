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
$totalPendingTodo = $processingPayroll + $pendingRemittances + $pendingCashAdvancesCount + $pendingSalaryLoansCount;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

    {{-- LEFT COLUMN (2/3) --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Quick Actions / Shortcuts --}}
        <div class="acd-slide-bounce bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" style="animation-delay:0.2s">
            <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-sm acd-float">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-gray-900">Quick Actions</span>
                </div>
                <span class="text-[0.55rem] font-mono text-gray-400">Accountant modules</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4">
                <a href="{{ route('payroll-approval.index') }}" class="group flex flex-col items-center gap-2 px-3 py-4 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-emerald-50 hover:border-emerald-200 hover:shadow-sm no-underline">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center transition-all group-hover:bg-emerald-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-emerald-200/50">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-700 group-hover:text-emerald-700 transition-colors text-center">Payroll Approval</span>
                    @if($processingPayroll > 0)
                        <span class="inline-flex items-center justify-center min-w-[18px] h-4 px-1 rounded-full bg-emerald-100 text-emerald-700 text-[0.45rem] font-bold">{{ $processingPayroll }}</span>
                    @endif
                </a>
                <a href="{{ route('remittance-approval.index') }}" class="group flex flex-col items-center gap-2 px-3 py-4 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-violet-50 hover:border-violet-200 hover:shadow-sm no-underline">
                    <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center transition-all group-hover:bg-violet-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-violet-200/50">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-700 group-hover:text-violet-700 transition-colors text-center">Remittance Approval</span>
                    @if($pendingRemittances > 0)
                        <span class="inline-flex items-center justify-center min-w-[18px] h-4 px-1 rounded-full bg-violet-100 text-violet-700 text-[0.45rem] font-bold">{{ $pendingRemittances }}</span>
                    @endif
                </a>
                <a href="{{ route('payroll.receivables.index', ['tab' => 'cash_advances']) }}" class="group flex flex-col items-center gap-2 px-3 py-4 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-blue-50 hover:border-blue-200 hover:shadow-sm no-underline">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center transition-all group-hover:bg-blue-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-blue-200/50">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M2 10h20"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-700 group-hover:text-blue-700 transition-colors text-center">Cash Advances</span>
                    @if($pendingCashAdvancesCount > 0)
                        <span class="inline-flex items-center justify-center min-w-[18px] h-4 px-1 rounded-full bg-blue-100 text-blue-700 text-[0.45rem] font-bold">{{ $pendingCashAdvancesCount }}</span>
                    @endif
                </a>
                <a href="{{ route('payroll.receivables.index', ['tab' => 'salary_loans']) }}" class="group flex flex-col items-center gap-2 px-3 py-4 rounded-xl bg-gray-50 border border-gray-100 transition-all hover:bg-amber-50 hover:border-amber-200 hover:shadow-sm no-underline">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center transition-all group-hover:bg-amber-500 group-hover:text-white group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-amber-200/50">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-700 group-hover:text-amber-700 transition-colors text-center">Salary Loans</span>
                    @if($pendingSalaryLoansCount > 0)
                        <span class="inline-flex items-center justify-center min-w-[18px] h-4 px-1 rounded-full bg-amber-100 text-amber-700 text-[0.45rem] font-bold">{{ $pendingSalaryLoansCount }}</span>
                    @endif
                </a>
            </div>
        </div>



        {{-- Stat cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <div class="acd-bounce stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-emerald-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.05s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Net Payroll</p>
                        <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5 acd-count-num" style="animation-delay:0.15s">?{{ number_format($totalPayroll, 0) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white group-hover:scale-110 transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>Approved + released</span>
                </div>
            </div>
            <div class="acd-bounce stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-blue-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.1s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Allowances</p>
                        <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5 acd-count-num" style="animation-delay:0.2s">?{{ number_format($totalAllowances, 0) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white group-hover:scale-110 transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span>{{ number_format($allowancePercentage, 1) }}% of A+D</span>
                </div>
            </div>
            <div class="acd-bounce stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-red-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.15s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Deductions</p>
                        <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5 acd-count-num" style="animation-delay:0.25s">?{{ number_format($totalDeductions, 0) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-red-50 text-red-500 flex items-center justify-center group-hover:bg-red-500 group-hover:text-white group-hover:scale-110 transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span>{{ number_format($deductionPercentage, 1) }}% of A+D</span>
                </div>
            </div>
            <div class="acd-bounce stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-purple-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.2s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Outstanding Loans</p>
                        <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5 acd-count-num" style="animation-delay:0.3s">?{{ number_format($totalOutstandingLoans, 0) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-500 flex items-center justify-center group-hover:bg-purple-500 group-hover:text-white group-hover:scale-110 transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>{{ $activeSalaryLoans }} active loans</span>
                </div>
            </div>
            <div class="acd-bounce stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-amber-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.25s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Avg. Basic</p>
                        <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5 acd-count-num" style="animation-delay:0.35s">?{{ number_format($averageBasicSalary ?? 0, 0) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white group-hover:scale-110 transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span>Per payroll row</span>
                </div>
            </div>
            <div class="acd-bounce stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 group hover:border-slate-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.3s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Headcount</p>
                        <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5 acd-count-num" style="animation-delay:0.4s">{{ $totalEmployees }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-slate-50 text-slate-500 flex items-center justify-center group-hover:bg-slate-500 group-hover:text-white group-hover:scale-110 transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span>Excl. admin roles</span>
                </div>
            </div>
        </div>

        {{-- Charts row 1 --}}
        @php
            $trendPts = $monthlyPayrollTrend;
            $trendCount = count($trendPts);
            $stLabels = $payrollStatusChartLabels ?? [];
            $stSeries = $payrollStatusChartSeries ?? [];
            $stTotal = array_sum($stSeries);
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Net Payroll Trend (SVG line chart � clerk style) --}}
            @php
            $chartMax = $trendCount > 0 ? max(array_column($trendPts, 'total')) : 1;
            $chartH = 160;
            $chartW = 600;
            $padL = 0; $padR = 0; $padT = 8; $padB = 24;
            $plotW = $chartW - $padL - $padR;
            $plotH = $chartH - $padT - $padB;
            $cnt = $trendCount;
            $step = $cnt > 1 ? $plotW / ($cnt - 1) : 0;
            $points = [];
            foreach ($trendPts as $i => $pt) {
                $x = $i * $step;
                $y = $plotH - ($chartMax > 0 ? ($pt['total'] / $chartMax) * $plotH : 0);
                $points[] = round($x + $padL, 1) . ',' . round($y + $padT, 1);
            }
            @endphp
            <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-50 flex items-center justify-between">
                    <h2 class="text-[11px] font-semibold text-gray-900">Net Payroll Trend</h2>
                    <span class="text-[9px] text-gray-400 font-medium">Last 6 months</span>
                </div>
                <div class="p-4">
                    @if($trendCount > 0)
                    <svg viewBox="0 0 {{ $chartW }} {{ $chartH }}" class="w-full h-auto" style="max-height:180px">
                        {{-- Grid lines --}}
                        @for ($g = 0; $g <= 4; $g++)
                        @php $gy = $padT + ($plotH / 4) * $g; @endphp
                        <line x1="{{ $padL }}" y1="{{ $gy }}" x2="{{ $chartW - $padR }}" y2="{{ $gy }}" stroke="#f0f0f0" stroke-width="1"/>
                        @endfor
                        {{-- Area fill --}}
                        <path d="M{{ $points[0] }} L{{ implode(' L', $points) }} L{{ $padL + ($cnt - 1) * $step }},{{ $padT + $plotH }} L{{ $padL }},{{ $padT + $plotH }} Z"
                              fill="url(#acChartGrad)" opacity="0.15"/>
                        {{-- Line --}}
                        <polyline points="{{ implode(' ', $points) }}" fill="none" stroke="#c8292a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                  class="acd-chart-line"/>
                        {{-- Dots --}}
                        @foreach ($trendPts as $i => $pt)
                        @php $dx = $i * $step + $padL; $dy = $plotH - ($chartMax > 0 ? ($pt['total'] / $chartMax) * $plotH : 0) + $padT; @endphp
                        <circle cx="{{ $dx }}" cy="{{ $dy }}" r="3.5" fill="#c8292a" stroke="white" stroke-width="2" class="acd-chart-dot"
                                style="animation-delay:{{ 0.1 + $i * 0.05 }}s"/>
                        @endforeach
                        {{-- X-axis labels --}}
                        @foreach ($trendPts as $i => $pt)
                        @php $lx = $i * $step + $padL; @endphp
                        <text x="{{ $lx }}" y="{{ $chartH - 4 }}" text-anchor="middle" fill="#9ca3af" font-size="9" font-family="monospace">{{ $pt['month'] ?? \Carbon\Carbon::parse($pt['label'])->format('M') }}</text>
                        @endforeach
                        <defs>
                            <linearGradient id="acChartGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#c8292a"/>
                                <stop offset="100%" stop-color="#c8292a" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="flex items-center justify-between mt-2 text-[10px] text-gray-400">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>
                            Net pay trend
                        </span>
                        <span>
                            Peak: <strong class="text-gray-700 font-mono">?{{ number_format($chartMax, 0) }}</strong>
                        </span>
                    </div>
                    @else
                    <div class="flex items-center justify-center h-[160px] text-xs text-gray-400">No trend data yet.</div>
                    @endif
                </div>
            </div>

            {{-- Payroll Status Mix (CSS donut) --}}
            <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-50 flex items-center justify-between">
                    <h2 class="text-[11px] font-semibold text-gray-900">Payroll Status Mix</h2>
                    <span class="text-[9px] text-gray-400 font-medium">By batch count</span>
                </div>
                <div class="px-4 py-4">
                    @if($stTotal > 0)
                    @php
                        $stColors = ['#f59e0b','#8b5cf6','#64748b','#2563eb','#22c55e','#15803d','#f43f5e'];
                        $cumulPct = 0;
                    @endphp
                    <div class="flex flex-col sm:flex-row items-center gap-5">
                        <div class="relative w-[120px] h-[120px] shrink-0">
                            <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                                @foreach($stSeries as $i => $val)
                                @php
                                    $pct = $val / $stTotal * 100;
                                    $circ = 2 * pi() * 36;
                                    $offset = $cumulPct / 100 * $circ;
                                    $cumulPct += $pct;
                                @endphp
                                <circle cx="50" cy="50" r="36" fill="none" stroke="{{ $stColors[$i % count($stColors)] }}" stroke-width="8" stroke-dasharray="{{ ($pct/100)*$circ }} {{ $circ }}" stroke-dashoffset="0" stroke-linecap="round" class="acd-donut-seg" style="--pct:{{ ($pct/100)*$circ }};animation-delay:{{ 0.1*$i }}s;transform-origin:center;transform:rotate({{ $offset }}deg)"/>
                                @endforeach
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-xs font-bold text-gray-900 tabular-nums">{{ $stTotal }}</span>
                            </div>
                        </div>
                        <div class="flex-1 grid grid-cols-2 gap-1.5 w-full">
                            @foreach($stSeries as $i => $val)
                            @php
                                $pct = $stTotal > 0 ? round(($val/$stTotal)*100) : 0;
                            @endphp
                            <div class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-gray-50 transition-colors">
                                <span class="w-2 h-2 rounded-sm shrink-0" style="background:{{ $stColors[$i % count($stColors)] }}"></span>
                                <span class="text-[10px] text-gray-600 flex-1 truncate">{{ $stLabels[$i] ?? '�' }}</span>
                                <span class="text-[10px] font-bold text-gray-900 tabular-nums">{{ $val }}</span>
                                <span class="text-[8px] text-gray-400 min-w-[24px] text-right">{{ $pct }}%</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="flex items-center justify-center h-[160px] text-xs text-gray-400">No status data yet.</div>
                    @endif
                </div>
            </div>
        </div>


        {{-- Charts row 2 --}}
        @php
            $ta = (float) ($totalAllowances ?? 0);
            $td = (float) ($totalDeductions ?? 0);
            $adTotal = $ta + $td;
            $adAllowPct = $adTotal > 0 ? round(($ta/$adTotal)*100) : 0;
            $adDeducPct = $adTotal > 0 ? round(($td/$adTotal)*100) : 0;

            $pipeVals = $pipelineBar['values'] ?? [];
            $pipeLabels = $pipelineBar['labels'] ?? [];
            $pipeMax = count($pipeVals) > 0 ? max($pipeVals) : 1;
            $pipeColors = ['#f59e0b','#8b5cf6','#22c55e','#f43f5e'];
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Allowances vs Deductions (CSS donut) --}}
            <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-50 flex items-center justify-between">
                    <h2 class="text-[11px] font-semibold text-gray-900">Allowances vs Deductions</h2>
                    <span class="text-[9px] text-gray-400 font-medium">Aggregate amounts</span>
                </div>
                <div class="px-4 py-4">
                    @if($adTotal > 0)
                    @php
                        $circ = 2 * pi() * 36;
                        $allowOffset = $adAllowPct / 100 * $circ;
                    @endphp
                    <div class="flex flex-col sm:flex-row items-center gap-5">
                        <div class="relative w-[120px] h-[120px] shrink-0">
                            <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                                <circle cx="50" cy="50" r="36" fill="none" stroke="#22c55e" stroke-width="8" stroke-dasharray="{{ $allowOffset }} {{ $circ }}" stroke-dashoffset="0" stroke-linecap="round" class="acd-donut-seg" style="--pct:{{ $allowOffset }}"/>
                                <circle cx="50" cy="50" r="36" fill="none" stroke="#f43f5e" stroke-width="8" stroke-dasharray="{{ $circ - $allowOffset }} {{ $circ }}" stroke-dashoffset="{{ -$allowOffset }}" stroke-linecap="round" class="acd-donut-seg" style="--pct:{{ $circ - $allowOffset }};animation-delay:0.3s"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-[9px] font-bold text-gray-900 tabular-nums">?{{ number_format($adTotal/1000,0) }}k</span>
                            </div>
                        </div>
                        <div class="flex-1 space-y-2 w-full">
                            <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-emerald-50 transition-colors">
                                <span class="w-2.5 h-2.5 rounded-sm shrink-0 bg-emerald-500"></span>
                                <span class="text-[10px] font-semibold text-gray-700 flex-1">Allowances</span>
                                <span class="text-[10px] font-bold text-emerald-600 tabular-nums">?{{ number_format($ta,0) }}</span>
                                <span class="text-[9px] text-gray-400 min-w-[28px] text-right">{{ $adAllowPct }}%</span>
                            </div>
                            <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-rose-50 transition-colors">
                                <span class="w-2.5 h-2.5 rounded-sm shrink-0 bg-rose-500"></span>
                                <span class="text-[10px] font-semibold text-gray-700 flex-1">Deductions</span>
                                <span class="text-[10px] font-bold text-rose-600 tabular-nums">?{{ number_format($td,0) }}</span>
                                <span class="text-[9px] text-gray-400 min-w-[28px] text-right">{{ $adDeducPct }}%</span>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="flex items-center justify-center h-[160px] text-xs text-gray-400">No data yet.</div>
                    @endif
                </div>
            </div>

            {{-- Pipeline (horizontal bars) --}}
            <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-50 flex items-center justify-between">
                    <h2 class="text-[11px] font-semibold text-gray-900">Pipeline</h2>
                    <span class="text-[9px] text-gray-400 font-medium">Awaiting � approved � released � rejected</span>
                </div>
                <div class="px-4 py-4 space-y-3">
                    @forelse($pipeVals as $i => $val)
                    @php $barW = $pipeMax > 0 ? ($val / $pipeMax) * 100 : 0; @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-[10px] font-semibold text-gray-600">{{ $pipeLabels[$i] ?? '�' }}</span>
                            <span class="text-[10px] font-bold text-gray-900 tabular-nums">{{ $val }} <span class="text-[8px] text-gray-400 font-medium">batch{{ $val !== 1 ? 'es' : '' }}</span></span>
                        </div>
                        <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full acd-bar transition-all duration-700" style="width:{{ $barW }}%;background:{{ $pipeColors[$i % count($pipeColors)] }};animation-delay:{{ 0.1 * $i }}s"></div>
                        </div>
                    </div>
                    @empty
                    <div class="flex items-center justify-center h-[140px] text-xs text-gray-400">No pipeline data yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- RIGHT COLUMN (1/3): Calendar + To-Do --}}
    <div class="space-y-5 max-w-[280px] sticky top-24 self-start">

        {{-- Calendar Card --}}
        @include('partials.dashboard-calendar')

        {{-- To-Do Card --}}
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
@endsection
