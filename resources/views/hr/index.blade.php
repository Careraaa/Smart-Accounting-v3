@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.hrd { font-family: 'Sora', sans-serif; }

/* -- Knight mascot inside hero -- */
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

/* -- Hero -- */
.hrd-hero {
    background: linear-gradient(135deg, #111827 0%, #0b1220 55%, #111827 100%);
    border-radius: 18px; padding: 22px 24px; margin-bottom: 22px;
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 18px; flex-wrap: wrap; position: relative; overflow: hidden;
}
.hrd-hero::before { content:''; position:absolute; top:-70px; right:-70px; width:260px; height:260px; border-radius:50%; background:rgba(200,41,42,0.18); pointer-events:none; }
.hrd-hero::after  { content:''; position:absolute; bottom:-90px; left:-90px; width:260px; height:260px; border-radius:50%; background:rgba(2,132,199,0.14); pointer-events:none; }
.hrd-hero-left { position:relative; z-index:1; min-width:240px; }
.hrd-hero h1 { font-size:1.25rem; font-weight:900; color:#fff; margin:0 0 6px; letter-spacing:-0.02em; }
.hrd-hero p  { margin:0; font-size:.82rem; color:#9ca3af; max-width:520px; line-height:1.5; }
.hrd-hero-actions { position:relative; z-index:1; display:flex; flex-wrap:wrap; gap:10px; align-items:center; }
.hrd-chip { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border:1px solid rgba(255,255,255,0.14); border-radius:999px; color:#e5e7eb; background:rgba(255,255,255,0.06); font-size:.75rem; }
.hrd-btn { display:inline-flex; align-items:center; gap:10px; padding:11px 18px; background:#c8292a; color:#fff; border:none; border-radius:12px; font-size:.86rem; font-weight:900; text-decoration:none; cursor:pointer; transition:background .15s,box-shadow .15s,transform .12s; box-shadow:0 4px 20px rgba(200,41,42,.5); white-space:nowrap; }
.hrd-btn:hover { background:#a81f20; color:#fff; box-shadow:0 10px 34px rgba(200,41,42,.62); transform:translateY(-1px); }
.hrd-btn-sec { display:inline-flex; align-items:center; gap:7px; padding:9px 14px; background:rgba(255,255,255,0.06); color:#e5e7eb; border:1px solid rgba(255,255,255,0.14); border-radius:10px; font-size:.82rem; font-weight:700; text-decoration:none; transition:background .15s,border-color .15s; }
.hrd-btn-sec:hover { background:rgba(255,255,255,0.10); border-color:rgba(255,255,255,0.22); color:#fff; }

/* -- KPI cards -- */
.hrd-kpis { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:22px; }
@media(max-width:900px) { .hrd-kpis { grid-template-columns:1fr; } }
.hrd-kpi { border:1px solid #e8e8ef; border-radius:14px; padding:16px 18px; background:#fff; }
.hrd-kpi-top { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:#9ca3af; margin-bottom:8px; }
.hrd-kpi-main { font-size:1.2rem; font-weight:800; color:#111827; font-family:'DM Mono',monospace; letter-spacing:-.02em; }
.hrd-kpi-note { font-size:.78rem; color:#6b7280; margin-top:8px; line-height:1.45; }

/* -- Chart panels -- */
.hrd-charts2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:28px; }
@media(max-width:900px)  { .hrd-charts2 { grid-template-columns:1fr; } }

.hrd-panel { border:1px solid #e8e8ef; border-radius:14px; background:#fff; overflow:hidden; }
.hrd-panel-hd { padding:14px 18px; border-bottom:1px solid #f3f4f6; display:flex; align-items:baseline; justify-content:space-between; gap:12px; flex-wrap:wrap; }
.hrd-panel-hd h2 { margin:0; font-size:.88rem; font-weight:700; color:#111827; letter-spacing:-.02em; }
.hrd-panel-hd span { font-size:.72rem; color:#9ca3af; font-weight:500; }
.hrd-panel-bd { padding:8px 12px 4px; }
.hrd-chart { min-height:260px; }

/* -- Leave mix card -- */
.lvm-body { padding:22px 20px 18px; }
.lvm-total { display:flex; align-items:baseline; gap:8px; margin-bottom:18px; }
.lvm-total-num { font-family:'DM Mono',monospace; font-size:2rem; font-weight:800; color:#111827; line-height:1; }
.lvm-total-lbl { font-size:.72rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.08em; }
.lvm-track { height:10px; border-radius:999px; background:#f3f4f6; overflow:hidden; display:flex; margin-bottom:20px; gap:2px; }
.lvm-seg {
    height:100%; border-radius:999px;
    transition:width .6s cubic-bezier(.4,0,.2,1), opacity .2s, filter .2s;
    cursor:pointer; position:relative;
}
.lvm-seg:hover { filter:brightness(1.12); }
.lvm-seg.dimmed { opacity:.25; }
.lvm-rows { display:flex; flex-direction:column; gap:10px; }
.lvm-row {
    display:flex; align-items:center; gap:10px;
    padding:10px 12px; border-radius:10px; cursor:pointer;
    transition:background .15s;
}
.lvm-row:hover { background:#f8f9fb; }
.lvm-row.active { background:#f3f4f6; }
.lvm-row-dot { width:10px; height:10px; border-radius:3px; flex-shrink:0; }
.lvm-row-label { font-size:.82rem; font-weight:600; color:#374151; flex:1; }
.lvm-row-count { font-family:'DM Mono',monospace; font-size:.88rem; font-weight:700; color:#111827; }
.lvm-row-pct { font-size:.72rem; color:#9ca3af; font-weight:600; min-width:36px; text-align:right; }

/* -- OT/UT card -- */
.otut-body { padding:22px 20px 20px; }
.otut-split { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:20px; }
.otut-side {
    border-radius:12px; padding:16px 14px;
    display:flex; flex-direction:column; gap:6px;
    cursor:pointer; transition:transform .15s, box-shadow .15s;
    position:relative; overflow:hidden;
}
.otut-side:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,.08); }
.otut-side.ot { background:#111827; }
.otut-side.ut { background:#f8f9fb; border:1px solid #e5e7eb; }
.otut-side-eyebrow { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; }
.otut-side.ot .otut-side-eyebrow { color:#6b7280; }
.otut-side.ut .otut-side-eyebrow { color:#9ca3af; }
.otut-side-val { font-family:'DM Mono',monospace; font-size:1.7rem; font-weight:800; line-height:1; }
.otut-side.ot .otut-side-val { color:#fff; }
.otut-side.ut .otut-side-val { color:#111827; }
.otut-side-sub { font-size:.72rem; font-weight:600; }
.otut-side.ot .otut-side-sub { color:#4b5563; }
.otut-side.ut .otut-side-sub { color:#9ca3af; }
.otut-side-glow {
    position:absolute; top:-30px; right:-30px;
    width:90px; height:90px; border-radius:50%;
    background:rgba(200,41,42,.18); pointer-events:none;
}
/* balance bar */
.otut-balance { margin-bottom:16px; }
.otut-balance-lbl { display:flex; justify-content:space-between; font-size:.68rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.08em; margin-bottom:6px; }
.otut-balance-track { height:8px; border-radius:999px; background:#f3f4f6; overflow:hidden; position:relative; }
.otut-balance-fill {
    height:100%; border-radius:999px; background:#111827;
    transition:width .7s cubic-bezier(.4,0,.2,1);
}
/* net badge */
.otut-net { display:flex; align-items:center; justify-content:space-between; padding:12px 14px; background:#f8f9fb; border-radius:10px; }
.otut-net-lbl { font-size:.72rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.08em; }
.otut-net-val { font-family:'DM Mono',monospace; font-size:.95rem; font-weight:800; }
.otut-net-val.positive { color:#16a34a; }
.otut-net-val.negative { color:#c8292a; }
.otut-net-val.neutral  { color:#6b7280; }

/* -- Attendance trend -- */
.hrd-trend-wrap { padding:20px 20px 0; }
.hrd-trend { display:grid; grid-template-columns:repeat(7,1fr); gap:8px; }
.hrd-trend-col {
    display:flex; flex-direction:column; align-items:center; gap:7px;
    padding:10px 6px 12px; border-radius:12px; cursor:pointer;
    transition:background .15s, transform .15s;
    position:relative;
}
.hrd-trend-col:hover { background:#f8f9fb; transform:translateY(-2px); }
.hrd-trend-col.active { background:#f3f4f6; transform:translateY(-2px); }
.hrd-trend-col.active .hrd-trend-day  { color:#111827; }
.hrd-trend-col.active .hrd-trend-date { color:#6b7280; }
.hrd-trend-bars { display:flex; align-items:flex-end; gap:4px; height:110px; }
.hrd-trend-bar {
    width:12px; border-radius:5px 5px 0 0;
    transition:height .35s cubic-bezier(.4,0,.2,1), opacity .15s, filter .15s;
}
.hrd-trend-col:hover .hrd-trend-bar,
.hrd-trend-col.active .hrd-trend-bar { filter:brightness(1.08); }
.hrd-trend-bar.present { background:#111827; }
.hrd-trend-bar.late    { background:#f59e0b; }
.hrd-trend-bar.absent  { background:#e5e7eb; }
.hrd-trend-day  { font-size:.68rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.07em; }
.hrd-trend-date { font-size:.63rem; color:#d1d5db; }
/* today column */
.hrd-trend-today .hrd-trend-day  { color:#111827; }
.hrd-trend-today .hrd-trend-date { color:#9ca3af; }
.hrd-trend-today .hrd-trend-bar.present { background:#c8292a; }
.hrd-trend-today-dot {
    width:5px; height:5px; border-radius:50%; background:#c8292a;
    position:absolute; top:6px;
}
/* detail strip */
.hrd-trend-detail {
    margin:14px 20px 0; padding:14px 18px;
    background:#f8f9fb; border-radius:10px;
    display:flex; align-items:center; gap:0;
    transition:opacity .2s;
}
.hrd-trend-detail-label {
    font-size:.72rem; font-weight:700; color:#9ca3af;
    text-transform:uppercase; letter-spacing:.08em;
    min-width:110px;
}
.hrd-trend-detail-stats { display:flex; gap:24px; flex:1; }
.hrd-trend-detail-stat { display:flex; flex-direction:column; gap:2px; }
.hrd-trend-detail-val {
    font-family:'DM Mono',monospace; font-size:1.1rem;
    font-weight:800; line-height:1;
}
.hrd-trend-detail-val.present { color:#111827; }
.hrd-trend-detail-val.late    { color:#d97706; }
.hrd-trend-detail-val.absent  { color:#9ca3af; }
.hrd-trend-detail-key { font-size:.65rem; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.06em; }
.hrd-trend-detail-rate {
    margin-left:auto; font-family:'DM Mono',monospace;
    font-size:.82rem; font-weight:700; color:#6b7280;
    background:#fff; border:1px solid #e5e7eb;
    padding:4px 10px; border-radius:8px;
}
/* legend */
.hrd-trend-legend { display:flex; gap:16px; padding:14px 20px; border-top:1px solid #f3f4f6; margin-top:14px; }
.hrd-trend-legend-item { display:flex; align-items:center; gap:6px; font-size:.71rem; font-weight:600; color:#6b7280; }
.hrd-trend-legend-dot { width:9px; height:9px; border-radius:3px; }
@media(max-width:700px) { .hrd-trend { grid-template-columns:repeat(4,1fr); } }

/* -- Feed -- */
.hrd-feed { margin:0; padding:0; list-style:none; }
.hrd-feed li { border-top:1px solid #f3f4f6; }
.hrd-feed li:first-child { border-top:none; }
.hrd-feed a { display:flex; align-items:flex-start; gap:14px; padding:14px 18px; text-decoration:none; color:inherit; transition:background .12s; }
.hrd-feed a:hover { background:#fafafa; }
.hrd-av { width:40px; height:40px; border-radius:12px; background:#f4f4f6; color:#6b7280; font-size:.72rem; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1px solid #ececec; }
.hrd-feed-body { flex:1; min-width:0; }
.hrd-feed-title { font-size:.84rem; font-weight:700; color:#111827; margin:0 0 4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.hrd-feed-meta  { font-size:.74rem; color:#6b7280; margin:0; line-height:1.45; }
.hrd-feed-meta code { font-family:'DM Mono',monospace; font-size:.72rem; background:#f8fafc; padding:1px 6px; border-radius:4px; color:#64748b; }
.hrd-feed-right { display:flex; flex-direction:column; align-items:flex-end; gap:6px; flex-shrink:0; }
.hrd-pill { font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; padding:4px 10px; border-radius:999px; border:1px solid transparent; }
.hrd-pill-pending  { background:#fffbeb; border-color:#fde68a; color:#b45309; }
.hrd-pill-approved { background:#f0fdf4; border-color:#bbf7d0; color:#15803d; }
.hrd-pill-rejected { background:#fef2f2; border-color:#fecaca; color:#b91c1c; }
.hrd-pill-present  { background:#f0fdf4; border-color:#bbf7d0; color:#15803d; }
.hrd-pill-late     { background:#fffbeb; border-color:#fde68a; color:#b45309; }
.hrd-pill-absent   { background:#fef2f2; border-color:#fecaca; color:#b91c1c; }
.hrd-chevron { color:#d1d5db; font-size:18px; margin-top:2px; }
.hrd-empty { text-align:center; padding:40px 20px; color:#9ca3af; font-size:.84rem; }

.hrd-layout {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}

.hrd-main {
    flex: 1;
    min-width: 0;
}

/* Right sidebar */
.hrd-sidebar {
    width: 280px;
    flex-shrink: 0;
    position: sticky;
}

/* Responsive */
@media (max-width: 1200px) {
    .hrd-layout {
        flex-direction: column;
    }

    .hrd-sidebar {
        width: 100%;
        position: static;
    }
}
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
<div class="hrd-layout">
    {{-- Main Dashboard --}}
    <div class="hrd-main">

        <header class="hrd-hero">
            <img src="{{ asset('images/landscape-knight.png') }}" alt="" class="hrd-hero-knight" aria-hidden="true">
            <div class="hrd-hero-left">
                <h1>HR Dashboard</h1>
                <p>Workforce, attendance, and leave — charts use live data from your <strong style="color:#e5e7eb;">attendance</strong> and <strong style="color:#e5e7eb;">users</strong> tables.</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="hrd-chip"><i class="feather-calendar"></i> {{ now()->format('l, F d, Y') }}</span>
                    <span class="hrd-chip"><i class="feather-users"></i> {{ $activeEmployees }} active / {{ $totalEmployees }} total</span>
                    <span class="hrd-chip"><i class="feather-activity"></i> {{ number_format($attendanceRate, 1) }}% today check-in</span>
                </div>
            </div>
            <div class="hrd-hero-actions">
                <a href="{{ route('employees.index') }}" class="hrd-btn-sec"><i class="feather-users"></i> Employees</a>
                <a href="{{ route('attendance.create') }}" class="hrd-btn-sec"><i class="feather-edit-3"></i> Manual log</a>
                <a href="{{ route('leave.create') }}" class="hrd-btn"><i class="feather-plus"></i> Create leave</a>
            </div>
        </header>

        <div class="hrd-kpis">
            <div class="hrd-kpi">
                <div class="hrd-kpi-top">Workforce</div>
                <div class="hrd-kpi-main">{{ $activeEmployees }} <span style="color:#9ca3af;font-weight:600;font-size:.95rem;">/ {{ $totalEmployees }}</span></div>
                <div class="hrd-kpi-note">{{ $inactiveEmployees }} inactive · {{ $onLeaveEmployees }} on leave today</div>
            </div>
            <div class="hrd-kpi">
                <div class="hrd-kpi-top">Today</div>
                <div class="hrd-kpi-main">{{ $presentToday + $lateToday }}<span style="color:#9ca3af;font-weight:600;font-size:.85rem;"> in</span></div>
                <div class="hrd-kpi-note">Absent {{ $absentToday }} · Late {{ $lateToday }} · {{ number_format($attendanceRate, 1) }}% check-in rate</div>
            </div>
            <div class="hrd-kpi">
                <div class="hrd-kpi-top">Leave (all time)</div>
                <div class="hrd-kpi-main">{{ $pendingLeaves }}<span style="color:#9ca3af;font-weight:600;font-size:.85rem;"> pending</span></div>
                <div class="hrd-kpi-note">{{ $approvedLeaves }} approved · {{ $rejectedLeaves }} rejected</div>
            </div>
        </div>

        <div class="hrd-charts2" style="margin-top:14px;">
            <div class="hrd-panel">
                <div class="hrd-panel-hd">
                    <h2>Attendance trend</h2>
                    <span>Last 7 days · present / late / absent</span>
                </div>
                @php
                    $trendMax = collect($attendanceTrend)->map(fn($d) => ($d['present'] + $d['late'] + $d['absent']))->max() ?: 1;
                    $todayIso = now()->toDateString();
                    $todayIdx = collect($attendanceTrend)->search(fn($d) => $d['date_iso'] === $todayIso);
                    $todayIdx = $todayIdx === false ? count($attendanceTrend) - 1 : $todayIdx;
                @endphp
                <div class="hrd-trend-wrap">
                    <div class="hrd-trend" id="hrd-trend-grid">
                        @foreach($attendanceTrend as $i => $day)
                            @php
                                $pxMax   = 110;
                                $pH      = $trendMax > 0 ? max(5, round(($day['present'] / $trendMax) * $pxMax)) : 5;
                                $lH      = $trendMax > 0 ? max(5, round(($day['late']    / $trendMax) * $pxMax)) : 5;
                                $aH      = $trendMax > 0 ? max(5, round(($day['absent']  / $trendMax) * $pxMax)) : 5;
                                $isToday = $day['date_iso'] === $todayIso;
                                [$dow, $md] = explode(' · ', $day['label']);
                            @endphp
                            <div class="hrd-trend-col {{ $isToday ? 'hrd-trend-today' : '' }} {{ $i === $todayIdx ? 'active' : '' }}"
                                 data-present="{{ $day['present'] }}"
                                 data-late="{{ $day['late'] }}"
                                 data-absent="{{ $day['absent'] }}"
                                 data-label="{{ $dow }}, {{ $md }}"
                                 data-total="{{ $day['present'] + $day['late'] + $day['absent'] }}"
                                 data-idx="{{ $i }}">
                                @if($isToday)<div class="hrd-trend-today-dot"></div>@endif
                                <div class="hrd-trend-bars">
                                    <div class="hrd-trend-bar present" style="height:{{ $pH }}px;"></div>
                                    <div class="hrd-trend-bar late"    style="height:{{ $lH }}px;"></div>
                                    <div class="hrd-trend-bar absent"  style="height:{{ $aH }}px;"></div>
                                </div>
                                <span class="hrd-trend-day">{{ $dow }}</span>
                                <span class="hrd-trend-date">{{ $md }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Detail strip --}}
                @php $d = $attendanceTrend[$todayIdx]; $dTotal = $d['present'] + $d['late'] + $d['absent']; @endphp
                <div class="hrd-trend-detail" id="hrd-trend-detail">
                    <span class="hrd-trend-detail-label" id="hrd-trend-detail-label">
                        {{ collect($attendanceTrend[$todayIdx]['label'])->implode('') }}
                    </span>
                    <div class="hrd-trend-detail-stats">
                        <div class="hrd-trend-detail-stat">
                            <span class="hrd-trend-detail-val present" id="hrd-td-present">{{ $d['present'] }}</span>
                            <span class="hrd-trend-detail-key">Present</span>
                        </div>
                        <div class="hrd-trend-detail-stat">
                            <span class="hrd-trend-detail-val late" id="hrd-td-late">{{ $d['late'] }}</span>
                            <span class="hrd-trend-detail-key">Late</span>
                        </div>
                        <div class="hrd-trend-detail-stat">
                            <span class="hrd-trend-detail-val absent" id="hrd-td-absent">{{ $d['absent'] }}</span>
                            <span class="hrd-trend-detail-key">Absent</span>
                        </div>
                    </div>
                    <span class="hrd-trend-detail-rate" id="hrd-td-rate">
                        {{ $dTotal > 0 ? number_format(($d['present'] / $dTotal) * 100, 0) : 0 }}% present
                    </span>
                </div>

                <div class="hrd-trend-legend">
                    <div class="hrd-trend-legend-item"><div class="hrd-trend-legend-dot" style="background:#111827;"></div> Present</div>
                    <div class="hrd-trend-legend-item"><div class="hrd-trend-legend-dot" style="background:#f59e0b;"></div> Late</div>
                    <div class="hrd-trend-legend-item"><div class="hrd-trend-legend-dot" style="background:#e5e7eb;border:1px solid #d1d5db;"></div> Absent</div>
                    <div class="hrd-trend-legend-item" style="margin-left:auto;"><div class="hrd-trend-legend-dot" style="background:#c8292a;"></div> Today</div>
                </div>
            </div>

            <div class="hrd-panel">
                <div class="hrd-panel-hd">
                    <h2>Leave mix</h2>
                    <span>All-time request totals</span>
                </div>
                @php
                    $lvTotal = $pendingLeaves + $approvedLeaves + $rejectedLeaves;
                    $lvPct   = fn($n) => $lvTotal > 0 ? round(($n / $lvTotal) * 100) : 0;
                @endphp
                <div class="lvm-body">
                    <div class="lvm-total">
                        <span class="lvm-total-num" id="lvm-total-num">{{ $lvTotal }}</span>
                        <span class="lvm-total-lbl">total requests</span>
                    </div>
                    <div class="lvm-track" id="lvm-track">
                        <div class="lvm-seg" id="lvm-seg-approved"
                             style="width:{{ $lvPct($approvedLeaves) }}%; background:#22c55e;"
                             data-key="approved" title="Approved: {{ $approvedLeaves }}"></div>
                        <div class="lvm-seg" id="lvm-seg-pending"
                             style="width:{{ $lvPct($pendingLeaves) }}%; background:#f59e0b;"
                             data-key="pending" title="Pending: {{ $pendingLeaves }}"></div>
                        <div class="lvm-seg" id="lvm-seg-rejected"
                             style="width:{{ $lvPct($rejectedLeaves) }}%; background:#f43f5e;"
                             data-key="rejected" title="Rejected: {{ $rejectedLeaves }}"></div>
                    </div>
                    <div class="lvm-rows" id="lvm-rows">
                        <div class="lvm-row active" data-key="approved">
                            <div class="lvm-row-dot" style="background:#22c55e;"></div>
                            <span class="lvm-row-label">Approved</span>
                            <span class="lvm-row-count">{{ $approvedLeaves }}</span>
                            <span class="lvm-row-pct">{{ $lvPct($approvedLeaves) }}%</span>
                        </div>
                        <div class="lvm-row" data-key="pending">
                            <div class="lvm-row-dot" style="background:#f59e0b;"></div>
                            <span class="lvm-row-label">Pending</span>
                            <span class="lvm-row-count">{{ $pendingLeaves }}</span>
                            <span class="lvm-row-pct">{{ $lvPct($pendingLeaves) }}%</span>
                        </div>
                        <div class="lvm-row" data-key="rejected">
                            <div class="lvm-row-dot" style="background:#f43f5e;"></div>
                            <span class="lvm-row-label">Rejected</span>
                            <span class="lvm-row-count">{{ $rejectedLeaves }}</span>
                            <span class="lvm-row-pct">{{ $lvPct($rejectedLeaves) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="hrd-panel" style="margin-bottom:14px;">
            <div class="hrd-panel-hd">
                <h2>Overtime / Undertime</h2>
                <span>Approved hours · all time</span>
            </div>
            @php
                $otTotal  = (float) $totalOvertimeHours;
                $utTotal  = (float) $totalUndertimeHours;
                $otutSum  = $otTotal + $utTotal;
                $otPct    = $otutSum > 0 ? round(($otTotal / $otutSum) * 100) : 50;
                $netHours = $otTotal - $utTotal;
            @endphp
            <div class="otut-body">
                <div class="otut-split">
                    <div class="otut-side ot">
                        <div class="otut-side-glow"></div>
                        <span class="otut-side-eyebrow">Overtime</span>
                        <span class="otut-side-val">{{ number_format($otTotal, 1) }}<span style="font-size:.9rem;font-weight:600;color:#4b5563;">h</span></span>
                        <span class="otut-side-sub">{{ $totalOvertimeRecords }} approved records</span>
                    </div>
                    <div class="otut-side ut">
                        <span class="otut-side-eyebrow">Undertime</span>
                        <span class="otut-side-val">{{ number_format($utTotal, 1) }}<span style="font-size:.9rem;font-weight:600;color:#9ca3af;">h</span></span>
                        <span class="otut-side-sub">deducted hours</span>
                    </div>
                </div>
                <div class="otut-balance">
                    <div class="otut-balance-lbl">
                        <span>OT share</span>
                        <span>{{ $otPct }}%</span>
                    </div>
                    <div class="otut-balance-track">
                        <div class="otut-balance-fill" style="width:{{ $otPct }}%;"></div>
                    </div>
                </div>
                <div class="otut-net">
                    <span class="otut-net-lbl">Net balance</span>
                    <span class="otut-net-val {{ $netHours > 0 ? 'positive' : ($netHours < 0 ? 'negative' : 'neutral') }}">
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
        const cols   = document.querySelectorAll('#hrd-trend-grid .hrd-trend-col');
        const label  = document.getElementById('hrd-trend-detail-label');
        const vP     = document.getElementById('hrd-td-present');
        const vL     = document.getElementById('hrd-td-late');
        const vA     = document.getElementById('hrd-td-absent');
        const vRate  = document.getElementById('hrd-td-rate');
        if (!cols.length || !label) return;

        function activate(col) {
            cols.forEach(c => c.classList.remove('active'));
            col.classList.add('active');

            const p     = parseInt(col.dataset.present, 10);
            const l     = parseInt(col.dataset.late,    10);
            const a     = parseInt(col.dataset.absent,  10);
            const total = p + l + a;
            const rate  = total > 0 ? Math.round((p / total) * 100) : 0;

            /* animate numbers */
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
        const rows = document.querySelectorAll('#lvm-rows .lvm-row');
        const segs = document.querySelectorAll('#lvm-track .lvm-seg');
        if (!rows.length) return;

        function activate(key) {
            rows.forEach(r => {
                r.classList.toggle('active', r.dataset.key === key);
            });
            segs.forEach(s => {
                s.classList.toggle('dimmed', s.dataset.key !== key);
            });
        }

        function reset() {
            rows.forEach(r => r.classList.remove('active'));
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

    /* ── OT/UT hover lift (CSS handles it, nothing extra needed) -- */

    /* ── ApexCharts (none remaining) ─────────────────────── */
    function initHrdCharts() {
        /* ApexCharts no longer used on this page */
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => { initTrend(); initLeaveMix(); initHrdCharts(); });
    } else {
        initTrend();
        initLeaveMix();
        initHrdCharts();
    }
})();
</script>
@endpush
