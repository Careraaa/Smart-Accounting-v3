<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Smart Accounting System">
    <title>@yield('title', 'Smart Accounting')</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/tailwind.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="font-[Sora,sans-serif] antialiased">

    {{-- Desktop: split layout --}}
    <div class="hidden md:grid md:grid-cols-[3fr_2fr] h-screen overflow-hidden">

        <div class="h-screen bg-[#020617] bg-cover bg-center relative overflow-hidden" style="background-image: url('{{ asset('images/login_bg.png') }}')">
            <div class="absolute inset-0 bg-gradient-to-t from-[#020617]/80 via-[#020617]/30 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-12 lg:p-16">
                <div class="backdrop-blur-md bg-white/10 border border-white/15 rounded-2xl px-8 py-6 w-fit">
                    <h1 class="text-2xl font-extrabold text-white tracking-tight">Knights Transport</h1>
                    <p class="text-xs text-white/50 uppercase tracking-[2px] mt-1">Smart Accounting System</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col justify-center p-12 relative bg-white">
            <div class="w-full max-w-[420px] mx-auto">
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

    {{-- Mobile: centered card layout --}}
    <div class="md:hidden min-h-screen flex flex-col relative bg-[#020617] bg-cover bg-center" style="background-image: url('{{ asset('images/login_bg.png') }}')">
        <div class="absolute inset-0 bg-gradient-to-t from-[#020617]/90 via-[#020617]/50 to-[#020617]/40"></div>
        <div class="relative z-10 flex-1 flex flex-col justify-center px-5 py-6">
            <div class="w-full max-w-sm mx-auto">
                <div class="text-center mb-8">
                    <img src="{{ asset('images/knights_white-bg.png') }}" alt=""
                        class="mx-auto w-20 h-20 object-contain rounded-xl mb-4">
                    <h1 class="text-xl font-extrabold text-white">Knights Transport</h1>
                    <p class="text-xs text-white/50 mt-0.5">Smart Accounting System</p>
                </div>

                <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-xl px-5 py-6 md:px-6 md:py-7">
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
    </div>

    @yield('scripts')
    <script>
        (() => {
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
