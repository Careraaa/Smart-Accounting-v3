@extends('layouts.layout')

@push('styles')
<style>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
@keyframes fadeSlideUp {
    0% { opacity: 0; transform: translateY(12px); }
    100% { opacity: 1; transform: translateY(0); }
}
@keyframes scaleIn {
    0% { opacity: 0; transform: scale(0.92); }
    100% { opacity: 1; transform: scale(1); }
}
@keyframes pulseGlow {
    0%, 100% { box-shadow: 0 0 0 0 rgba(5, 150, 105, 0.4); }
    50% { box-shadow: 0 0 0 8px rgba(5, 150, 105, 0); }
}
.anim-header { animation: fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.anim-card { animation: scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) 0.1s both; }
.anim-card:nth-child(2) { animation-delay: 0.15s; }
.spin-icon { animation: spin 1s linear infinite; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50/60">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">

        {{-- Top Bar --}}
        <div class="anim-header flex items-center justify-between gap-4 bg-gray-900 rounded-2xl px-6 py-5 mb-6 shadow-xl relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-44 h-44 rounded-full bg-rose-700/10 pointer-events-none"></div>
            <div class="absolute -bottom-12 left-1/4 w-40 h-40 rounded-full bg-white/[0.04] pointer-events-none"></div>

            <div class="relative z-10">
                <p class="text-xs font-bold tracking-widest text-gray-500 uppercase mb-0.5">Attendance</p>
                <h1 class="text-xl font-extrabold tracking-tight text-white leading-tight">Scan QR Code</h1>
            </div>

            <div class="relative z-10 flex flex-col items-end gap-1">
                <div class="flex flex-col items-end bg-white/10 border border-white/[0.14] rounded-xl px-4 py-2.5">
                    <div id="current-date" class="text-[0.6rem] font-semibold text-gray-400 leading-none mb-1"></div>
                    <div id="time-display" class="text-sm font-bold text-white tabular-nums leading-none font-mono"></div>
                </div>
            </div>
        </div>

        {{-- Two-column grid — scanner first --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Left — Scanner (first so user sees it immediately) --}}
            <section class="anim-card bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm flex flex-col">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center shrink-0">
                        <svg class="w-4.5 h-4.5 text-rose-600" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 leading-none mb-0.5">Start scanning</h2>
                        <p class="text-xs text-gray-400">Use your device camera</p>
                    </div>
                </div>
                <div class="p-5 flex-1 flex flex-col items-center gap-3">

                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 self-start">Scan QR Code</p>

                    {{-- Camera button --}}
                    <div id="camera-controls" class="w-full">
                        <button type="button" id="start-camera-btn"
                            class="w-full py-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 cursor-pointer transition-all disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9V5a2 2 0 012-2h4M3 15v4a2 2 0 002 2h4m6-18h4a2 2 0 012 2v4m0 6v4a2 2 0 01-2 2h-4"/></svg>
                            Start Camera
                        </button>
                        <p class="text-center text-xs text-gray-400 mt-2">Tap to request camera access</p>
                    </div>

                    {{-- Video stream --}}
                    <div id="video-container" class="hidden relative overflow-hidden rounded-xl bg-black w-full mb-1" style="aspect-ratio:4/3;">
                        <video id="camera-stream" playsinline autoplay muted webkit-playsinline
                            class="w-full h-full object-contain block"></video>
                        <canvas id="canvas" class="hidden"></canvas>
                        <div id="no-camera-message" class="hidden w-full h-full flex-col items-center justify-center absolute inset-0 bg-gray-900 text-white text-center">
                            <svg class="w-10 h-10 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            <p class="text-sm font-semibold">Camera Unavailable</p>
                            <p class="text-xs opacity-50">Use manual token entry below</p>
                        </div>
                    </div>

                    {{-- Status indicator --}}
                    <div id="scanner-status" class="w-full px-4 py-2.5 rounded-xl text-sm flex items-center gap-2 bg-gray-50 border border-gray-200 text-gray-500">
                        <svg class="w-4 h-4 spin-icon shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span id="status-text">Initializing camera…</span>
                    </div>

                    {{-- Scan result --}}
                    <div id="scan-result" class="hidden w-full px-4 py-2.5 rounded-xl text-sm flex items-center gap-2"></div>

                    {{-- Manual token --}}
                    <div class="mt-auto w-full">
                        <div class="flex items-center gap-3 my-3">
                            <div class="flex-1 h-px bg-gray-100"></div>
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">or enter token</span>
                            <div class="flex-1 h-px bg-gray-100"></div>
                        </div>
                        <form id="manual-form" class="flex gap-2">
                            <input type="text" id="manual-token"
                                placeholder="8-char token" required maxlength="8"
                                class="flex-1 bg-gray-50 border border-gray-300 rounded-xl text-sm font-semibold text-gray-900 tracking-widest px-4 py-2.5 placeholder-gray-400 outline-none transition-all duration-200 focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50">
                            <button type="submit"
                                class="px-4 py-2.5 bg-gray-900 hover:bg-gray-800 rounded-xl text-white text-sm font-semibold cursor-pointer transition-all shrink-0 inline-flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </section>

            {{-- Right — Instructions --}}
            <section class="anim-card bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm flex flex-col">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center shrink-0">
                        <svg class="w-4.5 h-4.5 text-rose-600" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 leading-none mb-0.5">How to log attendance</h2>
                        <p class="text-xs text-gray-400">Follow these steps on your device</p>
                    </div>
                </div>
                <div class="p-5 flex-1">
                    <ol class="flex flex-col gap-3">
                        <li class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-600 leading-snug">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold shrink-0">1</span>
                            <span>Allow <strong class="text-gray-800">camera access</strong> when prompted</span>
                        </li>
                        <li class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-600 leading-snug">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold shrink-0">2</span>
                            <span>Align your camera with the scanner</span>
                        </li>
                        <li class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-600 leading-snug">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold shrink-0">3</span>
                            <span>Hold steady — detection is <strong class="text-gray-800">automatic</strong></span>
                        </li>
                        <li class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-600 leading-snug">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold shrink-0">4</span>
                            <span>Wait for <strong class="text-gray-800">"Attendance Recorded"</strong> confirmation</span>
                        </li>
                    </ol>

                    <div class="mt-4 bg-gray-50 border border-gray-200 rounded-xl p-3.5 text-xs text-gray-600 leading-relaxed">
                        <strong class="text-gray-800">iOS:</strong> If camera is blocked, go to
                        <strong class="text-gray-800">Settings → Privacy → Camera</strong>, enable Safari, then reload.
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>

<script>
let video = null;
let canvas = null;
let ctx = null;
let scanner_running = false;
let scanFrameId = null;

const statusClasses = {
    idle:    'bg-gray-50 border border-gray-200 text-gray-500',
    success: 'bg-emerald-50 border border-emerald-200 text-emerald-700',
    danger:  'bg-rose-50 border border-rose-200 text-rose-700',
};

function updateClock() {
    const now = new Date();
    document.getElementById('time-display').textContent =
        now.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', second: '2-digit', hour12: true });
    document.getElementById('current-date').textContent =
        now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
}
setInterval(updateClock, 1000);
updateClock();

window.addEventListener('load', () => {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        updateScannerStatus('Browser does not support camera access', 'danger');
        document.getElementById('start-camera-btn').disabled = true;
        return;
    }
    fetchLastLog();
    document.getElementById('start-camera-btn').addEventListener('click', startCamera);
});

