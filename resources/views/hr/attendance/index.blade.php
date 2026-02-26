@extends('layouts.layout')

@section('content')
<div class="container-fluid">

    {{-- Quick Actions --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex gap-2">
                <a href="{{ route('attendance.create') }}" class="btn btn-primary btn-sm">
                    <i class="feather-plus me-2"></i>Record Attendance
                </a>
                <button class="btn btn-secondary btn-sm" onclick="toggleQRMonitor()">
                    <i class="feather-monitor me-2"></i><span id="qr-btn-label">Show QR Monitor</span>
                </button>
            </div>
        </div>
    </div>

    {{-- QR Monitor Panel --}}
    <div id="qr-monitor-section" style="display:none;" class="mb-4">
        <div class="row g-3 align-items-stretch">

            {{-- Instructions --}}
            <div class="col-12 col-lg-7">
                <div class="card h-100">
                    <div class="card-header">
                        <span class="card-title mb-0">How to log attendance</span>
                    </div>
                    <div class="card-body d-flex align-items-center">
                        <ol class="att-steps mb-0">
                            <li>Open <strong>Smart Accounting</strong> on your phone</li>
                            <li>Sign in with your registered email</li>
                            <li>Tap <strong>Scan QR</strong> from the main menu</li>
                            <li>Point your camera at the code on the right</li>
                            <li>Wait for the <strong>"Attendance Recorded"</strong> confirmation</li>
                        </ol>
                    </div>
                </div>
            </div>

            {{-- QR Code --}}
            <div class="col-12 col-lg-5">
                <div class="att-qr-card h-100">
                    <div class="att-qr-label">Scan to log attendance</div>

                    <div class="att-qr-wrapper" id="qr-wrapper">
                        <div id="qrcode"></div>
                        <div id="scan-success" class="att-success-overlay d-none">
                            <i class="feather-check-circle"></i>
                            <span>Attendance Recorded</span>
                        </div>
                    </div>

                    <div class="att-token" id="token-display">——</div>
                    <div class="att-timer" id="qr-timer">60s · refreshing soon</div>
                </div>
            </div>

        </div>
    </div>

    {{-- Attendance Records --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <span class="card-title mb-0">Attendance Records</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover w-100">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Date</th>
                                    <th>Time In</th>
                                    <th>Time Out</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="recent-scans-tbody">
                                <tr>
                                    <td colspan="5" class="text-muted text-center py-3">Loading…</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
let qrActive = false;
let tokenCheckInterval = null;
let qrRefreshInterval = null;
let qrCountdown = 60;

function toggleQRMonitor() {
    qrActive = !qrActive;
    const section = document.getElementById('qr-monitor-section');
    const label   = document.getElementById('qr-btn-label');
    if (qrActive) {
        section.style.display = 'block';
        label.textContent = 'Hide QR Monitor';
        loadQR();
    } else {
        section.style.display = 'none';
        label.textContent = 'Show QR Monitor';
        clearInterval(tokenCheckInterval);
        clearInterval(qrRefreshInterval);
    }
}

function updateQRTimer() {
    if (qrCountdown > 0) qrCountdown--;
    document.getElementById('qr-timer').textContent =
        `${qrCountdown}s · refreshing soon`;
}

function loadQR() {
    fetch("{{ route('hr.qr.generate') }}", {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        const qrDiv = document.getElementById('qrcode');
        qrDiv.innerHTML = '';
        new QRCode(qrDiv, {
            text: data.token,
            width: 200,
            height: 200,
            colorDark: '#1c1c1e',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
        document.getElementById('token-display').textContent = data.token;
        qrCountdown = 60;
        startTokenStatusCheck();
        startQRRefresh();
    });
}

function startQRRefresh() {
    if (qrRefreshInterval) clearInterval(qrRefreshInterval);
    qrRefreshInterval = setInterval(() => {
        updateQRTimer();
        if (qrCountdown <= 0) { clearInterval(qrRefreshInterval); loadQR(); }
    }, 1000);
}

function checkTokenStatus() {
    fetch("{{ route('api.qr.token-status') }}")
        .then(r => r.json())
        .then(data => {
            if (data.used) {
                triggerSuccess();
                stopTokenStatusCheck();
                loadQR();
                loadRecentScans();
            }
        });
}

function startTokenStatusCheck() {
    if (tokenCheckInterval) clearInterval(tokenCheckInterval);
    tokenCheckInterval = setInterval(checkTokenStatus, 2000);
}

function stopTokenStatusCheck() { clearInterval(tokenCheckInterval); }

function triggerSuccess() {
    const overlay  = document.getElementById('scan-success');
    const wrapper  = document.getElementById('qr-wrapper');
    wrapper.classList.add('success-active');
    overlay.classList.remove('d-none');
    setTimeout(() => {
        overlay.classList.add('d-none');
        wrapper.classList.remove('success-active');
    }, 3200);
}

async function loadRecentScans() {
    try {
        const response = await fetch("{{ route('api.attendance.recent') }}", { credentials: 'include' });
        const logs = await response.json();
        const tbody = document.getElementById('recent-scans-tbody');

        if (!logs || logs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-muted text-center py-3">No recent QR scans</td></tr>';
            return;
        }

        const consolidated = {};
        logs.forEach(log => {
            const key = `${log.employee_name}|${log.date}`;
            if (!consolidated[key]) {
                consolidated[key] = { employee_name: log.employee_name, date: log.date, time_in: 'N/A', time_out: 'N/A' };
            }
            if (log.type === 'time_in')  consolidated[key].time_in  = log.time;
            if (log.type === 'time_out') consolidated[key].time_out = log.time;
        });

        tbody.innerHTML = Object.values(consolidated).map(r => `
            <tr>
                <td><strong>${r.employee_name}</strong></td>
                <td><small class="text-muted">${r.date}</small></td>
                <td>${r.time_in}</td>
                <td>${r.time_out}</td>
                <td><span class="badge bg-success">QR Scanned</span></td>
            </tr>
        `).join('');
    } catch {
        document.getElementById('recent-scans-tbody').innerHTML =
            '<tr><td colspan="5" class="text-danger text-center py-3"><i class="feather-alert-circle me-2"></i>Failed to load</td></tr>';
    }
}

window.addEventListener('load', () => {
    loadRecentScans();
    setInterval(loadRecentScans, 30000);
});
</script>

<style>
/* -- QR card --------------------------------------------------- */
.att-qr-card {
    background: #1c1c1e;
    border-radius: 12px;
    padding: 32px 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 16px;
    min-height: 360px;
}

.att-qr-label {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 1.8px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.4);
}

.att-qr-wrapper {
    background: #fff;
    border-radius: 12px;
    padding: 14px;
    position: relative;
    transition: box-shadow 0.3s ease, transform 0.3s ease;
}
.att-qr-wrapper.success-active {
    box-shadow: 0 0 0 5px rgba(34,197,94,0.5);
    transform: scale(1.03);
}

#qrcode {
    width: 200px;
    height: 200px;
}

/* Success overlay inside qr box */
.att-success-overlay {
    position: absolute;
    inset: 0;
    background: rgba(22,163,74,0.95);
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 0.95rem;
    font-weight: 600;
    gap: 8px;
    z-index: 10;
}
.att-success-overlay i {
    font-size: 2rem;
}

/* Token text */
.att-token {
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: 4px;
    color: #ffffff;
    font-family: 'Courier New', monospace;
}

/* Timer */
.att-timer {
    font-size: 0.72rem;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.35);
}

/* -- Instructions list ----------------------------------------- */
.att-steps {
    padding-left: 0;
    list-style: none;
    counter-reset: att;
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
}
.att-steps li {
    counter-increment: att;
    display: flex;
    align-items: center;
    gap: 14px;
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
    min-width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #c8292a;
    color: #fff;
    font-size: 0.75rem;
    font-weight: 700;
    flex-shrink: 0;
}
</style>
@endsection