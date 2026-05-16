@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.acd-dash { font-family: 'Sora', sans-serif; }

/* ── Layout wrapper with sidebar ── */
.acd-layout {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}

.acd-main {
    flex: 1;
    min-width: 0;
}

.acd-sidebar {
    width: 280px;
    flex-shrink: 0;
    position: sticky;
}

@media (max-width: 1200px) {
    .acd-layout {
        flex-direction: column;
    }
    
    .acd-sidebar {
        width: 100%;
        position: relative;
        top: auto;
    }
}

/* ── Knight mascot inside hero ── */
.acd-hero-knight {
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
    animation: heroKnightChargeAcd 4s ease-in-out infinite;
    transform-origin: center center;
}
@keyframes heroKnightChargeAcd {
    0%   { transform: translate(-50%, -50%) translateY(0)     rotate(0deg);    opacity: 0.12; }
    30%  { transform: translate(-50%, -50%) translateY(-6px)  rotate(-1.5deg); opacity: 0.16; }
    60%  { transform: translate(-50%, -50%) translateY(-10px) rotate(-0.8deg); opacity: 0.14; }
    80%  { transform: translate(-50%, -50%) translateY(-4px)  rotate(-2deg);   opacity: 0.17; }
    100% { transform: translate(-50%, -50%) translateY(0)     rotate(0deg);    opacity: 0.12; }
}

