@extends('layouts.layout')

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
@endphp

<div class="bg-white border border-gray-200 rounded-xl shadow-sm mb-5 max-w-sm">
    <div class="p-4">
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
</div>

{{-- Bottom row: Leave Mix donut + OT/UT card --}}
<div class="grid grid-cols-2 gap-6 max-[900px]:grid-cols-1">

    {{-- Leave Mix Donut --}}
    <div class="rounded-2xl bg-white px-7 pt-7 pb-6 shadow-sm ring-1 ring-gray-200/60">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-[13px] font-semibold text-gray-800 tracking-tight">Leave Mix</h2>
            <span class="text-[10px] font-medium text-gray-400 tracking-widest uppercase">All-time totals</span>
        </div>

        <div class="flex items-center gap-7">
            {{-- Donut --}}
            <div class="relative shrink-0 w-[108px] h-[108px] rounded-full"
                 style="background: conic-gradient(#22c55e 0deg {{ $approvedDeg }}deg, #f59e0b {{ $approvedDeg }}deg {{ $approvedDeg + $pendingDeg }}deg, #f43f5e {{ $approvedDeg + $pendingDeg }}deg 360deg);">
                <div class="absolute inset-[15px] bg-white rounded-full flex flex-col items-center justify-center">
                    <span class="font-['DM_Mono',monospace] text-xl font-bold text-gray-900 leading-none" id="lvm-total-num">{{ $lvTotal }}</span>
                    <span class="text-[9px] font-semibold text-gray-400 uppercase tracking-wide">total</span>
                </div>
            </div>

            {{-- Legend rows --}}
            <div class="flex-1 flex flex-col gap-2.5 min-w-0" id="lvm-rows">
                <a href="{{ route('leave.approved') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer transition-all bg-green-50 hover:bg-green-100 no-underline" data-key="approved">
                    <span class="w-2.5 h-2.5 rounded-sm shrink-0 bg-green-500"></span>
                    <span class="text-[12px] font-semibold text-gray-700 flex-1 truncate">Approved</span>
                    <span class="font-['DM_Mono',monospace] text-[13px] font-bold text-gray-900">{{ $approvedLeaves }}</span>
                    <span class="text-[10px] text-gray-400 font-semibold min-w-[28px] text-right">{{ $lvPct($approvedLeaves) }}%</span>
                </a>
                <a href="{{ route('leave.pending') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer transition-all hover:bg-amber-50 no-underline" data-key="pending">
                    <span class="w-2.5 h-2.5 rounded-sm shrink-0 bg-amber-500"></span>
                    <span class="text-[12px] font-semibold text-gray-700 flex-1 truncate">Pending</span>
                    <span class="font-['DM_Mono',monospace] text-[13px] font-bold text-gray-900">{{ $pendingLeaves }}</span>
                    <span class="text-[10px] text-gray-400 font-semibold min-w-[28px] text-right">{{ $lvPct($pendingLeaves) }}%</span>
                </a>
                <a href="{{ route('leave.rejected') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg cursor-pointer transition-all hover:bg-rose-50 no-underline" data-key="rejected">
                    <span class="w-2.5 h-2.5 rounded-sm shrink-0 bg-rose-500"></span>
                    <span class="text-[12px] font-semibold text-gray-700 flex-1 truncate">Rejected</span>
                    <span class="font-['DM_Mono',monospace] text-[13px] font-bold text-gray-900">{{ $rejectedLeaves }}</span>
                    <span class="text-[10px] text-gray-400 font-semibold min-w-[28px] text-right">{{ $lvPct($rejectedLeaves) }}%</span>
                </a>
            </div>
        </div>
    </div>

    {{-- OT/UT Card --}}
    <div class="rounded-2xl bg-white px-7 pt-7 pb-6 shadow-sm ring-1 ring-gray-200/60">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-[13px] font-semibold text-gray-800 tracking-tight">OT / UT</h2>
            <span class="text-[10px] font-medium text-gray-400 tracking-widest uppercase">Pending requests</span>
        </div>

        <div class="flex gap-5 mb-5">
            <div class="flex-1 bg-gray-50 rounded-xl px-5 py-4">
                <span class="font-['DM_Mono',monospace] text-2xl font-bold text-gray-900 leading-none">{{ $pendingOT }}</span>
                <div class="text-[11px] text-gray-500 font-semibold mt-1">Pending OT</div>
            </div>
            <div class="flex-1 bg-gray-50 rounded-xl px-5 py-4">
                <span class="font-['DM_Mono',monospace] text-2xl font-bold text-gray-900 leading-none">{{ $pendingUT }}</span>
                <div class="text-[11px] text-gray-500 font-semibold mt-1">Pending UT</div>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-5 flex items-center justify-between">
            <div>
                <div class="text-[11px] text-gray-400 font-medium">OT this week</div>
                <span class="font-['DM_Mono',monospace] text-sm font-bold text-gray-800">{{ number_format($otHoursThisWeek, 1) }} hrs</span>
            </div>
            <div class="text-right">
                <div class="text-[11px] text-gray-400 font-medium">Approved all-time</div>
                <span class="font-['DM_Mono',monospace] text-sm font-bold text-gray-800">{{ $totalOTRecords }} records</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    /* Leave mix */
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
