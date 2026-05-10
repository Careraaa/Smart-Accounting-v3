@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.edb-page{font-family:'Sora',sans-serif;position:relative}
.edb-backdrop{position:absolute;inset:-40px -20px auto -20px;height:340px;pointer-events:none;z-index:0;background:
    radial-gradient(220px 220px at 10% 35%, rgba(200,41,42,0.14), transparent 60%),
    radial-gradient(260px 260px at 85% 10%, rgba(2,132,199,0.12), transparent 60%),
    radial-gradient(240px 240px at 70% 70%, rgba(22,163,74,0.10), transparent 60%),
    linear-gradient(to bottom, rgba(17,24,39,0.04), transparent 70%);filter:saturate(110%)}
.edb-grid{position:absolute;inset:0;background-image:linear-gradient(to right, rgba(17,24,39,0.06) 1px, transparent 1px),linear-gradient(to bottom, rgba(17,24,39,0.06) 1px, transparent 1px);background-size:48px 48px;mask-image:radial-gradient(closest-side at 50% 30%, rgba(0,0,0,0.75), transparent 80%);opacity:.5}
.edb-content{position:relative;z-index:1}

.edb-hero{background:linear-gradient(135deg,#111827 0%,#0b1220 55%,#111827 100%);border-radius:18px;padding:22px 24px;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;gap:18px;flex-wrap:wrap;position:relative;overflow:hidden}
.edb-hero:before{content:'';position:absolute;top:-70px;right:-70px;width:260px;height:260px;border-radius:50%;background:rgba(200,41,42,0.18);pointer-events:none}
.edb-hero:after{content:'';position:absolute;bottom:-90px;left:-90px;width:260px;height:260px;border-radius:50%;background:rgba(2,132,199,0.14);pointer-events:none}
.edb-hero-left{position:relative;z-index:1}
.edb-title{font-size:1.25rem;font-weight:900;color:#fff;margin:0 0 6px;letter-spacing:-0.02em}
.edb-sub{font-size:.82rem;color:#9ca3af;margin:0}
.edb-hero-right{position:relative;z-index:1;display:flex;gap:10px;flex-wrap:wrap;align-items:center}
.edb-chip{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border:1px solid rgba(255,255,255,0.14);border-radius:999px;color:#e5e7eb;background:rgba(255,255,255,0.06);font-size:.75rem}
.edb-btn{display:inline-flex;align-items:center;gap:10px;padding:11px 18px;background:#c8292a;color:#fff;border:none;border-radius:12px;font-family:'Sora',sans-serif;font-size:.86rem;font-weight:900;cursor:pointer;transition:background .15s,box-shadow .15s,transform .15s;text-decoration:none;box-shadow:0 4px 20px rgba(200,41,42,.5);white-space:nowrap}
.edb-btn:hover{background:#a81f20;color:#fff;box-shadow:0 10px 34px rgba(200,41,42,.62);transform:translateY(-1px)}
.edb-btn-sec{display:inline-flex;align-items:center;gap:7px;padding:9px 14px;background:#fff;color:#374151;border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:.82rem;font-weight:800;text-decoration:none;cursor:pointer;transition:all .15s;white-space:nowrap}
.edb-btn-sec:hover{border-color:#c8292a;color:#c8292a;background:#fff5f5;transform:translateY(-1px)}

.edb-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:18px}
@media(max-width:900px){.edb-stats{grid-template-columns:1fr}}
.edb-stat{background:rgba(255,255,255,0.92);backdrop-filter:blur(6px);border:1px solid rgba(229,231,235,0.9);border-radius:16px;padding:16px 18px;display:flex;gap:12px;align-items:flex-start;position:relative;overflow:hidden;box-shadow:0 10px 30px rgba(17,24,39,0.06)}
.edb-stat::after{content:'';position:absolute;bottom:0;left:0;right:0;height:3px}
.edb-stat.s-blue::after{background:#0284c7}.edb-stat.s-amber::after{background:#d97706}.edb-stat.s-green::after{background:#16a34a}
.edb-ico{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.edb-stat.s-blue .edb-ico{background:#f0f9ff;color:#0284c7}
.edb-stat.s-amber .edb-ico{background:#fffbeb;color:#d97706}
.edb-stat.s-green .edb-ico{background:#f0fdf4;color:#16a34a}
.edb-lbl{font-size:.67rem;font-weight:900;text-transform:uppercase;letter-spacing:.09em;color:#9ca3af;margin-bottom:4px}
.edb-val{font-size:1.05rem;font-weight:900;color:#111827;line-height:1.15}
.edb-mono{font-family:'DM Mono',monospace;font-variant-numeric:tabular-nums}

/* Keep your existing dashboard styles but make them feel tighter */
.kt-welcome-banner{background:linear-gradient(135deg,#111827 0%,#0b1220 100%)!important;position:relative;overflow:hidden;border:1px solid rgba(255,255,255,0.08)}
.kt-welcome-banner:before{content:'';position:absolute;top:-70px;right:-70px;width:260px;height:260px;border-radius:50%;background:rgba(200,41,42,0.18);pointer-events:none}
.kt-welcome-title{font-weight:900!important}
.scan-action-card{border-radius:16px!important}
</style>
@endpush

@section('content')
<div class="container-fluid edb-page">
    <div class="edb-backdrop"><div class="edb-grid"></div></div>
    <div class="edb-content">

    <div class="edb-hero">
        <div class="edb-hero-left">
            <h1 class="edb-title">Dashboard</h1>
            <p class="edb-sub">Quick attendance actions, today’s logs, and your profile snapshot.</p>
            <div class="d-flex flex-wrap gap-2 mt-2">
                <span class="edb-chip"><i class="feather-calendar"></i> {{ now()->format('l, F d, Y') }}</span>
                <span class="edb-chip"><i class="feather-bell"></i> Stay updated</span>
            </div>
        </div>
        <div class="edb-hero-right">
            <a class="edb-btn" href="{{ route('attendance.scan') }}">
                <i class="feather-camera"></i>
                Scan QR Attendance
            </a>
        </div>
    </div>

    <div class="edb-stats">
        <div class="edb-stat s-amber">
            <div class="edb-ico"><i class="feather-clock"></i></div>
            <div>
                <div class="edb-lbl">Current status</div>
                <div class="edb-val"><span id="current-status" class="edb-mono">Loading…</span></div>
                <div class="text-muted" style="font-size:.73rem;margin-top:4px;">based on your last log</div>
            </div>
        </div>
        <div class="edb-stat s-green">
            <div class="edb-ico"><i class="feather-log-in"></i></div>
            <div>
                <div class="edb-lbl">Last log</div>
                <div class="edb-val edb-mono"><span id="last-log-badge">--</span> <span id="last-log-time">--:--</span></div>
                <div class="text-muted" style="font-size:.73rem;margin-top:4px;">today</div>
            </div>
        </div>
        <div class="edb-stat s-blue">
            <div class="edb-ico"><i class="feather-list"></i></div>
            <div>
                <div class="edb-lbl">Today’s logs</div>
                <div class="edb-val"><span class="edb-mono" id="logs-count">—</span></div>
                <div class="text-muted" style="font-size:.73rem;margin-top:4px;">entries recorded</div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Left Column --}}
        <div class="col-lg-8">

            {{-- Attendance Action --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="feather-clock me-2"></i>Attendance
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-stretch">
                        <div class="col-md-6">
                            <a href="{{ route('attendance.scan') }}"
                                class="scan-action-card d-flex flex-column align-items-center justify-content-center text-decoration-none p-4 rounded-3 h-100">
                                <i class="feather-camera scan-action-icon mb-2"></i>
                                <strong class="scan-action-title">Scan QR Code</strong>
                                <small class="scan-action-sub mt-1">Log time in / time out</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <div class="p-4 rounded-3 h-100 d-flex flex-column justify-content-center"
                                style="background:#f4f5f7;">
                                <div class="fs-12 fw-medium text-muted mb-2">Last Recorded Log</div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span id="last-log-badge" class="badge bg-secondary">--</span>
                                    <span id="last-log-time" class="fw-semibold text-dark">--:--</span>
                                </div>
                                <small class="text-muted" style="font-size:.78rem;">Scan the QR code on the attendance monitor to log your attendance.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Today's Log --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="feather-list me-2"></i>Today's Attendance Log
                    </h5>
                </div>
                <div class="card-body" id="attendance-logs">
                    <div class="text-center py-3">
                        <i class="feather-loader d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                        <p class="text-muted mb-0" style="font-size:.845rem;">Loading attendance logs...</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Column --}}
        <div class="col-lg-4">

            {{-- My Profile --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="feather-user me-2"></i>My Profile
                    </h5>
                </div>
                <div class="card-body">
                    @if (auth()->user() && auth()->user()->salary_rate)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="fs-12 fw-medium text-muted mb-1">Full Name</div>
                            <div class="fw-semibold text-dark fs-12">{{ auth()->user()->name }}</div>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="fs-12 fw-medium text-muted mb-1">Position</div>
                            <div class="text-dark fs-12">{{ auth()->user()->position }}</div>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="fs-12 fw-medium text-muted mb-1">Department</div>
                            <div class="text-dark fs-12">{{ auth()->user()->department ?? 'N/A' }}</div>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="fs-12 fw-medium text-muted mb-1">Employment Status</div>
                            <div class="mt-1">
                                @if (auth()->user()->status === 'active')
                                    <span class="badge bg-soft-success text-success">Active</span>
                                @else
                                    <span class="badge bg-soft-danger text-danger">Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="fs-12 fw-medium text-muted mb-1">Date Hired</div>
                            <div class="text-dark fs-12">{{ auth()->user()->date_of_hire ? auth()->user()->date_of_hire->format('M d, Y') : 'N/A' }}</div>
                        </div>
                    @else
                        <p class="text-muted mb-0" style="font-size:.845rem;">No employee profile linked to this account.</p>
                    @endif
                </div>
            </div>

            {{-- Quick Tips --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="feather-info me-2"></i>Quick Tips
                    </h5>
                </div>
                <div class="card-body">
                    <ol class="att-steps mb-0">
                        <li>Scan QR code upon arrival for <strong>time in</strong></li>
                        <li>Scan again before leaving for <strong>time out</strong></li>
                        <li>Keep your device camera accessible</li>
                        <li><span class="text-warning fw-semibold">Report</span> any issues to HR immediately</li>
                    </ol>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
window.addEventListener('load', async () => {
    const statusEl = document.getElementById('current-status');
    const badgeEl  = document.getElementById('last-log-badge');
    const timeEl   = document.getElementById('last-log-time');
    const logsEl   = document.getElementById('attendance-logs');
    const logsCountEl = document.getElementById('logs-count');

    try {
        const response = await fetch("{{ route('attendance.lastlog') }}", {
            headers: { 'Accept': 'application/json' }
        });

        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        const data = await response.json();

        if (data && data.type) {
            const isIn = data.type === 'time_in';
            statusEl.textContent = isIn ? 'TIMED IN' : 'TIMED OUT';
            statusEl.className   = `badge fs-12 px-3 py-2 bg-${isIn ? 'success' : 'warning'}`;
            badgeEl.textContent  = data.type.replace('_', ' ').toUpperCase();
            badgeEl.className    = `badge bg-${isIn ? 'success' : 'warning'}`;
            timeEl.textContent   = data.time ?? '';
        } else {
            statusEl.textContent = 'NOT LOGGED';
            statusEl.className   = 'badge fs-12 px-3 py-2 bg-danger';
            badgeEl.textContent  = 'No log yet';
            timeEl.textContent   = '';
        }

        if (data.logs && data.logs.length > 0) {
            if (logsCountEl) logsCountEl.textContent = data.logs.length;
            const rows = data.logs.map(log => {
                const isLogIn   = log.type === 'time_in';
                const badgeCls  = isLogIn ? 'bg-success' : 'bg-warning';
                const label     = log.type.replace('_', ' ').toUpperCase();
                const iconClass = isLogIn ? 'feather-log-in' : 'feather-log-out';
                const iconColor = isLogIn ? '#16a34a' : '#d97706';
                return `
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="${iconClass}" style="font-size:15px;color:${iconColor};"></i>
                            <span class="badge ${badgeCls}">${label}</span>
                        </div>
                        <span class="fw-medium fs-12">${log.time}</span>
                    </div>`;
            }).join('');
            logsEl.innerHTML = `<div>${rows}</div>`;
        } else {
            if (logsCountEl) logsCountEl.textContent = 0;
            logsEl.innerHTML = `
                <div class="text-center py-4">
                    <i class="feather-clock d-block mb-2" style="font-size:28px;opacity:.25;"></i>
                    <p class="text-muted mb-0" style="font-size:.845rem;">No attendance logged yet today.</p>
                </div>`;
        }

    } catch (err) {
        console.error('Attendance fetch error:', err);
        statusEl.textContent = 'ERROR';
        statusEl.className   = 'badge fs-12 px-3 py-2 bg-danger';
        badgeEl.textContent  = 'Error';
        timeEl.textContent   = '';
        logsEl.innerHTML = `
            <div class="text-center py-4">
                <i class="feather-alert-circle d-block mb-2 text-danger" style="font-size:28px;"></i>
                <p class="text-muted mb-0" style="font-size:.845rem;">Could not load attendance data. Please refresh.</p>
            </div>`;
    }
});
</script>

<style>
/* Welcome banner — plain div, no .card/.card-body, theme JS ignores it */
.kt-welcome-banner {
    background: linear-gradient(135deg, #cc3d38 0%, #530a0a 100%);
    border-radius: 16px;
    padding: 24px;
}
.kt-welcome-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #fff !important;
    margin-bottom: 4px;
}
.kt-welcome-sub {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.7) !important;
    margin-bottom: 0;
}

.scan-action-card {
    background: #fdf2f2;
    border: 2px solid #cc3d38;
    transition: background 0.15s;
}
.scan-action-card:hover { background: #cc3d38; }
.scan-action-icon {
    font-size: 36px;
    color: #cc3d38;
    transition: color 0.15s;
}
.scan-action-card:hover .scan-action-icon { color: #fff; }
.scan-action-title {
    font-size: 0.875rem;
    color: #cc3d38;
    transition: color 0.15s;
}
.scan-action-card:hover .scan-action-title { color: #fff; }
.scan-action-sub {
    font-size: 0.78rem;
    color: #6c757d;
    transition: color 0.15s;
}
.scan-action-card:hover .scan-action-sub { color: rgba(255,255,255,0.8); }
</style>
@endsection