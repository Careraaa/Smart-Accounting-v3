@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');

:root {
    --qr-scan-size: 220px;
}

.scn-page {
    font-family: 'Sora', sans-serif;
    width: 100%;
    max-width: 1180px;
    margin: 0 auto;
    box-sizing: border-box;
    min-height: calc(100dvh - 110px);
}

.scn-wrap {
    width: 100%;
    padding: 8px 4px 0;
}

.scn-content {
    width: 100%;
}

.scn-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 16px;
    flex-wrap: wrap;
    padding: 18px 20px;
    border-radius: 16px;
    background: #111827;
    border: 1px solid rgba(17,24,39,0.2);
    box-shadow: 0 14px 38px rgba(15,23,42,0.16);
    position: relative;
    overflow: hidden;
}

.scn-topbar::before {
    content: '';
    position: absolute;
    top: -56px;
    right: -46px;
    width: 190px;
    height: 190px;
    border-radius: 50%;
    background: rgba(200,41,42,0.13);
    pointer-events: none;
}

.scn-topbar::after {
    content: '';
    position: absolute;
    bottom: -68px;
    left: 26%;
    width: 170px;
    height: 170px;
    border-radius: 50%;
    background: rgba(255,255,255,0.045);
    pointer-events: none;
}

.scn-eyebrow {
    font-size: 1.35rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: #ffffff;
    margin: 0 0 2px;
    position: relative;
    z-index: 1;
}

.scn-title {
    font-size: 0.78rem;
    color: #9ca3af;
    margin: 0;
    position: relative;
    z-index: 1;
}

.scn-sub {
    font-size: 0.65rem;
    color: #6b7280;
    margin: 0;
    line-height: 1.45;
}

.scn-clock {
    font-family: 'DM Mono', monospace;
    font-variant-numeric: tabular-nums;
    font-size: 0.9rem;
    font-weight: 800;
    color: #ffffff;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.16);
    border-radius: 12px;
    padding: 10px 14px;
    box-shadow: 0 10px 30px rgba(17,24,39,0.06);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    position: relative;
    z-index: 1;
}

.scn-clock-container {
    display: flex;
    flex-direction: column;
    gap: 6px;
    align-items: flex-end;
}

.scn-clock-date {
    font-size: 0.6rem;
    color: #cbd5e1;
    font-weight: 600;
    font-family: 'Sora', sans-serif;
}

.scn-grid-layout {
    display: grid;
    grid-template-columns: minmax(320px, 1fr) minmax(420px, 560px);
    gap: 16px;
    align-items: stretch;
    justify-content: center;
}

@media (max-width: 900px) {
    .scn-grid-layout {
        grid-template-columns: 1fr;
    }
}

.scn-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    min-height: 0;
    position: relative;
}

.scn-card::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    border-radius: 0 0 14px 14px;
    background: #e5e7eb;
}

.scn-card--scanner::after {
    background: #c8292a;
}

.scn-card--scanner {
    width: 100%;
    max-width: 560px;
    justify-self: center;
}

