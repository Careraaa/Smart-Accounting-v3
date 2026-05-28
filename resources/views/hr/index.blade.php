@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
@keyframes slideInRight { 0%{opacity:0;transform:translateX(16px)} 100%{opacity:1;transform:translateX(0)} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.slide-right { animation:slideInRight 0.5s cubic-bezier(0.16,1,0.3,1) both; }
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
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-gray-800 tracking-tight">Attendance Trend</h3>
                <span class="text-[9px] font-medium text-gray-400 bg-gray-100 rounded-md px-2 py-0.5">Last 7 days</span>
            </div>

            <div class="flex items-baseline gap-3 mb-3">
                <div>
                    <span class="text-gray-900 text-xl font-bold">{{ $td['present'] }}</span>
                    <span class="text-[10px] text-gray-400 ml-1">today</span>
                </div>
                <div class="text-[10px] text-gray-400">·</div>
                <div>
                    <span class="text-gray-900 text-xl font-bold">{{ $tdRate }}%</span>
                    <span class="text-[10px] text-gray-400 ml-1">rate</span>
                </div>
            </div>

            @php
            $sw = 280; $sh = 36;
            $pdL = 0; $pdR = 0; $pdT = 2; $pdB = 0;
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
            $area = ($pdL - 2) . ',' . ($pdT + $uy) . ' ' . $line . ' ' . ($pdL + $ux + 2) . ',' . ($pdT + $uy);
            @endphp

            <div class="relative h-[36px]">
                <svg viewBox="0 0 {{ $sw }} {{ $sh }}" class="w-full h-full" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="atGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#4f46e5" stop-opacity=".25"/>
                            <stop offset="100%" stop-color="#4f46e5" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    <polygon points="{{ $area }}" fill="url(#atGrad)"/>
                    <polyline points="{{ $lateLine }}" fill="none" stroke="#fbbf24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="3 3"/>
                    <polyline points="{{ $line }}" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    @foreach($lateCoords as $c)
                        <circle cx="{{ $c['x'] }}" cy="{{ $c['y'] }}" r="1.5" fill="#fbbf24" stroke="#fff" stroke-width="1"/>
                    @endforeach
                    @foreach($coords as $c)
                        <circle cx="{{ $c['x'] }}" cy="{{ $c['y'] }}" r="2" fill="#4f46e5" stroke="#fff" stroke-width="1.5"/>
                    @endforeach
                </svg>
            </div>

            <div class="flex justify-between mt-1">
                @foreach($coords as $c)
                    <span class="text-[7px] text-gray-400 font-medium">{{ $c['label'] }}</span>
                @endforeach
            </div>

            <div class="mt-3 pt-2.5 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2 text-[10px]">
                    <span class="inline-flex items-center gap-1 text-gray-400"><span class="w-1.5 h-1.5 rounded-sm bg-indigo-500"></span> Present</span>
                    <span class="inline-flex items-center gap-1 text-gray-400"><span class="w-1.5 h-1.5 rounded-sm bg-amber-400"></span> Late</span>
                </div>
                <span class="text-[10px] text-gray-500 font-semibold">{{ array_sum(array_column($attendanceTrend, 'present')) }} total</span>
            </div>
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
    </div>

    {{-- RIGHT COLUMN (1/3): Calendar + To-Do --}}
    <div class="space-y-5">

        {{-- Calendar Card --}}
        <div class="slide-right bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                    <span class="text-xs font-semibold text-gray-900">{{ $firstDay->format('F Y') }}</span>
                </div>
                <span class="text-[0.55rem] font-mono text-gray-400">{{ now()->format('D, M j') }}</span>
            </div>
            <div class="px-4 py-3">
                <div class="grid grid-cols-7 gap-0">
                    @foreach(['S','M','T','W','T','F','S'] as $dow)
                        <span class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 text-center py-1.5">{{ $dow }}</span>
                    @endforeach
                    @for($i = 0; $i < $startDow; $i++)
                        <span class="text-center py-1.5"></span>
                    @endfor
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $dateStr = sprintf('%04d-%02d-%02d', $calYear, $calMonth, $day);
                            $isToday = $dateStr === $today;
                        @endphp
                        <span class="text-center py-1.5 text-xs font-semibold rounded-lg transition-colors
                            {{ $isToday ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            {{ $day }}
                        </span>
                    @endfor
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
})();
</script>
@endpush
