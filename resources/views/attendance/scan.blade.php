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

@keyframes overlayExpand {
    0% { clip-path: circle(0%); }
    100% { clip-path: circle(150%); }
}
@keyframes successContentIn {
    0% { opacity: 0; transform: scale(0.85); }
    100% { opacity: 1; transform: scale(1); }
}
@keyframes drawCheck {
    0% { stroke-dashoffset: 22; }
    100% { stroke-dashoffset: 0; }
}

.viewfinder {
    position: absolute; inset: 0;
    border-radius: 12px;
    pointer-events: none;
}
.viewfinder .corners {
    position: absolute; inset: 12px;
}
.viewfinder .corners::before,
.viewfinder .corners::after {
    content: ''; position: absolute; width: 20px; height: 20px;
    border-color: rgba(255,255,255,0.6); border-style: solid;
}
.viewfinder .corners::before { top: 0; left: 0; border-width: 2px 0 0 2px; border-radius: 4px 0 0 0; }
.viewfinder .corners::after { top: 0; right: 0; border-width: 2px 2px 0 0; border-radius: 0 4px 0 0; }
.viewfinder .corners span:first-child { position: absolute; bottom: 0; left: 0; width: 20px; height: 20px; border-width: 0 0 2px 2px; border-style: solid; border-color: rgba(255,255,255,0.6); border-radius: 0 0 0 4px; }
.viewfinder .corners span:last-child { position: absolute; bottom: 0; right: 0; width: 20px; height: 20px; border-width: 0 2px 2px 0; border-style: solid; border-color: rgba(255,255,255,0.6); border-radius: 0 0 4px 0; }

.scan-line-bar {
    position: absolute; left: 24px; right: 24px; height: 1px; z-index: 10;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.7), rgba(255,255,255,0.85), rgba(255,255,255,0.7), transparent);
    animation: scanMove 2.2s ease-in-out infinite;
    top: 30%;
}
@keyframes scanMove {
    0%   { top: 20%; opacity: 0; }
    15%  { opacity: 0.6; }
    85%  { opacity: 0.5; }
    100% { top: 80%; opacity: 0; }
}
</style>
@endpush

