@extends('layouts.qr-monitor')

@section('content')
    <div style="width: 100%; max-width: 1200px;">
        {{-- QR Monitor Panel --}}
        <div>
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
                        <div class="att-timer" id="qr-timer">Generating…</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- QR Monitor JavaScript & Styles --}}
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script>
        let qrActive           = false;
        let tokenCheckInterval = null;
        let qrRefreshInterval  = null;
        let qrCountdown        = 60;
        let reloadPending      = false;

        // ── Generate & render a fresh QR token ───────────────────────────
        function loadQR() {
            reloadPending = false;
            document.getElementById('qr-timer').textContent = 'Generating…';

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

                stopAll();
                startQRRefresh();
                startTokenStatusCheck();
            })
            .catch(() => {
                document.getElementById('qr-timer').textContent = 'Error generating QR — retrying…';
                setTimeout(loadQR, 4000);
            });
        }

        // ── Countdown timer that auto-refreshes QR every 60 s ───────────
        function startQRRefresh() {
            qrRefreshInterval = setInterval(() => {
                if (reloadPending) return;

                qrCountdown--;
                document.getElementById('qr-timer').textContent =
                    `${qrCountdown}s · refreshing soon`;

                if (qrCountdown <= 0) {
                    clearInterval(qrRefreshInterval);
                    loadQR();
                }
            }, 1000);
        }

        // ── Poll every 2 s to see whether the current token was scanned ──
        function startTokenStatusCheck() {
            tokenCheckInterval = setInterval(checkTokenStatus, 2000);
        }

        function stopTokenStatusCheck() {
            clearInterval(tokenCheckInterval);
            tokenCheckInterval = null;
        }

        function checkTokenStatus() {
            if (reloadPending) return;

            fetch("{{ route('api.qr.token-status') }}")
                .then(r => r.json())
                .then(data => {
                    if (data.used) {
                        reloadPending = true;
                        stopAll();
                        triggerSuccessThenReload();
                    }
                })
                .catch(() => { /* silent – network hiccup, keep polling */ });
        }

        // ── Show green overlay for ~3 s, then reload to refresh ────
        function triggerSuccessThenReload() {
            const overlay = document.getElementById('scan-success');
            const wrapper = document.getElementById('qr-wrapper');

            wrapper.classList.add('success-active');
            overlay.classList.remove('d-none');

            document.getElementById('qr-timer').textContent = 'Reloading…';

            setTimeout(() => {
                location.reload();
            }, 3000);
        }

        function stopAll() {
            clearInterval(tokenCheckInterval);
            clearInterval(qrRefreshInterval);
            tokenCheckInterval = null;
            qrRefreshInterval  = null;
        }

        // ── Initialize QR on page load ──────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            loadQR();
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
