@extends('layouts.qr-monitor')

@section('content')

<div class="relative w-full h-full flex flex-col overflow-hidden select-none bg-[#f6f5f2]" id="qr-page">

    {{-- ── Single warm ambient field ── --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute w-[70vw] h-[70vw] rounded-full" style="top:-25%; left:-15%; background: radial-gradient(circle, rgba(180,160,130,0.08) 0%, transparent 60%); animation: lightDrift1 35s ease-in-out infinite;"></div>
        <div class="absolute w-[50vw] h-[50vw] rounded-full" style="bottom:-20%; right:-10%; background: radial-gradient(circle, rgba(167,139,250,0.04) 0%, transparent 55%); animation: lightDrift2 30s ease-in-out infinite reverse;"></div>
    </div>

    {{-- ── MAIN CONTENT ── --}}
    <div class="relative z-10 flex-1 flex flex-col items-center justify-center gap-6" id="main-content">

        {{-- Clock --}}
        <div class="animate-headline inline-flex items-center gap-3 px-4 py-2 rounded-full bg-white/70 border border-gray-200/50 shadow-sm">
            <span class="relative flex w-2.5 h-2.5">
                <span class="absolute inset-0 rounded-full bg-emerald-400" style="animation: pulseDot 1.2s ease-in-out infinite;"></span>
                <span class="absolute inset-0 rounded-full bg-emerald-400/30" style="animation: rippleDot 2s ease-out infinite;"></span>
            </span>
            <span id="live-clock" class="text-xs font-mono font-semibold text-gray-500 tabular-nums tracking-wide"></span>
            <span class="w-px h-3 bg-gray-200"></span>
            <span class="text-[0.55rem] font-semibold text-gray-400 tracking-[0.2em] uppercase">Terminal</span>
        </div>

        {{-- ── QR Code Card ── --}}
        <div class="relative animate-qr" id="qr-card">

            <div class="relative bg-white rounded-2xl shadow-xl border border-gray-100/80" id="qr-card-inner"
                 style="box-shadow: 0 8px 30px -6px rgba(0,0,0,0.04), 0 2px 8px -4px rgba(0,0,0,0.02);">

                <div class="relative rounded-2xl overflow-hidden bg-white p-5">

                    {{-- Block overlay --}}
                    <div id="block-overlay" class="hidden absolute inset-0 z-20 rounded-2xl bg-white"></div>

                    {{-- QR inner --}}
                    <div id="qr-inner" class="relative">
                        {{-- Scan line --}}
                        <div id="scan-line" class="absolute left-12 right-12 h-px z-10 pointer-events-none"
                             style="background: linear-gradient(90deg, transparent, rgba(100,100,100,0.25), rgba(120,120,120,0.3), rgba(100,100,100,0.25), transparent); animation: scanLine 2.5s ease-in-out infinite; top: 48%;"></div>
                        {{-- Loading shimmer (shown during refresh) --}}
                        <div id="qr-loading" class="hidden absolute inset-0 z-15 flex flex-col items-center justify-center gap-4 rounded-2xl bg-white/85 backdrop-blur-[2px]">
                            <div class="flex items-center gap-2.5">
                                <span class="block w-2.5 h-2.5 rounded-full bg-gray-400 shadow-[0_0_8px_rgba(0,0,0,0.08)]" style="animation: loadDot 0.9s ease-in-out infinite;"></span>
                                <span class="block w-2.5 h-2.5 rounded-full bg-gray-400 shadow-[0_0_8px_rgba(0,0,0,0.08)]" style="animation: loadDot 0.9s ease-in-out 0.15s infinite;"></span>
                                <span class="block w-2.5 h-2.5 rounded-full bg-gray-400 shadow-[0_0_8px_rgba(0,0,0,0.08)]" style="animation: loadDot 0.9s ease-in-out 0.3s infinite;"></span>
                            </div>
                            <span class="text-[0.55rem] font-bold text-gray-400 tracking-widest uppercase">Refreshing</span>
                        </div>
                        <div id="qrcode" class="relative"></div>
                    </div>

                    {{-- Success overlay --}}
                    <div id="scan-success" class="hidden absolute inset-0 z-30 flex flex-col items-center justify-center gap-5 rounded-2xl bg-[#e74c3c]"
                         style="clip-path: circle(0% at 50% 50%);">
                        <div class="flex flex-col items-center justify-center gap-4" style="opacity: 0; transform: scale(0.88);">
                            <div class="w-28 h-28 rounded-full bg-white/20 flex items-center justify-center shadow-xl">
                                <svg class="w-14 h-14 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path id="check-path" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="text-center">
                                <p class="text-xl font-bold text-white tracking-tight">Attendance Recorded</p>
                                <p class="text-[0.7rem] text-white/60 mt-1">Generating new code…</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Token + Timer ── --}}
        <div class="flex flex-col items-center gap-3 animate-token">
            <div class="flex flex-col items-center gap-1.5">
                <span class="text-[0.55rem] font-semibold uppercase tracking-[0.3em] text-gray-400">Manual Entry Token</span>
                <div id="token-display"
                     class="px-8 py-4 rounded-xl bg-white/80 border border-gray-100/80 text-4xl font-black tracking-[0.5em] text-gray-600 select-all tabular-nums shadow-sm"
                     style="min-width: 18rem; text-align: center; letter-spacing: 0.5em;">——</div>
            </div>

            <div class="flex items-center gap-3 w-60">
                <div class="flex-1 h-1.5 rounded-full bg-gray-100 overflow-hidden shadow-inner">
                    <div id="timer-bar" class="h-full rounded-full"
                         style="width: 100%; transition: width 1s linear; background: linear-gradient(90deg, #6ee7b7, #34d399, #10b981);"></div>
                </div>
                <div id="timer-text" class="text-xs font-mono font-semibold text-gray-400 tabular-nums w-7 text-center shrink-0">—</div>
            </div>
        </div>
    </div>

    {{-- ── Bottom bar ── --}}
    <div class="relative z-10 flex items-center justify-between px-8 py-3 animate-bottom">
        <span class="text-[0.45rem] font-semibold text-gray-300 uppercase tracking-widest">Knights Transport</span>
        <span class="text-[0.45rem] font-mono text-gray-300">60s refresh</span>
    </div>