.scn-card-head {
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.scn-card-icon {
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

.scn-card-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 2px;
    line-height: 1.25;
}

.scn-card-sub {
    font-size: 0.75rem;
    color: #6b7280;
    margin: 0;
}

.scn-card-body {
    padding: 18px 20px 22px;
    flex: 1;
}

.scn-card-body--center {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.scn-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #c8292a;
    display: inline-block;
}

.att-steps {
    padding-left: 0;
    margin: 0;
    list-style: none;
    counter-reset: scn;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.att-steps li {
    counter-increment: scn;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 0.9rem;
    color: #374151;
    line-height: 1.45;
    padding: 10px 12px;
    border-radius: 10px;
    background: #f9fafb;
    border: 1px solid #f3f4f6;
}

.att-steps li::before {
    content: counter(scn);
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 30px;
    height: 30px;
    border-radius: 8px;
    background: #fff0f0;
    color: #c8292a;
    font-size: 0.82rem;
    font-weight: 700;
    flex-shrink: 0;
}

.att-steps strong {
    color: #111827;
    font-weight: 600;
}

.scn-section-label {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #6b7280;
}

.scan-ios-note {
    background: #f4f5f7;
    border-radius: 8px;
    padding: 12px 14px;
    font-size: 0.75rem;
    color: #374151;
    line-height: 1.6;
}

/* Scanner specific styles */
.scan-panel-label {
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #6b7280;
    align-self: flex-start;
    width: 100%;
}

.scan-start-btn {
    width: 100%;
    padding: 11px 0;
    background: rgba(0,0,0,0.05);
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    color: #111827;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s;
}

.scan-start-btn:hover {
    background: rgba(0,0,0,0.08);
    border-color: #d1d5db;
}

.scan-start-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.scan-hint {
    text-align: center;
    font-size: 0.65rem;
    color: #6b7280;
    margin-top: 6px;
}

.scan-status {
    width: 100%;
    padding: 9px 14px;
    border-radius: 8px;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
}

.scan-status-idle {
    background: rgba(0,0,0,0.03);
    color: #374151;
}

.scan-status-success {
    background: rgba(22,163,74,0.15);
    color: #16a34a;
}

.scan-status-danger {
    background: rgba(200,41,42,0.15);
    color: #c8292a;
}

.scan-divider {
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #9ca3af;
    text-align: center;
    margin: 10px 0;
}

.scan-token-input {
    flex: 1;
    background: rgba(0,0,0,0.03) !important;
    border: 1px solid #9ca3af !important;
    border-radius: 8px !important;
    color: #111827 !important;
    font-size: 0.8rem !important;
    font-weight: 600 !important;
    letter-spacing: 2px !important;
    padding: 9px 14px !important;
}

.scan-token-input::placeholder {
    color: #9ca3af !important;
}

.scan-token-input:focus {
    background: rgba(0,0,0,0.05) !important;
    border-color: #bfdbfe !important;
    box-shadow: 0 0 0 3px rgba(200,41,42,0.08) !important;
    outline: none !important;
}

.scan-submit-btn {
    padding: 9px 16px;
    background: #c8292a;
    border: none;
    border-radius: 8px;
    color: #fff;
    font-size: 0.875rem;
    cursor: pointer;
    transition: background 0.15s;
    flex-shrink: 0;
}

.scan-submit-btn:hover {
    background: #a81f20;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.spin-icon {
    animation: spin 1s linear infinite;
    display: inline-block;
}

@media (max-width: 1200px) {
    :root {
        --qr-scan-size: 180px;
    }
}

@media (max-width: 900px) {
    :root {
        --qr-scan-size: 160px;
    }

    .scn-page {
        min-height: calc(100dvh - 110px);
        align-items: stretch;
    }

    .scn-topbar {
        padding: 14px 16px;
    }
}
</style>
@endpush

@section('content')
<div class="scn-page">
    <div class="scn-wrap">
        <div class="scn-content">

        <div class="scn-topbar">
            <div>
                <p class="scn-eyebrow">Attendance</p>
                <h1 class="scn-title">Scan QR Code</h1>
            </div>
            <div class="scn-clock-container">
                <div class="scn-clock" id="digital-clock">
                    <div class="scn-clock-date" id="current-date"></div>
                    <div id="time-display"></div>
                </div>
            </div>
        </div>

        <div class="scn-grid-layout">

            {{-- Left — Instructions --}}
            <section class="scn-card" aria-labelledby="scn-steps-title">
                <div class="scn-card-head">
                    <div class="scn-card-icon" aria-hidden="true">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 id="scn-steps-title" class="scn-card-title">How to log attendance</h2>
                        <p class="scn-card-sub">Follow these steps on your device</p>
                    </div>
                </div>
                <div class="scn-card-body">
                    <ol class="att-steps">
                        <li>Allow <strong>camera access</strong> when prompted</li>
                        <li>Align your camera with the scanner</li>
                        <li>Hold steady — detection is automatic</li>
                        <li>Wait for <strong>"Attendance Recorded"</strong> confirmation</li>
                    </ol>

                    {{-- iOS note --}}
                    <div class="scan-ios-note mt-4">
                        <strong>iOS:</strong>
                        If camera is blocked, go to <strong>Settings → Privacy → Camera</strong>, enable Safari, then reload.
                    </div>
                </div>
            </section>

            {{-- Right — Scanner --}}
            <section class="scn-card scn-card--scanner" aria-label="QR Code Scanner">
                <div class="scn-card-head">
                    <div class="scn-card-icon" aria-hidden="true">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="scn-card-title">Start scanning</h2>
                        <p class="scn-card-sub">Use your device camera</p>
                    </div>
                </div>
                <div class="scn-card-body scn-card-body--center">
                    <p class="scn-section-label mb-3">Scan QR Code</p>

                    {{-- Camera button --}}
                    <div id="camera-controls" class="w-100 mb-3">
                        <button type="button" class="scan-start-btn" id="start-camera-btn">
                            <i class="fa fa-camera me-2"></i>Start Camera
                        </button>
                        <div class="scan-hint">Tap to request camera access</div>
                    </div>

                    {{-- Video stream --}}
                    <div id="video-container" style="display:none; position:relative; overflow:hidden; border-radius:12px; background:#000; width:100%; aspect-ratio:4/3; margin-bottom:12px;">
                        <video id="camera-stream" playsinline autoplay muted webkit-playsinline
                            style="width:100%; height:100%; object-fit:contain; display:block;"></video>
                        <canvas id="canvas" style="display:none;"></canvas>
                        <div id="no-camera-message" class="text-white text-center"
                            style="display:none; width:100%; height:100%; flex-direction:column; align-items:center; justify-content:center; position:absolute; top:0; left:0; background:#111;">
                            <i class="fa fa-camera-slash" style="font-size:40px; margin-bottom:10px; opacity:.5;"></i>
                            <p class="mb-0 small fw-semibold">Camera Unavailable</p>
                            <p class="small opacity-50">Use manual token entry</p>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div id="scanner-status" class="scan-status scan-status-idle w-100 mb-3">
                        <i class="fa fa-spinner me-2 spin-icon"></i>
                        <span id="status-text">Initializing camera…</span>
                    </div>

                    {{-- Scan result --}}
                    <div id="scan-result" style="display:none;" class="scan-status w-100 mb-3"></div>

                    {{-- Manual token --}}
                    <div class="mt-auto w-100">
                        <div class="scan-divider">or enter token manually</div>
                        <form id="manual-form" class="d-flex gap-2">
                            <input type="text" class="scan-token-input" id="manual-token"
                                placeholder="8-char token" required maxlength="8">
                            <button class="scan-submit-btn" type="submit">
                                <i class="fa fa-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </section>
        </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
<script>
let video, canvas, ctx, scanner_running = false;
let scanFrameId = null;

function isIOS() {
    return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
}

function updateClock() {
    const now = new Date();
    document.getElementById('time-display').textContent =
        now.toLocaleTimeString([], { hour12: true });
    document.getElementById('current-date').textContent =
        now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
}
setInterval(updateClock, 1000);
updateClock();

window.addEventListener('load', async () => {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        updateScannerStatus('Browser does not support camera access', 'danger');
        document.getElementById('start-camera-btn').disabled = true;
        return;
    }
    await fetchLastLog();
    document.getElementById('start-camera-btn').addEventListener('click', async () => {
        const btn = document.getElementById('start-camera-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner me-2 spin-icon"></i>Requesting…';
        updateScannerStatus('Requesting camera permission…', 'idle');
        await initializeCamera();
        if (!scanner_running) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-camera me-2"></i>Start Camera';
        }
    });
});

async function fetchLastLog() {
    try {
        const r = await fetch("{{ route('attendance.lastlog') }}", { headers: { 'Accept': 'application/json' } });
        if (r.ok) { const d = await r.json(); if (d?.type) { displayLastLog(d); return; } }
        document.getElementById('last-log').style.display = 'none';
    } catch { document.getElementById('last-log').style.display = 'none'; }
}

function displayLastLog(logData) {
    document.getElementById('last-log').style.display = 'block';
    const badge = document.getElementById('last-log-type');
    badge.textContent = logData.type.toUpperCase().replace('_', ' ');
    badge.className = 'badge bg-' + (logData.type === 'time_in' ? 'success' : 'warning');
    document.getElementById('last-log-time').textContent = logData.time;
}

async function initializeCamera() {
    video  = document.getElementById('camera-stream');
    canvas = document.getElementById('canvas');
    ctx    = canvas.getContext('2d', { willReadFrequently: true });
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
        video.srcObject = stream;
        try { await video.play(); } catch {}
        video.onloadedmetadata = () => {
            if (video.videoWidth === 0) { setTimeout(() => { if (video.videoWidth > 0) startScanning(); }, 1000); return; }
            startScanning();
        };
        setTimeout(() => { if (!scanner_running && video.videoWidth > 0) startScanning(); }, 5000);
    } catch (error) { handleCameraError(error); }
}

