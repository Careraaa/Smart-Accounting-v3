@extends('layouts.layout')

@section('content')
<div class="container-fluid">

    {{-- Welcome Header — plain div, no .card/.card-body so theme JS ignores it --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="kt-welcome-banner">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h4 class="kt-welcome-title">Welcome back, {{ auth()->user()->name }}!</h4>
                        <p class="kt-welcome-sub">
                            <i class="feather-calendar me-1"></i>{{ now()->format('l, F d, Y') }}
                        </p>
                    </div>
                    <div class="text-end">
                        <p class="kt-welcome-sub mb-1">Current Status</p>
                        <span id="current-status" class="badge bg-warning fs-12 px-3 py-2">Loading...</span>
                    </div>
                </div>
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
                    @if (auth()->user()->employee)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="fs-12 fw-medium text-muted mb-1">Full Name</div>
                            <div class="fw-semibold text-dark fs-12">{{ auth()->user()->name }}</div>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="fs-12 fw-medium text-muted mb-1">Position</div>
                            <div class="text-dark fs-12">{{ auth()->user()->employee->position }}</div>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="fs-12 fw-medium text-muted mb-1">Department</div>
                            <div class="text-dark fs-12">{{ auth()->user()->employee->department ?? 'N/A' }}</div>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="fs-12 fw-medium text-muted mb-1">Employment Status</div>
                            <div class="mt-1">
                                @if (auth()->user()->employee->status === 'active')
                                    <span class="badge bg-soft-success text-success">Active</span>
                                @else
                                    <span class="badge bg-soft-danger text-danger">Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="fs-12 fw-medium text-muted mb-1">Date Hired</div>
                            <div class="text-dark fs-12">{{ auth()->user()->employee->date_of_hire->format('M d, Y') }}</div>
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

.att-steps {
    padding-left: 0;
    list-style: none;
    counter-reset: att;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.att-steps li {
    counter-increment: att;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 0.845rem;
    color: #4a4a58;
    padding: 10px 14px;
    border-radius: 8px;
    background: #f4f5f7;
}
.att-steps li::before {
    content: counter(att);
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #1c1c1e;
    color: #fff;
    font-size: 0.72rem;
    font-weight: 700;
    flex-shrink: 0;
}
</style>
@endsection