</div>

<audio id="qr-success-sound" src="{{ asset('sounds/qr-success.mp3') }}" preload="auto"></audio>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
(function () {
    const QR_REFRESH_SECONDS = 60;
    let qrCountdown   = QR_REFRESH_SECONDS;
    let reloadPending = false;
    let tokenCheckInt = null;
    let refreshInt    = null;
    let resizeDeb     = null;
    let currentLoginUrl = null;

    const qrDiv    = document.getElementById('qrcode');
    const qrInner  = document.getElementById('qr-inner');
    const qrCard   = document.getElementById('qr-card-inner');
    const qrLoad   = document.getElementById('qr-loading');
    const tokenEl  = document.getElementById('token-display');
    const blockEl  = document.getElementById('block-overlay');
    const overlay  = document.getElementById('scan-success');
    const timerTxt = document.getElementById('timer-text');
    const timerBar = document.getElementById('timer-bar');

    function updateClock() {
        var now = new Date();
        var h = now.getHours();
        var m = String(now.getMinutes()).padStart(2, '0');
        var ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        var el = document.getElementById('live-clock');
        if (el) el.textContent = h + ':' + m + ' ' + ampm;
    }
    updateClock();
    setInterval(updateClock, 10000);

    function getQRSize() {
        var w = window.innerWidth - 200;
        var h = window.innerHeight - 340;
        var s = Math.min(w, h);
        return Math.max(220, Math.min(s, 400));
    }

    function renderQR(url) {
        if (!url) return;
        qrDiv.innerHTML = '';
        var size = getQRSize();
        qrDiv.style.width  = size + 'px';
        qrDiv.style.height = size + 'px';
        new QRCode(qrDiv, {
            text: url,
            width: size, height: size,
            colorDark: '#292524',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    function animateRefresh() {
        qrCard.style.transition = 'transform 0.18s cubic-bezier(0.34,1.56,0.64,1), opacity 0.12s ease';
        qrCard.style.transform = 'scale(0.85) rotate(-2deg)';
        qrCard.style.opacity = '0';
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                qrCard.style.transition = 'transform 0.5s cubic-bezier(0.16,1,0.3,1), opacity 0.25s ease 0.06s';
                qrCard.style.transform = 'scale(1) rotate(0deg)';
                qrCard.style.opacity = '1';
            });
        });
        setTimeout(function () { qrCard.style.transition = ''; }, 600);
    }

    function resetOverlays() {
        blockEl.classList.add('hidden');
        overlay.style.transition = 'none';
        overlay.style.clipPath = 'circle(0% at 50% 50%)';
        var content = overlay.querySelector('div');
        if (content) { content.style.opacity = '0'; content.style.transform = 'scale(0.88)'; content.style.transition = ''; }
        var check = document.getElementById('check-path');
        if (check) { check.style.animation = 'none'; void check.offsetWidth; }
        overlay.classList.add('hidden');
    }

    function loadQR() {
        reloadPending = false;
        resetOverlays();
        qrCard.style.opacity = '1';
        qrCard.style.transform = '';
        timerTxt.textContent = '—';
        timerBar.style.transition = 'none';
        timerBar.style.width = '100%';
        fetch("{{ route('hr.qr.generate') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            currentLoginUrl = data.login_url;
            animateRefresh();
            setTimeout(function () { renderQR(data.login_url); }, 60);
            revealToken(data.token);
            qrCountdown = QR_REFRESH_SECONDS;
            timerTxt.textContent = qrCountdown + 's';
            timerBar.style.transition = 'width 1s linear';
            timerBar.style.width = '100%';
            stopAll();
            refreshInt    = setInterval(tick, 1000);
            tokenCheckInt = setInterval(checkToken, 2000);
        })
        .catch(function () {
            timerTxt.textContent = '…';
            setTimeout(loadQR, 4000);
        });
    }

    function revealToken(token) {
        tokenEl.textContent = '';
        var i = 0;
        function typeChar() {
            if (i >= token.length) return;
            tokenEl.textContent = token.slice(0, i + 1);
            tokenEl.style.transition = 'transform 0.1s ease-out';
            tokenEl.style.transform = 'scale(1.03)';
            setTimeout(function () { tokenEl.style.transform = 'scale(1)'; }, 80);
            i++;
            setTimeout(typeChar, 60 + (i === token.length ? 0 : 0));
        }
        typeChar();
    }

    function tick() {
        if (reloadPending) return;
        qrCountdown--;
        timerTxt.textContent = qrCountdown + 's';
        var pct = (qrCountdown / QR_REFRESH_SECONDS) * 100;
        timerBar.style.width = pct + '%';
        if (qrCountdown <= 10) {
            timerBar.style.background = '#10b981';
        } else if (qrCountdown <= 20) {
            timerBar.style.background = '#34d399';
        } else {
            timerBar.style.background = '#6ee7b7';
        }
        if (qrCountdown <= 0) { clearInterval(refreshInt); loadQR(); }
    }

    function checkToken() {
        if (reloadPending) return;
        fetch("{{ route('api.qr.token-status') }}")
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (d.used) {
                    reloadPending = true;
                    stopAll();
                    triggerSweep();
                }
            })
            .catch(function () {});
    }

    function triggerSweep() {
        timerTxt.textContent = '✓';
        timerBar.style.transition = 'none';
        timerBar.style.width = '0%';
        document.getElementById('qr-success-sound').play().catch(function(){});
        overlay.classList.remove('hidden');
        requestAnimationFrame(function () {
            overlay.style.transition = 'clip-path 0.35s cubic-bezier(0.34,1.56,0.64,1)';
            overlay.style.clipPath = 'circle(150% at 50% 50%)';
        });
        var content = overlay.querySelector('div');
        if (content) {
            content.style.transform = 'scale(0.88)';
            setTimeout(function () {
                content.style.transition = 'opacity 0.18s ease, transform 0.28s cubic-bezier(0.34,1.56,0.64,1)';
                content.style.opacity = '1';
                content.style.transform = 'scale(1)';
            }, 220);
            setTimeout(function () {
                var check = document.getElementById('check-path');
                if (check) check.style.animation = 'drawCheck 0.25s ease-out 0.05s forwards';
            }, 260);
        }
        setTimeout(function () { loadQR(); }, 1800);
    }

    function stopAll() {
        clearInterval(tokenCheckInt);
        clearInterval(refreshInt);
        tokenCheckInt = null;
        refreshInt = null;
    }

    document.addEventListener('DOMContentLoaded', function () {
        loadQR();
        window.addEventListener('resize', function () {
            clearTimeout(resizeDeb);
            resizeDeb = setTimeout(function () {
                if (currentLoginUrl) renderQR(currentLoginUrl);
            }, 150);
        });
    });
})();
</script>

