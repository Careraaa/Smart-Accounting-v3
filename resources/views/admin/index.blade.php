@extends('layouts.qr-monitor')

@section('content')
<div class="relative w-full h-full flex items-center justify-center overflow-hidden select-none" style="background: radial-gradient(ellipse at 50% 30%, #0c1628 0%, #060a16 100%);">

    {{-- Particle field --}}
    <div id="particles" class="absolute inset-0 overflow-hidden pointer-events-none"></div>

    {{-- Ambient glow orbs --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-[32rem] h-[32rem] bg-emerald-500/8 rounded-full blur-[150px] animate-[orbA_14s_ease-in-out_infinite]"></div>
        <div class="absolute bottom-1/4 right-1/4 w-[28rem] h-[28rem] bg-emerald-400/5 rounded-full blur-[130px] animate-[orbB_18s_ease-in-out_infinite_reverse]"></div>
        <div class="absolute top-1/3 right-1/3 w-[20rem] h-[20rem] bg-amber-400/4 rounded-full blur-[120px] animate-[orbC_16s_ease-in-out_infinite_2s]"></div>
    </div>

    {{-- Animated road dashes --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-[0.035]">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-px h-full" style="background: repeating-linear-gradient(to bottom, #fff 0px, #fff 24px, transparent 24px, transparent 48px); animation: roadScroll 10s linear infinite;"></div>
    </div>

    {{-- Main content --}}
    <div class="relative flex flex-col items-center gap-6 animate-[fadeUp_1s_cubic-bezier(0.16,1,0.3,1)_both]">

        {{-- Fleet header --}}
        <div class="flex flex-col items-center gap-2.5">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-px bg-gradient-to-r from-transparent via-emerald-400/30 to-transparent"></div>
                <svg class="w-4 h-4 text-amber-400/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                </svg>
                <h1 class="text-[0.6rem] font-bold tracking-[0.35em] uppercase text-white/35">Knights Transport</h1>
                <svg class="w-4 h-4 text-amber-400/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                </svg>
                <div class="w-6 h-px bg-gradient-to-r from-transparent via-emerald-400/30 to-transparent"></div>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-1 h-1 rounded-full bg-emerald-400/30"></div>
                <div class="w-8 h-px bg-gradient-to-r from-emerald-400/20 via-amber-400/30 to-emerald-400/20"></div>
                <span class="text-[0.45rem] uppercase tracking-[0.4em] text-white/20">Fleet Attendance</span>
                <div class="w-8 h-px bg-gradient-to-r from-emerald-400/20 via-amber-400/30 to-emerald-400/20"></div>
                <div class="w-1 h-1 rounded-full bg-emerald-400/30"></div>
            </div>
        </div>

        {{-- QR with rotating glow ring --}}
        <div class="relative">
            {{-- Outer rotating gradient ring --}}
            <div id="glow-ring" class="absolute -inset-[5px] rounded-[26px]" style="background: conic-gradient(from 0deg, transparent 30%, rgba(16,185,129,0.35), rgba(245,158,11,0.2), rgba(52,211,153,0.15), transparent 70%); mask: radial-gradient(farthest-side, transparent calc(100% - 2.5px), #000 calc(100% - 1.5px)); -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - 2.5px), #000 calc(100% - 1.5px));"></div>

            {{-- QR wrapper --}}
            <div id="qr-wrap" class="relative p-[2.5px] rounded-[21px]" style="background: linear-gradient(135deg, rgba(255,255,255,0.07), rgba(255,255,255,0.01));">
                <div class="relative bg-[#0b1121] rounded-[18px] overflow-hidden shadow-[0_30px_80px_-20px_rgba(0,0,0,0.8)]">

                    {{-- Instant block overlay (appears BEFORE sweep to prevent double-scan) --}}
                    <div id="block-overlay" class="hidden absolute inset-0 z-20 rounded-[18px]" style="background: #0b1121;"></div>

                    {{-- Sweep overlay --}}
                    <div id="sweep-overlay" class="absolute inset-0 z-20 rounded-[18px] pointer-events-none opacity-0" style="background: radial-gradient(circle at center, rgba(5,150,105,0.95) 0%, rgba(16,185,129,0.8) 40%, rgba(52,211,153,0.4) 100%);"></div>

                    {{-- QR inner --}}
                    <div id="qr-inner" class="p-5">
                        <div id="qrcode" class="w-[--qr-size] h-[--qr-size]" style="--qr-size:260px"></div>
                    </div>

                    {{-- Success overlay --}}
                    <div id="scan-success" class="hidden absolute inset-0 z-30 flex flex-col items-center justify-center gap-3 rounded-[18px]">
                        <div class="w-14 h-14 rounded-full bg-white/15 flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-bold text-white tracking-wide">Recorded</p>
                            <p class="text-[0.6rem] text-white/60 mt-0.5 tracking-wide">Loading new code…</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Token — large for manual time-in --}}
        <div class="flex flex-col items-center gap-2">
            <div id="token-display" class="text-2xl font-mono tracking-[0.35em] text-white/60 select-all">——</div>
            <div class="flex items-center gap-3">
                {{-- Circular countdown --}}
                <svg class="w-7 h-7 -rotate-90" viewBox="0 0 40 40">
                    <circle cx="20" cy="20" r="17" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="2"/>
                    <circle id="timer-ring" cx="20" cy="20" r="17" fill="none" stroke="rgba(16,185,129,0.7)" stroke-width="2" stroke-linecap="round" stroke-dasharray="106.8" stroke-dashoffset="0" style="transition: stroke-dashoffset 1s linear;"/>
                </svg>
                {{-- Timer label --}}
                <div id="timer-text" class="text-[0.5rem] font-semibold uppercase tracking-[0.2em] text-white/25">—</div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
(function () {
    const QR_REFRESH_SECONDS = 60;
    const CIRCUMFERENCE      = 106.8;
    let qrCountdown   = QR_REFRESH_SECONDS;
    let reloadPending = false;
    let tokenCheckInt = null;
    let refreshInt    = null;
    let resizeDeb     = null;

    const qrDiv       = document.getElementById('qrcode');
    const qrInner     = document.getElementById('qr-inner');
    const tokenEl     = document.getElementById('token-display');
    const blockEl     = document.getElementById('block-overlay');
    const sweepEl     = document.getElementById('sweep-overlay');
    const overlay     = document.getElementById('scan-success');
    const timerText   = document.getElementById('timer-text');
    const timerRing   = document.getElementById('timer-ring');
    const glowRing    = document.getElementById('glow-ring');

    // ── Particle field ──
    (function initParticles() {
        const c = document.getElementById('particles');
        if (!c) return;
        const frag = document.createDocumentFragment();
        for (let i = 0; i < 80; i++) {
            const dot = document.createElement('div');
            const size = 1 + Math.random() * 2;
            const x = Math.random() * 100;
            const y = Math.random() * 100;
            const dur = 3 + Math.random() * 5;
            const del = Math.random() * 4;
            Object.assign(dot.style, {
                position: 'absolute',
                left: x + '%', top: y + '%',
                width: size + 'px', height: size + 'px',
                borderRadius: '50%',
                background: 'rgba(255,255,255,' + (0.15 + Math.random() * 0.3) + ')',
                opacity: '0',
                animation: 'particleFade ' + dur + 's ease-in-out ' + del + 's infinite alternate',
            });
            frag.appendChild(dot);
        }
        c.appendChild(frag);
    })();

    // ── QR sizing ──
    function getQRSize() {
        return Math.max(200, Math.min(400, Math.min(window.innerWidth - 80, 400)));
    }

    // ── Render QR ──
    function renderQR(token) {
        if (!token || token === '——') return;
        qrDiv.innerHTML = '';
        const size = getQRSize();
        qrDiv.style.setProperty('--qr-size', size + 'px');
        new QRCode(qrDiv, {
            text: token,
            width: size,
            height: size,
            colorDark: '#e5e7eb',
            colorLight: 'transparent',
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    // ── Refresh animation ──
    function animateRefresh() {
        qrInner.style.transition = 'transform 0.25s ease, opacity 0.2s ease';
        qrInner.style.transform = 'scale(0.92) rotate(3deg)';
        qrInner.style.opacity = '0';
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                qrInner.style.transition = 'transform 0.5s cubic-bezier(0.16,1,0.3,1), opacity 0.35s ease 0.05s';
                qrInner.style.transform = 'scale(1) rotate(0deg)';
                qrInner.style.opacity = '1';
            });
        });
        setTimeout(() => { qrInner.style.transition = ''; }, 600);
    }

    // ── Reset all overlays ──
    function resetOverlays() {
        blockEl.classList.add('hidden');
        sweepEl.style.transition = 'none';
        sweepEl.style.opacity = '0';
        sweepEl.style.clipPath = '';
        overlay.classList.add('hidden');
        glowRing.style.transition = '';
        glowRing.style.opacity = '';
    }

    // ── Load QR ──
    function loadQR() {
        reloadPending = false;
        resetOverlays();
        qrInner.style.opacity = '1';
        qrInner.style.transform = '';
        timerText.textContent = 'Generating…';
        timerRing.style.transition = 'none';
        timerRing.style.strokeDashoffset = CIRCUMFERENCE;
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
            timerText.textContent = qrCountdown + 's';
            timerRing.style.transition = 'stroke-dashoffset 1s linear';
            timerRing.style.strokeDashoffset = '0';
            stopAll();
            refreshInt = setInterval(tick, 1000);
            tokenCheckInt = setInterval(checkToken, 2000);
        })
        .catch(() => {
            timerText.textContent = 'Retrying…';
            setTimeout(loadQR, 4000);
        });
    }

    // ── Tick ──
    function tick() {
        if (reloadPending) return;
        qrCountdown--;
        timerText.textContent = qrCountdown + 's';
        const offset = CIRCUMFERENCE * (1 - qrCountdown / QR_REFRESH_SECONDS);
        timerRing.style.strokeDashoffset = offset;
        if (qrCountdown <= 0) {
            clearInterval(refreshInt);
            loadQR();
        }
    }

    // ── Check token ──
    function checkToken() {
        if (reloadPending) return;
        fetch("{{ route('api.qr.token-status') }}")
            .then(r => r.json())
            .then(d => {
                if (d.used) {
                    reloadPending = true;
                    stopAll();
                    triggerSweep();
                }
            })
            .catch(() => {});
    }

    // ── Trigger sweep ──
    function triggerSweep() {
        timerText.textContent = 'Scanned';

        // 1. Instantly hide QR from view — no double-scan possible
        qrInner.style.transition = 'none';
        qrInner.style.opacity = '0';

        // 2. Instantly show block overlay (same color as bg, hidden QR)
        blockEl.classList.remove('hidden');

        // 3. Dim glow ring
        glowRing.style.transition = 'opacity 0.3s ease';
        glowRing.style.opacity = '0';

        // 4. Animate green sweep over the block overlay
        sweepEl.style.transition = 'clip-path 0.38s cubic-bezier(0.22,1,0.36,1)';
        sweepEl.style.opacity = '1';
        sweepEl.style.clipPath = 'circle(150% at 50% 50%)';

        // 5. Show success checkmark after sweep
        setTimeout(() => {
            overlay.classList.remove('hidden');
            overlay.style.animation = 'successIn 0.45s cubic-bezier(0.34,1.56,0.64,1) both';
        }, 420);

        // 6. Load new QR seamlessly (no full page reload)
        setTimeout(() => loadQR(), 2000);
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
                if (t && t !== '——') renderQR(t);
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
    border-radius: 12px;
}

/* ── Rotating glow ring ── */
#glow-ring {
    animation: ringSpin 6s linear infinite;
}
@keyframes ringSpin {
    to { transform: rotate(360deg); }
}

