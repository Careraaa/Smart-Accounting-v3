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
@keyframes pulseGlow { 0%,100%{box-shadow:0 0 0 0 rgba(79,70,229,0.4)} 50%{box-shadow:0 0 0 8px rgba(79,70,229,0)} }
@keyframes floatSlow { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-4px)} }
@keyframes barGrow { 0%{transform:scaleY(0);opacity:0} 100%{transform:scaleY(1);opacity:1} }
@keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
@keyframes countUp { 0%{opacity:0;transform:translateY(8px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes slideUpBounce { 0%{opacity:0;transform:translateY(24px)} 60%{transform:translateY(-4px)} 100%{opacity:1;transform:translateY(0)} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.slide-right { animation:slideInRight 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.prl-chart-line { stroke-dasharray: 800; stroke-dashoffset: 800; animation: drawLine 1.2s cubic-bezier(0.16,1,0.3,1) 0.2s forwards; }
.prl-chart-line-delay { stroke-dasharray: 600; stroke-dashoffset: 600; animation: drawLine 1s cubic-bezier(0.16,1,0.3,1) 0.6s forwards; }
.prl-chart-dot { opacity: 0; animation: fadeInDot 0.3s ease both; }
.prl-chart-dot:nth-child(1) { animation-delay: 0.4s; }
.prl-chart-dot:nth-child(2) { animation-delay: 0.5s; }
.prl-chart-dot:nth-child(3) { animation-delay: 0.6s; }
.prl-chart-dot:nth-child(4) { animation-delay: 0.7s; }
.prl-chart-dot:nth-child(5) { animation-delay: 0.8s; }
.prl-chart-dot:nth-child(6) { animation-delay: 0.9s; }
.prl-chart-dot:nth-child(7) { animation-delay: 1s; }
.prl-chart-fill { opacity: 0; animation: fillArea 0.6s ease 1.1s forwards; }
.prl-bounce { animation:bounceIn 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.prl-float { animation:floatSlow 3s ease-in-out infinite; }
.prl-pulse { animation:pulseGlow 2s ease-in-out infinite; }
.prl-shimmer { background:linear-gradient(90deg,transparent 25%,rgba(255,255,255,0.15) 50%,transparent 75%);background-size:200% 100%;animation:shimmer 2.5s infinite; }
.prl-count-num { animation:countUp 0.6s cubic-bezier(0.16,1,0.3,1) both; }
.prl-slide-bounce { animation:slideUpBounce 0.6s cubic-bezier(0.16,1,0.3,1) both; }
.prl-bar { transform-origin:bottom; animation:barGrow 0.8s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
@php
$maxV = collect($attendanceTrend)->map(fn($d) => $d['present'] + $d['late'] + $d['absent'])->max() ?: 1;
$td = $attendanceTrend[array_key_last($attendanceTrend)];
$tdTotal = $td['present'] + $td['late'] + $td['absent'];
$tdRate = $tdTotal > 0 ? round(($td['present'] / $tdTotal) * 100) : 0;

$lvTotal = $approvedLeaves + $pendingLeaves + $rejectedLeaves;
$lvPct = fn($n) => $lvTotal > 0 ? round(($n / $lvTotal) * 100) : 0;
$approvedDeg = $lvTotal > 0 ? round(($approvedLeaves / $lvTotal) * 360) : 0;
$pendingDeg = $lvTotal > 0 ? round(($pendingLeaves / $lvTotal) * 360) : 0;
$rejectedDeg = max(0, 360 - $approvedDeg - $pendingDeg);

// Calendar
$calMonth = now()->month;
$calYear  = now()->year;
$firstDay = \Carbon\Carbon::createFromDate($calYear, $calMonth, 1);
$daysInMonth = $firstDay->daysInMonth;
$startDow = $firstDay->dayOfWeek; // 0=Sun, 1=Mon, ...
$today = now()->format('Y-m-d');
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- LEFT COLUMN (2/3): existing dashboard content --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Attendance Trend --}}
        <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <span class="text-[9px] font-semibold text-gray-600 uppercase tracking-widest">Attendance</span>
                    <div class="flex items-baseline gap-1.5 mt-0.5">
                        <span class="text-lg font-bold text-gray-900 tabular-nums">{{ $td['present'] }}</span>
                        <span class="text-[10px] text-gray-400">today</span>
                        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                        <span class="text-[10px] font-semibold text-emerald-500">{{ $tdRate }}%</span>
                    </div>
                </div>
                <div class="flex gap-2 text-[9px]">
                    <span class="flex items-center gap-1 text-gray-400"><span class="w-1.5 h-1.5 rounded-full ring-1 ring-indigo-100 bg-indigo-500"></span> Present</span>
                    <span class="flex items-center gap-1 text-gray-400"><span class="w-1.5 h-1.5 rounded-full ring-1 ring-amber-100 bg-amber-400"></span> Late</span>
                </div>
            </div>

            @php
            $sw = 340; $sh = 50;
            $pdL = 0; $pdR = 0; $pdT = 0; $pdB = 16;
            $ux = $sw - $pdL - $pdR;
            $uy = $sh - $pdT - $pdB;
            $count = count($attendanceTrend);
            $step = $count > 1 ? $ux / ($count - 1) : 0;
            $coords = [];
            $lateCoords = [];
            foreach ($attendanceTrend as $i => $d) {
                $x = $i * $step;
                $y = $maxV > 0 ? $pdT + $uy - ($d['present'] / $maxV) * $uy : $pdT + $uy;
                $ly = $maxV > 0 ? $pdT + $uy - ($d['late'] / $maxV) * $uy : $pdT + $uy;
                $coords[] = ['x' => round($x,1), 'y' => round($y,1), 'label' => \Carbon\Carbon::parse($d['date_iso'])->format('D')];
                $lateCoords[] = ['x' => round($x,1), 'y' => round($ly,1)];
            }
            $line = implode(' ', array_map(fn($c) => $c['x'].','.$c['y'], $coords));
            $lateLine = implode(' ', array_map(fn($c) => $c['x'].','.$c['y'], $lateCoords));
            $area = ($pdL) . ',' . ($pdT + $uy) . ' ' . $line . ' ' . ($pdL + $ux) . ',' . ($pdT + $uy);
            @endphp

            <div class="relative">
                <svg viewBox="0 0 {{ $sw }} {{ $sh }}" class="w-full h-[36px]" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="atGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#4f46e5" stop-opacity=".2"/>
                            <stop offset="100%" stop-color="#4f46e5" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    <polygon points="{{ $area }}" fill="url(#atGrad)" class="prl-chart-fill"/>
                    <polyline points="{{ $lateLine }}" fill="none" stroke="#fbbf24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="3 3" class="prl-chart-line-delay"/>
                    <polyline points="{{ $line }}" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="prl-chart-line"/>
                    @foreach($coords as $c)
                        <circle cx="{{ $c['x'] }}" cy="{{ $c['y'] }}" r="1.5" fill="#4f46e5" class="prl-chart-dot"/>
                    @endforeach
                </svg>
                <div class="flex justify-between px-0.5 -mt-0.5">
                    @foreach($coords as $c)
                        <span class="text-[7px] text-gray-400 font-medium">{{ $c['label'] }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Mini stat cards row --}}
        <div class="grid grid-cols-3 gap-3">
            <a href="{{ route('payroll.salary-computation.index') }}" class="prl-bounce stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 no-underline group hover:border-indigo-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.05s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Batches</p>
                        <p class="text-xl font-bold text-gray-900 tabular-nums mt-0.5 prl-count-num" style="animation-delay:0.15s">{{ $totalBatches }}</p>
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
            <a href="{{ route('payroll.history.index') }}" class="prl-bounce stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 no-underline group hover:border-emerald-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.1s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Released</p>
                        <p class="text-xl font-bold text-emerald-600 tabular-nums mt-0.5 prl-count-num" style="animation-delay:0.2s">₱{{ number_format($totalReleasedPayroll, 0) }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center transition-all duration-300 group-hover:bg-emerald-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span>{{ $batchPaid }} completed batches</span>
                </div>
            </a>
            <a href="{{ route('payroll.receivables.index') }}" class="prl-bounce stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-3.5 no-underline group hover:border-amber-200 hover:shadow-md transition-all duration-300" style="animation-delay:0.15s">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider">Receivables</p>
                        <p class="text-xl font-bold text-gray-900 tabular-nums mt-0.5 prl-count-num" style="animation-delay:0.25s">{{ $pendingCashAdvances + $pendingSalaryLoans }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center transition-all duration-300 group-hover:bg-amber-500 group-hover:text-white group-hover:scale-110">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    </div>
                </div>
                <div class="mt-2 flex items-center gap-2 text-[9px] text-gray-400">
                    <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>{{ $pendingCashAdvances }} advances</span>
                    <span class="text-gray-300">·</span>
                    <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>{{ $pendingSalaryLoans }} loans</span>
                </div>
            </a>
        </div>

        {{-- Bottom row: Leave Mix donut + OT/UT card --}}
        <div class="grid grid-cols-2 gap-5 max-[900px]:grid-cols-1">

            {{-- Leave Mix Donut --}}
            <div class="fade-up rounded-xl bg-white px-6 pt-6 pb-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xs font-semibold text-gray-800 tracking-tight">Leave Mix</h2>
                    <span class="text-[10px] font-medium text-gray-400 tracking-widest uppercase">All-time totals</span>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-6">
                    <div class="relative shrink-0 w-[100px] h-[100px] rounded-full"
                         style="background: conic-gradient(#22c55e 0deg {{ $approvedDeg }}deg, #f59e0b {{ $approvedDeg }}deg {{ $approvedDeg + $pendingDeg }}deg, #f43f5e {{ $approvedDeg + $pendingDeg }}deg 360deg);">
                        <div class="absolute inset-[14px] bg-white rounded-full flex flex-col items-center justify-center">
                            <span class="font-mono text-lg font-bold text-gray-900 leading-none" id="lvm-total-num">{{ $lvTotal }}</span>
                            <span class="text-[9px] font-semibold text-gray-400 uppercase tracking-wide">total</span>
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col gap-2 min-w-0" id="lvm-rows">
                        <a href="{{ route('leave.approved') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all bg-green-50 hover:bg-green-100 no-underline" data-key="approved">
                            <span class="w-2.5 h-2.5 rounded-sm shrink-0 bg-green-500"></span>
                            <span class="text-xs font-semibold text-gray-700 flex-1 truncate">Approved</span>
                            <span class="font-mono text-xs font-bold text-gray-900">{{ $approvedLeaves }}</span>
                            <span class="text-[10px] text-gray-400 font-semibold min-w-[28px] text-right">{{ $lvPct($approvedLeaves) }}%</span>
                        </a>
                        <a href="{{ route('leave.pending') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all hover:bg-amber-50 no-underline" data-key="pending">
                            <span class="w-2.5 h-2.5 rounded-sm shrink-0 bg-amber-500"></span>
                            <span class="text-xs font-semibold text-gray-700 flex-1 truncate">Pending</span>
                            <span class="font-mono text-xs font-bold text-gray-900">{{ $pendingLeaves }}</span>
                            <span class="text-[10px] text-gray-400 font-semibold min-w-[28px] text-right">{{ $lvPct($pendingLeaves) }}%</span>
                        </a>
                        <a href="{{ route('leave.rejected') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all hover:bg-rose-50 no-underline" data-key="rejected">
                            <span class="w-2.5 h-2.5 rounded-sm shrink-0 bg-rose-500"></span>
                            <span class="text-xs font-semibold text-gray-700 flex-1 truncate">Rejected</span>
                            <span class="font-mono text-xs font-bold text-gray-900">{{ $rejectedLeaves }}</span>
                            <span class="text-[10px] text-gray-400 font-semibold min-w-[28px] text-right">{{ $lvPct($rejectedLeaves) }}%</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- OT/UT Card --}}
            <div class="fade-up rounded-xl bg-white px-6 pt-6 pb-5 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xs font-semibold text-gray-800 tracking-tight">OT / UT</h2>
                    <span class="text-[10px] font-medium text-gray-400 tracking-widest uppercase">Pending requests</span>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <a href="{{ route('overtime.pending') }}" class="bg-gray-50 rounded-xl px-4 py-3.5 no-underline transition-colors hover:bg-amber-50">
                        <span class="font-mono text-xl font-bold text-gray-900 leading-none">{{ $pendingOT }}</span>
                        <div class="text-[11px] text-gray-500 font-semibold mt-1">Pending OT</div>
                    </a>
                    <a href="{{ route('overtime.pending') }}" class="bg-gray-50 rounded-xl px-4 py-3.5 no-underline transition-colors hover:bg-amber-50">
                        <span class="font-mono text-xl font-bold text-gray-900 leading-none">{{ $pendingUT }}</span>
                        <div class="text-[11px] text-gray-500 font-semibold mt-1">Pending UT</div>
                    </a>
                </div>

                <div class="border-t border-gray-100 pt-4 flex items-start sm:items-center justify-between gap-3 flex-col sm:flex-row">
                    <div>
                        <div class="text-[11px] text-gray-400 font-medium">OT this week</div>
                        <span class="font-mono text-sm font-bold text-gray-800">{{ number_format($otHoursThisWeek, 1) }} hrs</span>
                    </div>
                    <div class="text-right">
                        <div class="text-[11px] text-gray-400 font-medium">Approved all-time</div>
                        <span class="font-mono text-sm font-bold text-gray-800">{{ $totalOTRecords }} records</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payroll Pulse --}}
        <div class="prl-slide-bounce bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" style="animation-delay:0.2s">
            <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-sm prl-float">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-gray-900">Payroll Pulse</span>
                </div>
                <div class="flex items-center gap-2">
                    @if($currentInProgress)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[0.55rem] font-semibold border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> In Progress
                        </span>
                    @endif
                    <a href="{{ route('payroll.salary-computation.index') }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-900 text-white rounded-lg text-[0.6rem] font-bold transition-all hover:bg-gray-800 active:scale-[0.97] no-underline prl-pulse">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Create Batch
                    </a>
                </div>
            </div>
            <div class="px-5 py-4">
                <div class="grid grid-cols-4 gap-4 mb-4">
                    <div class="text-center">
                        <div class="text-lg font-bold text-indigo-600 tabular-nums prl-count-num" style="animation-delay:0.3s">{{ $batchSubmitted }}</div>
                        <p class="text-[9px] text-gray-400 font-medium mt-0.5">Submitted</p>
                        <div class="mt-1.5 h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-500 rounded-full prl-bar transition-all duration-700" style="width:{{ $totalBatches > 0 ? round(($batchSubmitted/$totalBatches)*100) : 0 }}%;animation-delay:0.3s"></div>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-emerald-600 tabular-nums prl-count-num" style="animation-delay:0.4s">{{ $batchApproved }}</div>
                        <p class="text-[9px] text-gray-400 font-medium mt-0.5">Approved</p>
                        <div class="mt-1.5 h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full prl-bar" style="width:{{ $totalBatches > 0 ? round(($batchApproved/$totalBatches)*100) : 0 }}%;animation-delay:0.4s"></div>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-blue-600 tabular-nums prl-count-num" style="animation-delay:0.5s">{{ $batchPaid }}</div>
                        <p class="text-[9px] text-gray-400 font-medium mt-0.5">Paid</p>
                        <div class="mt-1.5 h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full prl-bar" style="width:{{ $totalBatches > 0 ? round(($batchPaid/$totalBatches)*100) : 0 }}%;animation-delay:0.5s"></div>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-bold text-red-500 tabular-nums prl-count-num" style="animation-delay:0.6s">{{ $batchRejected }}</div>
                        <p class="text-[9px] text-gray-400 font-medium mt-0.5">Rejected</p>
                        <div class="mt-1.5 h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-red-400 rounded-full prl-bar" style="width:{{ $totalBatches > 0 ? round(($batchRejected/$totalBatches)*100) : 0 }}%;animation-delay:0.6s"></div>
                        </div>
                    </div>
                </div>
                @if($currentInProgress)
                <div class="bg-amber-50 border border-amber-100 rounded-xl px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <div>
                            <p class="text-[11px] font-semibold text-amber-800">Batch in progress</p>
                            <p class="text-[10px] text-amber-600 font-mono">{{ \Carbon\Carbon::parse($currentInProgress->period_start)->format('M d') }} &mdash; {{ \Carbon\Carbon::parse($currentInProgress->period_end)->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('payroll.batch.confirm', $currentInProgress) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-600 text-white rounded-lg text-[0.6rem] font-bold transition-all hover:bg-amber-700 active:scale-[0.97] no-underline">Continue</a>
                </div>
                @endif
            </div>
        </div>

        {{-- Reports Row --}}
        <div class="grid grid-cols-2 gap-5 max-[900px]:grid-cols-1">
            <div class="prl-slide-bounce bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" style="animation-delay:0.25s">
                <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M8 21h8M12 17v4"/></svg>
                        <span class="text-xs font-semibold text-gray-900">Payroll Summary</span>
                    </div>
                    <a href="{{ route('payroll.history.index') }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-md text-[9px] font-bold no-underline transition-all hover:bg-indigo-100 hover:text-indigo-800 active:scale-[0.95]">View all →</a>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-gray-50 rounded-xl px-3.5 py-3">
                            <p class="text-[9px] text-gray-400 font-medium uppercase tracking-wider">Total Batches</p>
                            <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5 prl-count-num" style="animation-delay:0.35s">{{ $totalBatches }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl px-3.5 py-3">
                            <p class="text-[9px] text-gray-400 font-medium uppercase tracking-wider">Released</p>
                            <p class="text-lg font-bold text-emerald-600 tabular-nums mt-0.5 prl-count-num" style="animation-delay:0.4s">₱{{ number_format($totalReleasedPayroll, 0) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-[9px] text-gray-400 font-medium">Status</span>
                        <div class="flex-1 flex gap-0.5 h-5 rounded-full overflow-hidden">
                            @php
                                $statuses = [
                                    'submitted' => ['count' => $batchSubmitted, 'color' => 'bg-indigo-500'],
                                    'approved' => ['count' => $batchApproved, 'color' => 'bg-emerald-500'],
                                    'paid' => ['count' => $batchPaid, 'color' => 'bg-blue-500'],
                                    'rejected' => ['count' => $batchRejected, 'color' => 'bg-red-400'],
                                ];
                            @endphp
                            @foreach($statuses as $label => $s)
                                @if($s['count'] > 0)
                                <div class="{{ $s['color'] }} prl-bar transition-all duration-700" style="width:{{ $totalBatches > 0 ? round(($s['count']/$totalBatches)*100) : 0 }}%;animation-delay:0.5s" title="{{ ucfirst($label) }}: {{ $s['count'] }}"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="prl-slide-bounce bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" style="animation-delay:0.3s">
                <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                        <span class="text-xs font-semibold text-gray-900">Employee Receivables</span>
                    </div>
                    <a href="{{ route('payroll.receivables.index') }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-md text-[9px] font-bold no-underline transition-all hover:bg-indigo-100 hover:text-indigo-800 active:scale-[0.95]">View all →</a>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-blue-50 rounded-xl px-3.5 py-3">
                            <p class="text-[9px] text-blue-500 font-medium uppercase tracking-wider">Cash Advances</p>
                            <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5 prl-count-num" style="animation-delay:0.45s">{{ $pendingCashAdvances }} <span class="text-[10px] font-medium text-gray-400">pending</span></p>
                            @if($totalApprovedCA > 0)
                            <p class="text-[9px] text-gray-400 mt-1 font-mono">₱{{ number_format($totalApprovedCA, 0) }} approved</p>
                            @endif
                        </div>
                        <div class="bg-emerald-50 rounded-xl px-3.5 py-3">
                            <p class="text-[9px] text-emerald-500 font-medium uppercase tracking-wider">Salary Loans</p>
                            <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5 prl-count-num" style="animation-delay:0.5s">{{ $pendingSalaryLoans }} <span class="text-[10px] font-medium text-gray-400">pending</span></p>
                            @if($totalApprovedLoans > 0)
                            <p class="text-[9px] text-gray-400 mt-1 font-mono">₱{{ number_format($totalApprovedLoans, 0) }} approved</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pt-2 border-t border-gray-50">
                        <span class="text-[9px] text-gray-400 font-medium">Collections</span>
                        <div class="flex items-center gap-4 text-[10px]">
                            <span class="text-gray-600 font-semibold">₱{{ number_format($totalPaidCA + $totalPaidLoans, 0) }}</span>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-400">₱{{ number_format($totalApprovedCA + $totalApprovedLoans, 0) }} total approved</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN (1/3): Calendar + To-Do --}}
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
                <div class="grid grid-cols-7 gap-0" id="cal-grid">
                </div>
            </div>
        </div>

        {{-- To-Do Card --}}
        <div class="slide-right bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" style="animation-delay:0.1s">
            <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span class="text-xs font-semibold text-gray-900">To Do</span>
                </div>
                @php $totalPending = $pendingLeaves + $pendingOT + $pendingUT + $pendingCashAdvances + $pendingSalaryLoans; @endphp
                @if($totalPending > 0)
                    <span class="inline-flex items-center justify-center min-w-[18px] h-[18px] rounded-full bg-amber-100 text-amber-700 text-[0.5rem] font-bold px-1">{{ $totalPending }}</span>
                @endif
            </div>
            @if($totalPending > 0)
            <div class="divide-y divide-gray-50">
                {{-- Cash Advances --}}
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

                {{-- Salary Loans --}}
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

                {{-- Leave Requests --}}
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

                {{-- Overtime Requests --}}
                <a href="{{ route('overtime.pending') }}" class="flex items-center gap-3 px-5 py-3 transition-colors hover:bg-gray-50 no-underline">
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
@endsection

@push('scripts')
<script>
(function () {
    /* Leave mix hover */
    function initLeaveMix() {
        const rows = document.querySelectorAll('#lvm-rows > a');
        if (!rows.length) return;
        function activate(key) {
            rows.forEach(r => {
                ['bg-green-50','bg-amber-50','bg-rose-50'].forEach(c => r.classList.remove(c));
                if (r.dataset.key === key) {
                    if (key === 'approved') r.classList.add('bg-green-50');
                    if (key === 'pending')  r.classList.add('bg-amber-50');
                    if (key === 'rejected') r.classList.add('bg-rose-50');
                }
            });
        }
        function reset() {
            rows.forEach(r => ['bg-green-50','bg-amber-50','bg-rose-50'].forEach(c => r.classList.remove(c)));
        }
        rows.forEach(r => {
            r.addEventListener('mouseenter', () => activate(r.dataset.key));
            r.addEventListener('mouseleave', reset);
        });
    }
    initLeaveMix();

    /* Interactive calendar */
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