<style>
/* ── QR image fill ── */
#qrcode > img, #qrcode > canvas { width: 100% !important; height: 100% !important; display: block; border-radius: 10px; }

/* ── Page entrance stagger ── */
#main-content { animation: mainFadeUp 0.9s cubic-bezier(0.16,1,0.3,1) both; }
.animate-headline { animation: slideDown 0.5s cubic-bezier(0.16,1,0.3,1) 0.08s both; }
.animate-qr { animation: qrEntrance 0.7s cubic-bezier(0.16,1,0.3,1) 0.2s both; }
.animate-token { animation: fadeUp 0.6s cubic-bezier(0.16,1,0.3,1) 0.35s both; }
.animate-bottom { animation: fadeUp 0.5s cubic-bezier(0.16,1,0.3,1) 0.5s both; }

@keyframes mainFadeUp {
    0%   { opacity: 0; }
    100% { opacity: 1; }
}
@keyframes slideDown {
    0%   { opacity: 0; transform: translateY(-8px); }
    100% { opacity: 1; transform: translateY(0); }
}
@keyframes fadeUp {
    0%   { opacity: 0; transform: translateY(10px); }
    100% { opacity: 1; transform: translateY(0); }
}
@keyframes qrEntrance {
    0%   { opacity: 0; transform: scale(0.9) translateY(16px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}

/* ── Ambient light drift ── */
@keyframes lightDrift1 {
    0%, 100% { transform: translate(0, 0); }
    33%      { transform: translate(40px, -20px); }
    66%      { transform: translate(-25px, 15px); }
}
@keyframes lightDrift2 {
    0%, 100% { transform: translate(0, 0); }
    50%      { transform: translate(-35px, 25px); }
}

/* ── Headline dot pulse + ripple ── */
@keyframes pulseDot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%      { opacity: 0.4; transform: scale(0.65); }
}
@keyframes rippleDot {
    0%   { transform: scale(1); opacity: 0.4; }
    100% { transform: scale(2.8); opacity: 0; }
}

/* ── Scan line ── */
@keyframes scanLine {
    0%   { top: 6%; opacity: 0; }
    15%  { opacity: 0.4; }
    85%  { opacity: 0.35; }
    100% { top: 94%; opacity: 0; }
}

/* ── Loading dots ── */
@keyframes loadDot {
    0%, 80%, 100% { transform: scale(0.5); opacity: 0.25; }
    40%           { transform: scale(1.15); opacity: 0.9; }
}

/* ── Success overlay ── */
@keyframes successCircle {
    0%   { opacity: 0; transform: scale(0.4); }
    60%  { transform: scale(1.1); }
    100% { opacity: 1; transform: scale(1); }
}
@keyframes successFadeUp {
    0%   { opacity: 0; transform: translateY(12px); }
    100% { opacity: 1; transform: translateY(0); }
}
#scan-success:not(.hidden) svg path {
    stroke-dasharray: 22;
    stroke-dashoffset: 22;
}
@keyframes drawCheck { to { stroke-dashoffset: 0; } }
</style>

@endsection
