@extends('layouts.layout')

@section('content')
    <div class="col-md-12">

        {{-- Quick Actions --}}
        <div class="d-flex gap-2 mb-4">
            <a href="{{ route('attendance.create') }}" class="btn btn-primary btn-sm">
                <i class="feather-plus me-1"></i> Record Attendance
            </a>
            <button class="btn btn-secondary btn-sm" onclick="toggleQRMonitor()">
                <i class="feather-monitor me-1"></i>
                <span id="qr-btn-label">Show QR Monitor</span>
            </button>
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

        {{-- Attendance Records Table --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="card-title mb-0">Attendance Records</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th>
                                    <div class="sort-link">Employee</div>
                                </th>
                                <th>
                                    <div class="sort-link">Date</div>
                                </th>
                                <th>
                                    <div class="sort-link">Time In</div>
                                </th>
                                <th>
                                    <div class="sort-link">Time Out</div>
                                </th>
                                <th class="text-center">
                                    <div class="sort-link justify-content-center">Status</div>
                                </th>
                                <th class="text-center">
                                    <div class="sort-link justify-content-center">Entry Type</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td>
                                        <strong>
                                            {{ $attendance->employee->first_name }}
                                            {{ $attendance->employee->last_name }}
                                        </strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $attendance->date->format('M d, Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        {{ $attendance->time_in
                                            ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->time_in)->format('g:i A')
                                            : '—' }}
                                    </td>
                                    <td>
                                        {{ $attendance->time_out
                                            ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->time_out)->format('g:i A')
                                            : '—' }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusStyles = [
                                                'present' => [
                                                    'bg' => '#f0fdf4',
                                                    'color' => '#16a34a',
                                                    'border' => '#bbf7d0',
                                                ],
                                                'late' => [
                                                    'bg' => '#fffbeb',
                                                    'color' => '#d97706',
                                                    'border' => '#fde68a',
                                                ],
                                                'absent' => [
                                                    'bg' => '#fff1f2',
                                                    'color' => '#e11d48',
                                                    'border' => '#fcd0d0',
                                                ],
                                            ];
                                            $s = $statusStyles[$attendance->status] ?? [
                                                'bg' => '#f4f5f7',
                                                'color' => '#9898a8',
                                                'border' => '#e8e8ef',
                                            ];
                                        @endphp
                                        <span class="emp-badge"
                                            style="background:{{ $s['bg'] }}; color:{{ $s['color'] }}; border:1px solid {{ $s['border'] }};">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if ($attendance->is_manual)
                                            <span class="emp-badge"
                                                style="background:#f0f9ff; color:#0284c7; border:1px solid #bae6fd;">
                                                <i class="feather-edit-2 me-1" style="font-size:0.7rem;"></i>Manual
                                            </span>
                                        @else
                                            <span class="emp-badge"
                                                style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0;">
                                                <i class="feather-check-circle me-1" style="font-size:0.7rem;"></i>QR
                                                Scanned
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="feather-clock d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                        No attendance records found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($attendances->hasPages())
                    <div class="d-flex justify-content-between align-items-center px-3 py-2">

                        {{-- Showing Text (LEFT) --}}
                        <div class="small text-muted">
                            Showing
                            <strong>{{ $attendances->firstItem() }}</strong>
                            to
                            <strong>{{ $attendances->lastItem() }}</strong>
                            of
                            <strong>{{ $attendances->total() }}</strong>
                            entries
                        </div>

                        {{-- Pagination Arrows (RIGHT) --}}
                        <div>
                            {{ $attendances->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                @endif

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
            if (qrCountdown > 0) qrCountdown--;
            document.getElementById('qr-timer').textContent = `${qrCountdown}s · refreshing soon`;
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
                        loadRecentScans();
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
            const overlay = document.getElementById('scan-success');
            const wrapper = document.getElementById('qr-wrapper');
            wrapper.classList.add('success-active');
            overlay.classList.remove('d-none');
            setTimeout(() => {
                overlay.classList.add('d-none');
                wrapper.classList.remove('success-active');
            }, 3200);
        }

        async function loadRecentScans() {
            try {
                const response = await fetch("{{ route('api.attendance.recent') }}", {
                    credentials: 'include'
                });
                const logs = await response.json();
                if (logs && logs.length > 0) location.reload();
            } catch {
                /* silent fail */
            }
        }

        window.addEventListener('load', () => {
            setInterval(loadRecentScans, 30000);
        });
    </script>

    <style>
        /* -- QR Monitor Panel ------------------------------------------ */
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
            color: rgba(255, 255, 255, 0.4);
        }

        .att-qr-wrapper {
            background: #fff;
            border-radius: 12px;
            padding: 14px;
            position: relative;
            transition: box-shadow 0.3s ease, transform 0.3s ease;
        }

        .att-qr-wrapper.success-active {
            box-shadow: 0 0 0 5px rgba(34, 197, 94, 0.5);
            transform: scale(1.03);
        }

        #qrcode {
            width: 200px;
            height: 200px;
        }

        .att-success-overlay {
            position: absolute;
            inset: 0;
            background: rgba(22, 163, 74, 0.95);
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

        .att-token {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 4px;
            color: #ffffff;
            font-family: 'Courier New', monospace;
        }

        .att-timer {
            font-size: 0.72rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.35);
        }

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