async function startCamera() {
    const btn = document.getElementById('start-camera-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 spin-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg><span>Starting...</span>';
    updateScannerStatus('Requesting camera access...', 'idle');
    await initializeCamera();
    if (!scanner_running) {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9V5a2 2 0 012-2h4M3 15v4a2 2 0 002 2h4m6-18h4a2 2 0 012 2v4m0 6v4a2 2 0 01-2 2h-4"/></svg>Start Camera';
    }
}

async function fetchLastLog() {
    try {
        const response = await fetch("{{ route('attendance.lastlog') }}", {
            headers: { Accept: 'application/json' }
        });
        if (!response.ok) return;
        const data = await response.json();
        if (data?.type) {
            document.getElementById('last-log-type') && (document.getElementById('last-log-type').textContent = data.type.replace('_', ' ').toUpperCase());
            document.getElementById('last-log-time') && (document.getElementById('last-log-time').textContent = data.time);
        }
    } catch (e) {}
}

async function initializeCamera() {
    video  = document.getElementById('camera-stream');
    canvas = document.getElementById('canvas');
    ctx    = canvas.getContext('2d', { willReadFrequently: true });
    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment' }, audio: false
        });
        video.srcObject = stream;
        await video.play();
        video.onloadedmetadata = () => { requestAnimationFrame(startScanning); };
    } catch (error) {
        handleCameraError(error);
    }
}

