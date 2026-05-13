@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.edb { font-family: 'Sora', sans-serif; }

/* ── Hero ── */
.edb-hero {
    background: linear-gradient(135deg, #111827 0%, #0b1220 55%, #111827 100%);
    border-radius: 18px; padding: 22px 24px; margin-bottom: 22px;
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 18px; flex-wrap: wrap; position: relative; overflow: hidden;
}
.edb-hero::before { content:''; position:absolute; top:-70px; right:-70px; width:260px; height:260px; border-radius:50%; background:rgba(200,41,42,0.18); pointer-events:none; }
.edb-hero::after  { content:''; position:absolute; bottom:-90px; left:-90px; width:260px; height:260px; border-radius:50%; background:rgba(2,132,199,0.14); pointer-events:none; }
.edb-hero-left { position:relative; z-index:1; }
.edb-hero h1 { font-size:1.25rem; font-weight:900; color:#fff; margin:0 0 6px; letter-spacing:-0.02em; }
.edb-hero p  { margin:0; font-size:.82rem; color:#9ca3af; line-height:1.5; }
.edb-hero-right { position:relative; z-index:1; display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
.edb-chip { display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border:1px solid rgba(255,255,255,0.14); border-radius:999px; color:#e5e7eb; background:rgba(255,255,255,0.06); font-size:.75rem; }
.edb-btn { display:inline-flex; align-items:center; gap:10px; padding:11px 18px; background:#c8292a; color:#fff; border:none; border-radius:12px; font-size:.86rem; font-weight:900; cursor:pointer; transition:background .15s,box-shadow .15s,transform .15s; text-decoration:none; box-shadow:0 4px 20px rgba(200,41,42,.5); white-space:nowrap; }
.edb-btn:hover { background:#a81f20; color:#fff; box-shadow:0 10px 34px rgba(200,41,42,.62); transform:translateY(-1px); }

/* ── Stat cards ── */
.edb-stats { display:grid; grid-template-columns:repeat(2,1fr); gap:14px; margin-bottom:18px; }
@media(max-width:700px) { .edb-stats { grid-template-columns:1fr; } }

.edb-stat { background:#fff; border:1px solid #e5e7eb; border-radius:16px; padding:20px 22px; display:flex; align-items:flex-start; gap:14px; position:relative; overflow:hidden; transition:box-shadow .15s,transform .15s; }
.edb-stat:hover { box-shadow:0 14px 36px rgba(17,24,39,0.10); transform:translateY(-2px); }
.edb-stat::after { content:''; position:absolute; bottom:0; left:0; right:0; height:3px; border-radius:0 0 16px 16px; }
.edb-stat.s-amber::after { background:#d97706; }
.edb-stat.s-green::after { background:#16a34a; }

.edb-ico { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.edb-stat.s-amber .edb-ico { background:#fffbeb; color:#d97706; }
.edb-stat.s-green .edb-ico { background:#f0fdf4; color:#16a34a; }

.edb-stat-lbl { font-size:.67rem; font-weight:700; text-transform:uppercase; letter-spacing:.09em; color:#9ca3af; margin-bottom:5px; }
.edb-stat-val { font-size:1.05rem; font-weight:900; color:#111827; line-height:1.2; font-family:'DM Mono',monospace; font-variant-numeric:tabular-nums; }
.edb-stat-sub { font-size:.72rem; color:#9ca3af; margin-top:5px; }

/* ── Two-col layout ── */
.edb-two-col { display:grid; grid-template-columns:1fr 340px; gap:16px; align-items:start; }
@media(max-width:960px) { .edb-two-col { grid-template-columns:1fr; } }

/* ── Cards ── */
.edb-card { background:#fff; border:1px solid #e5e7eb; border-radius:16px; overflow:hidden; margin-bottom:16px; }
.edb-card:last-child { margin-bottom:0; }
.edb-card-head { padding:14px 18px; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; gap:10px; }
.edb-card-title { font-size:.82rem; font-weight:800; color:#111827; margin:0; display:flex; align-items:center; gap:8px; }
.edb-card-dot { width:7px; height:7px; border-radius:50%; background:#c8292a; display:inline-block; flex-shrink:0; }
.edb-card-body { padding:18px; }

/* ── Scan action ── */
.edb-scan-btn { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:10px; padding:28px 20px; border-radius:14px; text-decoration:none; background:linear-gradient(135deg,#fff5f5,#fff0f0); border:2px dashed rgba(200,41,42,0.35); transition:all .18s; text-align:center; }
.edb-scan-btn:hover { background:linear-gradient(135deg,#c8292a,#a81f20); border-color:transparent; transform:translateY(-2px); box-shadow:0 10px 28px rgba(200,41,42,0.35); }
.edb-scan-icon { color:#c8292a; transition:color .18s; }
.edb-scan-btn:hover .edb-scan-icon { color:#fff; }
.edb-scan-label { font-size:.88rem; font-weight:800; color:#c8292a; transition:color .18s; }
.edb-scan-btn:hover .edb-scan-label { color:#fff; }
.edb-scan-sub { font-size:.74rem; color:#9ca3af; transition:color .18s; }
.edb-scan-btn:hover .edb-scan-sub { color:rgba(255,255,255,0.75); }

/* ── Last log panel ── */
.edb-lastlog-panel { background:#f8f9fb; border-radius:12px; padding:16px 18px; display:flex; flex-direction:column; justify-content:center; height:100%; }
.edb-lastlog-lbl { font-size:.67rem; font-weight:700; text-transform:uppercase; letter-spacing:.09em; color:#9ca3af; margin-bottom:8px; }
.edb-lastlog-row { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.edb-lastlog-time { font-family:'DM Mono',monospace; font-size:1.1rem; font-weight:700; color:#111827; }
.edb-lastlog-hint { font-size:.74rem; color:#9ca3af; margin-top:10px; line-height:1.55; }

/* ── Badges ── */
.edb-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:20px; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
.edb-badge.in   { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
.edb-badge.out  { background:#fffbeb; color:#b45309; border:1px solid #fde68a; }
.edb-badge.none { background:#f3f4f6; color:#6b7280; border:1px solid #e5e7eb; }

/* ── Profile sidebar ── */
.edb-kv { display:grid; grid-template-columns:110px 1fr; gap:10px 14px; align-items:center; }
.edb-k { font-size:.67rem; font-weight:700; text-transform:uppercase; letter-spacing:.09em; color:#9ca3af; }
.edb-v { font-size:.845rem; font-weight:700; color:#111827; }
.edb-divider { height:1px; background:#f3f4f6; margin:12px 0; }

/* ── Tips ── */
.edb-tips-list { list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:12px; }
.edb-tips-item { display:flex; align-items:flex-start; gap:10px; }
.edb-tips-num { width:22px; height:22px; border-radius:50%; background:#111827; color:#fff; font-size:.68rem; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px; }
.edb-tips-text { font-size:.8rem; color:#374151; line-height:1.55; }
.edb-tips-text strong { color:#111827; }
.edb-tips-warn { color:#c8292a; font-weight:700; }
</style>
@endpush

@section('content')
<div class="col-12 edb">

    {{-- Hero --}}
    <div class="edb-hero">
        <div class="edb-hero-left">
            <h1>Employee Dashboard</h1>
            <p>Your attendance overview and quick actions for today.</p>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <span class="edb-chip">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                    {{ now()->format('l, F d, Y') }}
                </span>
                <span class="edb-chip">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                    {{ now()->format('h:i A') }}
                </span>
            </div>
        </div>
        <div class="edb-hero-right">
            <a class="edb-btn" href="{{ route('attendance.scan') }}">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9V5a2 2 0 012-2h4M3 15v4a2 2 0 002 2h4m6-18h4a2 2 0 012 2v4m0 6v4a2 2 0 01-2 2h-4"/></svg>
                Scan QR Attendance
            </a>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="edb-stats">
        <div class="edb-stat s-amber">
            <div class="edb-ico">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
            </div>
            <div style="flex:1;">
                <div class="edb-stat-lbl">Current Status</div>
                <div class="edb-stat-val"><span id="current-status">—</span></div>
                <div class="edb-stat-sub">Based on your most recent log</div>
            </div>
        </div>
        <div class="edb-stat s-green">
            <div class="edb-ico">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14"/></svg>
            </div>
            <div style="flex:1;">
                <div class="edb-stat-lbl">Last Log</div>
                <div class="edb-stat-val">
                    <span id="last-log-badge-stat">—</span>
                    <span id="last-log-time-stat" style="font-size:.88rem;color:#6b7280;margin-left:6px;font-family:'DM Mono',monospace;"></span>
                </div>
                <div class="edb-stat-sub">Most recent attendance entry today</div>
            </div>
        </div>
    </div>

    {{-- Two-column layout --}}
    <div class="edb-two-col">

        {{-- Left: Attendance card --}}
        <div>
            <div class="edb-card">
                <div class="edb-card-head">
                    <span class="edb-card-title">
                        <span class="edb-card-dot"></span>
                        Attendance
                    </span>
                </div>
                <div class="edb-card-body">
                    <div class="row g-3 align-items-stretch">
                        <div class="col-md-6">
                            <a href="{{ route('attendance.scan') }}" class="edb-scan-btn h-100">
                                <svg class="edb-scan-icon" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9V5a2 2 0 012-2h4M3 15v4a2 2 0 002 2h4m6-18h4a2 2 0 012 2v4m0 6v4a2 2 0 01-2 2h-4"/></svg>
                                <span class="edb-scan-label">Scan QR Code</span>
                                <span class="edb-scan-sub">Tap to log time in or time out</span>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <div class="edb-lastlog-panel h-100">
                                <div class="edb-lastlog-lbl">Last Recorded Log</div>
                                <div class="edb-lastlog-row">
                                    <span id="last-log-badge">—</span>
                                    <span id="last-log-time" class="edb-lastlog-time"></span>
                                </div>
                                <div class="edb-lastlog-hint">Use the QR scanner on the attendance monitor to record your time in or time out.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Profile + Tips --}}
        <div>
            <div class="edb-card">
                <div class="edb-card-head">
                    <span class="edb-card-title">
                        <span class="edb-card-dot"></span>
                        My Profile
                    </span>
                </div>
                <div class="edb-card-body">
                    @php $u = auth()->user(); @endphp
                    @if($u && $u->salary_rate)
                        <div class="edb-kv">
                            <span class="edb-k">Full Name</span>
                            <span class="edb-v">{{ $u->first_name }} {{ $u->last_name }}</span>
                            <span class="edb-k">Position</span>
                            <span class="edb-v">{{ $u->position ?? '—' }}</span>
                            <span class="edb-k">Department</span>
                            <span class="edb-v">{{ $u->department ?? '—' }}</span>
                            <span class="edb-k">Status</span>
                            <span class="edb-v">
                                @if($u->status === 'active')
                                    <span class="edb-badge in">Active</span>
                                @else
                                    <span class="edb-badge none">Inactive</span>
                                @endif
                            </span>
                            <span class="edb-k">Date Hired</span>
                            <span class="edb-v">{{ $u->date_of_hire ? $u->date_of_hire->format('M d, Y') : '—' }}</span>
                        </div>
                    @else
                        <p style="font-size:.82rem;color:#9ca3af;margin:0;">No employee profile is linked to this account.</p>
                    @endif
                </div>
            </div>

            <div class="edb-card">
                <div class="edb-card-head">
                    <span class="edb-card-title">
                        <span class="edb-card-dot"></span>
                        Attendance Guidelines
                    </span>
                </div>
                <div class="edb-card-body">
                    <ul class="edb-tips-list">
                        <li class="edb-tips-item">
                            <span class="edb-tips-num">1</span>
                            <span class="edb-tips-text">Scan the QR code upon arrival to record your <strong>time in</strong>.</span>
                        </li>
                        <li class="edb-tips-item">
                            <span class="edb-tips-num">2</span>
                            <span class="edb-tips-text">Scan again before leaving to record your <strong>time out</strong>.</span>
                        </li>
                        <li class="edb-tips-item">
                            <span class="edb-tips-num">3</span>
                            <span class="edb-tips-text">Ensure your device camera is accessible and the QR code is clearly visible when scanning.</span>
                        </li>
                        <li class="edb-tips-item">
                            <span class="edb-tips-num">4</span>
                            <span class="edb-tips-text"><span class="edb-tips-warn">Immediately report</span> any attendance discrepancies or scanning issues to the HR department.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
window.addEventListener('load', async () => {
    const statusEl    = document.getElementById('current-status');
    const badgeEl     = document.getElementById('last-log-badge');
    const timeEl      = document.getElementById('last-log-time');
    const badgeStatEl = document.getElementById('last-log-badge-stat');
    const timeStatEl  = document.getElementById('last-log-time-stat');

    function makeBadge(type) {
        if (!type) return '<span class="edb-badge none">No log yet</span>';
        const isIn = type === 'time_in';
        return `<span class="edb-badge ${isIn ? 'in' : 'out'}">${isIn ? 'Time In' : 'Time Out'}</span>`;
    }

    try {
        const response = await fetch("{{ route('attendance.lastlog') }}", { headers: { 'Accept': 'application/json' } });
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const data = await response.json();

        if (data && data.type) {
            const isIn = data.type === 'time_in';
            statusEl.innerHTML    = isIn ? '<span class="edb-badge in">Timed In</span>' : '<span class="edb-badge out">Timed Out</span>';
            badgeStatEl.innerHTML = makeBadge(data.type);
            timeStatEl.textContent = data.time ?? '';
            badgeEl.innerHTML     = makeBadge(data.type);
            timeEl.textContent    = data.time ?? '';
        } else {
            statusEl.innerHTML    = '<span class="edb-badge none">Not Logged</span>';
            badgeStatEl.innerHTML = makeBadge(null);
            badgeEl.innerHTML     = makeBadge(null);
        }
    } catch (err) {
        const errBadge = '<span class="edb-badge none">Error</span>';
        statusEl.innerHTML    = errBadge;
        badgeStatEl.innerHTML = errBadge;
        badgeEl.innerHTML     = errBadge;
    }
});
</script>
@endsection
