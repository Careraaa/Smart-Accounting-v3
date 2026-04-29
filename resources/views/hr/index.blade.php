@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.hrd{font-family:'Sora',sans-serif;--hrd-border:#e8e8ef;--hrd-muted:#9ca3af;--hrd-ink:#111827;--hrd-red:#c8292a;position:relative}
/* Match employee dashboard: soft backdrop + grid */
.hrd-backdrop{position:absolute;inset:-40px -20px auto -20px;height:340px;pointer-events:none;z-index:0;background:
    radial-gradient(220px 220px at 10% 35%, rgba(200,41,42,0.14), transparent 60%),
    radial-gradient(260px 260px at 85% 10%, rgba(2,132,199,0.12), transparent 60%),
    radial-gradient(240px 240px at 70% 70%, rgba(22,163,74,0.10), transparent 60%),
    linear-gradient(to bottom, rgba(17,24,39,0.04), transparent 70%);filter:saturate(110%)}
.hrd-grid{position:absolute;inset:0;background-image:linear-gradient(to right, rgba(17,24,39,0.06) 1px, transparent 1px),linear-gradient(to bottom, rgba(17,24,39,0.06) 1px, transparent 1px);background-size:48px 48px;mask-image:radial-gradient(closest-side at 50% 30%, rgba(0,0,0,0.75), transparent 80%);opacity:.5}
.hrd-inner{position:relative;z-index:1}

/* Dark hero bar — same language as employee edb-hero */
.hrd-hero{background:linear-gradient(135deg,#111827 0%,#0b1220 55%,#111827 100%);border-radius:18px;padding:22px 24px;margin-bottom:22px;display:flex;align-items:flex-start;justify-content:space-between;gap:18px;flex-wrap:wrap;position:relative;overflow:hidden}
.hrd-hero:before{content:'';position:absolute;top:-70px;right:-70px;width:260px;height:260px;border-radius:50%;background:rgba(200,41,42,0.18);pointer-events:none}
.hrd-hero:after{content:'';position:absolute;bottom:-90px;left:-90px;width:260px;height:260px;border-radius:50%;background:rgba(2,132,199,0.14);pointer-events:none}
.hrd-hero-left{position:relative;z-index:1;min-width:240px}
.hrd-hero h1{font-size:1.25rem;font-weight:900;color:#fff;margin:0 0 6px;letter-spacing:-0.02em}
.hrd-hero p{margin:0;font-size:.82rem;color:#9ca3af;max-width:520px;line-height:1.5}
.hrd-hero-actions{position:relative;z-index:1;display:flex;flex-wrap:wrap;gap:10px;align-items:center}
.hrd-chip{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border:1px solid rgba(255,255,255,0.14);border-radius:999px;color:#e5e7eb;background:rgba(255,255,255,0.06);font-size:.75rem}
.hrd-btn{display:inline-flex;align-items:center;gap:10px;padding:11px 18px;background:#c8292a;color:#fff;border:none;border-radius:12px;font-size:.86rem;font-weight:900;text-decoration:none;cursor:pointer;transition:background .15s,box-shadow .15s,transform .12s;box-shadow:0 4px 20px rgba(200,41,42,.5);white-space:nowrap}
.hrd-btn:hover{background:#a81f20;color:#fff;box-shadow:0 10px 34px rgba(200,41,42,.62);transform:translateY(-1px)}
.hrd-btn-sec{display:inline-flex;align-items:center;gap:7px;padding:9px 14px;background:rgba(255,255,255,0.06);color:#e5e7eb;border:1px solid rgba(255,255,255,0.14);border-radius:10px;font-size:.82rem;font-weight:700;text-decoration:none;transition:background .15s,border-color .15s}
.hrd-btn-sec:hover{background:rgba(255,255,255,0.10);border-color:rgba(255,255,255,0.22);color:#fff}

.hrd-kpis{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:22px}
@media (max-width:900px){.hrd-kpis{grid-template-columns:1fr}}
.hrd-kpi{border:1px solid var(--hrd-border);border-radius:14px;padding:16px 18px;background:#fff}
.hrd-kpi-top{font-size:.62rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--hrd-muted);margin-bottom:8px}
.hrd-kpi-main{font-size:1.2rem;font-weight:800;color:var(--hrd-ink);font-family:'DM Mono',monospace;letter-spacing:-.02em}
.hrd-kpi-note{font-size:.78rem;color:#6b7280;margin-top:8px;line-height:1.45}

.hrd-charts{display:grid;grid-template-columns:1.55fr 1fr;gap:14px;margin-bottom:14px}
@media (max-width:1100px){.hrd-charts{grid-template-columns:1fr}}
.hrd-charts2{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:28px}
@media (max-width:900px){.hrd-charts2{grid-template-columns:1fr}}

.hrd-panel{border:1px solid var(--hrd-border);border-radius:14px;background:#fff;overflow:hidden}
.hrd-panel-hd{padding:14px 18px;border-bottom:1px solid #f3f4f6;display:flex;align-items:baseline;justify-content:space-between;gap:12px;flex-wrap:wrap}
.hrd-panel-hd h2{margin:0;font-size:.88rem;font-weight:700;color:var(--hrd-ink);letter-spacing:-.02em}
.hrd-panel-hd span{font-size:.72rem;color:var(--hrd-muted);font-weight:500}
.hrd-panel-bd{padding:8px 12px 4px}
.hrd-chart{min-height:260px}

.hrd-feed{margin:0;padding:0;list-style:none}
.hrd-feed li{border-top:1px solid #f3f4f6}
.hrd-feed li:first-child{border-top:none}
.hrd-feed a{display:flex;align-items:flex-start;gap:14px;padding:14px 18px;text-decoration:none;color:inherit;transition:background .12s}
.hrd-feed a:hover{background:#fafafa}
.hrd-av{width:40px;height:40px;border-radius:12px;background:#f4f4f6;color:#6b7280;font-size:.72rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid #ececec}
.hrd-feed-body{flex:1;min-width:0}
.hrd-feed-title{font-size:.84rem;font-weight:700;color:var(--hrd-ink);margin:0 0 4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.hrd-feed-meta{font-size:.74rem;color:#6b7280;margin:0;line-height:1.45}
.hrd-feed-meta code{font-family:'DM Mono',monospace;font-size:.72rem;background:#f8fafc;padding:1px 6px;border-radius:4px;color:#64748b}
.hrd-feed-right{display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0}
.hrd-pill{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;padding:4px 10px;border-radius:999px;border:1px solid transparent}
.hrd-pill-pending{background:#fffbeb;border-color:#fde68a;color:#b45309}
.hrd-pill-approved{background:#f0fdf4;border-color:#bbf7d0;color:#15803d}
.hrd-pill-rejected{background:#fef2f2;border-color:#fecaca;color:#b91c1c}
.hrd-pill-present{background:#f0fdf4;border-color:#bbf7d0;color:#15803d}
.hrd-pill-late{background:#fffbeb;border-color:#fde68a;color:#b45309}
.hrd-pill-absent{background:#fef2f2;border-color:#fecaca;color:#b91c1c}
.hrd-chevron{color:#d1d5db;font-size:18px;margin-top:2px}

.hrd-empty{text-align:center;padding:40px 20px;color:var(--hrd-muted);font-size:.84rem}
.hrd-empty svg{opacity:.35;margin-bottom:8px}
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
<div class="col-md-12 hrd">
    <div class="hrd-backdrop"><div class="hrd-grid"></div></div>
    <div class="hrd-inner">

        <header class="hrd-hero">
            <div class="hrd-hero-left">
                <h1>HR dashboard</h1>
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
                <div class="hrd-kpi-main">{{ $activeEmployees }} <span style="color:var(--hrd-muted);font-weight:600;font-size:.95rem;">/ {{ $totalEmployees }}</span></div>
                <div class="hrd-kpi-note">{{ $inactiveEmployees }} inactive · {{ $onLeaveEmployees }} on leave today</div>
            </div>
            <div class="hrd-kpi">
                <div class="hrd-kpi-top">Today</div>
                <div class="hrd-kpi-main">{{ $presentToday + $lateToday }}<span style="color:var(--hrd-muted);font-weight:600;font-size:.85rem;"> in</span></div>
                <div class="hrd-kpi-note">Absent {{ $absentToday }} · Late {{ $lateToday }} · {{ number_format($attendanceRate, 1) }}% check-in rate</div>
            </div>
            <div class="hrd-kpi">
                <div class="hrd-kpi-top">Leave (all time)</div>
                <div class="hrd-kpi-main">{{ $pendingLeaves }}<span style="color:var(--hrd-muted);font-weight:600;font-size:.85rem;"> pending</span></div>
                <div class="hrd-kpi-note">{{ $approvedLeaves }} approved · {{ $rejectedLeaves }} rejected</div>
            </div>
        </div>

        <div class="hrd-charts">
            <div class="hrd-panel">
                <div class="hrd-panel-hd">
                    <h2>Attendance trend</h2>
                    <span>Daily counts from <code style="font-family:'DM Mono',monospace;font-size:.68rem;background:#f8fafc;padding:2px 6px;border-radius:4px;">attendances</code> · present / late / absent</span>
                </div>
                <div class="hrd-panel-bd">
                    <div id="hrd-chart-attendance" class="hrd-chart"></div>
                </div>
            </div>
            <div class="hrd-panel">
                <div class="hrd-panel-hd">
                    <h2>Workforce</h2>
                    <span>Active vs inactive vs on leave</span>
                </div>
                <div class="hrd-panel-bd">
                    <div id="hrd-chart-workforce" class="hrd-chart" style="min-height:280px;"></div>
                </div>
            </div>
        </div>

        <div class="hrd-charts2">
            <div class="hrd-panel">
                <div class="hrd-panel-hd">
                    <h2>Leave mix</h2>
                    <span>All-time request totals</span>
                </div>
                <div class="hrd-panel-bd">
                    <div id="hrd-chart-leaves" class="hrd-chart"></div>
                </div>
            </div>
            <div class="hrd-panel">
                <div class="hrd-panel-hd">
                    <h2>OT / UT</h2>
                    <span>Total hours in system</span>
                </div>
                <div class="hrd-panel-bd">
                    <div id="hrd-chart-otut" class="hrd-chart"></div>
                </div>
            </div>
        </div>

        {{-- Recent leave — custom feed (no Bootstrap table) --}}
        <div class="hrd-panel" style="margin-bottom:14px;">
            <div class="hrd-panel-hd">
                <h2>Recent Pending Leave Requests</h2>
                <a href="{{ route('leave.pending') }}" style="font-size:.78rem;font-weight:700;color:var(--hrd-red);text-decoration:none;">View all →</a>
            </div>
            @if($recentLeaves->isEmpty())
                <div class="hrd-empty"><i class="feather-inbox" style="font-size:32px;display:block;"></i>No leave requests yet.</div>
            @else
                <ul class="hrd-feed">
                    @foreach($recentLeaves as $leave)
                        @if(($leave->status ?? '') === 'pending')
                            @php
                                $st = $leave->status ?? '';
                                $pillClass = match($st) {
                                    'approved' => 'hrd-pill-approved',
                                    'rejected' => 'hrd-pill-rejected',
                                    'pending' => 'hrd-pill-pending',
                                    default => 'hrd-pill-pending',
                                };
                            @endphp
                            <li>
                                <a href="{{ route('leave.show', $leave) }}">
                                    <div class="hrd-av">{{ $initials($leave->employee->name ?? '') }}</div>
                                    <div class="hrd-feed-body">
                                        <p class="hrd-feed-title">{{ $leave->employee->name ?? 'Unknown' }}</p>
                                        <p class="hrd-feed-meta">
                                            {{ ucfirst($leave->leave_type ?? 'Leave') }}
                                            · {{ $leave->start_date?->format('M j') ?? '—' }} – {{ $leave->end_date?->format('M j, Y') ?? '—' }}
                                            @if(isset($leave->duration_days)) · <code>{{ $leave->duration_days }}d</code>@endif
                                        </p>
                                    </div>
                                    <div class="hrd-feed-right">
                                        <span class="hrd-pill {{ $pillClass }}">{{ ucfirst($st) }}</span>
                                        <i class="feather-chevron-right hrd-chevron"></i>
                                    </div>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Recent attendance — custom feed --}}
        <div class="hrd-panel" style="margin-bottom:8px;">
            <div class="hrd-panel-hd">
                <h2>Recent attendance</h2>
                <a href="{{ route('attendance.index') }}" style="font-size:.78rem;font-weight:700;color:var(--hrd-red);text-decoration:none;">View all →</a>
            </div>
            @if($recentAttendance->isEmpty())
                <div class="hrd-empty"><i class="feather-clock" style="font-size:32px;display:block;"></i>No attendance rows yet.</div>
            @else
                <ul class="hrd-feed">
                    @foreach($recentAttendance as $att)
                        @php
                            $st = $att->status ?? '';
                            $pillClass = match($st) {
                                'present' => 'hrd-pill-present',
                                'late' => 'hrd-pill-late',
                                'absent' => 'hrd-pill-absent',
                                default => 'hrd-pill-pending',
                            };
                            $tIn = $att->time_in ? \Carbon\Carbon::parse($att->time_in)->format('g:i A') : '—';
                            $tOut = $att->time_out ? \Carbon\Carbon::parse($att->time_out)->format('g:i A') : '—';
                        @endphp
                        <li>
                            <a href="{{ route('attendance.show', $att) }}">
                                <div class="hrd-av">{{ $initials($att->employee->name ?? '') }}</div>
                                <div class="hrd-feed-body">
                                    <p class="hrd-feed-title">{{ $att->employee->name ?? 'Unknown' }}</p>
                                    <p class="hrd-feed-meta">
                                        {{ $att->date?->format('D, M j, Y') ?? '—' }}
                                        · In <code>{{ $tIn }}</code> · Out <code>{{ $tOut }}</code>
                                    </p>
                                </div>
                                <div class="hrd-feed-right">
                                    <span class="hrd-pill {{ $pillClass }}">{{ ucfirst($st) }}</span>
                                    <i class="feather-chevron-right hrd-chevron"></i>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    if (typeof ApexCharts === 'undefined') return;

    const font = 'Sora, sans-serif';
    const mono = "'DM Mono', monospace";
    const red = '#c8292a';
    const ink = '#111827';
    const muted = '#94a3b8';
    const soft = '#cbd5e1';

    // attendanceTrend: daily present, late, absent counts from the database
    const trend = @json($attendanceTrend);
    const categories = trend.map(function (t) { return t.label || t.date_iso; });

    new ApexCharts(document.querySelector('#hrd-chart-attendance'), {
        chart: {
            type: 'line',
            height: 280,
            fontFamily: font,
            toolbar: { show: false },
            zoom: { enabled: false },
            animations: { enabled: true, speed: 400 },
            parentHeightOffset: 0
        },
        series: [
            { name: 'Present', data: trend.map(function (t) { return t.present; }) },
            { name: 'Late', data: trend.map(function (t) { return t.late; }) },
            { name: 'Absent', data: trend.map(function (t) { return t.absent; }) }
        ],
        colors: [red, ink, soft],
        stroke: { width: 2, curve: 'straight' },
        fill: { opacity: 0 },
        markers: {
            size: 3,
            strokeWidth: 0,
            hover: { size: 5 }
        },
        dataLabels: { enabled: false },
        xaxis: {
            categories: categories,
            labels: { style: { colors: '#64748b', fontSize: '11px' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: { style: { colors: '#64748b', fontSize: '11px' } },
            min: 0,
            tickAmount: 4,
            forceNiceScale: true
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 4,
            xaxis: { lines: { show: false } },
            yaxis: { lines: { show: true } },
            padding: { top: 8, right: 12, bottom: 0, left: 8 }
        },
        legend: {
            show: true,
            position: 'top',
            horizontalAlign: 'right',
            fontSize: '11px',
            fontWeight: 600,
            itemMargin: { horizontal: 12 }
        },
        tooltip: {
            theme: 'light',
            style: { fontSize: '12px' },
            y: { formatter: function (v) { return v + ' logs'; } }
        }
    }).render();

    const wfActive = {{ (int) $activeEmployees }};
    const wfInactive = {{ (int) $inactiveEmployees }};
    const wfLeave = {{ (int) $onLeaveEmployees }};

    new ApexCharts(document.querySelector('#hrd-chart-workforce'), {
        chart: { type: 'donut', height: 280, fontFamily: font },
        series: [wfActive, wfInactive, wfLeave],
        labels: ['Active', 'Inactive', 'On leave'],
        colors: [red, '#e2e8f0', '#64748b'],
        stroke: { width: 1, colors: ['#fff'] },
        plotOptions: {
            pie: {
                donut: {
                    size: '78%',
                    labels: {
                        show: true,
                        name: { fontSize: '11px', color: '#94a3b8' },
                        value: { fontSize: '18px', fontWeight: 700, color: ink, fontFamily: mono },
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '10px',
                            color: '#94a3b8',
                            formatter: function () {
                                return String(wfActive + wfInactive + wfLeave);
                            }
                        }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        legend: { position: 'bottom', fontSize: '11px', fontWeight: 600, markers: { width: 8, height: 8, radius: 2 } }
    }).render();

    const lp = {{ (int) $pendingLeaves }};
    const la = {{ (int) $approvedLeaves }};
    const lr = {{ (int) $rejectedLeaves }};

    new ApexCharts(document.querySelector('#hrd-chart-leaves'), {
        chart: { type: 'donut', height: 260, fontFamily: font },
        series: [lp, la, lr],
        labels: ['Pending', 'Approved', 'Rejected'],
        colors: ['#f59e0b', '#22c55e', '#f43f5e'],
        stroke: { width: 1, colors: ['#fff'] },
        plotOptions: {
            pie: {
                donut: {
                    size: '78%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Requests',
                            fontSize: '10px',
                            color: '#94a3b8',
                            formatter: function () { return String(lp + la + lr); }
                        }
                    }
                }
            }
        },
        dataLabels: { enabled: false },
        legend: { position: 'bottom', fontSize: '11px', fontWeight: 600, markers: { width: 8, height: 8, radius: 2 } }
    }).render();

    const ot = {{ (float) $totalOvertimeHours }};
    const ut = {{ (float) $totalUndertimeHours }};

    new ApexCharts(document.querySelector('#hrd-chart-otut'), {
        chart: { type: 'bar', height: 260, fontFamily: font, toolbar: { show: false } },
        series: [{ name: 'Hours', data: [ot, ut] }],
        colors: [red, '#94a3b8'],
        plotOptions: { bar: { borderRadius: 6, columnWidth: '38%', distributed: true } },
        xaxis: {
            categories: ['Overtime', 'Undertime'],
            labels: { style: { fontSize: '11px', fontWeight: 600, colors: '#64748b' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: { style: { colors: '#94a3b8', fontSize: '11px' } },
            min: 0
        },
        dataLabels: {
            enabled: ot + ut > 0,
            offsetY: -18,
            style: { fontSize: '11px', fontWeight: 700, colors: ['#fff'], fontFamily: mono },
            formatter: function (v) { return v.toFixed(1) + 'h'; }
        },
        grid: { show: false },
        legend: { show: false },
        tooltip: { y: { formatter: function (v) { return v.toFixed(2) + ' h'; } } }
    }).render();
})();
</script>
@endpush