function startScanning() {
    document.getElementById('camera-controls').style.display = 'none';
    const vc = document.getElementById('video-container');
    vc.style.display = 'block';
    document.getElementById('camera-stream').style.display = 'block';
    document.getElementById('no-camera-message').style.display = 'none';
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    scanner_running = true;
    updateScannerStatus('Camera ready — scanning…', 'idle');
    scanQRCode();
}

function handleCameraError(error) {
    let msg = 'Camera not available.', hint = 'Use manual token input below.';
    if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
        msg  = isIOS() ? 'Camera Permission Denied' : 'Permission Denied';
        hint = isIOS() ? 'Settings → Privacy → Camera → enable Safari.' : 'Allow camera access in browser settings.';
    } else if (error.name === 'NotFoundError')    { msg = 'No camera found on this device.'; }
    else if (error.name === 'NotReadableError')   { msg = 'Camera in use by another app.'; hint = 'Close other apps and try again.'; }
    else if (error.name === 'SecurityError')      { msg = 'HTTPS required for camera access.'; }

    document.getElementById('video-container').style.display = 'block';
    document.getElementById('camera-controls').style.display = 'block';
    document.getElementById('camera-stream').style.display = 'none';
    const noCam = document.getElementById('no-camera-message');
    noCam.style.display = 'flex';
    noCam.innerHTML = `<div style="text-align:center;"><i class="fa fa-camera-slash" style="font-size:40px;margin-bottom:10px;display:block;opacity:.5;"></i><p class="mb-0 fw-semibold small">${msg}</p><p class="small opacity-50">${hint}</p></div>`;
    const btn = document.getElementById('start-camera-btn');
    btn.disabled = false;
    btn.innerHTML = '<i class="fa fa-camera me-2"></i>Start Camera';
    updateScannerStatus(msg, 'danger');
}

