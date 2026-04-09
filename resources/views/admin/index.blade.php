@extends('layouts.qr-monitor')

@section('content')
<div class="qrmon-page">
    <div class="qrmon-wrap">
        <header class="qrmon-top">
            <p class="qrmon-eyebrow">Attendance</p>
            <h1 class="qrmon-title">QR monitor</h1>
            <p class="qrmon-sub">Employees scan this code in the mobile app to record attendance.</p>
        </header>

        <div class="qrmon-grid">
            {{-- Instructions --}}
            <section class="qrmon-card" aria-labelledby="qrmon-steps-title">
                <div class="qrmon-card-head">
                    <div class="qrmon-card-icon" aria-hidden="true">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 id="qrmon-steps-title" class="qrmon-card-title">How to log attendance</h2>
                        <p class="qrmon-card-sub">Follow these steps on your phone</p>
                    </div>
                </div>
                <div class="qrmon-card-body">
                    <ol class="qrmon-steps">
                        <li>Open <strong>Smart Accounting</strong> on your phone</li>
                        <li>Sign in with your registered email</li>
                        <li>Tap <strong>Scan QR</strong> from the main menu</li>
                        <li>Point your camera at the code on the right</li>
                        <li>Wait for the <strong>Attendance Recorded</strong> confirmation</li>
                    </ol>
                </div>
            </section>

            {{-- QR --}}
            <section class="qrmon-card qrmon-card--qr" aria-label="Attendance QR code">
                <div class="qrmon-card-head">
                    <div class="qrmon-card-icon qrmon-card-icon--live" aria-hidden="true">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="qrmon-card-title">Live code</h2>
                        <p class="qrmon-card-sub">Refreshes automatically for security</p>
                    </div>
                </div>
                <div class="qrmon-card-body qrmon-card-body--center">
                    <p class="qrmon-qr-label">Scan to log attendance</p>
                    <div class="qrmon-qr-frame" id="qr-wrapper">
                        <div id="qrcode"></div>
                        <div id="scan-success" class="qrmon-success-overlay d-none">
                            <i class="feather-check-circle"></i>
                            <span>Attendance Recorded</span>
                        </div>
                    </div>
                    <div class="qrmon-token" id="token-display">——</div>
                    <div class="qrmon-timer" id="qr-timer">Generating…</div>
                </div>
            </section>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    let qrActive           = false;
    let tokenCheckInterval = null;
    let qrRefreshInterval  = null;
    let qrCountdown        = 60;
    let reloadPending      = false;

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
                width: 220,
                height: 220,
                colorDark: '#111827',
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
            .catch(() => { /* silent */ });
    }

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

    document.addEventListener('DOMContentLoaded', function() {
        loadQR();
    });
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

.qrmon-page {
    font-family: 'Sora', sans-serif;
    width: 100%;
    max-width: 920px;
    margin: 0 auto;
    box-sizing: border-box;
}

.qrmon-wrap { padding: 0 4px; }

.qrmon-top { margin-bottom: 20px; }

.qrmon-eyebrow {
    font-size: 0.67rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: #c8292a;
    margin: 0 0 6px;
}

.qrmon-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: #111827;
    letter-spacing: -0.02em;
    margin: 0 0 4px;
    line-height: 1.2;
}

.qrmon-sub {
    font-size: 0.78rem;
    color: #9ca3af;
    margin: 0;
    line-height: 1.45;
}

.qrmon-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    align-items: stretch;
}

@media (max-width: 900px) {
    .qrmon-grid { grid-template-columns: 1fr; }
}

.qrmon-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    min-height: 0;
    position: relative;
}

.qrmon-card::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    border-radius: 0 0 14px 14px;
    background: #e5e7eb;
}

.qrmon-card--qr::after {
    background: #c8292a;
}

.qrmon-card-head {
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.qrmon-card-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #fff0f0;
    color: #c8292a;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.qrmon-card-icon--live {
    background: #f3f4f6;
    color: #111827;
}

.qrmon-card-title {
    font-size: 0.92rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 2px;
    line-height: 1.25;
}

.qrmon-card-sub {
    font-size: 0.72rem;
    color: #9ca3af;
    margin: 0;
}

.qrmon-card-body {
    padding: 18px 20px 22px;
    flex: 1;
}

.qrmon-card-body--center {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.qrmon-steps {
    padding-left: 0;
    margin: 0;
    list-style: none;
    counter-reset: qrm;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.qrmon-steps li {
    counter-increment: qrm;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 0.835rem;
    color: #374151;
    line-height: 1.45;
    padding: 10px 12px;
    border-radius: 10px;
    background: #f9fafb;
    border: 1px solid #f3f4f6;
}

.qrmon-steps li::before {
    content: counter(qrm);
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 26px;
    height: 26px;
    border-radius: 8px;
    background: #fff0f0;
    color: #c8292a;
    font-size: 0.72rem;
    font-weight: 700;
    flex-shrink: 0;
}

.qrmon-steps strong {
    color: #111827;
    font-weight: 600;
}

.qrmon-qr-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #9ca3af;
    margin: 0 0 14px;
}

.qrmon-qr-frame {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 14px;
    position: relative;
    transition: box-shadow 0.25s ease, transform 0.25s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.qrmon-qr-frame.success-active {
    box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.35);
    transform: scale(1.02);
}

#qrcode {
    width: 220px;
    height: 220px;
}

.qrmon-success-overlay {
    position: absolute;
    inset: 0;
    background: rgba(22, 163, 74, 0.95);
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 0.9rem;
    font-weight: 600;
    gap: 8px;
    z-index: 10;
}

.qrmon-success-overlay i {
    font-size: 2rem;
}

.qrmon-token {
    margin-top: 14px;
    font-size: 1.35rem;
    font-weight: 700;
    letter-spacing: 0.18em;
    color: #111827;
    font-family: 'DM Mono', monospace;
    font-variant-numeric: tabular-nums;
}

.qrmon-timer {
    margin-top: 8px;
    font-size: 0.68rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #9ca3af;
}
</style>
@endsection
