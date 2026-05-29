<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Smart Accounting System">
    <title>@yield('title', 'Smart Accounting')</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights-icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="preload" as="image" href="{{ asset('images/login_bg.png') }}">
    <link rel="preload" as="image" href="{{ asset('images/login_bg2.png') }}">
    <link rel="preload" as="image" href="{{ asset('images/login_bg3.png') }}">
    <link rel="preload" as="image" href="{{ asset('images/login_bg4.png') }}">

    @vite(['resources/css/tailwind.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        @keyframes bgEntrance {
            0% { opacity: 0; transform: scale(1.08); }
            100% { opacity: 1; transform: scale(1); }
        }
        @keyframes bgPanZoom {
            0%   { transform: scale(1)      translate(0%, 0%); }
            25%  { transform: scale(1.12)   translate(-3%, -1%); }
            50%  { transform: scale(1.15)   translate(1.5%, -2%); }
            75%  { transform: scale(1.10)   translate(2.5%, 1%); }
            100% { transform: scale(1.14)   translate(-1.5%, 1.5%); }
        }
        .bg-slide {
            background-size: cover;
            background-position: center;
            will-change: opacity, transform;
        }
        .bg-slide.enter {
            animation: bgEntrance 2s cubic-bezier(0.16,1,0.3,1) both, bgPanZoom 45s ease-in-out 2s infinite alternate;
        }
        .bg-slide.zoom-only {
            animation: bgPanZoom 45s ease-in-out infinite alternate;
        }
        .bg-wrap {
            opacity: 0;
            transition: opacity 0.6s ease;
        }
        .bg-wrap.loaded {
            opacity: 1;
        }

    </style>
</head>

<body class="font-[Sora,sans-serif] antialiased">

    <div class="grid md:grid-cols-[3fr_2fr] h-screen overflow-hidden">

        {{-- Desktop left: background slideshow --}}
        <div class="hidden md:block h-screen bg-[#020617] relative overflow-hidden">
            @php $bgImages = ['login_bg.png', 'login_bg2.png', 'login_bg3.png', 'login_bg4.png']; @endphp
            <div class="absolute inset-0 bg-wrap">
            @foreach($bgImages as $i => $img)
            <div class="bg-slide absolute inset-0 {{ $i === 0 ? 'enter' : 'zoom-only' }}"
                 style="background-image: url('{{ asset('images/' . $img) }}'); opacity: {{ $i === 0 ? 1 : 0 }}; transition: opacity 2.5s ease-in-out;">
            </div>
            @endforeach
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#020617]/80 via-[#020617]/30 to-transparent pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 right-0 p-12 lg:p-16">
                <div class="backdrop-blur-md bg-white/10 border border-white/15 rounded-2xl px-8 py-6 w-fit flex items-center gap-3">
                    <img src="{{ asset('images/knights-icon.png') }}" alt="" class="w-16 h-16 object-contain rounded-xl shrink-0">
                    <div>
                        <h1 class="text-2xl font-extrabold text-white tracking-tight">Knights Transport</h1>
                        <p class="text-xs text-white/50 uppercase tracking-[2px] mt-1">Smart Accounting System</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right panel: shown on both desktop and mobile --}}
        <div class="relative z-10 flex flex-col justify-center px-5 py-6 md:p-12 bg-[#020617] md:bg-white">

            {{-- Mobile-only header --}}
            <div class="md:hidden text-center mb-8">
                <img src="{{ asset('images/knights-icon.png') }}" alt="Knights Transport"
                    class="mx-auto w-14 h-14 object-contain rounded-xl mb-4">
                <h1 class="text-xl font-extrabold text-white">Knights Transport</h1>
                <p class="text-xs text-white/50 mt-0.5">Smart Accounting System</p>
            </div>

            {{-- Card wrapper for mobile; plain on desktop --}}
            <div class="w-full max-w-[420px] mx-auto bg-white/95 md:bg-transparent backdrop-blur-sm md:backdrop-blur-none rounded-2xl md:rounded-none shadow-xl md:shadow-none px-5 py-6 md:px-0 md:py-0">

                @if ($errors->any())
                <div class="relative w-full flex flex-wrap items-center justify-center py-3 pl-4 pr-14 rounded-lg text-base font-medium transition-all duration-500 ease-linear border border-[#f85149] text-[#b22b2b] bg-[linear-gradient(#f851491a,#f851491a)] mb-4">
                    <button type="button" aria-label="close-error" onclick="this.parentElement.remove()"
                        class="absolute right-4 p-1 rounded-md transition-opacity text-[#f85149] border border-[#f85149] opacity-40 hover:opacity-100">
                        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="16" width="16" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6 6 18"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                    </button>
                    <p class="flex flex-row items-center mr-auto gap-x-2">
                        <svg stroke="currentColor" fill="none" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" height="28" width="28" class="h-7 w-7" xmlns="http://www.w3.org/2000/svg">
                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                            <path d="M12 9v4"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                        {{ $errors->first() }}
                    </p>
                </div>
                @endif
                @if (session('status'))
                    <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm p-4 mb-4" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    @yield('scripts')
    <script>
        (() => {
            // ── Background slideshow ──
            var slides = document.querySelectorAll('.bg-slide');
            var wrap   = document.querySelector('.bg-wrap');
            if (slides.length > 1 && wrap) {
                var first = new Image();
                first.src = slides[0].style.backgroundImage.replace(/^url\(["']?|["']?\)$/g, '');
                var ready = function () {
                    wrap.classList.add('loaded');
                    var idx = 0;
                    setInterval(function () {
                        slides[idx].style.opacity = '0';
                        slides[idx].classList.remove('enter');
                        slides[idx].classList.add('zoom-only');
                        idx = (idx + 1) % slides.length;
                        slides[idx].style.opacity = '1';
                        slides[idx].classList.remove('zoom-only');
                        slides[idx].classList.add('enter');
                    }, 7500);
                };
                if (first.complete && first.naturalWidth) {
                    ready();
                } else {
                    first.addEventListener('load', ready);
                    first.addEventListener('error', ready);
                }
            }

            // ── Refresh toast ──
            const showRefreshToast = () => {
                if (document.getElementById('sa-refresh-toast')) return;
                const toast = document.createElement('div');
                toast.id = 'sa-refresh-toast';
                toast.innerHTML = `
                    <span class="sa-refresh-dot" aria-hidden="true"></span>
                    <span>Refreshing latest data...</span>
                `;
                Object.assign(toast.style, {
                    position: 'fixed', top: '24px', left: '50%', zIndex: '99999',
                    display: 'inline-flex', alignItems: 'center', gap: '8px',
                    padding: '8px 14px', borderRadius: '10px',
                    border: '1px solid #fbcaca',
                    background: 'linear-gradient(135deg, #fff5f5 0%, #ffffff 100%)',
                    color: '#7f1d1d', fontSize: '.82rem', fontWeight: '600',
                    letterSpacing: '0.01em',
                    boxShadow: '0 8px 20px rgba(200, 41, 42, 0.2)',
                    backdropFilter: 'blur(3px)',
                    opacity: '0', transform: 'translate(-50%, -8px) scale(0.97)',
                    transition: 'opacity .2s ease, transform .2s ease',
                });
                document.body.appendChild(toast);
                requestAnimationFrame(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translate(-50%, 0) scale(1)';
                });
                const dot = toast.querySelector('.sa-refresh-dot');
                if (dot) {
                    Object.assign(dot.style, {
                        width: '8px', height: '8px', borderRadius: '999px',
                        background: '#c8292a',
                        boxShadow: '0 0 0 0 rgba(200, 41, 42, 0.4)',
                        animation: 'saRefreshPulse 1.2s ease-out infinite',
                        flexShrink: '0',
                    });
                }
                if (!document.getElementById('sa-refresh-toast-style')) {
                    const style = document.createElement('style');
                    style.id = 'sa-refresh-toast-style';
                    style.textContent = `
                        @keyframes saRefreshPulse {
                            0% { box-shadow: 0 0 0 0 rgba(200, 41, 42, 0.4); }
                            70% { box-shadow: 0 0 0 8px rgba(200, 41, 42, 0); }
                            100% { box-shadow: 0 0 0 0 rgba(200, 41, 42, 0); }
                        }
                    `;
                    document.head.appendChild(style);
                }
            };
            window.addEventListener('pageshow', (event) => {
                const navEntry = performance.getEntriesByType?.('navigation')?.[0];
                const fromHistory = event.persisted || navEntry?.type === 'back_forward';
                if (fromHistory) {
                    showRefreshToast();
                    setTimeout(() => window.location.reload(), 180);
                }
            });
        })();
    </script>
</body>

</html>