function scanQRCode() {
    if (!scanner_running) return;
    if (video.readyState !== video.HAVE_ENOUGH_DATA) { scanFrameId = requestAnimationFrame(scanQRCode); return; }
    try {
        if (canvas.width !== video.videoWidth || canvas.height !== video.videoHeight) {
            canvas.width = video.videoWidth; canvas.height = video.videoHeight;
        }
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const code = jsQR(ctx.getImageData(0, 0, canvas.width, canvas.height).data, canvas.width, canvas.height);
        if (code) { handleQRCode(code.data); return; }
    } catch {}
    scanFrameId = requestAnimationFrame(scanQRCode);
}

function handleQRCode(qrData) {
    scanner_running = false;
    if (scanFrameId) cancelAnimationFrame(scanFrameId);
    try {
        const url = new URL(qrData);
        const token = url.searchParams.get('token');
        if (token) { submitAttendance(token); }
        else if (qrData.length === 20) { submitAttendance(qrData); }
        else { updateScannerStatus('Invalid QR code format', 'danger'); resumeScanning(); }
    } catch {
        if (qrData?.length > 0) { submitAttendance(qrData); }
        else { updateScannerStatus('Invalid QR code', 'danger'); resumeScanning(); }
    }
}

async function submitAttendance(token) {
    try {
        updateScannerStatus('Submitting attendance…', 'idle');
        const response = await fetch("{{ route('hr.qr.submit') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ token })
        });
        const data = await response.json();
        if (response.ok) {
            const logType = data.message.toLowerCase().includes('time in') ? 'time_in' : 'time_out';
            showResult(data.message, 'success');
            updateScannerStatus('Attendance recorded!', 'success');
            displayLastLog({ type: logType, time: new Date().toLocaleTimeString() });
            setTimeout(async () => { await fetchLastLog(); resumeScanning(); }, 3000);
        } else {
            showResult(data.message || 'Failed to record attendance', 'danger');
            updateScannerStatus('Scan failed — try again', 'danger');
            resumeScanning();
        }
    } catch {
        showResult('Network error. Check your connection.', 'danger');
        updateScannerStatus('Error — try again', 'danger');
        resumeScanning();
    }
}

function resumeScanning() {
    scanner_running = true;
    updateScannerStatus('Camera ready — scanning…', 'idle');
    scanQRCode();
}

function updateScannerStatus(message, type) {
    const el = document.getElementById('scanner-status');
    el.className = 'scan-status scan-status-' + (type || 'idle') + ' mb-3';
    document.getElementById('status-text').textContent = message;
}

function showResult(message, type) {
    const el = document.getElementById('scan-result');
    el.className = 'scan-status scan-status-' + type + ' mb-3';
    el.style.display = 'block';
    el.innerHTML = `<i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i><strong>${message}</strong>`;
    if (type === 'danger') setTimeout(() => { el.style.display = 'none'; }, 5000);
}

document.getElementById('manual-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const input = document.getElementById('manual-token');
    const token = input.value.trim().toUpperCase();
    if (!token) { showResult('Please enter a token', 'danger'); return; }
    if (token.length !== 8) { showResult('Token must be 8 characters', 'danger'); return; }
    input.value = '';
    scanner_running = false;
    if (scanFrameId) cancelAnimationFrame(scanFrameId);
    await submitAttendance(token);
});

window.addEventListener('beforeunload', function() {
    scanner_running = false;
    if (scanFrameId) cancelAnimationFrame(scanFrameId);
    if (video?.srcObject) video.srcObject.getTracks().forEach(t => t.stop());
});
</script>

@endsection