@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.rem-dash { font-family: 'Sora', sans-serif; }

/* ── Layout wrapper with sidebar ── */
.rem-layout {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}

.rem-main {
    flex: 1;
    min-width: 0;
}

.rem-sidebar {
    width: 280px;
    flex-shrink: 0;
    position: sticky;
}

@media (max-width: 1200px) {
    .rem-layout {
        flex-direction: column;
    }
    
    .rem-sidebar {
        width: 100%;
        position: relative;
        top: auto;
    }
}

/* ── Knight mascot inside hero ── */
.rem-hero-knight {
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
    animation: heroKnightChargeRem 4s ease-in-out infinite;
    transform-origin: center center;
}
@keyframes heroKnightChargeRem {
    0%   { transform: translate(-50%, -50%) translateY(0)     rotate(0deg);    opacity: 0.12; }
    30%  { transform: translate(-50%, -50%) translateY(-6px)  rotate(-1.5deg); opacity: 0.16; }
    60%  { transform: translate(-50%, -50%) translateY(-10px) rotate(-0.8deg); opacity: 0.14; }
    80%  { transform: translate(-50%, -50%) translateY(-4px)  rotate(-2deg);   opacity: 0.17; }
    100% { transform: translate(-50%, -50%) translateY(0)     rotate(0deg);    opacity: 0.12; }
}

