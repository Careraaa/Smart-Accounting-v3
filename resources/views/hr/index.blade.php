@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

/* Knight mascot animation */
.hrd-hero-knight {
    position: absolute;
    left: 50%;
    top: 50%;
    height: 280px;
    width: auto;
    transform: translate(-50%, -50%);
    object-fit: contain;
    pointer-events: none;
    z-index: 0;
    opacity: 0.12;
    animation: heroKnightCharge 4s ease-in-out infinite;
    transform-origin: center center;
}
@keyframes heroKnightCharge {
    0%   { transform: translate(-50%, -50%) translateY(0)    rotate(0deg);    opacity: 0.12; }
    30%  { transform: translate(-50%, -50%) translateY(-6px) rotate(-1.5deg); opacity: 0.16; }
    60%  { transform: translate(-50%, -50%) translateY(-10px) rotate(-0.8deg); opacity: 0.14; }
    80%  { transform: translate(-50%, -50%) translateY(-4px) rotate(-2deg);   opacity: 0.17; }
    100% { transform: translate(-50%, -50%) translateY(0)    rotate(0deg);    opacity: 0.12; }
}

/* Transition for leave track segments */
.lvm-seg { transition: width .6s cubic-bezier(.4,0,.2,1), opacity .2s, filter .2s; }
.lvm-seg.dimmed { opacity:.25; }

/* Transition for trend bars */
.hrd-trend-bar { transition:height .35s cubic-bezier(.4,0,.2,1), opacity .15s, filter .15s; }

/* Balance fill transition */
.otut-balance-fill { transition:width .7s cubic-bezier(.4,0,.2,1); }
</style>
@endpush

@section('content')
@php
    $initials = function ($name) {
        $name = trim((string) $name);
        if ($name === '') return '?';
        $p = preg_split('/\s+/', $name);
        $a = strtoupper(substr($p[0] ?? '', 0, 1));
        $b = strtoupper(substr($p[1] ?? '', 0, 1));
        return $b !== '' ? $a . $b : $a;
    };