/* ── Hero ── */
.acd-hero {
    background: linear-gradient(135deg, #111827 0%, #0b1220 55%, #111827 100%);
    border-radius: 18px; padding: 22px 24px; margin-bottom: 22px;
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 18px; flex-wrap: wrap; position: relative; overflow: hidden;
}
.acd-hero::before { content:''; position:absolute; top:-70px; right:-70px; width:260px; height:260px; border-radius:50%; background:rgba(200,41,42,0.18); pointer-events:none; }
.acd-hero::after  { content:''; position:absolute; bottom:-90px; left:-90px; width:260px; height:260px; border-radius:50%; background:rgba(2,132,199,0.14); pointer-events:none; }
.acd-hero-left { position:relative; z-index:1; }
.acd-hero h1 { font-size:1.25rem; font-weight:900; color:#fff; margin:0 0 6px; letter-spacing:-0.02em; }
.acd-hero p  { margin:0; font-size:.82rem; color:#9ca3af; line-height:1.5; }
.acd-hero-actions { position:relative; z-index:1; display:flex; flex-wrap:wrap; gap:10px; align-items:center; }
.acd-chip { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border:1px solid rgba(255,255,255,0.14); border-radius:999px; color:#e5e7eb; background:rgba(255,255,255,0.06); font-size:.75rem; }
.acd-btn { display:inline-flex; align-items:center; gap:8px; padding:10px 18px; background:#c8292a; color:#fff; border:none; border-radius:12px; font-size:.84rem; font-weight:700; text-decoration:none; transition:background .15s,box-shadow .15s,transform .12s; box-shadow:0 4px 20px rgba(200,41,42,.5); white-space:nowrap; }
.acd-btn:hover { background:#a81f20; color:#fff; box-shadow:0 10px 34px rgba(200,41,42,.62); transform:translateY(-1px); }
.acd-btn-sec { display:inline-flex; align-items:center; gap:7px; padding:9px 14px; background:rgba(255,255,255,0.06); color:#e5e7eb; border:1px solid rgba(255,255,255,0.14); border-radius:10px; font-size:.82rem; font-weight:700; text-decoration:none; transition:background .15s; }
.acd-btn-sec:hover { background:rgba(255,255,255,0.10); color:#fff; }

/* ── Stat grid ── */
.acd-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:18px; }
@media(max-width:1100px) { .acd-stats { grid-template-columns:repeat(2,1fr); } }
@media(max-width:560px)  { .acd-stats { grid-template-columns:1fr; } }

.acd-stat { background:#fff; border:1px solid #e5e7eb; border-radius:16px; padding:20px 22px; display:flex; align-items:flex-start; gap:14px; position:relative; overflow:hidden; transition:box-shadow .15s,transform .15s; }
.acd-stat:hover { box-shadow:0 14px 36px rgba(17,24,39,0.10); transform:translateY(-2px); }
.acd-stat::after { content:''; position:absolute; bottom:0; left:0; right:0; height:3px; border-radius:0 0 16px 16px; }
.acd-stat.s-green::after  { background:#16a34a; }
.acd-stat.s-red::after    { background:#c8292a; }
.acd-stat.s-blue::after   { background:#0284c7; }
.acd-stat.s-amber::after  { background:#d97706; }
.acd-stat.s-purple::after { background:#7c3aed; }
.acd-stat.s-slate::after  { background:#64748b; }

.acd-ico { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.acd-stat.s-green  .acd-ico { background:#f0fdf4; color:#16a34a; }
.acd-stat.s-red    .acd-ico { background:#fff0f0; color:#c8292a; }
.acd-stat.s-blue   .acd-ico { background:#f0f9ff; color:#0284c7; }
.acd-stat.s-amber  .acd-ico { background:#fffbeb; color:#d97706; }
.acd-stat.s-purple .acd-ico { background:#f5f3ff; color:#7c3aed; }
.acd-stat.s-slate  .acd-ico { background:#f3f4f6; color:#64748b; }

.acd-stat-lbl { font-size:.67rem; font-weight:700; text-transform:uppercase; letter-spacing:.09em; color:#9ca3af; margin-bottom:5px; }
.acd-stat-val { font-size:1.35rem; font-weight:800; color:#111827; line-height:1.1; font-family:'DM Mono',monospace; font-variant-numeric:tabular-nums; }
.acd-stat-sub { font-size:.72rem; color:#9ca3af; margin-top:5px; }

/* ── Charts ── */
.acd-two-col { display:grid; grid-template-columns:1.55fr 1fr; gap:16px; margin-bottom:18px; }
.acd-two-col2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:18px; }
@media(max-width:1100px) { .acd-two-col { grid-template-columns:1fr; } }
@media(max-width:900px)  { .acd-two-col2 { grid-template-columns:1fr; } }

.acd-panel { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; }
.acd-panel-hd { padding:14px 18px; border-bottom:1px solid #f3f4f6; display:flex; align-items:baseline; justify-content:space-between; gap:12px; flex-wrap:wrap; }
.acd-panel-hd h2 { margin:0; font-size:.88rem; font-weight:700; color:#111827; letter-spacing:-.02em; }
.acd-panel-hd span { font-size:.72rem; color:#9ca3af; font-weight:500; }
.acd-panel-bd { padding:8px 12px 4px; }
.acd-chart { min-height:260px; }

/* ── Feed ── */
.acd-feed { margin:0; padding:0; list-style:none; }
.acd-feed li { border-top:1px solid #f3f4f6; }
.acd-feed li:first-child { border-top:none; }
.acd-feed a { display:flex; align-items:flex-start; gap:14px; padding:14px 18px; text-decoration:none; color:inherit; transition:background .12s; }
.acd-feed a:hover { background:#fafafa; }
.acd-av { width:40px; height:40px; border-radius:10px; background:#f4f4f6; color:#6b7280; font-size:.72rem; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1px solid #ececec; }
.acd-feed-body { flex:1; min-width:0; }
.acd-feed-title { font-size:.84rem; font-weight:700; color:#111827; margin:0 0 4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.acd-feed-meta  { font-size:.74rem; color:#6b7280; margin:0; line-height:1.45; }
.acd-feed-meta code { font-family:'DM Mono',monospace; font-size:.72rem; background:#f8fafc; padding:1px 6px; border-radius:4px; color:#64748b; }
.acd-feed-right { display:flex; flex-direction:column; align-items:flex-end; gap:6px; flex-shrink:0; }
.acd-pill { font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; padding:4px 10px; border-radius:999px; border:1px solid transparent; }
.acd-pill-paid     { background:#f0fdf4; border-color:#bbf7d0; color:#15803d; }
.acd-pill-approved { background:#f0f9ff; border-color:#bae6fd; color:#0284c7; }
.acd-pill-pending  { background:#fffbeb; border-color:#fde68a; color:#b45309; }
.acd-pill-rejected { background:#fef2f2; border-color:#fecaca; color:#b91c1c; }
.acd-chevron { color:#d1d5db; font-size:18px; margin-top:2px; }
.acd-empty { text-align:center; padding:40px 20px; color:#9ca3af; font-size:.84rem; }
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
    $pillClass = function ($status) {
        return match ($status) {
            'released', 'paid' => 'acd-pill-paid',
            'approved' => 'acd-pill-approved',
            'rejected' => 'acd-pill-rejected',
            default => 'acd-pill-pending',
        };
    };
@endphp

<div class="col-12 acd-dash">
    <div class="acd-layout">
        <div class="acd-main">

    {{-- Hero --}}
    <div class="acd-hero">
        <img src="{{ asset('images/landscape-knight.png') }}" alt="" class="acd-hero-knight" aria-hidden="true">
        <div class="acd-hero-left">
            <h1>Accountant Dashboard</h1>
            <p>Payroll totals, pipeline status, and loan overview — live from approved records.</p>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <span class="acd-chip"><i class="feather-calendar"></i> {{ now()->format('l, F d, Y') }}</span>
                <span class="acd-chip"><i class="feather-users"></i> {{ $totalEmployees }} employees</span>
                <span class="acd-chip"><i class="feather-percent"></i> {{ number_format($allowancePercentage, 1) }}% allowances / {{ number_format($deductionPercentage, 1) }}% deductions</span>
            </div>
        </div>
        <div class="acd-hero-actions">
            <a href="{{ route('reports.payroll') }}" class="acd-btn-sec"><i class="feather-file-text"></i> Reports</a>
            <a href="{{ route('payroll-approval.index') }}" class="acd-btn"><i class="feather-check-square"></i> Payroll Approval</a>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="acd-stats">
        <div class="acd-stat s-green">
            <div class="acd-ico">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="acd-stat-lbl">Net Payroll</div>
                <div class="acd-stat-val" style="font-size:1.1rem;">₱{{ number_format($totalPayroll, 2) }}</div>
                <div class="acd-stat-sub">Approved + released records</div>
            </div>
        </div>
        <div class="acd-stat s-blue">
            <div class="acd-ico">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            </div>
            <div>
                <div class="acd-stat-lbl">Allowances</div>
                <div class="acd-stat-val" style="font-size:1.1rem;">₱{{ number_format($totalAllowances, 2) }}</div>
                <div class="acd-stat-sub">{{ number_format($allowancePercentage, 1) }}% of A+D total</div>
            </div>
        </div>
        <div class="acd-stat s-red">
            <div class="acd-ico">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
            </div>
            <div>
                <div class="acd-stat-lbl">Deductions</div>
                <div class="acd-stat-val" style="font-size:1.1rem;">₱{{ number_format($totalDeductions, 2) }}</div>
                <div class="acd-stat-sub">{{ number_format($deductionPercentage, 1) }}% of A+D total</div>
            </div>
        </div>
        <div class="acd-stat s-amber">
            <div class="acd-ico">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
                <div class="acd-stat-lbl">Avg. Basic Salary</div>
                <div class="acd-stat-val" style="font-size:1.1rem;">₱{{ number_format($averageBasicSalary ?? 0, 2) }}</div>
                <div class="acd-stat-sub">Across payroll rows</div>
            </div>
        </div>
        <div class="acd-stat s-purple">
            <div class="acd-ico">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="acd-stat-lbl">Outstanding Loans</div>
                <div class="acd-stat-val" style="font-size:1.1rem;">₱{{ number_format($totalOutstandingLoans, 2) }}</div>
                <div class="acd-stat-sub">{{ $activeSalaryLoans }} active loan(s)</div>
            </div>
        </div>
        <div class="acd-stat s-slate">
            <div class="acd-ico">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <div class="acd-stat-lbl">Headcount</div>
                <div class="acd-stat-val">{{ $totalEmployees }}</div>
                <div class="acd-stat-sub">Excl. admin roles</div>
            </div>
        </div>
    </div>

    {{-- Charts row 1 --}}
    <div class="acd-two-col">
        <div class="acd-panel">
            <div class="acd-panel-hd">
                <h2>Net payroll trend</h2>
                <span>Last 6 months</span>
            </div>
            <div class="acd-panel-bd">
                <div id="acd-chart-trend" class="acd-chart"></div>
            </div>
        </div>
        <div class="acd-panel">
            <div class="acd-panel-hd">
                <h2>Payroll status mix</h2>
                <span>By status count</span>
            </div>
            <div class="acd-panel-bd">
                <div id="acd-chart-status" class="acd-chart" style="min-height:280px;"></div>
            </div>
        </div>
    </div>

    {{-- Charts row 2 --}}
    <div class="acd-two-col2">
        <div class="acd-panel">
            <div class="acd-panel-hd">
                <h2>Allowances vs Deductions</h2>
                <span>Aggregate peso amounts</span>
            </div>
            <div class="acd-panel-bd">
                <div id="acd-chart-ad" class="acd-chart"></div>
            </div>
        </div>
        <div class="acd-panel">
            <div class="acd-panel-hd">
                <h2>Pipeline</h2>
                <span>Awaiting · approved · released · rejected</span>
            </div>
            <div class="acd-panel-bd">
                <div id="acd-chart-pipeline" class="acd-chart"></div>
            </div>
        </div>
    </div>

    {{-- Recent payroll feed --}}
    <div class="acd-panel" style="margin-bottom:8px;">
        <div class="acd-panel-hd">
            <h2>Recent payroll records</h2>
            <a href="{{ route('payroll-approval.index') }}" style="font-size:.78rem;font-weight:700;color:#c8292a;text-decoration:none;">Payroll approval →</a>
        </div>
        @if ($recentPayroll->isEmpty())
            <div class="acd-empty">No payroll records yet.</div>
        @else
            <ul class="acd-feed">
                @foreach ($recentPayroll as $payroll)
                    @php
                        $u = $payroll->employee;
                        $name = $u ? trim(($u->first_name ?? '').' '.($u->last_name ?? '')) ?: ($u->name ?? 'Unknown') : 'Unknown';
                        $st = $payroll->status ?? 'pending';
                    @endphp
                    <li>
                        <a href="{{ route('payroll-approval.show', $payroll) }}">
                            <div class="acd-av">{{ $initials($name) }}</div>
                            <div class="acd-feed-body">
                                <p class="acd-feed-title">{{ $name }}</p>
                                <p class="acd-feed-meta">
                                    Net <code>₱{{ number_format($payroll->net_pay ?? 0, 2) }}</code>
                                    · Gross <code>₱{{ number_format($payroll->gross_pay ?? 0, 2) }}</code>
                                    · {{ $payroll->payroll_period_start?->format('M j') ?? '—' }} – {{ $payroll->payroll_period_end?->format('M j, Y') ?? '—' }}
                                </p>
                            </div>
                            <div class="acd-feed-right">
                                <span class="acd-pill {{ $pillClass($st) }}">{{ in_array($st, ['released','paid'],true) ? 'Released' : ucfirst($st) }}</span>
                                <i class="feather-chevron-right acd-chevron"></i>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
        </div>
    </div>

    {{-- Right Calendar Sidebar --}}
    <aside class="acd-sidebar">
        @include('partials.calendar')
        @include('partials.todo')
    </aside>
</div>
@endsection

@push('scripts')
<script>
(function () {
    if (typeof ApexCharts === 'undefined') return;
    const font = 'Sora, sans-serif';
    const mono = "'DM Mono', monospace";
    const red  = '#c8292a';
    const ink  = '#111827';

    const monthly = @json($monthlyPayrollTrend);
    const categories = monthly.map(function(m){ return m.label || m.month; });

    new ApexCharts(document.querySelector('#acd-chart-trend'), {
        chart: { type: 'line', height: 280, fontFamily: font, toolbar: { show: false }, zoom: { enabled: false }, animations: { enabled: true, speed: 400 } },
        series: [{ name: 'Net pay', data: monthly.map(function(m){ return m.total; }) }],
        colors: [red],
        stroke: { width: 2, curve: 'smooth' },
        fill: { type: 'gradient', gradient: { shadeIntensity: 0.4, opacityFrom: 0.35, opacityTo: 0.02, stops: [0,90,100] } },
        markers: { size: 3, strokeWidth: 0, hover: { size: 5 } },
        dataLabels: { enabled: false },
        xaxis: { categories: categories, labels: { style: { colors: '#64748b', fontSize: '11px' } }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { style: { colors: '#64748b', fontSize: '11px' }, formatter: function(v){ return '₱'+(v>=1e6?(v/1e6).toFixed(1)+'M':(v>=1e3?(v/1e3).toFixed(0)+'k':v.toFixed(0))); } }, min: 0 },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4, padding: { top: 8, right: 12, bottom: 0, left: 8 } },
        tooltip: { y: { formatter: function(v){ return '₱'+Number(v).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}); } } }
    }).render();

    const stLabels = @json($payrollStatusChartLabels);
    const stSeries = @json($payrollStatusChartSeries);
    const stEl = document.querySelector('#acd-chart-status');
    if (stSeries.length && stEl) {
        new ApexCharts(stEl, {
            chart: { type: 'donut', height: 280, fontFamily: font },
            series: stSeries, labels: stLabels,
            colors: ['#f59e0b','#8b5cf6','#64748b','#2563eb','#22c55e','#15803d','#f43f5e'],
            stroke: { width: 1, colors: ['#fff'] },
            plotOptions: { pie: { donut: { size: '78%', labels: { show: true, total: { show: true, label: 'Records', fontSize: '10px', color: '#94a3b8', formatter: function(){ return String(stSeries.reduce(function(a,b){return a+b;},0)); } } } } } },
            dataLabels: { enabled: false },
            legend: { position: 'bottom', fontSize: '11px', fontWeight: 600 }
        }).render();
    } else if (stEl) {
        stEl.innerHTML = '<div class="acd-empty" style="min-height:240px;display:flex;align-items:center;justify-content:center;">No status data yet.</div>';
    }

    const ta = {{ (float) $totalAllowances }};
    const td = {{ (float) $totalDeductions }};
    const adEl = document.querySelector('#acd-chart-ad');
    if (adEl && (ta > 0 || td > 0)) {
        new ApexCharts(adEl, {
            chart: { type: 'donut', height: 260, fontFamily: font },
            series: [ta, td], labels: ['Allowances', 'Deductions'],
            colors: ['#22c55e', '#f43f5e'],
            stroke: { width: 1, colors: ['#fff'] },
            plotOptions: { pie: { donut: { size: '78%', labels: { show: true, value: { fontSize: '16px', fontWeight: 700, color: ink, fontFamily: mono }, total: { show: true, label: 'A + D', fontSize: '10px', color: '#94a3b8', formatter: function(w){ return '₱'+w.globals.seriesTotals.reduce(function(a,b){return a+b;},0).toLocaleString(undefined,{maximumFractionDigits:0}); } } } } } },
            dataLabels: { enabled: false },
            legend: { position: 'bottom', fontSize: '11px', fontWeight: 600 }
        }).render();
    } else if (adEl) {
        adEl.innerHTML = '<div class="acd-empty" style="min-height:240px;display:flex;align-items:center;justify-content:center;">No data yet.</div>';
    }

    const pipe = @json($pipelineBar);
    new ApexCharts(document.querySelector('#acd-chart-pipeline'), {
        chart: { type: 'bar', height: 260, fontFamily: font, toolbar: { show: false } },
        series: [{ name: 'Payrolls', data: pipe.values }],
        colors: [red],
        plotOptions: { bar: { borderRadius: 8, columnWidth: '42%' } },
        xaxis: { categories: pipe.labels, labels: { style: { fontSize: '11px', fontWeight: 600, colors: '#64748b' } }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { style: { colors: '#94a3b8', fontSize: '11px' } }, min: 0, tickAmount: 4, forceNiceScale: true },
        dataLabels: { enabled: true, offsetY: -18, style: { fontSize: '11px', fontWeight: 700, colors: ['#fff'], fontFamily: mono } },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        legend: { show: false },
        tooltip: { y: { formatter: function(v){ return v+' record(s)'; } } }
    }).render();
})();
</script>
@endpush