/* ── Particle animation ── */
@keyframes particleFade {
    0%   { opacity: 0; transform: translateY(0) scale(0.5); }
    50%  { opacity: 1; transform: translateY(-12px) scale(1); }
    100% { opacity: 0; transform: translateY(-24px) scale(0.3); }
}

/* ── Orb animations ── */
@keyframes orbA {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(40px, -25px) scale(1.08); }
    66% { transform: translate(-25px, 20px) scale(0.92); }
}
@keyframes orbB {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(-30px, 20px) scale(1.05); }
    66% { transform: translate(25px, -15px) scale(0.95); }
}
@keyframes orbC {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(20px, -30px) scale(1.1); }
}

/* ── Road divider scroll ── */
@keyframes roadScroll {
    0%   { transform: translateY(-48px); }
    100% { transform: translateY(0); }
}

/* ── Entrance ── */
@keyframes fadeUp {
    0% { opacity: 0; transform: translateY(20px) scale(0.97); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}

/* ── Success overlay ── */
@keyframes successIn {
    0% { opacity: 0; transform: scale(0.85); }
    100% { opacity: 1; transform: scale(1); }
}

#scan-success:not(.hidden) svg path {
    stroke-dasharray: 20;
    stroke-dashoffset: 20;
    animation: drawCheck 0.35s ease-out 0.12s forwards;
}
@keyframes drawCheck {
    to { stroke-dashoffset: 0; }
}
</style>
@endsection