@endphp
<div class="flex gap-5 items-start max-lg:flex-col">
    {{-- Main Dashboard --}}
    <div class="flex-1 min-w-0">

        {{-- Hero --}}
        <header class="bg-gradient-to-br from-gray-900 via-[#0b1220] to-gray-900 rounded-[18px] p-[22px_24px] mb-[22px] flex items-start justify-between gap-[18px] flex-wrap relative overflow-hidden
            before:absolute before:top-[-70px] before:right-[-70px] before:w-[260px] before:h-[260px] before:rounded-full before:bg-red-500/18 before:pointer-events-none
            after:absolute after:bottom-[-90px] after:left-[-90px] after:w-[260px] after:h-[260px] after:rounded-full after:bg-sky-500/14 after:pointer-events-none">
            <img src="{{ asset('images/landscape-knight.png') }}" alt="" class="hrd-hero-knight" aria-hidden="true">
            <div class="relative z-10 min-w-[240px]">
                <h1 class="text-xl font-black text-white m-0 mb-1.5 tracking-tight">HR Dashboard</h1>
                <p class="m-0 text-[.82rem] text-gray-400 max-w-[520px] leading-relaxed">Workforce, attendance, and leave — charts use live data from your <strong class="text-gray-200">attendance</strong> and <strong class="text-gray-200">users</strong> tables.</p>
                <div class="flex flex-wrap gap-2 mt-2">
                    <span class="inline-flex items-center gap-[6px] px-[10px] py-[6px] border border-white/14 rounded-full text-gray-200 bg-white/6 text-[.75rem]"><i class="feather-calendar"></i> {{ now()->format('l, F d, Y') }}</span>
                    <span class="inline-flex items-center gap-[6px] px-[10px] py-[6px] border border-white/14 rounded-full text-gray-200 bg-white/6 text-[.75rem]"><i class="feather-users"></i> {{ $activeEmployees }} active / {{ $totalEmployees }} total</span>
                    <span class="inline-flex items-center gap-[6px] px-[10px] py-[6px] border border-white/14 rounded-full text-gray-200 bg-white/6 text-[.75rem]"><i class="feather-activity"></i> {{ number_format($attendanceRate, 1) }}% today check-in</span>
                </div>
            </div>
            <div class="relative z-10 flex flex-wrap gap-[10px] items-center">
                <a href="{{ route('employees.index') }}" class="inline-flex items-center gap-[7px] px-[14px] py-[9px] bg-white/6 text-gray-200 border border-white/14 rounded-[10px] text-[.82rem] font-bold no-underline transition-all hover:bg-white/10 hover:border-white/22 hover:text-white"><i class="feather-users"></i> Employees</a>
                <a href="{{ route('attendance.create') }}" class="inline-flex items-center gap-[7px] px-[14px] py-[9px] bg-white/6 text-gray-200 border border-white/14 rounded-[10px] text-[.82rem] font-bold no-underline transition-all hover:bg-white/10 hover:border-white/22 hover:text-white"><i class="feather-edit-3"></i> Manual log</a>
                <a href="{{ route('leave.create') }}" class="inline-flex items-center gap-[10px] px-[18px] py-[11px] bg-[#c8292a] text-white border-none rounded-[12px] text-[.86rem] font-black no-underline cursor-pointer whitespace-nowrap shadow-[0_4px_20px_rgba(200,41,42,.5)] transition-all hover:bg-[#a81f20] hover:shadow-[0_10px_34px_rgba(200,41,42,.62)] hover:-translate-y-px"><i class="feather-plus"></i> Create leave</a>
            </div>
        </header>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-3 gap-3 mb-[22px] max-[900px]:grid-cols-1">
            <div class="border border-[#e8e8ef] rounded-[14px] p-[16px_18px] bg-white">
                <div class="text-[.62rem] font-bold uppercase tracking-[.1em] text-gray-400 mb-2">Workforce</div>
                <div class="text-[1.2rem] font-extrabold text-gray-900 tracking-tight font-['DM_Mono',monospace]">{{ $activeEmployees }} <span class="text-gray-400 font-semibold text-[.95rem]">/ {{ $totalEmployees }}</span></div>
                <div class="text-[.78rem] text-gray-500 mt-2 leading-relaxed">{{ $inactiveEmployees }} inactive · {{ $onLeaveEmployees }} on leave today</div>
            </div>
            <div class="border border-[#e8e8ef] rounded-[14px] p-[16px_18px] bg-white">
                <div class="text-[.62rem] font-bold uppercase tracking-[.1em] text-gray-400 mb-2">Today</div>
                <div class="text-[1.2rem] font-extrabold text-gray-900 tracking-tight font-['DM_Mono',monospace]">{{ $presentToday + $lateToday }}<span class="text-gray-400 font-semibold text-[.85rem]"> in</span></div>
                <div class="text-[.78rem] text-gray-500 mt-2 leading-relaxed">Absent {{ $absentToday }} · Late {{ $lateToday }} · {{ number_format($attendanceRate, 1) }}% check-in rate</div>
            </div>
            <div class="border border-[#e8e8ef] rounded-[14px] p-[16px_18px] bg-white">
                <div class="text-[.62rem] font-bold uppercase tracking-[.1em] text-gray-400 mb-2">Leave (all time)</div>
                <div class="text-[1.2rem] font-extrabold text-gray-900 tracking-tight font-['DM_Mono',monospace]">{{ $pendingLeaves }}<span class="text-gray-400 font-semibold text-[.85rem]"> pending</span></div>
                <div class="text-[.78rem] text-gray-500 mt-2 leading-relaxed">{{ $approvedLeaves }} approved · {{ $rejectedLeaves }} rejected</div>
            </div>
        </div>

        {{-- Charts 2-col grid --}}
        <div class="grid grid-cols-2 gap-[14px] mb-7 max-[900px]:grid-cols-1 mt-[14px]">
            {{-- Attendance Trend --}}
            <div class="border border-[#e8e8ef] rounded-[14px] bg-white overflow-hidden">
                <div class="px-[18px] py-[14px] border-b border-gray-100 flex items-baseline justify-between gap-3 flex-wrap">
                    <h2 class="m-0 text-[.88rem] font-bold text-gray-900 tracking-tight">Attendance trend</h2>
                    <span class="text-[.72rem] text-gray-400 font-medium">Last 7 days · present / late / absent</span>
                </div>
                @php
                    $trendMax = collect($attendanceTrend)->map(fn($d) => ($d['present'] + $d['late'] + $d['absent']))->max() ?: 1;
                    $todayIso = now()->toDateString();
                    $todayIdx = collect($attendanceTrend)->search(fn($d) => $d['date_iso'] === $todayIso);
                    $todayIdx = $todayIdx === false ? count($attendanceTrend) - 1 : $todayIdx;
                @endphp
                <div class="px-5 pt-5">
                    <div class="grid grid-cols-7 gap-2 max-[700px]:grid-cols-4" id="hrd-trend-grid">
                        @foreach($attendanceTrend as $i => $day)
                            @php
                                $pxMax   = 110;
                                $pH      = $trendMax > 0 ? max(5, round(($day['present'] / $trendMax) * $pxMax)) : 5;
                                $lH      = $trendMax > 0 ? max(5, round(($day['late']    / $trendMax) * $pxMax)) : 5;
                                $aH      = $trendMax > 0 ? max(5, round(($day['absent']  / $trendMax) * $pxMax)) : 5;
                                $isToday = $day['date_iso'] === $todayIso;
                                [$dow, $md] = explode(' · ', $day['label']);
                            @endphp
                            <div class="flex flex-col items-center gap-[7px] px-[6px] py-[10px_6px_12px] rounded-[12px] cursor-pointer transition-all hover:bg-gray-100 hover:-translate-y-0.5 relative {{ $i === $todayIdx ? 'bg-gray-100 -translate-y-0.5' : '' }}"
                                 data-present="{{ $day['present'] }}"
                                 data-late="{{ $day['late'] }}"
                                 data-absent="{{ $day['absent'] }}"
                                 data-label="{{ $dow }}, {{ $md }}"
                                 data-total="{{ $day['present'] + $day['late'] + $day['absent'] }}"
                                 data-idx="{{ $i }}">
                                @if($isToday)
                                    <div class="w-[5px] h-[5px] rounded-full bg-[#c8292a] absolute top-[6px]"></div>
                                @endif
                                <div class="flex items-end gap-1 h-[110px]">
                                    <div class="w-3 rounded-t-[5px] hrd-trend-bar {{ $isToday ? 'bg-[#c8292a]' : 'bg-gray-900' }}" style="height:{{ $pH }}px;"></div>
                                    <div class="w-3 rounded-t-[5px] hrd-trend-bar bg-amber-500" style="height:{{ $lH }}px;"></div>
                                    <div class="w-3 rounded-t-[5px] hrd-trend-bar bg-gray-200" style="height:{{ $aH }}px;"></div>
                                </div>
                                <span class="text-[.68rem] font-bold uppercase tracking-[.07em] {{ $isToday ? 'text-gray-900' : 'text-gray-400' }}">{{ $dow }}</span>
                                <span class="text-[.63rem] {{ $isToday ? 'text-gray-500' : 'text-gray-300' }}">{{ $md }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Detail strip --}}
                @php $d = $attendanceTrend[$todayIdx]; $dTotal = $d['present'] + $d['late'] + $d['absent']; @endphp
                <div class="mx-5 mt-[14px] px-[18px] py-[14px] bg-gray-100 rounded-[10px] flex items-center" id="hrd-trend-detail">
                    <span class="text-[.72rem] font-bold uppercase tracking-[.08em] text-gray-400 min-w-[110px]" id="hrd-trend-detail-label">
                        {{ collect($attendanceTrend[$todayIdx]['label'])->implode('') }}
                    </span>
                    <div class="flex gap-6 flex-1">
                        <div class="flex flex-col gap-0.5">
                            <span class="font-['DM_Mono',monospace] text-[1.1rem] font-extrabold leading-none text-gray-900" id="hrd-td-present">{{ $d['present'] }}</span>
                            <span class="text-[.65rem] font-semibold uppercase tracking-[.06em] text-gray-400">Present</span>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <span class="font-['DM_Mono',monospace] text-[1.1rem] font-extrabold leading-none text-amber-600" id="hrd-td-late">{{ $d['late'] }}</span>
                            <span class="text-[.65rem] font-semibold uppercase tracking-[.06em] text-gray-400">Late</span>
                        </div>
                        <div class="flex flex-col gap-0.5">
                            <span class="font-['DM_Mono',monospace] text-[1.1rem] font-extrabold leading-none text-gray-400" id="hrd-td-absent">{{ $d['absent'] }}</span>
                            <span class="text-[.65rem] font-semibold uppercase tracking-[.06em] text-gray-400">Absent</span>
                        </div>
                    </div>
                    <span class="ml-auto font-['DM_Mono',monospace] text-[.82rem] font-bold text-gray-500 bg-white border border-gray-200 px-[10px] py-[4px] rounded-[8px]" id="hrd-td-rate">
                        {{ $dTotal > 0 ? number_format(($d['present'] / $dTotal) * 100, 0) : 0 }}% present
                    </span>
                </div>

                <div class="flex gap-4 px-5 py-[14px] border-t border-gray-100 mt-[14px]">
                    <div class="flex items-center gap-[6px] text-[.71rem] font-semibold text-gray-500"><div class="w-[9px] h-[9px] rounded-[3px] bg-gray-900"></div> Present</div>
                    <div class="flex items-center gap-[6px] text-[.71rem] font-semibold text-gray-500"><div class="w-[9px] h-[9px] rounded-[3px] bg-amber-500"></div> Late</div>
                    <div class="flex items-center gap-[6px] text-[.71rem] font-semibold text-gray-500"><div class="w-[9px] h-[9px] rounded-[3px] bg-gray-200 border border-gray-300"></div> Absent</div>
                    <div class="flex items-center gap-[6px] text-[.71rem] font-semibold text-gray-500 ml-auto"><div class="w-[9px] h-[9px] rounded-[3px] bg-[#c8292a]"></div> Today</div>
                </div>
            </div>

            {{-- Leave Mix --}}
            <div class="border border-[#e8e8ef] rounded-[14px] bg-white overflow-hidden">
                <div class="px-[18px] py-[14px] border-b border-gray-100 flex items-baseline justify-between gap-3 flex-wrap">
                    <h2 class="m-0 text-[.88rem] font-bold text-gray-900 tracking-tight">Leave mix</h2>
                    <span class="text-[.72rem] text-gray-400 font-medium">All-time request totals</span>
                </div>
                @php
                    $lvTotal = $pendingLeaves + $approvedLeaves + $rejectedLeaves;
                    $lvPct   = fn($n) => $lvTotal > 0 ? round(($n / $lvTotal) * 100) : 0;
                @endphp
                <div class="p-[22px_20px_18px]">
                    <div class="flex items-baseline gap-2 mb-[18px]">
                        <span class="font-['DM_Mono',monospace] text-[2rem] font-extrabold text-gray-900 leading-none" id="lvm-total-num">{{ $lvTotal }}</span>
                        <span class="text-[.72rem] font-bold uppercase tracking-[.08em] text-gray-400">total requests</span>
                    </div>
                    <div class="h-[10px] rounded-full bg-gray-100 overflow-hidden flex mb-5 gap-0.5" id="lvm-track">
                        <div class="lvm-seg h-full rounded-full cursor-pointer relative hover:brightness-110" id="lvm-seg-approved"
                             style="width:{{ $lvPct($approvedLeaves) }}%; background:#22c55e;"
                             data-key="approved" title="Approved: {{ $approvedLeaves }}"></div>
                        <div class="lvm-seg h-full rounded-full cursor-pointer relative hover:brightness-110" id="lvm-seg-pending"
                             style="width:{{ $lvPct($pendingLeaves) }}%; background:#f59e0b;"
                             data-key="pending" title="Pending: {{ $pendingLeaves }}"></div>
                        <div class="lvm-seg h-full rounded-full cursor-pointer relative hover:brightness-110" id="lvm-seg-rejected"
                             style="width:{{ $lvPct($rejectedLeaves) }}%; background:#f43f5e;"
                             data-key="rejected" title="Rejected: {{ $rejectedLeaves }}"></div>
                    </div>
                    <div class="flex flex-col gap-[10px]" id="lvm-rows">
                        <div class="flex items-center gap-[10px] px-3 py-[10px] rounded-[10px] cursor-pointer transition-all bg-gray-100" data-key="approved">
                            <div class="w-[10px] h-[10px] rounded-[3px] shrink-0" style="background:#22c55e;"></div>
                            <span class="text-[.82rem] font-semibold text-gray-700 flex-1">Approved</span>
                            <span class="font-['DM_Mono',monospace] text-[.88rem] font-bold text-gray-900">{{ $approvedLeaves }}</span>
                            <span class="text-[.72rem] text-gray-400 font-semibold min-w-[36px] text-right">{{ $lvPct($approvedLeaves) }}%</span>
                        </div>
                        <div class="flex items-center gap-[10px] px-3 py-[10px] rounded-[10px] cursor-pointer transition-all hover:bg-gray-100" data-key="pending">
                            <div class="w-[10px] h-[10px] rounded-[3px] shrink-0" style="background:#f59e0b;"></div>
                            <span class="text-[.82rem] font-semibold text-gray-700 flex-1">Pending</span>
                            <span class="font-['DM_Mono',monospace] text-[.88rem] font-bold text-gray-900">{{ $pendingLeaves }}</span>
                            <span class="text-[.72rem] text-gray-400 font-semibold min-w-[36px] text-right">{{ $lvPct($pendingLeaves) }}%</span>
                        </div>
                        <div class="flex items-center gap-[10px] px-3 py-[10px] rounded-[10px] cursor-pointer transition-all hover:bg-gray-100" data-key="rejected">
                            <div class="w-[10px] h-[10px] rounded-[3px] shrink-0" style="background:#f43f5e;"></div>
                            <span class="text-[.82rem] font-semibold text-gray-700 flex-1">Rejected</span>
                            <span class="font-['DM_Mono',monospace] text-[.88rem] font-bold text-gray-900">{{ $rejectedLeaves }}</span>
                            <span class="text-[.72rem] text-gray-400 font-semibold min-w-[36px] text-right">{{ $lvPct($rejectedLeaves) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- OT/UT Panel --}}
        <div class="border border-[#e8e8ef] rounded-[14px] bg-white overflow-hidden mb-[14px]">
            <div class="px-[18px] py-[14px] border-b border-gray-100 flex items-baseline justify-between gap-3 flex-wrap">
                <h2 class="m-0 text-[.88rem] font-bold text-gray-900 tracking-tight">Overtime / Undertime</h2>
                <span class="text-[.72rem] text-gray-400 font-medium">Approved hours · all time</span>
            </div>
            @php
                $otTotal  = (float) $totalOvertimeHours;
                $utTotal  = (float) $totalUndertimeHours;
                $otutSum  = $otTotal + $utTotal;
                $otPct    = $otutSum > 0 ? round(($otTotal / $otutSum) * 100) : 50;
                $netHours = $otTotal - $utTotal;
            @endphp
            <div class="p-[22px_20px_20px]">
                <div class="grid grid-cols-2 gap-3 mb-5">
                    <div class="rounded-[12px] p-[16px_14px] flex flex-col gap-[6px] cursor-pointer transition-all hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(0,0,0,.08)] relative overflow-hidden bg-gray-900">
                        <div class="absolute top-[-30px] right-[-30px] w-[90px] h-[90px] rounded-full bg-red-500/18 pointer-events-none"></div>
                        <span class="text-[.62rem] font-bold uppercase tracking-[.1em] text-gray-500">Overtime</span>
                        <span class="font-['DM_Mono',monospace] text-[1.7rem] font-extrabold leading-none text-white">{{ number_format($otTotal, 1) }}<span class="text-[.9rem] font-semibold text-gray-500">h</span></span>
                        <span class="text-[.72rem] font-semibold text-gray-600">{{ $totalOvertimeRecords }} approved records</span>
                    </div>
                    <div class="rounded-[12px] p-[16px_14px] flex flex-col gap-[6px] cursor-pointer transition-all hover:-translate-y-0.5 hover:shadow-[0_6px_20px_rgba(0,0,0,.08)] relative overflow-hidden bg-gray-100 border border-gray-200">
                        <span class="text-[.62rem] font-bold uppercase tracking-[.1em] text-gray-400">Undertime</span>
                        <span class="font-['DM_Mono',monospace] text-[1.7rem] font-extrabold leading-none text-gray-900">{{ number_format($utTotal, 1) }}<span class="text-[.9rem] font-semibold text-gray-400">h</span></span>
                        <span class="text-[.72rem] font-semibold text-gray-400">deducted hours</span>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="flex justify-between text-[.68rem] font-bold uppercase tracking-[.08em] text-gray-400 mb-[6px]">
                        <span>OT share</span>
                        <span>{{ $otPct }}%</span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-100 overflow-hidden relative">
                        <div class="otut-balance-fill h-full rounded-full bg-gray-900" style="width:{{ $otPct }}%;"></div>
                    </div>
                </div>
                <div class="flex items-center justify-between px-[14px] py-3 bg-gray-100 rounded-[10px]">
                    <span class="text-[.72rem] font-bold uppercase tracking-[.08em] text-gray-400">Net balance</span>
                    <span class="font-['DM_Mono',monospace] text-[.95rem] font-extrabold {{ $netHours > 0 ? 'text-green-600' : ($netHours < 0 ? 'text-[#c8292a]' : 'text-gray-500') }}">
                        {{ $netHours >= 0 ? '+' : '' }}{{ number_format($netHours, 1) }}h
                    </span>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
