@extends('layouts.layout')

@section('content')
<div class="container-fluid">

    {{-- Welcome Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0"
                style="background: linear-gradient(135deg, #cc3d38 0%, #530a0a 100%); border-radius: 16px;">
                <div class="card-body px-4 py-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h4 class="fw-bold mb-1" style="color:#fff !important;">Welcome back, {{ auth()->user()->name }}!</h4>
                            <p class="mb-0 fs-12" style="color:rgba(255,255,255,0.6) !important;">
                                <i class="feather-calendar me-1"></i>{{ now()->format('l, F d, Y') }}
                            </p>
                        </div>
                        <div class="text-end">
                            <div class="fs-12 mb-1" style="color:rgba(255,255,255,0.6) !important;">Current Status</div>
                            <span id="current-status" class="badge bg-warning fs-12 px-3 py-2">Loading...</span>
                        </div>
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
                    <span class="card-title mb-0">
                        <i class="feather-clock me-2"></i>Attendance
                    </span>
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
                                style="background: #f4f5f7; border-radius: 10px;">
                                <div class="scan-section-label mb-2">Last Recorded Log</div>
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
                    <span class="card-title mb-0">
                        <i class="feather-list me-2"></i>Today's Attendance Log
                    </span>
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
                    <span class="card-title mb-0">
                        <i class="feather-user me-2"></i>My Profile
                    </span>
                </div>
                <div class="card-body">
                    @if (auth()->user()->employee)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="scan-section-label mb-1">Full Name</div>
                            <div class="emp-field-value fw-semibold">{{ auth()->user()->name }}</div>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="scan-section-label mb-1">Position</div>
                            <div class="emp-field-value">{{ auth()->user()->employee->position }}</div>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="scan-section-label mb-1">Department</div>
                            <div class="emp-field-value">{{ auth()->user()->employee->department ?? 'N/A' }}</div>
                        </div>
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="scan-section-label mb-1">Employment Status</div>
                            <div class="mt-1">
                                @if (auth()->user()->employee->status === 'active')
                                    <span class="emp-badge emp-badge-active">Active</span>
                                @else
                                    <span class="emp-badge emp-badge-inactive">Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="scan-section-label mb-1">Date Hired</div>
                            <div class="emp-field-value">{{ auth()->user()->employee->date_of_hire->format('M d, Y') }}</div>
                        </div>
                    @else
                        <p class="text-muted mb-0" style="font-size:.845rem;">No employee profile linked to this account.</p>
                    @endif
                </div>
            </div>

            {{-- Quick Tips --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-title mb-0">
                        <i class="feather-info me-2"></i>Quick Tips
                    </span>
                </div>
                <div class="card-body">
                    <ol class="att-steps mb-0">
                        <li>Scan QR code upon arrival for <strong>time in</strong></li>
                        <li>Scan again before leaving for <strong>time out</strong></li>
                        <li>Keep your device camera accessible</li>
                        <li><span style="color:#d97706; font-weight:600;">Report</span> any issues to HR immediately</li>
                    </ol>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    window.addEventListener('load', async () => {
        try {
            const response = await fetch("{{ route('attendance.lastlog') }}", {
                headers: { 'Accept': 'application/json' }
            });

            const statusEl = document.getElementById('current-status');
            const badgeEl  = document.getElementById('last-log-badge');
            const timeEl   = document.getElementById('last-log-time');

            if (response.ok) {
                const data = await response.json();
                if (data && data.type) {
                    const isIn = data.type === 'time_in';
                    statusEl.textContent  = isIn ? 'TIMED IN' : 'TIMED OUT';
                    statusEl.className    = `badge fs-12 px-3 py-2 bg-${isIn ? 'success' : 'warning'}`;
                    badgeEl.textContent   = data.type.replace('_', ' ').toUpperCase();
                    badgeEl.className     = `badge bg-${isIn ? 'success' : 'warning'}`;
                    timeEl.textContent    = data.time;
                } else {
                    statusEl.textContent = 'NOT LOGGED';
                    statusEl.className   = 'badge fs-12 px-3 py-2 bg-danger';
                    badgeEl.textContent  = 'No log yet';
                    timeEl.textContent   = '';
                }
            }

            document.getElementById('attendance-logs').innerHTML = `
                <div class="text-center py-3">
                    <i class="feather-check-circle text-success d-block mb-2" style="font-size:28px;"></i>
                    <p class="text-muted mt-2 mb-0" style="font-size:.845rem;">Your attendance logs will appear here after scanning.</p>
                </div>
            `;
        } catch (e) {
            document.getElementById('current-status').textContent = 'NOT LOGGED';
            document.getElementById('current-status').className   = 'badge fs-12 px-3 py-2 bg-danger';
        }
    });
</script>

<style>
/* Scan action card */
.scan-action-card {
    background: #fdf2f2;
    border: 2px solid #cc3d38;
    transition: background 0.15s, border-color 0.15s;
}
.scan-action-card:hover {
    background: #cc3d38;
}
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

/* Section label — from scan blade */
.scan-section-label {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1.4px;
    text-transform: uppercase;
    color: #9898a8;
}

/* Field value */
.emp-field-value { font-size: 0.875rem; color: #4a4a58; }

/* Status badges */
.emp-badge {
    display: inline-block;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.4px;
    padding: 3px 10px;
    border-radius: 20px;
}
.emp-badge-active   { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
.emp-badge-inactive { background: #fff5f5; color: #c8292a; border: 1px solid #fcd0d0; }

/* Numbered steps — from scan blade */
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
    font-size: 0.875rem;
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