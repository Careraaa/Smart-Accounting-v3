@extends('layouts.qr-monitor')

@section('content')

{{-- ================================================================
     QR ATTENDANCE MONITOR — Knights Transport Fleet System
     Crimson theme | Minibus attendance terminal
================================================================ --}}

<div class="relative w-full h-full flex flex-col overflow-hidden select-none" id="qr-page">

    {{-- ── Animated gradient background ── --}}
    <div class="absolute inset-0" id="bg-layer" style="background: linear-gradient(135deg, #fef2f2 0%, #fff1f2 30%, #fef6f6 60%, #fafafa 100%);"></div>

    {{-- ── Slow-drift blob orbs ── --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="blob blob-a absolute w-[44rem] h-[44rem] rounded-full -top-40 -left-32"
             style="background: radial-gradient(circle, rgba(244,63,94,0.11) 0%, transparent 65%);"></div>
        <div class="blob blob-b absolute w-[36rem] h-[36rem] rounded-full -bottom-32 -right-20"
             style="background: radial-gradient(circle, rgba(239,68,68,0.08) 0%, transparent 65%);"></div>
        <div class="blob blob-c absolute w-[28rem] h-[28rem] rounded-full top-1/3 right-1/4"
             style="background: radial-gradient(circle, rgba(251,146,60,0.06) 0%, transparent 65%);"></div>
    </div>

    {{-- ── Subtle dot grid ── --}}
    <div class="absolute inset-0 pointer-events-none" style="background-image: radial-gradient(circle, rgba(0,0,0,0.06) 1px, transparent 1px); background-size: 32px 32px; mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%);"></div>

    {{-- ── Floating shapes ── --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none" id="float-shapes">
        <div class="float-shape absolute w-3 h-3 rounded-sm border-2 border-rose-300/40" style="top:12%; left:8%; animation: floatA 8s ease-in-out infinite;"></div>
        <div class="float-shape absolute w-2 h-2 rounded-full bg-rose-300/30" style="top:25%; right:12%; animation: floatB 10s ease-in-out 1s infinite;"></div>
        <div class="float-shape absolute w-4 h-4 rounded-full border border-red-300/30" style="bottom:30%; left:6%; animation: floatA 12s ease-in-out 2s infinite;"></div>
        <div class="float-shape absolute w-2 h-2 rounded-sm bg-orange-300/25" style="bottom:20%; right:8%; animation: floatC 9s ease-in-out 0.5s infinite;"></div>
        <div class="float-shape absolute w-1.5 h-1.5 rounded-full bg-rose-400/40" style="top:60%; left:14%; animation: floatB 7s ease-in-out 3s infinite;"></div>
        <div class="float-shape absolute w-3 h-3 rounded border border-rose-200/50" style="top:70%; right:16%; animation: floatC 11s ease-in-out 1.5s infinite;"></div>
        <div class="float-shape absolute w-2 h-2 rounded-full border border-red-200/40" style="top:40%; left:5%; animation: floatA 9s ease-in-out 4s infinite;"></div>
        <div class="float-shape absolute w-1.5 h-1.5 bg-orange-200/50 rounded-full" style="top:15%; right:22%; animation: floatB 13s ease-in-out 2.5s infinite;"></div>
    </div>

    {{-- ─────────────────────────────────────────────────────────────
         MAIN CONTENT
    ───────────────────────────────────────────────────────────────── --}}
    <div class="relative z-10 flex-1 flex flex-col items-center justify-center gap-6" id="main-content">

        {{-- Headline --}}
        <div class="text-center animate-title">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white border border-gray-200 shadow-sm mb-3">
                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                <span class="text-xs font-bold text-gray-500 tracking-widest uppercase">Scan to Log Attendance</span>
            </div>
        </div>

        {{-- ── QR Code Card ── --}}
        <div class="relative animate-qr">

            {{-- Outer glow ring --}}
            <div class="absolute -inset-3 rounded-[2rem] opacity-60 glow-ring"
                 style="background: conic-gradient(from 0deg, #f43f5e, #fb7185, #fda4af, #f43f5e); filter: blur(18px); z-index: 0;"></div>

            {{-- Rotating border --}}
            <div class="absolute -inset-[2px] rounded-[26px] z-0 rotate-border"
                 style="background: conic-gradient(from 0deg, #f43f5e 0%, #fda4af 25%, transparent 50%, #fb7185 75%, #f43f5e 100%); padding: 2px; border-radius: 26px;"></div>

            {{-- Card --}}
            <div class="relative z-10 bg-white rounded-[24px] p-2 shadow-2xl shadow-rose-100/80" style="box-shadow: 0 32px 64px -12px rgba(244,63,94,0.18), 0 8px 24px -6px rgba(0,0,0,0.08);">

                {{-- Inner QR frame --}}
                <div class="relative rounded-[18px] overflow-hidden bg-gray-50" style="padding: 2px;">
                    <div class="relative rounded-[17px] overflow-hidden bg-white">

                        {{-- Block overlay --}}
                        <div id="block-overlay" class="hidden absolute inset-0 z-20 rounded-[17px] bg-white"></div>

                        {{-- Sweep overlay --}}
                        <div id="sweep-overlay" class="absolute inset-0 z-20 rounded-[17px] pointer-events-none opacity-0"
                             style="background: radial-gradient(circle at center, rgba(225,29,72,0.92) 0%, rgba(244,63,94,0.75) 40%, rgba(251,113,133,0.4) 100%);"></div>

                        {{-- QR inner --}}
                        <div id="qr-inner" class="p-4 relative">
                            {{-- Scan line --}}
                            <div id="scan-line" class="absolute left-6 right-6 h-0.5 z-10 pointer-events-none"
                                 style="background: linear-gradient(90deg, transparent, #f43f5e, #fb7185, #f43f5e, transparent); border-radius: 9999px; animation: scanLineLight 2.6s ease-in-out infinite; top: 50%;"></div>
                            <div id="qrcode"></div>
                        </div>

                        {{-- Success overlay --}}
                        <div id="scan-success" class="hidden absolute inset-0 z-30 flex flex-col items-center justify-center gap-3 rounded-[17px]"
                             style="background: linear-gradient(135deg, rgba(225,29,72,0.95), rgba(244,63,94,0.9));">
                            <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center backdrop-blur-sm shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-extrabold text-white tracking-wide">Attendance Recorded!</p>
                                <p class="text-[0.62rem] text-white/70 mt-1 tracking-wide">Generating new code…</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Corner brackets inside card --}}
                <div class="absolute inset-4 pointer-events-none z-10">
                    <div class="absolute top-0 left-0 w-5 h-5 border-t-2 border-l-2 border-rose-400 rounded-tl-md"></div>
                    <div class="absolute top-0 right-0 w-5 h-5 border-t-2 border-r-2 border-rose-400 rounded-tr-md"></div>
                    <div class="absolute bottom-0 left-0 w-5 h-5 border-b-2 border-l-2 border-rose-400 rounded-bl-md"></div>
                    <div class="absolute bottom-0 right-0 w-5 h-5 border-b-2 border-r-2 border-rose-400 rounded-br-md"></div>
                </div>
            </div>
        </div>

        {{-- ── Token + Timer ── --}}
        <div class="flex flex-col items-center gap-4 animate-token">

            {{-- Token chip --}}
            <div class="flex flex-col items-center gap-1.5">
                <div class="text-[0.55rem] font-bold uppercase tracking-[0.35em] text-gray-400">Manual Entry Token</div>
                <div class="relative flex items-center gap-1">
                    <div class="token-glow absolute inset-0 rounded-xl bg-rose-300/30 blur-md"></div>
                    <div id="token-display"
                         class="relative px-6 py-2.5 rounded-xl bg-white border border-gray-200 shadow-sm text-2xl font-mono font-bold tracking-[0.4em] text-gray-700 select-all tabular-nums"
                         style="letter-spacing: 0.5em; min-width: 14rem; text-align: center;">——</div>
                </div>
            </div>

            {{-- Timer bar --}}
            <div class="flex items-center gap-3 w-56">
                <div class="flex-1 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                    <div id="timer-bar" class="h-full rounded-full"
                         style="width: 100%; background: linear-gradient(90deg, #e11d48, #f43f5e, #fb7185); transition: width 1s linear;"></div>
                </div>
                <div id="timer-text" class="text-xs font-mono font-bold text-gray-400 tabular-nums w-7 text-center shrink-0">—</div>
            </div>
        </div>

    </div>

    {{-- ─────────────────────────────────────────────────────────────
         BOTTOM BAR
    ───────────────────────────────────────────────────────────────── --}}
    <div class="relative z-10 flex items-center justify-between px-8 py-4">
        <div class="flex items-center gap-2">
            <div class="w-1.5 h-1.5 rounded-full bg-rose-400/60"></div>
            <span class="text-[0.58rem] font-semibold text-gray-400 uppercase tracking-widest">Attendance Terminal</span>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-[0.58rem] font-mono text-gray-300">Auto-refresh: 60s</span>
            <div class="w-px h-3 bg-gray-200"></div>
            <span class="text-[0.58rem] font-mono text-gray-200">v2.0</span>
        </div>
    </div>

</div>

{{-- ─────────────────────────────────────────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
(function () {
    const QR_REFRESH_SECONDS = 60;
    let qrCountdown   = QR_REFRESH_SECONDS;
    let reloadPending = false;
    let tokenCheckInt = null;
    let refreshInt    = null;
    let resizeDeb     = null;

    const qrDiv    = document.getElementById('qrcode');
    const qrInner  = document.getElementById('qr-inner');
    const tokenEl  = document.getElementById('token-display');
    const blockEl  = document.getElementById('block-overlay');
    const sweepEl  = document.getElementById('sweep-overlay');
    const overlay  = document.getElementById('scan-success');
    const timerTxt = document.getElementById('timer-text');
    const timerBar = document.getElementById('timer-bar');

    // ── QR size ──
    function getQRSize() {
        return Math.max(200, Math.min(320, Math.min(window.innerWidth - 140, 300)));
    }

    // ── Render QR ──
    function renderQR(token) {
        if (!token || token === '——') return;
        qrDiv.innerHTML = '';
        const size = getQRSize();
        qrDiv.style.width  = size + 'px';
        qrDiv.style.height = size + 'px';
        new QRCode(qrDiv, {
            text: token,
            width: size, height: size,
            colorDark: '#111827',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    // ── Animate refresh ──
    function animateRefresh() {
        qrInner.style.transition = 'transform 0.2s ease, opacity 0.16s ease';
        qrInner.style.transform = 'scale(0.92) rotate(-2deg)';
        qrInner.style.opacity = '0';
        requestAnimationFrame(() => requestAnimationFrame(() => {
            qrInner.style.transition = 'transform 0.5s cubic-bezier(0.16,1,0.3,1), opacity 0.32s ease 0.04s';
            qrInner.style.transform = 'scale(1) rotate(0deg)';
            qrInner.style.opacity = '1';
        }));
        setTimeout(() => { qrInner.style.transition = ''; }, 600);
    }

    // ── Reset overlays ──
    function resetOverlays() {
        blockEl.classList.add('hidden');
        sweepEl.style.transition = 'none';
        sweepEl.style.opacity = '0';
        sweepEl.style.clipPath = '';
        overlay.classList.add('hidden');
    }

    // ── Load QR ──
    function loadQR() {
        reloadPending = false;
        resetOverlays();
        qrInner.style.opacity = '1';
        qrInner.style.transform = '';
        timerTxt.textContent = '—';
        timerBar.style.transition = 'none';
        timerBar.style.width = '100%';
        fetch("{{ route('hr.qr.generate') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(r => r.json())
        .then(data => {
            animateRefresh();
            setTimeout(() => renderQR(data.token), 50);
            revealToken(data.token);
            qrCountdown = QR_REFRESH_SECONDS;
            timerTxt.textContent = qrCountdown + 's';
            timerBar.style.transition = 'width 1s linear';
            timerBar.style.width = '100%';
            stopAll();
            refreshInt    = setInterval(tick, 1000);
            tokenCheckInt = setInterval(checkToken, 2000);
        })
        .catch(() => {
            timerTxt.textContent = '…';
            setTimeout(loadQR, 4000);
        });
    }

    // ── Token reveal animation ──
    function revealToken(token) {
        tokenEl.textContent = '';
        let i = 0;
        const interval = setInterval(() => {
            tokenEl.textContent = token.slice(0, i + 1) + (i < token.length - 1 ? '·'.repeat(token.length - i - 1) : '');
            i++;
            if (i >= token.length) {
                clearInterval(interval);
                tokenEl.textContent = token;
            }
        }, 55);
    }

    // ── Tick ──
    function tick() {
        if (reloadPending) return;
        qrCountdown--;
        timerTxt.textContent = qrCountdown + 's';
        const pct = (qrCountdown / QR_REFRESH_SECONDS) * 100;
        timerBar.style.width = pct + '%';
        // Color shift: rose → deeper red as time runs out
        if (qrCountdown <= 10) {
            timerBar.style.background = 'linear-gradient(90deg, #9f1239, #be123c)';
        } else if (qrCountdown <= 20) {
            timerBar.style.background = 'linear-gradient(90deg, #be123c, #e11d48, #f43f5e)';
        } else {
            timerBar.style.background = 'linear-gradient(90deg, #e11d48, #f43f5e, #fb7185)';
        }
        if (qrCountdown <= 0) { clearInterval(refreshInt); loadQR(); }
    }

    // ── Check token ──
    function checkToken() {
        if (reloadPending) return;
        fetch("{{ route('api.qr.token-status') }}")
            .then(r => r.json())
            .then(d => { if (d.used) { reloadPending = true; stopAll(); triggerSweep(); } })
            .catch(() => {});
    }

    // ── Sweep ──
    function triggerSweep() {
        timerTxt.textContent = '✓';
        timerBar.style.transition = 'none';
        timerBar.style.width = '0%';
        qrInner.style.transition = 'none';
        qrInner.style.opacity = '0';
        blockEl.classList.remove('hidden');
        sweepEl.style.transition = 'clip-path 0.4s cubic-bezier(0.22,1,0.36,1)';
        sweepEl.style.opacity = '1';
        sweepEl.style.clipPath = 'circle(150% at 50% 50%)';
        setTimeout(() => {
            overlay.classList.remove('hidden');
            overlay.style.animation = 'successIn 0.45s cubic-bezier(0.34,1.56,0.64,1) both';
        }, 440);
        setTimeout(() => loadQR(), 2200);
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
                const t = tokenEl.textContent.replace(/·/g, '').trim();
                if (t && t !== '——') renderQR(t);
            }, 150);
        });
    });
})();
</script>

<style>
/* ── QR image fill ── */
#qrcode > img,
#qrcode > canvas {
    width: 100% !important;
    height: 100% !important;
    display: block;
    border-radius: 10px;
}

/* ── Page entrance ── */
#main-content {
    animation: mainFadeUp 0.8s cubic-bezier(0.16,1,0.3,1) both;
}
.animate-title {
    animation: mainFadeUp 0.7s cubic-bezier(0.16,1,0.3,1) 0.1s both;
}
.animate-qr {
    animation: qrPop 0.9s cubic-bezier(0.16,1,0.3,1) 0.2s both;
}
.animate-token {
    animation: mainFadeUp 0.7s cubic-bezier(0.16,1,0.3,1) 0.4s both;
}

@keyframes mainFadeUp {
    0%   { opacity: 0; transform: translateY(18px) scale(0.98); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes qrPop {
    0%   { opacity: 0; transform: scale(0.88) translateY(20px); }
    100% { opacity: 1; transform: scale(1) translateY(0); }
}

/* ── Blob drift ── */
.blob-a { animation: blobA 18s ease-in-out infinite; }
.blob-b { animation: blobB 22s ease-in-out infinite; }
.blob-c { animation: blobC 16s ease-in-out infinite; }
@keyframes blobA {
    0%, 100% { transform: translate(0,0) scale(1); }
    33%       { transform: translate(50px,-30px) scale(1.06); }
    66%       { transform: translate(-30px,25px) scale(0.94); }
}
@keyframes blobB {
    0%, 100% { transform: translate(0,0) scale(1); }
    40%       { transform: translate(-40px,30px) scale(1.07); }
    70%       { transform: translate(30px,-20px) scale(0.96); }
}
@keyframes blobC {
    0%, 100% { transform: translate(0,0) scale(1); }
    50%       { transform: translate(25px,-35px) scale(1.1); }
}

/* ── Floating shapes ── */
@keyframes floatA {
    0%, 100% { transform: translate(0,0) rotate(0deg); }
    50%       { transform: translate(8px,-14px) rotate(12deg); }
}
@keyframes floatB {
    0%, 100% { transform: translate(0,0) scale(1); }
    50%       { transform: translate(-6px,-10px) scale(1.15); }
}
@keyframes floatC {
    0%, 100% { transform: translate(0,0) rotate(0deg); }
    33%       { transform: translate(5px,-12px) rotate(-8deg); }
    66%       { transform: translate(-5px,-6px) rotate(6deg); }
}

/* ── Rotating glow ring ── */
.rotate-border {
    animation: rotateBorder 6s linear infinite;
    border-radius: 26px;
}
@keyframes rotateBorder {
    to { transform: rotate(360deg); }
}
.glow-ring {
    animation: glowPulse 4s ease-in-out infinite;
}
@keyframes glowPulse {
    0%, 100% { opacity: 0.45; transform: scale(0.98); }
    50%       { opacity: 0.7;  transform: scale(1.02); }
}

/* ── Scan line ── */
@keyframes scanLineLight {
    0%   { top: 4%; opacity: 0; }
    8%   { opacity: 0.9; }
    92%  { opacity: 0.9; }
    100% { top: 96%; opacity: 0; }
}


/* ── Token glow pulse ── */
.token-glow {
    animation: tokenGlow 2.5s ease-in-out infinite;
}
@keyframes tokenGlow {
    0%, 100% { opacity: 0.4; transform: scale(0.97); }
    50%       { opacity: 0.75; transform: scale(1.03); }
}

/* ── Success overlay ── */
@keyframes successIn {
    0%   { opacity: 0; transform: scale(0.82); }
    100% { opacity: 1; transform: scale(1); }
}
#scan-success:not(.hidden) svg path {
    stroke-dasharray: 22;
    stroke-dashoffset: 22;
    animation: drawCheck 0.38s ease-out 0.14s forwards;
}
@keyframes drawCheck {
    to { stroke-dashoffset: 0; }
}
</style>

@endsection