<script>
(function () {
    /* ── Attendance trend interaction ─────────────────────── */
    function initTrend() {
        const cols   = document.querySelectorAll('#hrd-trend-grid > div');
        const label  = document.getElementById('hrd-trend-detail-label');
        const vP     = document.getElementById('hrd-td-present');
        const vL     = document.getElementById('hrd-td-late');
        const vA     = document.getElementById('hrd-td-absent');
        const vRate  = document.getElementById('hrd-td-rate');
        if (!cols.length || !label) return;

        function activate(col) {
            cols.forEach(c => c.classList.remove('bg-gray-100', '-translate-y-0.5'));
            col.classList.add('bg-gray-100', '-translate-y-0.5');

            const p     = parseInt(col.dataset.present, 10);
            const l     = parseInt(col.dataset.late,    10);
            const a     = parseInt(col.dataset.absent,  10);
            const total = p + l + a;
            const rate  = total > 0 ? Math.round((p / total) * 100) : 0;

            function countUp(el, target) {
                const start = parseInt(el.textContent, 10) || 0;
                if (start === target) return;
                const dur   = 220;
                const step  = 16;
                const steps = Math.ceil(dur / step);
                let   cur   = 0;
                const inc   = (target - start) / steps;
                const t = setInterval(() => {
                    cur++;
                    el.textContent = Math.round(start + inc * cur);
                    if (cur >= steps) { el.textContent = target; clearInterval(t); }
                }, step);
            }

            countUp(vP, p);
            countUp(vL, l);
            countUp(vA, a);
            label.textContent = col.dataset.label;
            vRate.textContent = rate + '% present';
        }

        cols.forEach(col => {
            col.addEventListener('mouseenter', () => activate(col));
            col.addEventListener('click',      () => activate(col));
        });
    }

    /* ── Leave mix interaction ────────────────────────────── */
    function initLeaveMix() {
        const rows = document.querySelectorAll('#lvm-rows > div');
        const segs = document.querySelectorAll('#lvm-track > div');
        if (!rows.length) return;

        function activate(key) {
            rows.forEach(r => {
                r.classList.toggle('bg-gray-100', r.dataset.key === key);
                if (r.dataset.key !== key) r.classList.remove('bg-gray-100');
            });
            segs.forEach(s => {
                s.classList.toggle('dimmed', s.dataset.key !== key);
            });
        }

        function reset() {
            rows.forEach(r => r.classList.remove('bg-gray-100'));
            segs.forEach(s => s.classList.remove('dimmed'));
        }

        rows.forEach(row => {
            row.addEventListener('mouseenter', () => activate(row.dataset.key));
            row.addEventListener('mouseleave', reset);
            row.addEventListener('click',      () => activate(row.dataset.key));
        });
        segs.forEach(seg => {
            seg.addEventListener('mouseenter', () => activate(seg.dataset.key));
            seg.addEventListener('mouseleave', reset);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => { initTrend(); initLeaveMix(); });
    } else {
        initTrend();
        initLeaveMix();
    }
})();
</script>
@endpush