@section('content')
<div class="min-h-screen">
    <div class="max-w-5xl mx-auto px-3 sm:px-6 py-4 sm:py-8">

        {{-- Top Bar --}}
        <div class="anim-header flex items-center justify-between gap-4 mb-4 sm:mb-6">
            <div>
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-0.5">Attendance</p>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Scan QR Code</h1>
            </div>
            <div class="flex flex-col items-end gap-1 bg-white dark:bg-[#0a0a14] border border-gray-200 dark:border-[#18182a] rounded-xl px-4 py-2.5">
                <div id="current-date" class="text-[0.6rem] font-semibold text-gray-400 dark:text-gray-500 leading-none mb-1"></div>
                <div id="time-display" class="text-sm font-bold text-gray-700 dark:text-gray-300 tabular-nums leading-none font-mono"></div>
            </div>
        </div>

        {{-- Single column on mobile, two on desktop --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5">

            {{-- Left — Scanner --}}
            <section class="anim-card bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm flex flex-col md:order-1 relative">
                {{-- Header --}}
                <div class="flex items-center gap-3 px-4 sm:px-5 py-3.5 sm:py-4 border-b border-gray-100">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center shrink-0">
                        <svg class="w-4.5 h-4.5 text-rose-600" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 leading-none mb-0.5">Scan QR Code</h2>
                        <p id="scanner-subtitle" class="text-xs text-gray-400">Point your camera at the QR code</p>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-4 sm:p-5 flex-1 flex flex-col items-center gap-3">

                    {{-- Start button (always visible) --}}
                    <div id="camera-start-area" class="w-full flex flex-col items-center gap-3">
                        <button type="button" id="start-camera-btn"
                            class="w-full py-3.5 bg-gray-900 hover:bg-gray-800 text-white font-bold text-sm rounded-xl cursor-pointer active:scale-[0.97] transition-all duration-150 inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9V5a2 2 0 012-2h4M3 15v4a2 2 0 002 2h4m6-18h4a2 2 0 012 2v4m0 6v4a2 2 0 01-2 2h-4"/></svg>
                            Start Camera
                        </button>
                        <p class="text-xs text-gray-400">Allow camera access when prompted</p>
                    </div>

                    {{-- Camera view (hidden until active) --}}
                    <div id="video-container" class="hidden relative overflow-hidden rounded-xl bg-black w-full" style="aspect-ratio:3/4; max-height: 420px;">
                        <video id="camera-stream" playsinline autoplay muted webkit-playsinline
                            class="w-full h-full object-cover block"></video>
                        <canvas id="canvas" class="hidden"></canvas>

                        {{-- Viewfinder overlay --}}
                        <div class="viewfinder">
                            <div class="corners">
                                <span></span><span></span>
                            </div>
                            <div class="scan-line-bar"></div>
                        </div>

                        {{-- No camera fallback --}}
                        <div id="no-camera-message" class="hidden w-full h-full flex-col items-center justify-center absolute inset-0 bg-gray-900 text-white text-center z-20">
                            <svg class="w-10 h-10 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            <p class="text-sm font-semibold">Camera Unavailable</p>
                            <p class="text-xs opacity-50 mt-1">Use the manual token entry below</p>
                        </div>
                    </div>

                    {{-- Status bar --}}
                    <div id="scanner-status" class="w-full px-4 py-2.5 rounded-xl text-sm flex items-center gap-2 bg-gray-50 border border-gray-200 text-gray-500">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9V5a2 2 0 012-2h4M3 15v4a2 2 0 002 2h4m6-18h4a2 2 0 012 2v4m0 6v4a2 2 0 01-2 2h-4"/></svg>
                        <span id="status-text">Start camera to begin scanning</span>
                    </div>

                    {{-- Scan result --}}
                    <div id="scan-result" class="hidden w-full px-4 py-2.5 rounded-xl text-sm flex items-center gap-2"></div>

                    {{-- Manual fallback --}}
                    <div class="mt-2 w-full">
                        <div class="flex items-center gap-3 my-2">
                            <div class="flex-1 h-px bg-gray-100"></div>
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">or enter token</span>
                            <div class="flex-1 h-px bg-gray-100"></div>
                        </div>
                        <form id="manual-form" class="flex gap-2">
                            <input type="text" id="manual-token"
                                placeholder="8-character code" required maxlength="8"
                                class="flex-1 bg-gray-50 border border-gray-300 rounded-xl text-sm font-semibold text-gray-900 tracking-widest px-4 py-2.5 placeholder-gray-400 outline-none transition-all duration-200 focus:border-gray-400 focus:ring-2 focus:ring-gray-200/50 uppercase">
                            <button type="submit"
                                class="px-4 py-2.5 bg-gray-900 hover:bg-gray-800 rounded-xl text-white text-sm font-semibold cursor-pointer transition-all shrink-0 inline-flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Success overlay --}}
                <div id="success-overlay" class="hidden absolute inset-0 z-30 rounded-2xl bg-[#10b981] flex flex-col items-center justify-center text-white overflow-hidden cursor-default" style="clip-path: circle(0%);">
                    <div id="overlay-content" class="flex flex-col items-center gap-4 px-6" style="opacity: 0; transform: scale(0.85);">
                        <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center">
                            <svg class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <path class="check-path" d="M5 13l4 4L19 7" style="stroke-dasharray: 22; stroke-dashoffset: 22;" />
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="text-lg font-bold leading-tight">Attendance Recorded!</p>
                            <p id="overlay-message" class="text-sm text-white/80 mt-1 leading-snub"></p>
                        </div>
                        <div class="flex items-center gap-3 mt-3">
                            <button type="button" id="scan-again-btn"
                                class="px-5 py-2.5 bg-white text-[#10b981] text-sm font-bold rounded-xl cursor-pointer active:scale-[0.97] transition-all duration-150 inline-flex items-center gap-1.5 shadow-lg hover:shadow-xl whitespace-nowrap">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span class="shrink-0">Scan Again</span>
                            </button>
                            <a href="{{ route('dashboard') }}"
                                class="px-5 py-2.5 bg-white/15 hover:bg-white/25 text-white text-sm font-semibold rounded-xl cursor-pointer transition-all inline-flex items-center gap-2 border border-white/20 active:scale-[0.97]">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Right — Instructions (hidden on mobile in favor of inline hints) --}}
            <section class="anim-card bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm flex-col hidden md:flex">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
                    <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center shrink-0">
                        <svg class="w-4.5 h-4.5 text-rose-600" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 leading-none mb-0.5">How to log attendance</h2>
                        <p class="text-xs text-gray-400">Follow these steps</p>
                    </div>
                </div>
                <div class="p-5 flex-1">
                    <ol class="flex flex-col gap-3">
                        <li class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-600 leading-snug">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold shrink-0">1</span>
                            <span>Tap <strong class="text-gray-800">Start</strong> and allow camera access</span>
                        </li>
                        <li class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-600 leading-snug">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold shrink-0">2</span>
                            <span>Align the QR code within the frame</span>
                        </li>
                        <li class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-600 leading-snug">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold shrink-0">3</span>
                            <span>Hold steady — <strong class="text-gray-800">auto-detected</strong></span>
                        </li>
                        <li class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-600 leading-snug">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold shrink-0">4</span>
                            <span>Wait for <strong class="text-gray-800">"Attendance Recorded"</strong></span>
                        </li>
                    </ol>

                    <div class="mt-4 bg-gray-50 border border-gray-200 rounded-xl p-3.5 text-xs text-gray-600 leading-relaxed">
                        <strong class="text-gray-800">iOS:</strong> Camera blocked? Go to
                        <strong class="text-gray-800">Settings → Privacy → Camera</strong>, enable Safari, reload.
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<audio id="qr-success-sound" src="{{ asset('sounds/qr-success.mp3') }}" preload="auto"></audio>
<script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>

