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
.rem-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:18px; }
@media(max-width:900px)  { .rem-stats { grid-template-columns:repeat(2,1fr); } }
@media(max-width:560px)  { .rem-stats { grid-template-columns:1fr; } }

.rem-stat { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:16px 18px; position:relative; overflow:hidden; transition:box-shadow .15s,transform .15s; }
.rem-stat:hover { box-shadow:0 10px 28px rgba(17,24,39,.09); transform:translateY(-2px); }
.rem-stat::after { content:''; position:absolute; bottom:0; left:0; right:0; height:3px; border-radius:0 0 14px 14px; }
.rem-stat.s-green::after  { background:#16a34a; }
.rem-stat.s-red::after    { background:#c8292a; }
.rem-stat.s-blue::after   { background:#0284c7; }
.rem-stat.s-amber::after  { background:#d97706; }
.rem-stat.s-slate::after  { background:#64748b; }
.rem-stat.s-purple::after { background:#7c3aed; }

.rem-stat-lbl { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:#9ca3af; margin-bottom:6px; }
.rem-stat-val { font-size:1.3rem; font-weight:800; color:#111827; line-height:1.1; font-family:'DM Mono',monospace; font-variant-numeric:tabular-nums; }
.rem-stat-sub { font-size:.71rem; color:#9ca3af; margin-top:5px; line-height:1.4; }
.rem-stat-badge { display:inline-flex; align-items:center; gap:3px; font-size:.68rem; font-weight:700; padding:2px 7px; border-radius:999px; margin-top:5px; }
.rem-stat-badge.up   { background:#f0fdf4; color:#16a34a; }
.rem-stat-badge.down { background:#fff0f0; color:#c8292a; }
.rem-stat-badge.flat { background:#f3f4f6; color:#6b7280; }


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

/* ── Collections vs Expenses card ── */
.cve-body { padding:20px 20px 18px; }
.cve-totals { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:18px; }
.cve-side { border-radius:12px; padding:14px 16px; }
.cve-side.col { background:#111827; }
.cve-side.exp { background:#f8f9fb; border:1px solid #e5e7eb; }
.cve-side-lbl { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; margin-bottom:5px; }
.cve-side.col .cve-side-lbl { color:#6b7280; }
.cve-side.exp .cve-side-lbl { color:#9ca3af; }
.cve-side-val { font-family:'DM Mono',monospace; font-size:1.35rem; font-weight:800; line-height:1; }
.cve-side.col .cve-side-val { color:#fff; }
.cve-side.exp .cve-side-val { color:#111827; }
.cve-side-sub { font-size:.68rem; margin-top:4px; }
.cve-side.col .cve-side-sub { color:#4b5563; }
.cve-side.exp .cve-side-sub { color:#9ca3af; }
/* stacked bar */
.cve-track-wrap { margin-bottom:14px; }
.cve-track-labels { display:flex; justify-content:space-between; font-size:.67rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.08em; margin-bottom:6px; }
.cve-track { height:10px; border-radius:999px; background:#f3f4f6; overflow:hidden; display:flex; gap:2px; }
.cve-track-col { height:100%; border-radius:999px; background:#111827; transition:width .7s cubic-bezier(.4,0,.2,1); }
.cve-track-exp { height:100%; border-radius:999px; background:#c8292a; transition:width .7s cubic-bezier(.4,0,.2,1); }
/* net row */
.cve-net { display:flex; align-items:center; justify-content:space-between; padding:11px 14px; background:#f8f9fb; border-radius:10px; }
.cve-net-lbl { font-size:.72rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.08em; }
.cve-net-val { font-family:'DM Mono',monospace; font-size:1rem; font-weight:800; color:#111827; }
.cve-margin-lbl { font-size:.68rem; font-weight:700; color:#9ca3af; }
.cve-margin-val { font-family:'DM Mono',monospace; font-size:.82rem; font-weight:700; color:#16a34a; }

/* ── Monthly trend card ── */
.mct-body { padding:20px 20px 0; }
.mct-bars { display:flex; align-items:flex-end; gap:8px; height:130px; margin-bottom:12px; }
.mct-col { flex:1; display:flex; flex-direction:column; align-items:center; gap:0; cursor:pointer; }
.mct-bar-wrap { width:100%; display:flex; align-items:flex-end; justify-content:center; height:110px; }
.mct-bar {
    width:100%; max-width:36px; border-radius:6px 6px 0 0;
    transition:height .5s cubic-bezier(.4,0,.2,1), filter .15s, transform .15s;
    background:#e5e7eb;
    position:relative;
}
.mct-bar.has-data { background:#111827; }
.mct-bar.active   { background:#c8292a; transform:scaleX(1.08); }
.mct-bar:hover    { filter:brightness(1.15); }
.mct-month { font-size:.65rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.06em; margin-top:6px; }
.mct-col.active .mct-month { color:#111827; }
/* detail strip */
.mct-detail {
    display:flex; align-items:center; justify-content:space-between;
    padding:12px 16px; background:#f8f9fb; border-radius:10px;
    margin-bottom:16px; transition:opacity .2s;
}
.mct-detail-label { font-size:.72rem; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:.08em; }
.mct-detail-val { font-family:'DM Mono',monospace; font-size:1rem; font-weight:800; color:#111827; }
.mct-detail-sub { font-size:.68rem; color:#9ca3af; margin-top:1px; }

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
    @php
        $growthClass = $collectionGrowth > 0 ? 'up' : ($collectionGrowth < 0 ? 'down' : 'flat');
        $growthIcon  = $collectionGrowth > 0 ? '↑' : ($collectionGrowth < 0 ? '↓' : '→');
        $margin      = $totalCollections > 0 ? round(($totalNetRemittance / $totalCollections) * 100, 1) : 0;
    @endphp
    <div class="rem-stats">
        {{-- Collections --}}
        <div class="rem-stat s-green">
            <div class="rem-stat-lbl">Collections</div>
            <div class="rem-stat-val">₱{{ number_format($totalCollections, 0) }}</div>
            <div>
                <span class="rem-stat-badge {{ $growthClass }}">
                    {{ $growthIcon }} {{ number_format(abs($collectionGrowth), 1) }}% vs prev 30d
                </span>
            </div>
        </div>
        {{-- Expenses --}}
        <div class="rem-stat s-red">
            <div class="rem-stat-lbl">Expenses</div>
            <div class="rem-stat-val">₱{{ number_format($totalExpenses, 0) }}</div>
            <div class="rem-stat-sub">Avg ₱{{ number_format($averageExpenses, 0) }} / record</div>
        </div>
        {{-- Net --}}
        <div class="rem-stat s-blue">
            <div class="rem-stat-lbl">Net Remittance</div>
            <div class="rem-stat-val">₱{{ number_format($totalNetRemittance, 0) }}</div>
            <div class="rem-stat-sub">{{ $margin }}% margin</div>
        </div>
        {{-- Avg collection --}}
        <div class="rem-stat s-amber">
            <div class="rem-stat-lbl">Avg. Collection</div>
            <div class="rem-stat-val">₱{{ number_format($averageCollection, 0) }}</div>
            <div class="rem-stat-sub">Per finalized record</div>
        </div>
    </div>

    {{-- Charts row --}}
    @php
        $cveSum    = $totalCollections + $totalExpenses;
        $colPct    = $cveSum > 0 ? round(($totalCollections / $cveSum) * 100) : 50;
        $expPct    = $cveSum > 0 ? round(($totalExpenses    / $cveSum) * 100) : 50;
        $mctMax    = collect($monthlyCollectionTrend)->max('total_collection') ?: 1;
        $mctLast   = count($monthlyCollectionTrend) > 0 ? $monthlyCollectionTrend[array_key_last($monthlyCollectionTrend)] : null;
        $mctActive = count($monthlyCollectionTrend) - 1;
    @endphp
    <div class="rem-two-col">

        {{-- Collections vs Expenses --}}
        <div class="rem-panel">
            <div class="rem-panel-hd">
                <h2>Collections vs Expenses</h2>
                <span>Finalized records only</span>
            </div>
            <div class="cve-body">
                <div class="cve-totals">
                    <div class="cve-side col">
                        <div class="cve-side-lbl">Collections</div>
                        <div class="cve-side-val">₱{{ number_format($totalCollections, 0) }}</div>
                        <div class="cve-side-sub">{{ $colPct }}% of total</div>
                    </div>
                    <div class="cve-side exp">
                        <div class="cve-side-lbl">Expenses</div>
                        <div class="cve-side-val">₱{{ number_format($totalExpenses, 0) }}</div>
                        <div class="cve-side-sub">{{ $expPct }}% of total</div>
                    </div>
                </div>
                <div class="cve-track-wrap">
                    <div class="cve-track-labels">
                        <span>Collections</span>
                        <span>Expenses</span>
                    </div>
                    <div class="cve-track">
                        <div class="cve-track-col" style="width:{{ $colPct }}%;"></div>
                        <div class="cve-track-exp" style="width:{{ $expPct }}%;"></div>
                    </div>
                </div>
                <div class="cve-net">
                    <div>
                        <div class="cve-net-lbl">Net Remittance</div>
                        <div class="cve-net-val">₱{{ number_format($totalNetRemittance, 0) }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div class="cve-margin-lbl">Margin</div>
                        <div class="cve-margin-val">{{ $margin }}%</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Collection Trend --}}
        <div class="rem-panel">
            <div class="rem-panel-hd">
                <h2>Monthly Collection Trend</h2>
                <span>Last 6 months</span>
            </div>
            <div class="mct-body">
                @if(count($monthlyCollectionTrend))
                    <div class="mct-bars" id="mct-bars">
                        @foreach($monthlyCollectionTrend as $i => $m)
                            @php
                                $barH = $mctMax > 0 ? max(6, round(($m['total_collection'] / $mctMax) * 110)) : 6;
                            @endphp
                            <div class="mct-col {{ $i === $mctActive ? 'active' : '' }}"
                                 data-val="{{ $m['total_collection'] }}"
                                 data-month="{{ $m['month'] }}"
                                 data-idx="{{ $i }}">
                                <div class="mct-bar-wrap">
                                    <div class="mct-bar has-data {{ $i === $mctActive ? 'active' : '' }}"
                                         style="height:{{ $barH }}px;"></div>
                                </div>
                                <span class="mct-month">{{ $m['month'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mct-detail" id="mct-detail">
                        <div>
                            <div class="mct-detail-label" id="mct-detail-month">{{ $mctLast['month'] ?? '—' }}</div>
                            <div class="mct-detail-val" id="mct-detail-val">₱{{ number_format($mctLast['total_collection'] ?? 0, 0) }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div class="mct-detail-sub">Total collection</div>
                        </div>
                    </div>
                @else
                    <div style="display:flex;align-items:center;justify-content:center;height:180px;color:#9ca3af;font-size:.84rem;">No data yet.</div>
                @endif
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
    /* ── Monthly trend interaction ── */
    function initMCT() {
        const cols     = document.querySelectorAll('#mct-bars .mct-col');
        const detMonth = document.getElementById('mct-detail-month');
        const detVal   = document.getElementById('mct-detail-val');
        if (!cols.length || !detMonth) return;

        function fmt(n) {
            return '₱' + Number(n).toLocaleString('en-PH', { maximumFractionDigits: 0 });
        }

        function activate(col) {
            cols.forEach(c => {
                c.classList.remove('active');
                c.querySelector('.mct-bar').classList.remove('active');
            });
            col.classList.add('active');
            col.querySelector('.mct-bar').classList.add('active');

            const raw = parseFloat(col.dataset.val) || 0;
            detMonth.textContent = col.dataset.month;

            /* count-up */
            const prev = parseFloat(detVal.textContent.replace(/[^0-9.]/g, '')) || 0;
            if (prev === raw) { detVal.textContent = fmt(raw); return; }
            const steps = 18, dur = 200;
            let cur = 0;
            const inc = (raw - prev) / steps;
            const t = setInterval(() => {
                cur++;
                detVal.textContent = fmt(Math.round(prev + inc * cur));
                if (cur >= steps) { detVal.textContent = fmt(raw); clearInterval(t); }
            }, dur / steps);
        }

        cols.forEach(col => {
            col.addEventListener('mouseenter', () => activate(col));
            col.addEventListener('click',      () => activate(col));
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMCT);
    } else {
        initMCT();
    }
})();
</script>
@endpush
