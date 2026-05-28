@extends('layouts.qr-monitor')

@section('content')
<div class="w-full h-full flex items-center justify-center px-6 py-4" style="background:radial-gradient(ellipse at 50% 30%, #1e293b 0%, #0f172a 100%)">

    {{-- Main QR Card --}}
    <div class="anim-card w-full max-w-[540px]">
        <div class="backdrop-blur-xl bg-white/95 rounded-3xl shadow-2xl shadow-black/30 overflow-hidden border border-white/20">

            {{-- Header --}}
            <div class="px-7 py-4 flex items-center justify-between border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-gray-900 text-white flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-gray-900 leading-tight">Attendance Scanner</h2>
                        <p class="text-[0.6rem] text-gray-400">Point your camera at the QR code</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[0.55rem] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200">Live</span>
                </div>
            </div>

            {{-- QR Body --}}
            <div class="px-7 py-6 flex flex-col items-center">
                <div id="qr-wrapper" class="relative inline-flex p-3 bg-white rounded-2xl border border-gray-200 shadow-sm">
                    <div id="qr-inner" class="transition-all duration-300">
                        <div id="qrcode" class="w-[--qr-size] h-[--qr-size]" style="--qr-size:280px"></div>
                    </div>
                    <div id="scan-success"
                         class="hidden absolute inset-0 bg-emerald-500 rounded-2xl flex flex-col items-center justify-center text-white gap-3 z-10
                                animate-[successPop_0.5s_cubic-bezier(0.34,1.56,0.64,1)_both]">
                        <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center animate-[successRing_0.6s_ease-out_0.1s_both]">
                            <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" class="animate-[drawCheck_0.4s_ease-out_0.2s_both]"/>
                            </svg>
                        </div>
                        <span class="text-base font-bold tracking-wide">Attendance Recorded</span>
                        <span class="text-xs text-white/80">Preparing new code…</span>
                    </div>
                </div>

                <div id="token-display" class="mt-5 text-xl font-bold tracking-[0.25em] text-gray-900 font-mono bg-gray-50 px-5 py-2 rounded-xl border border-gray-100">——</div>

                <div id="qr-timer" class="mt-4 flex items-center gap-2.5 text-[0.6rem] font-semibold uppercase tracking-[0.12em] text-gray-400">
                    <svg class="w-3 h-3 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span id="timer-text">Generating…</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
(function () {
    const QR_REFRESH_SECONDS = 60;
    let qrCountdown       = QR_REFRESH_SECONDS;
    let reloadPending     = false;
    let tokenCheckInt     = null;
    let refreshInt        = null;
    let resizeDeb         = null;

    const qrDiv     = document.getElementById('qrcode');
    const qrInner   = document.getElementById('qr-inner');
    const tokenEl   = document.getElementById('token-display');
    const timerText = document.getElementById('timer-text');
    const overlay   = document.getElementById('scan-success');
    const wrapper   = document.getElementById('qr-wrapper');

    function getQRSize() {
        return Math.max(200, Math.min(480, Math.min(window.innerWidth - 100, 480)));
    }

    function renderQR(token) {
        if (!token || token === '——') return;
        qrDiv.innerHTML = '';
        const size = getQRSize();
        qrDiv.style.setProperty('--qr-size', size + 'px');
        new QRCode(qrDiv, {
            text: token,
            width: size,
            height: size,
            colorDark: '#111827',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    function animateRefresh() {
        qrInner.style.transform = 'scale(0.88)';
        qrInner.style.opacity = '0';
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                qrInner.style.transition = 'transform 0.35s cubic-bezier(0.34,1.56,0.64,1), opacity 0.25s ease';
                qrInner.style.transform = 'scale(1)';
                qrInner.style.opacity = '1';
            });
        });
        setTimeout(() => { qrInner.style.transition = ''; }, 400);
    }

    function loadQR() {
        reloadPending = false;
        timerText.textContent = 'Generating…';
        fetch("{{ route('hr.qr.generate') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            animateRefresh();
            setTimeout(() => renderQR(data.token), 50);
            tokenEl.textContent = data.token;
            qrCountdown = QR_REFRESH_SECONDS;
            stopAll();
            refreshInt = setInterval(tick, 1000);
            tokenCheckInt = setInterval(checkToken, 2000);
        })
        .catch(() => {
            timerText.textContent = 'Retrying…';
            setTimeout(loadQR, 4000);
        });
    }

    function tick() {
        if (reloadPending) return;
        qrCountdown--;
        timerText.textContent = qrCountdown + 's';
        if (qrCountdown <= 0) {
            clearInterval(refreshInt);
            loadQR();
        }
    }

    function checkToken() {
        if (reloadPending) return;
        fetch("{{ route('api.qr.token-status') }}")
            .then(r => r.json())
            .then(d => {
                if (d.used) {
                    reloadPending = true;
                    stopAll();
                    showSuccess();
                }
            })
            .catch(() => {});
    }

    function showSuccess() {
        wrapper.classList.add('ring-4', 'ring-emerald-400/40');
        overlay.classList.remove('hidden');
        timerText.textContent = 'Reloading…';
        setTimeout(() => location.reload(), 3000);
    }

    function stopAll() {
        clearInterval(tokenCheckInt);
        clearInterval(refreshInt);
        tokenCheckInt = null;
        refreshInt = null;
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadQR();
        window.addEventListener('resize', () => {
            clearTimeout(resizeDeb);
            resizeDeb = setTimeout(() => {
                const t = tokenEl.textContent.trim();
                renderQR(t);
            }, 150);
        });
    });
})();
</script>

<style>
#qrcode > img,
#qrcode > canvas {
    width: 100% !important;
    height: 100% !important;
    display: block;
    border-radius: 10px;
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
</style>
@endsection