<script>
let video = null;
let canvas = null;
let ctx = null;
let scannerRunning = false;
let scanFrameId = null;

const statusClasses = {
    idle:    'bg-gray-50 border border-gray-200 text-gray-500',
    success: 'bg-emerald-50 border border-emerald-200 text-emerald-700',
    danger:  'bg-rose-50 border border-rose-200 text-rose-700',
};

const statusIcons = {
    idle:    '<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9V5a2 2 0 012-2h4M3 15v4a2 2 0 002 2h4m6-18h4a2 2 0 012 2v4m0 6v4a2 2 0 01-2 2h-4"/></svg>',
    success: '<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>',
    danger:  '<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>',
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

function updateStatus(text, type) {
    const el = document.getElementById('scanner-status');
    el.className = 'w-full px-4 py-2.5 rounded-xl text-sm flex items-center gap-2 ' + statusClasses[type];
    el.innerHTML = statusIcons[type] + '<span>' + text + '</span>';
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
    el.hideTimer = setTimeout(function () { el.classList.add('hidden'); }, 3000);
}

function showCameraError(msg) {
    updateStatus(msg || 'Camera unavailable', 'danger');
    document.getElementById('camera-start-area').classList.add('hidden');
    document.getElementById('video-container').classList.remove('hidden');
    document.getElementById('no-camera-message').classList.remove('hidden');
}

async function startCamera() {
    updateStatus('Requesting camera access…', 'idle');
    document.getElementById('start-camera-btn').disabled = true;
    document.getElementById('start-camera-btn').innerHTML =
        '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg> Starting…';

    video  = document.getElementById('camera-stream');
    canvas = document.getElementById('canvas');
    ctx    = canvas.getContext('2d', { willReadFrequently: true });

    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment' },
            audio: false
        });
        video.srcObject = stream;
        video.setAttribute('playsinline', '');
        video.setAttribute('autoplay', '');
        video.setAttribute('muted', '');
        await video.play();

        document.getElementById('camera-start-area').classList.add('hidden');
        document.getElementById('video-container').classList.remove('hidden');

        function startScanningFromVideo() {
            var w = video.videoWidth || video.scrollWidth || 320;
            var h = video.videoHeight || video.scrollHeight || 240;
            canvas.width  = w;
            canvas.height = h;
            scannerRunning = true;
            updateStatus('Camera ready — scanning…', 'idle');
            scanQRCode();
        }

        if (video.readyState >= 1 && video.videoWidth > 0) {
            startScanningFromVideo();
        } else {
            video.addEventListener('loadedmetadata', startScanningFromVideo, { once: true });
        }
    } catch (error) {
        document.getElementById('start-camera-btn').disabled = false;
        document.getElementById('start-camera-btn').innerHTML =
            '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9V5a2 2 0 012-2h4M3 15v4a2 2 0 002 2h4m6-18h4a2 2 0 012 2v4m0 6v4a2 2 0 01-2 2h-4"/></svg> Start Camera';
        showCameraError(error.message || 'Camera access denied');
    }
}

