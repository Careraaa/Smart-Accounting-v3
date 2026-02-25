@extends('layouts.layout')

@section('content')
    <div class="container-fluid">

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex gap-2">
                    <a href="{{ route('attendance.create') }}" class="btn btn-primary btn-sm">
                        <i class="feather-plus me-2"></i>Record Attendance
                    </a>
                    <button class="btn btn-success btn-sm" onclick="toggleQRMonitor()">
                        <i class="feather-monitor me-2"></i><span id="qr-btn-label">Show QR Monitor</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- QR Monitor Panel (hidden by default) -->
        <div id="qr-monitor-section" style="display:none;">
            <div class="row g-4 align-items-stretch mb-4">

                <!-- Instructions -->
                <div class="col-12 col-lg-7">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title mb-0">How to Log Attendance</h5>
                        </div>
                        <div class="card-body">
                            <ol class="instruction-steps list-unstyled mb-0">
                                <li>
                                    <strong>Open Smart Accounting</strong>
                                    <span class="text-muted d-block mt-1 small">on your mobile device</span>
                                </li>
                                <li>
                                    <strong>Sign in</strong>
                                    <span class="text-muted d-block mt-1 small">using your registered email (complete OTP if
                                        required)</span>
                                </li>
                                <li>
                                    <strong>Go to Scan QR</strong>
                                    <span class="text-muted d-block mt-1 small">from the main menu</span>
                                </li>
                                <li>
                                    <strong>Scan the code on the right</strong>
                                    <span class="text-muted d-block mt-1 small">align your camera with the QR code</span>
                                </li>
                                <li>
                                    <strong>Confirmation appears</strong>
                                    <span class="text-muted d-block mt-1 small">"Attendance logged" + time in/out</span>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="col-12 col-lg-5">
                    <div class="card stretch stretch-full qr-panel">
                        <div
                            class="card-body d-flex flex-column align-items-center justify-content-center text-center position-relative py-5">
                            <h5 class="fw-semibold mb-4 text-white">SCAN HERE</h5>

                            <div class="qr-wrapper position-relative">
                                <div id="qrcode" class="qr-code"></div>
                                <div id="scan-success" class="success-flash d-none">
                                    <i class="feather-check-circle me-2 fs-3"></i>
                                    <span>Attendance Recorded!</span>
                                </div>
                            </div>

                            <div class="mt-3 text-white fw-bold font-monospace" id="token-display"
                                style="font-size: 1.4rem; letter-spacing: 3px;">
                                Loading...
                            </div>

                            <div class="mt-2 text-white-50 small fw-medium" id="qr-timer">
                                60 SECONDS &nbsp;|&nbsp; QR REFRESHING SOON
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Attendance Records Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Attendance Records</h5>
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
                                        <td colspan="5" class="text-muted text-center py-3">Loading recent scans...</td>
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
        // ── QR Monitor Toggle ──────────────────────────────
        let qrActive = false;
        let tokenCheckInterval = null;
        let qrRefreshInterval = null;
        let qrCountdown = 60;

        function toggleQRMonitor() {
            qrActive = !qrActive;
            const section = document.getElementById('qr-monitor-section');
            const label = document.getElementById('qr-btn-label');

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
            if (qrCountdown > 0) {
                qrCountdown--;
                document.getElementById("qr-timer").innerHTML =
                    `${qrCountdown} SECONDS &nbsp;|&nbsp; QR REFRESHING SOON`;
            }
        }

        function loadQR() {
            fetch("{{ route('hr.qr.generate') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    const qrDiv = document.getElementById('qrcode');
                    qrDiv.innerHTML = '';

                    new QRCode(qrDiv, {
                        text: data.token,
                        width: 220,
                        height: 220,
                        colorDark: "#1a1a1a",
                        colorLight: "#ffffff",
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
                if (qrCountdown <= 0) {
                    clearInterval(qrRefreshInterval);
                    loadQR();
                }
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
                        loadRecentScans(); // refresh table immediately on scan
                    }
                });
        }

        function startTokenStatusCheck() {
            if (tokenCheckInterval) clearInterval(tokenCheckInterval);
            tokenCheckInterval = setInterval(checkTokenStatus, 2000);
        }

        function stopTokenStatusCheck() {
            clearInterval(tokenCheckInterval);
        }

        function triggerSuccess() {
            const success = document.getElementById("scan-success");
            const wrapper = document.querySelector(".qr-wrapper");
            wrapper.classList.add("success-active");
            success.classList.remove("d-none");
            setTimeout(() => {
                success.classList.add("d-none");
                wrapper.classList.remove("success-active");
            }, 3200);
        }

        // ── Attendance Table ───────────────────────────────
        async function loadRecentScans() {
            try {
                const response = await fetch("{{ route('api.attendance.recent') }}", {
                    credentials: 'include'
                });
                const logs = await response.json();
                const tbody = document.getElementById('recent-scans-tbody');

                if (!logs || logs.length === 0) {
                    tbody.innerHTML =
                        '<tr><td colspan="5" class="text-muted text-center py-3">No recent QR scans</td></tr>';
                    return;
                }

                const consolidated = {};
                logs.forEach(log => {
                    const key = `${log.employee_name}|${log.date}`;
                    if (!consolidated[key]) {
                        consolidated[key] = {
                            employee_name: log.employee_name,
                            date: log.date,
                            time_in: 'N/A',
                            time_out: 'N/A',
                            time_in_id: null,
                            time_out_id: null
                        };
                    }
                    if (log.type === 'time_in') {
                        consolidated[key].time_in = log.time;
                        consolidated[key].time_in_id = log.id;
                    } else if (log.type === 'time_out') {
                        consolidated[key].time_out = log.time;
                        consolidated[key].time_out_id = log.id;
                    }
                });

                let html = '';
                Object.values(consolidated).forEach(record => {
                    html += `
                    <tr>
                        <td><strong>${record.employee_name}</strong></td>
                        <td><small class="text-muted">${record.date}</small></td>
                        <td>${record.time_in}</td>
                        <td>${record.time_out}</td>
                        <td><span class="badge bg-success">QR Scanned</span></td>
                    </tr>
                `;
                });

                tbody.innerHTML = html;
            } catch (error) {
                document.getElementById('recent-scans-tbody').innerHTML =
                    '<tr><td colspan="5" class="text-danger text-center py-3"><i class="feather-alert-circle me-2"></i>Failed to load recent scans</td></tr>';
            }
        }

        window.addEventListener('load', () => {
            loadRecentScans();
            setInterval(loadRecentScans, 30000);
        });
    </script>

    <style>
        .qr-panel {
            background: linear-gradient(135deg, #cc3d38 0%, #530a0a 100%);
            border: none !important;
        }

        .qr-wrapper {
            background: #fff;
            border-radius: 16px;
            padding: 1.25rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: all 0.4s ease;
        }

        .qr-wrapper.success-active {
            box-shadow: 0 0 0 6px rgba(34, 197, 94, 0.5);
            transform: scale(1.04);
        }

        .qr-code {
            width: 220px;
            height: 220px;
            margin: 0 auto;
        }

        .success-flash {
            position: absolute;
            inset: 0;
            background: rgba(34, 197, 94, 0.95);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            z-index: 10;
        }

        .instruction-steps {
            counter-reset: step;
            padding-left: 0;
        }

        .instruction-steps li {
            counter-increment: step;
            position: relative;
            padding: 0.6rem 0.75rem 0.6rem 3.8rem;
            margin-bottom: 0.85rem;
            border-left: 3px solid #cc3d38;
            background: #fdf2f2;
            border-radius: 0 12px 12px 0;
        }

        .instruction-steps li::before {
            content: counter(step);
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            width: 2rem;
            height: 2rem;
            background: #cc3d38;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }
    </style>
@endsection
