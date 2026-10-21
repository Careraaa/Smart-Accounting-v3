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

        {{-- CHART CARDS: Attendance, Net Payroll Trend, Allowances vs Deductions, Daily Remittance --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

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




        {{-- Charts row: Net Payroll Trend + Allowances vs Deductions --}}
        @php
            $trendPts = $monthlyPayrollTrend;
            $trendCount = count($trendPts);
            $ta = (float) ($totalAllowances ?? 0);
            $td = (float) ($totalDeductions ?? 0);
            $adTotal = $ta + $td;
            $adAllowPct = $adTotal > 0 ? round(($ta/$adTotal)*100) : 0;
            $adDeducPct = $adTotal > 0 ? round(($td/$adTotal)*100) : 0;
            $circ = 2 * pi() * 36;
            $allowOffset = $adAllowPct / 100 * $circ;

            // Net Payroll Trend geometry — 2px line, 3 recessive gridlines,
            // room on the right for the direct end-label, no per-point dots.
            $nW = 600; $nH = 180;
            $nPadL = 4; $nPadR = 62; $nPadT = 16; $nPadB = 26;
            $nPlotW = $nW - $nPadL - $nPadR;
            $nPlotH = $nH - $nPadT - $nPadB;
            $nMax = $trendCount > 0 ? max(array_column($trendPts, 'total')) : 1;
            $nLastIdx = $trendCount - 1;
            $nStep = $trendCount > 1 ? $nPlotW / ($trendCount - 1) : 0;
            $nPts = [];
            foreach ($trendPts as $i => $pt) {
                $nx = $nPadL + $i * $nStep;
                $ny = $nPadT + $nPlotH - ($nMax > 0 ? ($pt['total'] / $nMax) * $nPlotH : 0);
                $nPts[] = round($nx, 1) . ',' . round($ny, 1);
            }
            $nEndX = $nPadL + $nLastIdx * $nStep;
            $nEndY = $trendCount > 0 ? $nPadT + $nPlotH - ($trendPts[$nLastIdx]['total'] / max($nMax, 1)) * $nPlotH : $nPadT + $nPlotH;
            $nEndVal = $trendCount > 0 ? $trendPts[$nLastIdx]['total'] : 0;
            $nEndText = $nEndVal >= 1000 ? '₱' . number_format($nEndVal / 1000, 1) . 'k' : '₱' . number_format($nEndVal);
        @endphp

            {{-- Net Payroll Trend --}}
            <div class="sad-fade-up kpi-card overflow-hidden">
                <div class="px-4 pt-4 pb-1 flex items-start justify-between gap-3">
                    <div>
                        <p class="kcard-label">Net Payroll Trend</p>
                        <p class="text-xl font-semibold text-gray-900 tabular-nums mt-1">₱{{ number_format($nMax, 0) }}</p>
                    </div>
                    <span class="text-[11px] text-gray-400 mt-0.5">approved payrolls · peak, last 6 months</span>
                </div>
                <div class="px-4 pb-4 pt-2">
                    @if($trendCount > 1)
                    <svg viewBox="0 0 {{ $nW }} {{ $nH }}" class="w-full h-auto" role="img"
                         aria-label="Net payroll over the last {{ $trendCount }} months, peaking at {{ number_format($nMax, 0) }} pesos">
                        <line class="chart-grid" x1="{{ $nPadL }}" y1="{{ $nPadT + $nPlotH }}" x2="{{ $nPadL + $nPlotW }}" y2="{{ $nPadT + $nPlotH }}"/>
                        <line class="chart-grid" x1="{{ $nPadL }}" y1="{{ $nPadT + $nPlotH / 2 }}" x2="{{ $nPadL + $nPlotW }}" y2="{{ $nPadT + $nPlotH / 2 }}"/>
                        <line class="chart-grid" x1="{{ $nPadL }}" y1="{{ $nPadT }}" x2="{{ $nPadL + $nPlotW }}" y2="{{ $nPadT }}"/>

                        <polyline points="{{ implode(' ', $nPts) }}" fill="none" stroke="#c8292a"
                                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                  class="sad-chart-line"/>

                        <circle class="chart-ring" cx="{{ round($nEndX, 1) }}" cy="{{ round($nEndY, 1) }}" r="4" fill="#c8292a"/>
                        <text class="chart-endlabel" x="{{ round($nEndX + 10, 1) }}" y="{{ round($nEndY + 4, 1) }}">{{ $nEndText }}</text>

                        <text class="chart-label" x="{{ $nPadL }}" y="{{ $nH - 6 }}" text-anchor="start">{{ $trendPts[0]['month'] ?? \Carbon\Carbon::parse($trendPts[0]['label'])->format('M') }}</text>
                        <text class="chart-label" x="{{ $nPadL + $nPlotW }}" y="{{ $nH - 6 }}" text-anchor="end">{{ $trendPts[$nLastIdx]['month'] ?? \Carbon\Carbon::parse($trendPts[$nLastIdx]['label'])->format('M') }}</text>
                    </svg>
                    @else
                    <div class="flex items-center justify-center h-[150px] text-xs text-gray-400">Not enough data for a trend.</div>
                    @endif
                </div>
            </div>

            {{-- Allowances vs Deductions (CSS donut) --}}
            <div class="sad-fade-up kpi-card overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-[13px] font-semibold text-gray-900">Allowances vs Deductions</h2>
                    <span class="text-[11px] text-gray-400">Approved payrolls</span>
                </div>
                <div class="px-4 py-4">
                    @if($adTotal > 0)
                    <div class="flex flex-col sm:flex-row items-center gap-5">
                        <div class="relative w-[120px] h-[120px] shrink-0">
                            <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                                <circle cx="50" cy="50" r="36" fill="none" stroke="#22c55e" stroke-width="8" stroke-dasharray="{{ $allowOffset }} {{ $circ }}" stroke-dashoffset="0" stroke-linecap="round" class="sad-donut-seg" style="--pct:{{ $allowOffset }}"/>
                                <circle cx="50" cy="50" r="36" fill="none" stroke="#f43f5e" stroke-width="8" stroke-dasharray="{{ $circ - $allowOffset }} {{ $circ }}" stroke-dashoffset="{{ -$allowOffset }}" stroke-linecap="round" class="sad-donut-seg" style="--pct:{{ $circ - $allowOffset }};animation-delay:0.3s"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-[9px] font-bold text-gray-900 tabular-nums">₱{{ number_format($adTotal/1000,2) }}k</span>
                            </div>
                        </div>
                        <div class="flex-1 space-y-2 w-full">
                            <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-emerald-50 transition-colors">
                                <span class="w-2.5 h-2.5 rounded-sm shrink-0 bg-emerald-500"></span>
                                <span class="text-[10px] font-semibold text-gray-700 flex-1">Allowances</span>
                                <span class="text-[10px] font-bold text-emerald-600 tabular-nums">₱{{ number_format($ta,2) }}</span>
                                <span class="text-[9px] text-gray-400 min-w-[28px] text-right">{{ $adAllowPct }}%</span>
                            </div>
                            <div class="flex items-center gap-2.5 px-3 py-2 rounded-lg bg-rose-50 transition-colors">
                                <span class="w-2.5 h-2.5 rounded-sm shrink-0 bg-rose-500"></span>
                                <span class="text-[10px] font-semibold text-gray-700 flex-1">Deductions</span>
                                <span class="text-[10px] font-bold text-rose-600 tabular-nums">₱{{ number_format($td,2) }}</span>
                                <span class="text-[9px] text-gray-400 min-w-[28px] text-right">{{ $adDeducPct }}%</span>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="flex items-center justify-center h-[160px] text-xs text-gray-400">No data yet.</div>
                    @endif
                </div>
            </div>

{{-- Daily Remittance Trend --}}
@php
    $dailyData = collect($dailyRemittanceTrend);
    $rcHasData = $dailyData->sum('count') > 0;
    $rcCount = $rcHasData ? $dailyData->count() : 0;
    $rcMax = $dailyData->max('net') ?: 1;
    $rcTotal = $dailyData->sum('net');
    $rcW = 600; $rcH = 180;
    $rcPadL = 4; $rcPadR = 62; $rcPadT = 16; $rcPadB = 26;
    $rcPlotW = $rcW - $rcPadL - $rcPadR;
    $rcPlotH = $rcH - $rcPadT - $rcPadB;
    $rcStep = $rcCount > 1 ? $rcPlotW / ($rcCount - 1) : 0;
    $rcPts = [];
    foreach ($dailyData as $i => $d) {
        $rcPts[] = round($rcPadL + $i * $rcStep, 1) . ',' . round($rcPadT + $rcPlotH - ($rcMax > 0 ? ($d['net'] / $rcMax) * $rcPlotH : 0), 1);
    }
    $rcLast = $rcCount - 1;
    $rcEndX = $rcPadL + $rcLast * $rcStep;
    $rcEndY = $rcPadT + $rcPlotH - ($rcCount > 0 ? ($dailyData[$rcLast]['net'] / max($rcMax, 1)) * $rcPlotH : 0);
    $rcEndVal = $rcCount > 0 ? $dailyData[$rcLast]['net'] : 0;
    $rcEndText = $rcEndVal >= 1000 ? "\u{20B1}" . number_format($rcEndVal / 1000, 1) . 'k' : "\u{20B1}" . number_format($rcEndVal);
@endphp

<div class="sad-fade-up kpi-card overflow-hidden">
    <div class="px-4 pt-4 pb-1 flex items-start justify-between gap-3">
        <div>
            <p class="kcard-label">Daily Remittance Trend <span class="font-normal text-gray-400">· last 7 days</span></p>
            <p class="text-xl font-semibold text-gray-900 tabular-nums mt-1">&#8369;{{ number_format($rcTotal, 0) }}</p>
        </div>
        <a href="{{ route('remittances.index') }}" class="shrink-0 mt-1 text-[11px] font-medium text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">View all &rarr;</a>
    </div>
    <div class="px-4 pb-4 pt-2">
        @if($rcCount > 1)
        <svg viewBox="0 0 {{ $rcW }} {{ $rcH }}" class="w-full h-auto" role="img"
             aria-label="Net remittance across {{ $rcCount }} days, totalling {{ number_format($rcTotal, 0) }} pesos">
            <line class="chart-grid" x1="{{ $rcPadL }}" y1="{{ $rcPadT + $rcPlotH }}" x2="{{ $rcPadL + $rcPlotW }}" y2="{{ $rcPadT + $rcPlotH }}"/>
            <line class="chart-grid" x1="{{ $rcPadL }}" y1="{{ $rcPadT + $rcPlotH / 2 }}" x2="{{ $rcPadL + $rcPlotW }}" y2="{{ $rcPadT + $rcPlotH / 2 }}"/>
            <line class="chart-grid" x1="{{ $rcPadL }}" y1="{{ $rcPadT }}" x2="{{ $rcPadL + $rcPlotW }}" y2="{{ $rcPadT }}"/>

            <polyline points="{{ implode(' ', $rcPts) }}" fill="none" stroke="#059669"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      class="sad-chart-line"/>

            <circle class="chart-ring" cx="{{ round($rcEndX, 1) }}" cy="{{ round($rcEndY, 1) }}" r="4" fill="#059669"/>
            <text class="chart-endlabel" x="{{ round($rcEndX + 10, 1) }}" y="{{ round($rcEndY + 4, 1) }}">{{ $rcEndText }}</text>

            <text class="chart-label" x="{{ $rcPadL }}" y="{{ $rcH - 6 }}" text-anchor="start">{{ $dailyData->first()['label'] }}</text>
            <text class="chart-label" x="{{ $rcPadL + $rcPlotW }}" y="{{ $rcH - 6 }}" text-anchor="end">{{ $dailyData->last()['label'] }}</text>
        </svg>
        @else
        <div class="flex items-center justify-center h-[150px] text-xs text-gray-400">No finalized remittances in the last 7 days.</div>
        @endif
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
