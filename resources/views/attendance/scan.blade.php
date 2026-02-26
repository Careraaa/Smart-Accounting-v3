@extends('layouts.layout')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Scan QR Attendance</h5>
                    <div class="small text-muted" id="current-date"></div>
                </div>
                <div class="scan-clock" id="digital-clock"></div>
            </div>
        </div>
    </div>

    <div class="row g-3 align-items-stretch">

        {{-- Left — Scanner --}}
        <div class="col-12 col-lg-5">
            <div class="scan-panel h-100">

                <div class="scan-panel-label">Scan QR Code</div>

                {{-- Camera button --}}
                <div id="camera-controls" class="w-100">
                    <button type="button" class="scan-start-btn" id="start-camera-btn">
                        <i class="fa fa-camera me-2"></i>Start Camera
                    </button>
                    <div class="scan-hint">Tap to request camera access</div>
                </div>

                {{-- Video stream --}}
                <div id="video-container" style="display:none; position:relative; overflow:hidden; border-radius:12px; background:#000; width:100%; aspect-ratio:4/3;" class="mb-3">
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
                <div id="scanner-status" class="scan-status scan-status-idle mb-3">
                    <i class="fa fa-spinner me-2 spin-icon"></i>
                    <span id="status-text">Initializing camera…</span>
                </div>

                {{-- Scan result --}}
                <div id="scan-result" style="display:none;" class="scan-status mb-3"></div>

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
        </div>

        {{-- Right — Info & Instructions --}}
        <div class="col-12 col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span class="card-title mb-0">
                        Welcome, <strong>{{ auth()->user()->name }}</strong>
                    </span>
                    <span class="scan-time-badge" id="current-time"></span>
                </div>
                <div class="card-body">

                    {{-- Last log --}}
                    <div id="last-log" style="display:none;" class="mb-4">
                        <div class="scan-last-log">
                            <span class="scan-last-log-label">Last Log</span>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span id="last-log-type" class="badge"></span>
                                <span class="text-muted small" id="last-log-time"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Instructions --}}
                    <p class="scan-section-label mb-3">How to log attendance</p>
                    <ol class="att-steps mb-4">
                        <li>Open <strong>Smart Accounting</strong> on your phone</li>
                        <li>Allow camera access when prompted</li>
                        <li>Align your camera with the scanner on the left</li>
                        <li>Hold steady — detection is automatic</li>
                        <li>Wait for the <strong>"Attendance Recorded"</strong> confirmation</li>
                    </ol>

                    {{-- iPhone note --}}
                    <div class="scan-ios-note">
                        <strong>iPhone / iPad:</strong>
                        If camera is blocked, go to <strong>Settings → Privacy → Camera</strong>, enable Safari, then reload.
                    </div>

                </div>
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
    document.getElementById('digital-clock').textContent =
        now.toLocaleTimeString([], { hour12: true });
    document.getElementById('current-date').textContent =
        now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
    document.getElementById('current-time').textContent = now.toLocaleTimeString();
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

<style>
/* -- Scanner panel --------------------------------------------- */
.scan-panel {
    background: #1c1c1e;
    border-radius: 12px;
    padding: 28px 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 14px;
}

.scan-panel-label {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 1.8px;
    text-transform: uppercase;
    color: rgba(255,255,255,0.35);
    align-self: flex-start;
}

/* Start camera button */
.scan-start-btn {
    width: 100%;
    padding: 11px 0;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 8px;
    color: #fff;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s;
}
.scan-start-btn:hover   { background: rgba(255,255,255,0.14); border-color: rgba(255,255,255,0.22); }
.scan-start-btn:disabled { opacity: .5; cursor: not-allowed; }

.scan-hint {
    text-align: center;
    font-size: 0.72rem;
    color: rgba(255,255,255,0.3);
    margin-top: 6px;
}

/* Status pills */
.scan-status {
    width: 100%;
    padding: 9px 14px;
    border-radius: 8px;
    font-size: 0.845rem;
    display: flex;
    align-items: center;
}
.scan-status-idle    { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.7); }
.scan-status-success { background: rgba(22,163,74,0.2);   color: #4ade80; }
.scan-status-danger  { background: rgba(200,41,42,0.25);  color: #f87171; }

/* Divider */
.scan-divider {
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: rgba(255,255,255,0.25);
    text-align: center;
    margin-bottom: 10px;
}

/* Token input row */
.scan-token-input {
    flex: 1;
    background: rgba(255,255,255,0.07) !important;
    border: 1px solid rgba(255,255,255,0.12) !important;
    border-radius: 8px !important;
    color: #fff !important;
    font-size: 0.875rem !important;
    font-weight: 600 !important;
    letter-spacing: 2px !important;
    padding: 9px 14px !important;
}
.scan-token-input::placeholder { color: rgba(255,255,255,0.25) !important; }
.scan-token-input:focus {
    background: rgba(255,255,255,0.1) !important;
    border-color: rgba(255,255,255,0.28) !important;
    box-shadow: none !important;
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
.scan-submit-btn:hover { background: #a81f20; }

/* -- Right panel ------------------------------------------------ */
.scan-clock {
    font-size: 1.35rem;
    font-weight: 700;
    color: #1c1c1e;
    font-variant-numeric: tabular-nums;
}

.scan-time-badge {
    font-size: 0.775rem;
    font-weight: 600;
    color: rgba(255,255,255,0.85);
    background: #1c1c1e;
    padding: 3px 12px;
    border-radius: 20px;
    font-variant-numeric: tabular-nums;
}

.scan-last-log {
    background: #f4f5f7;
    border-radius: 8px;
    padding: 12px 16px;
}
.scan-last-log-label {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: #9898a8;
}

.scan-section-label {
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 1.4px;
    text-transform: uppercase;
    color: #9898a8;
}

/* Instructions */
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

/* iOS note */
.scan-ios-note {
    background: #f4f5f7;
    border-radius: 8px;
    padding: 12px 14px;
    font-size: 0.815rem;
    color: #4a4a58;
    line-height: 1.6;
}

/* Spin animation */
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
.spin-icon { animation: spin 1s linear infinite; display: inline-block; }
</style>
@endsection