function startScanning() {
    if (!video || !video.videoWidth) { setTimeout(startScanning, 300); return; }
    scanner_running = true;
    document.getElementById('camera-controls').style.display = 'none';
    document.getElementById('video-container').classList.remove('hidden');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    updateScannerStatus('Camera ready — scanning...', 'idle');
    scanQRCode();
}

function scanQRCode() {
    if (!scanner_running) return;
    if (video.readyState < 2) { scanFrameId = requestAnimationFrame(scanQRCode); return; }
    try {
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const image = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code  = jsQR(image.data, canvas.width, canvas.height);
        if (code && code.data) { handleQRCode(code.data); return; }
    } catch (e) {}
    scanFrameId = requestAnimationFrame(scanQRCode);
}

function handleQRCode(data) {
    scanner_running = false;
    cancelAnimationFrame(scanFrameId);
    submitAttendance(data);
}

async function submitAttendance(token) {
    try {
        updateScannerStatus('Submitting attendance…', 'idle');
        const response = await fetch("{{ route('hr.qr.submit') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN':  '{{ csrf_token() }}',
                'Content-Type':  'application/json',
                'Accept':        'application/json'
            },
            body: JSON.stringify({ token })
        });
        const data = await response.json();
        if (response.ok) {
            showResult(data.message, 'success');
            updateScannerStatus('Attendance recorded!', 'success');
            setTimeout(async () => {
                document.getElementById('scan-result').classList.add('hidden');
                updateScannerStatus('Camera ready — scanning…', 'idle');
                await fetchLastLog();
                resumeScanning();
            }, 3000);
        } else {
            showResult(data.message || 'Failed to record attendance', 'danger');
            updateScannerStatus('Scan failed — try again', 'danger');
            setTimeout(() => { updateScannerStatus('Camera ready — scanning…', 'idle'); resumeScanning(); }, 3000);
        }
    } catch (e) {
        showResult('Network error. Check connection.', 'danger');
        updateScannerStatus('Error — try again', 'danger');
        setTimeout(() => { updateScannerStatus('Camera ready — scanning…', 'idle'); resumeScanning(); }, 3000);
    }
}

function resumeScanning() { scanner_running = true; scanQRCode(); }

function updateScannerStatus(text, type) {
    const el = document.getElementById('scanner-status');
    el.className = 'w-full px-4 py-2.5 rounded-xl text-sm flex items-center gap-2 ' + statusClasses[type];
    document.getElementById('status-text').textContent = text;
}

function showResult(message, type) {
    const el = document.getElementById('scan-result');
    clearTimeout(el.hideTimer);
    el.className = 'w-full px-4 py-2.5 rounded-xl text-sm flex items-center gap-2 ' + statusClasses[type];
    el.classList.remove('hidden');
    const icon = type === 'success'
        ? '<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>'
        : '<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>';
    el.innerHTML = icon + '<strong>' + message + '</strong>';
    el.hideTimer = setTimeout(() => {
        el.classList.add('hidden');
        if (scanner_running) updateScannerStatus('Camera ready — scanning…', 'idle');
    }, 3000);
}

function handleCameraError(error) {
    updateScannerStatus(error.message || 'Camera unavailable', 'danger');
    const btn = document.getElementById('start-camera-btn');
    btn.disabled = false;
    btn.innerHTML = '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9V5a2 2 0 012-2h4M3 15v4a2 2 0 002 2h4m6-18h4a2 2 0 012 2v4m0 6v4a2 2 0 01-2 2h-4"/></svg>Start Camera';
}

document.getElementById('manual-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const input = document.getElementById('manual-token');
    const token = input.value.trim();
    if (token.length !== 8) { showResult('Token must be 8 characters', 'danger'); return; }
    input.value = '';
    await submitAttendance(token);
});

window.addEventListener('beforeunload', () => {
    scanner_running = false;
    if (scanFrameId) cancelAnimationFrame(scanFrameId);
    if (video?.srcObject) video.srcObject.getTracks().forEach(t => t.stop());
});
</script>

@endsection
