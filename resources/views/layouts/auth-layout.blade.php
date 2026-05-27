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
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm p-4 mb-4" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
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
    <div class="md:hidden min-h-screen flex flex-col bg-white">
        <div class="flex-1 flex flex-col justify-center px-6 py-8">
            <div class="w-full max-w-sm mx-auto">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-[#020617] mb-4">
                        <span class="text-white font-extrabold text-lg">KT</span>
                    </div>
                    <h1 class="text-xl font-extrabold text-gray-900">Knights Transport</h1>
                    <p class="text-xs text-gray-400 mt-0.5">Smart Accounting System</p>
                </div>

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm p-4 mb-4" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
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