function scanQRCode() {
    if (!scannerRunning) return;
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
    scannerRunning = false;
    cancelAnimationFrame(scanFrameId);
    submitAttendance(data);
}

function stopCamera() {
    scannerRunning = false;
    if (scanFrameId) cancelAnimationFrame(scanFrameId);
    if (video && video.srcObject) {
        video.srcObject.getTracks().forEach(function (t) { t.stop(); });
        video.srcObject = null;
    }
}

function showSuccessOverlay(message) {
    var overlay = document.getElementById('success-overlay');
    document.getElementById('overlay-message').textContent = message || 'Your attendance has been logged.';
    overlay.classList.remove('hidden');
    overlay.style.transition = 'clip-path 0.45s cubic-bezier(0.34,1.56,0.64,1)';
    overlay.style.clipPath = 'circle(150%)';
    setTimeout(function () {
        var content = document.getElementById('overlay-content');
        content.style.transition = 'opacity 0.25s ease-out, transform 0.3s cubic-bezier(0.34,1.56,0.64,1)';
        content.style.opacity = '1';
        content.style.transform = 'scale(1)';
        setTimeout(function () {
            var check = document.querySelector('#success-overlay .check-path');
            if (check) { check.style.transition = 'stroke-dashoffset 0.35s ease-out 0.05s'; check.style.strokeDashoffset = '0'; }
        }, 50);
    }, 180);
    stopCamera();
    document.getElementById('video-container').classList.add('hidden');
}

function hideSuccessOverlay() {
    var overlay = document.getElementById('success-overlay');
    var check = document.querySelector('#success-overlay .check-path');
    if (check) { check.style.transition = 'none'; check.style.strokeDashoffset = '22'; }
    var content = document.getElementById('overlay-content');
    content.style.transition = 'none';
    content.style.opacity = '0';
    content.style.transform = 'scale(0.85)';
    overlay.style.transition = 'none';
    overlay.style.clipPath = 'circle(0%)';
    overlay.classList.add('hidden');
}

async function submitAttendance(token) {
    try {
        updateStatus('Submitting attendance…', 'idle');
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
            document.getElementById('qr-success-sound').play().catch(function(){});
            showSuccessOverlay(data.message);
        } else {
            showResult(data.message || 'Failed to record attendance', 'danger');
            updateStatus('Scan failed — try again', 'danger');
            setTimeout(function () {
                updateStatus('Camera ready — scanning…', 'idle');
                scannerRunning = true;
                scanQRCode();
            }, 3000);
        }
    } catch (e) {
        showResult('Network error. Check connection.', 'danger');
        updateStatus('Error — try again', 'danger');
        setTimeout(function () {
            updateStatus('Camera ready — scanning…', 'idle');
            scannerRunning = true;
            scanQRCode();
        }, 3000);
    }
}

// ── Init ──
document.addEventListener('DOMContentLoaded', function () {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        updateStatus('Browser does not support camera access', 'danger');
        document.getElementById('start-camera-btn').disabled = true;
        return;
    }
    document.getElementById('start-camera-btn').addEventListener('click', startCamera);
    document.getElementById('manual-form').addEventListener('submit', async function (e) {
        e.preventDefault();
        const input = document.getElementById('manual-token');
        const token = input.value.trim();
        if (token.length !== 8) { showResult('Token must be 8 characters', 'danger'); return; }
        input.value = '';
        await submitAttendance(token);
    });
    document.getElementById('scan-again-btn').addEventListener('click', function () {
        hideSuccessOverlay();
        document.getElementById('scanner-subtitle').textContent = 'Point your camera at the QR code';
        document.getElementById('camera-start-area').classList.remove('hidden');
        document.getElementById('start-camera-btn').disabled = false;
        document.getElementById('start-camera-btn').innerHTML =
            '<svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9V5a2 2 0 012-2h4M3 15v4a2 2 0 002 2h4m6-18h4a2 2 0 012 2v4m0 6v4a2 2 0 01-2 2h-4"/></svg> Start Camera';
        updateStatus('Start camera to begin scanning', 'idle');
    });
});

window.addEventListener('beforeunload', function () {
    scannerRunning = false;
    if (scanFrameId) cancelAnimationFrame(scanFrameId);
    if (video && video.srcObject) video.srcObject.getTracks().forEach(function (t) { t.stop(); });
});
</script>

@endsection