/* ── Hero ── */
.rem-hero {
    background: linear-gradient(135deg, #111827 0%, #0b1220 55%, #111827 100%);
    border-radius: 18px; padding: 22px 24px; margin-bottom: 22px;
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 18px; flex-wrap: wrap; position: relative; overflow: hidden;
}
.rem-hero::before { content:''; position:absolute; top:-70px; right:-70px; width:260px; height:260px; border-radius:50%; background:rgba(200,41,42,0.18); pointer-events:none; }
.rem-hero::after  { content:''; position:absolute; bottom:-90px; left:-90px; width:260px; height:260px; border-radius:50%; background:rgba(2,132,199,0.14); pointer-events:none; }
.rem-hero-left { position:relative; z-index:1; }
.rem-hero h1 { font-size:1.25rem; font-weight:900; color:#fff; margin:0 0 6px; letter-spacing:-0.02em; }
.rem-hero p  { margin:0; font-size:.82rem; color:#9ca3af; line-height:1.5; }
.rem-hero-actions { position:relative; z-index:1; display:flex; flex-wrap:wrap; gap:10px; align-items:center; }
.rem-chip { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border:1px solid rgba(255,255,255,0.14); border-radius:999px; color:#e5e7eb; background:rgba(255,255,255,0.06); font-size:.75rem; }
.rem-btn { display:inline-flex; align-items:center; gap:8px; padding:10px 18px; background:#c8292a; color:#fff; border:none; border-radius:12px; font-size:.84rem; font-weight:700; text-decoration:none; transition:background .15s,box-shadow .15s,transform .12s; box-shadow:0 4px 20px rgba(200,41,42,.5); white-space:nowrap; }
.rem-btn:hover { background:#a81f20; color:#fff; box-shadow:0 10px 34px rgba(200,41,42,.62); transform:translateY(-1px); }
.rem-btn-sec { display:inline-flex; align-items:center; gap:7px; padding:9px 14px; background:rgba(255,255,255,0.06); color:#e5e7eb; border:1px solid rgba(255,255,255,0.14); border-radius:10px; font-size:.82rem; font-weight:700; text-decoration:none; transition:background .15s; }
.rem-btn-sec:hover { background:rgba(255,255,255,0.10); color:#fff; }

/* ── Stat grid ── */
.rem-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:18px; }
@media(max-width:900px) { .rem-stats { grid-template-columns:1fr 1fr; } }
@media(max-width:560px) { .rem-stats { grid-template-columns:1fr; } }

.rem-stat { background:#fff; border:1px solid #e5e7eb; border-radius:16px; padding:20px 22px; display:flex; align-items:flex-start; gap:14px; position:relative; overflow:hidden; transition:box-shadow .15s,transform .15s; }
.rem-stat:hover { box-shadow:0 14px 36px rgba(17,24,39,0.10); transform:translateY(-2px); }
.rem-stat::after { content:''; position:absolute; bottom:0; left:0; right:0; height:3px; border-radius:0 0 16px 16px; }
.rem-stat.s-green::after { background:#16a34a; }
.rem-stat.s-red::after   { background:#c8292a; }
.rem-stat.s-blue::after  { background:#0284c7; }
.rem-stat.s-amber::after { background:#d97706; }
.rem-stat.s-slate::after { background:#64748b; }
.rem-stat.s-purple::after{ background:#7c3aed; }

.rem-ico { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.rem-stat.s-green .rem-ico  { background:#f0fdf4; color:#16a34a; }
.rem-stat.s-red   .rem-ico  { background:#fff0f0; color:#c8292a; }
.rem-stat.s-blue  .rem-ico  { background:#f0f9ff; color:#0284c7; }
.rem-stat.s-amber .rem-ico  { background:#fffbeb; color:#d97706; }
.rem-stat.s-slate .rem-ico  { background:#f3f4f6; color:#64748b; }
.rem-stat.s-purple .rem-ico { background:#f5f3ff; color:#7c3aed; }

.rem-stat-lbl { font-size:.67rem; font-weight:700; text-transform:uppercase; letter-spacing:.09em; color:#9ca3af; margin-bottom:5px; }
.rem-stat-val { font-size:1.45rem; font-weight:800; color:#111827; line-height:1.1; font-family:'DM Mono',monospace; font-variant-numeric:tabular-nums; }
.rem-stat-sub { font-size:.72rem; color:#9ca3af; margin-top:5px; }

/* ── Two-col charts ── */
.rem-two-col { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:18px; }
@media(max-width:900px) { .rem-two-col { grid-template-columns:1fr; } }

/* ── Panel ── */
.rem-panel { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; }
.rem-panel-hd { padding:14px 18px; border-bottom:1px solid #f3f4f6; display:flex; align-items:baseline; justify-content:space-between; gap:12px; flex-wrap:wrap; }
.rem-panel-hd h2 { margin:0; font-size:.88rem; font-weight:700; color:#111827; letter-spacing:-.02em; }
.rem-panel-hd span { font-size:.72rem; color:#9ca3af; font-weight:500; }
.rem-panel-bd { padding:8px 12px 4px; }
.rem-chart { min-height:260px; }

/* ── Feed ── */
.rem-feed { margin:0; padding:0; list-style:none; }
.rem-feed li { border-top:1px solid #f3f4f6; }
.rem-feed li:first-child { border-top:none; }
.rem-feed a { display:flex; align-items:flex-start; gap:14px; padding:14px 18px; text-decoration:none; color:inherit; transition:background .12s; }
.rem-feed a:hover { background:#fafafa; }
.rem-av { width:40px; height:40px; border-radius:10px; background:#f4f4f6; color:#6b7280; font-size:.72rem; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1px solid #ececec; }
.rem-feed-body { flex:1; min-width:0; }
.rem-feed-title { font-size:.84rem; font-weight:700; color:#111827; margin:0 0 4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.rem-feed-meta  { font-size:.74rem; color:#6b7280; margin:0; line-height:1.45; }
.rem-feed-meta code { font-family:'DM Mono',monospace; font-size:.72rem; background:#f8fafc; padding:1px 6px; border-radius:4px; color:#64748b; }
.rem-feed-right { display:flex; flex-direction:column; align-items:flex-end; gap:6px; flex-shrink:0; }
.rem-pill { font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; padding:4px 10px; border-radius:999px; border:1px solid transparent; }
.rem-pill-completed { background:#f0fdf4; border-color:#bbf7d0; color:#15803d; }
.rem-pill-approved  { background:#f0f9ff; border-color:#bae6fd; color:#0284c7; }
.rem-pill-pending   { background:#fffbeb; border-color:#fde68a; color:#b45309; }
.rem-pill-rejected  { background:#fef2f2; border-color:#fecaca; color:#b91c1c; }
.rem-chevron { color:#d1d5db; font-size:18px; margin-top:2px; }
.rem-empty { text-align:center; padding:40px 20px; color:#9ca3af; font-size:.84rem; }
</style>
@endpush

@section('content')
<div class="col-12 rem-dash">
    <div class="rem-layout">
        <div class="rem-main">

    {{-- Hero --}}
    <div class="rem-hero">
        <img src="{{ asset('images/landscape-knight.png') }}" alt="" class="rem-hero-knight" aria-hidden="true">
        <div class="rem-hero-left">
            <h1>Remittance Dashboard</h1>
            <p>Collections, expenses, and driver activity — live from finalized remittances.</p>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <span class="rem-chip"><i class="feather-calendar"></i> {{ now()->format('l, F d, Y') }}</span>
                <span class="rem-chip"><i class="feather-layers"></i> {{ $totalRemittances }} finalized</span>
                <span class="rem-chip"><i class="feather-clock"></i> {{ $pendingRemittances }} pending</span>
            </div>
        </div>
        <div class="rem-hero-actions">
            <a href="{{ route('remittances.index') }}" class="rem-btn-sec"><i class="feather-layers"></i> View All</a>
            <a href="{{ route('remittances.create') }}" class="rem-btn"><i class="feather-plus"></i> New Remittance</a>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="rem-stats">
        <div class="rem-stat s-green">
            <div class="rem-ico">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="rem-stat-lbl">Total Collections</div>
                <div class="rem-stat-val">₱{{ number_format($totalCollections, 0) }}</div>
                <div class="rem-stat-sub">{{ $collectionGrowth > 0 ? '+' : '' }}{{ number_format($collectionGrowth, 1) }}% vs last 30 days</div>
            </div>
        </div>
        <div class="rem-stat s-red">
            <div class="rem-ico">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <div class="rem-stat-lbl">Total Expenses</div>
                <div class="rem-stat-val">₱{{ number_format($totalExpenses, 0) }}</div>
                <div class="rem-stat-sub">Across all finalized records</div>
            </div>
        </div>
        <div class="rem-stat s-blue">
            <div class="rem-ico">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div>
                <div class="rem-stat-lbl">Net Remittance</div>
                <div class="rem-stat-val">₱{{ number_format($totalNetRemittance, 0) }}</div>
                <div class="rem-stat-sub">Collections minus expenses</div>
            </div>
        </div>
        <div class="rem-stat s-amber">
            <div class="rem-ico">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <div class="rem-stat-lbl">Avg. Collection</div>
                <div class="rem-stat-val">₱{{ number_format($averageCollection, 0) }}</div>
                <div class="rem-stat-sub">Per finalized remittance</div>
            </div>
        </div>
        <div class="rem-stat s-slate">
            <div class="rem-ico">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <div class="rem-stat-lbl">Active Drivers</div>
                <div class="rem-stat-val">{{ $activeDrivers }}</div>
                <div class="rem-stat-sub"><a href="{{ route('drivers.index') }}" style="color:#c8292a;font-weight:600;text-decoration:none;">View drivers →</a></div>
            </div>
        </div>
        <div class="rem-stat s-purple">
            <div class="rem-ico">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <div>
                <div class="rem-stat-lbl">Resources</div>
                <div class="rem-stat-val">{{ $activeVehicles }}<span style="font-size:.9rem;color:#9ca3af;font-weight:600;"> v · </span>{{ $activePAOs }}<span style="font-size:.9rem;color:#9ca3af;font-weight:600;"> p</span></div>
                <div class="rem-stat-sub">Vehicles · PAOs active</div>
            </div>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="rem-two-col">
        <div class="rem-panel">
            <div class="rem-panel-hd">
                <h2>Collections vs Expenses</h2>
                <span>Finalized records only</span>
            </div>
            <div class="rem-panel-bd">
                <div id="rem-chart-colexp" class="rem-chart"></div>
            </div>
        </div>
        <div class="rem-panel">
            <div class="rem-panel-hd">
                <h2>Monthly Collection Trend</h2>
                <span>Last 6 months</span>
            </div>
            <div class="rem-panel-bd">
                <div id="rem-chart-monthly" class="rem-chart"></div>
            </div>
        </div>
    </div>

    {{-- Recent remittances feed --}}
    <div class="rem-panel" style="margin-bottom:8px;">
        <div class="rem-panel-hd">
            <h2>Recent Approved Remittances</h2>
            <a href="{{ route('remittances.index') }}" style="font-size:.78rem;font-weight:700;color:#c8292a;text-decoration:none;">View all →</a>
        </div>
        @if($recentRemittances->isEmpty())
            <div class="rem-empty">No finalized remittances yet.</div>
        @else
            <ul class="rem-feed">
                @foreach($recentRemittances as $rem)
                    @php
                        $pillMap = ['completed'=>'rem-pill-completed','approved'=>'rem-pill-approved','pending'=>'rem-pill-pending','rejected'=>'rem-pill-rejected'];
                        $pc = $pillMap[$rem->status] ?? 'rem-pill-pending';
                        $driverInitials = '';
                        if ($rem->driver && $rem->driver->name) {
                            $parts = preg_split('/\s+/', trim($rem->driver->name));
                            $driverInitials = strtoupper(substr($parts[0]??'',0,1).substr($parts[1]??'',0,1));
                        }
                    @endphp
                    <li>
                        <a href="{{ route('remittances.show', $rem) }}">
                            <div class="rem-av">{{ $driverInitials ?: '?' }}</div>
                            <div class="rem-feed-body">
                                <p class="rem-feed-title">{{ $rem->driver->name ?? 'N/A' }}</p>
                                <p class="rem-feed-meta">
                                    {{ $rem->remittance_date?->format('M d, Y') ?? '—' }}
                                    · <code>{{ $rem->vehicle->plate_number ?? '—' }}</code>
                                    · {{ $rem->route->route_name ?? '—' }}
                                    · Net <code>₱{{ number_format($rem->net_remittance, 2) }}</code>
                                </p>
                            </div>
                            <div class="rem-feed-right">
                                <span class="rem-pill {{ $pc }}">{{ ucfirst($rem->status) }}</span>
                                <i class="feather-chevron-right rem-chevron"></i>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
        </div>
    </div>

    {{-- Right Calendar Sidebar --}}
    <aside class="rem-sidebar">
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

    const tc = {{ (float) $totalCollections }};
    const te = {{ (float) $totalExpenses }};

    const elCE = document.querySelector('#rem-chart-colexp');
    if (elCE && (tc > 0 || te > 0)) {
        new ApexCharts(elCE, {
            chart: { type: 'donut', height: 260, fontFamily: font },
            series: [tc, te],
            labels: ['Collections', 'Expenses'],
            colors: ['#16a34a', '#c8292a'],
            stroke: { width: 1, colors: ['#fff'] },
            plotOptions: {
                pie: {
                    donut: {
                        size: '78%',
                        labels: {
                            show: true,
                            value: { fontSize: '16px', fontWeight: 700, color: '#111827', fontFamily: mono,
                                formatter: function(v) { return '₱' + Number(v).toLocaleString(undefined,{maximumFractionDigits:0}); }
                            },
                            total: {
                                show: true, label: 'Total', fontSize: '10px', color: '#94a3b8',
                                formatter: function(w) {
                                    var s = w.globals.seriesTotals.reduce(function(a,b){return a+b;},0);
                                    return '₱' + s.toLocaleString(undefined,{maximumFractionDigits:0});
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: { position: 'bottom', fontSize: '11px', fontWeight: 600 }
        }).render();
    } else if (elCE) {
        elCE.innerHTML = '<div class="rem-empty" style="min-height:240px;display:flex;align-items:center;justify-content:center;">No data yet.</div>';
    }

    const monthly = @json($monthlyCollectionTrend);
    const elM = document.querySelector('#rem-chart-monthly');
    if (elM) {
        new ApexCharts(elM, {
            chart: { type: 'bar', height: 260, fontFamily: font, toolbar: { show: false } },
            series: [{ name: 'Collection', data: monthly.map(function(m){ return m.total_collection; }) }],
            colors: [red],
            plotOptions: { bar: { borderRadius: 8, columnWidth: '52%' } },
            dataLabels: { enabled: false },
            xaxis: {
                categories: monthly.map(function(m){ return m.month; }),
                labels: { style: { colors: '#64748b', fontSize: '11px' } },
                axisBorder: { show: false }, axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#64748b', fontSize: '11px' },
                    formatter: function(v){ return '₱'+(v>=1000?(v/1000).toFixed(0)+'k':v.toFixed(0)); }
                }
            },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
            tooltip: { y: { formatter: function(v){ return '₱'+Number(v).toLocaleString(undefined,{maximumFractionDigits:0}); } } }
        }).render();
    }
})();
</script>
@endpush
