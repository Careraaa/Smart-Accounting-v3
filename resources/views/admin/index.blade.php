@extends('layouts.qr-monitor')

@section('content')
<div class="w-full max-w-[1180px] mx-auto min-h-[calc(100vh-110px)] flex flex-col justify-center px-2">

    {{-- Header --}}
    <div class="mb-4 animate-[fadeSlideUp_0.4s_cubic-bezier(0.16,1,0.3,1)_both]">
        <p class="text-[0.65rem] font-bold uppercase tracking-[0.15em] text-rose-500 mb-1">Attendance</p>
        <h1 class="text-lg font-extrabold text-gray-900 tracking-tight">QR Monitor</h1>
        <p class="text-xs text-gray-400 leading-relaxed mt-0.5">Employees scan this code on their mobile app to log attendance.</p>
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.2fr] gap-5 items-stretch">

        {{-- Instructions --}}
        <section class="animate-[scaleIn_0.4s_cubic-bezier(0.16,1,0.3,1)_0.05s_both] bg-white border border-gray-200 rounded-2xl overflow-hidden flex flex-col shadow-sm">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center shrink-0">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">How to log attendance</h2>
                    <p class="text-xs text-gray-400">Quick steps for your phone</p>
                </div>
            </div>
            <div class="px-5 py-4 flex-1 flex flex-col gap-2">
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-600">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 text-rose-500 text-xs font-bold shrink-0">1</span>
                    <span>Open <strong class="text-gray-900">Smart Accounting</strong> on your phone</span>
                </div>
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-600">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 text-rose-500 text-xs font-bold shrink-0">2</span>
                    <span>Sign in with your registered email</span>
                </div>
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-600">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 text-rose-500 text-xs font-bold shrink-0">3</span>
                    <span>Tap <strong class="text-gray-900">Scan QR</strong> from the main menu</span>
                </div>
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-600">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 text-rose-500 text-xs font-bold shrink-0">4</span>
                    <span>Point your camera at the QR code</span>
                </div>
                <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-sm text-gray-600">
                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-rose-50 text-rose-500 text-xs font-bold shrink-0">5</span>
                    <span>Wait for <strong class="text-gray-900">Attendance Recorded</strong></span>
                </div>
            </div>
        </section>

        {{-- QR Code --}}
        <section class="animate-[scaleIn_0.4s_cubic-bezier(0.16,1,0.3,1)_0.1s_both] bg-white border border-gray-200 rounded-2xl overflow-hidden flex flex-col shadow-sm">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
                <div class="w-9 h-9 rounded-xl bg-gray-100 text-gray-900 flex items-center justify-center shrink-0">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Live code</h2>
                    <p class="text-xs text-gray-400">Auto-refreshes every 60 seconds</p>
                </div>
            </div>
            <div class="px-5 py-5 flex-1 flex flex-col items-center text-center">
                <p class="text-[0.65rem] font-bold uppercase tracking-[0.12em] text-gray-400 mb-4">Scan to log attendance</p>

                {{-- QR container with refresh animation --}}
                <div id="qr-wrapper" class="bg-white border border-gray-200 rounded-xl p-3.5 relative transition-all duration-300">
                    <div id="qr-inner" class="transition-all duration-300">
                        <div id="qrcode" class="w-[--qr-size] h-[--qr-size]" style="--qr-size: 180px;"></div>
                    </div>

                    {{-- Success overlay --}}
                    <div id="scan-success"
                         class="hidden absolute inset-0 bg-emerald-500 rounded-[10px] flex flex-col items-center justify-center text-white gap-2 z-10
                                animate-[successPop_0.5s_cubic-bezier(0.34,1.56,0.64,1)_both]">
                        <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center animate-[successRing_0.6s_ease-out_0.1s_both]">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" class="animate-[drawCheck_0.4s_ease-out_0.2s_both]"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold tracking-wide">Attendance Recorded</span>
                    </div>
                </div>

                <div id="token-display" class="mt-4 text-lg font-bold tracking-[0.15em] text-gray-900 font-mono">——</div>

                {{-- Timer with refresh indicator --}}
                <div id="qr-timer" class="mt-2 flex items-center gap-2 text-[0.65rem] font-semibold uppercase tracking-[0.1em] text-gray-400">
                    <svg id="timer-spinner" class="w-3 h-3 hidden animate-[spin_1s_linear_infinite]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span id="timer-text">Generating…</span>
                </div>
            </div>
        </section>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    let qrActive           = false;
    let tokenCheckInterval = null;
    let qrRefreshInterval  = null;
    let qrCountdown        = 60;
    let reloadPending      = false;
    let resizeDebounceTimer = null;

    function getResponsiveQRSize() {
        const minDim = Math.min(window.innerWidth - 100, 360);
        return Math.max(140, Math.min(360, minDim));
    }

    function renderQRCode(token) {
        if (!token || token === '——') return;
        const qrDiv = document.getElementById('qrcode');
        qrDiv.innerHTML = '';
        const qrSize = getResponsiveQRSize();
        qrDiv.style.setProperty('--qr-size', qrSize + 'px');
        new QRCode(qrDiv, {
            text: token,
            width: qrSize,
            height: qrSize,
            colorDark: '#111827',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    function animateQRRefresh() {
        const inner = document.getElementById('qr-inner');
        inner.style.transform = 'scale(0.85)';
        inner.style.opacity = '0';
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                inner.style.transition = 'transform 0.35s cubic-bezier(0.34,1.56,0.64,1), opacity 0.25s ease';
                inner.style.transform = 'scale(1)';
                inner.style.opacity = '1';
            });
        });
        setTimeout(() => {
            inner.style.transition = '';
        }, 400);
    }

    function loadQR() {
        reloadPending = false;
        document.getElementById('timer-text').textContent = 'Generating…';
        document.getElementById('timer-spinner').classList.remove('hidden');
        fetch("{{ route('hr.qr.generate') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            animateQRRefresh();
            setTimeout(() => renderQRCode(data.token), 50);
            document.getElementById('token-display').textContent = data.token;
            qrCountdown = 60;
            stopAll();
            startQRRefresh();
            startTokenStatusCheck();
            document.getElementById('timer-spinner').classList.add('hidden');
        })
        .catch(() => {
            document.getElementById('timer-text').textContent = 'Error — retrying…';
            document.getElementById('timer-spinner').classList.add('hidden');
            setTimeout(loadQR, 4000);
        });
    }

    function startQRRefresh() {
        qrRefreshInterval = setInterval(() => {
            if (reloadPending) return;
            qrCountdown--;
            const txt = document.getElementById('timer-text');
            txt.textContent = `${qrCountdown}s`;
            if (qrCountdown <= 5 && qrCountdown > 0) {
                txt.textContent = `${qrCountdown}s · refreshing`;
            }
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
            .catch(() => {});
    }

    function triggerSuccessThenReload() {
        const overlay = document.getElementById('scan-success');
        const wrapper = document.getElementById('qr-wrapper');
        wrapper.classList.add('ring-4', 'ring-emerald-400/40');
        overlay.classList.remove('hidden');
        document.getElementById('timer-text').textContent = 'Reloading…';
        setTimeout(() => { location.reload(); }, 3000);
    }

    function stopAll() {
        clearInterval(tokenCheckInterval);
        clearInterval(qrRefreshInterval);
        tokenCheckInterval = null;
        qrRefreshInterval  = null;
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadQR();
        window.addEventListener('resize', function () {
            clearTimeout(resizeDebounceTimer);
            resizeDebounceTimer = setTimeout(function () {
                const token = document.getElementById('token-display').textContent.trim();
                renderQRCode(token);
            }, 120);
        });
    });
</script>

<style>
#qrcode > img,
#qrcode > canvas {
    width: 100% !important;
    height: 100% !important;
    display: block;
}

@keyframes successPop {
    0%   { opacity:0; transform:scale(0.85); }
    100% { opacity:1; transform:scale(1); }
}
@keyframes successRing {
    0%   { transform:scale(0.5); opacity:0; }
    60%  { transform:scale(1.15); opacity:1; }
    100% { transform:scale(1); opacity:1; }
}
@keyframes drawCheck {
    0%   { stroke-dashoffset:20; stroke-dasharray:20; }
    100% { stroke-dashoffset:0; stroke-dasharray:20; }
}

@media (max-width: 1200px) {
    #qrcode { --qr-size: 160px; }
}
@media (max-width: 900px) {
    #qrcode { --qr-size: 140px; }
}
</style>
@endsection
