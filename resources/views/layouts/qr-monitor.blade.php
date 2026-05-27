<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Attendance Monitor - Smart Accounting</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}">
    
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('vendors/css/vendors.min.css') }}">
    {{-- daterangepicker assets removed (no longer used) --}}

    <!-- Vite compiled assets -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <style>
        html, body { width: 100%; height: 100vh; margin: 0; padding: 0; overflow: hidden; }
        #mobile-collapse { display: none !important; }
        .dropdown-menu { animation: 0.4s ease-in-out 0s normal forwards 1 fadein; }
        @keyframes fadein {
            from { opacity: 0; transform: translate3d(0, 8px, 0); }
            to { opacity: 1; transform: translate3d(0, 0, 0); }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 h-[70px] bg-white border-b border-[#e8e8e8] shadow-sm z-50">
        <div class="flex items-center h-full px-5 gap-1">

            {{-- ── Left ── --}}
            <div class="flex items-center gap-3">
                {{-- Brand --}}
                <div class="hidden md:flex items-center gap-2">
                    <span class="font-bold text-sm text-gray-700 tracking-[0.2px]">Knights Transport</span>
                    <span class="bg-red-50 text-red-600 border border-red-200 text-[0.67rem] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-[0.5px]">
                        {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                    </span>
                </div>
            </div>

            {{-- ── Right ── --}}
            <div class="ml-auto flex items-center gap-1">

                {{-- Fullscreen (monitor display) --}}
                <button type="button" class="w-9 h-9 rounded-md flex items-center justify-center text-[#757575] hover:bg-gray-100 hover:text-gray-700 transition-colors no-underline cursor-pointer bg-transparent border-none" id="ktFullscreenBtn" title="Full screen" aria-label="Full screen">
                    <i class="feather-maximize"></i>
                </button>

                {{-- Divider --}}
                <div class="w-px h-[22px] bg-[#e8e8e8] mx-1.5 hidden sm:block"></div>

                {{-- User Dropdown --}}
                <div class="dropdown">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside"
                        class="flex items-center gap-2 no-underline cursor-pointer px-1.5 py-1 rounded-md hover:bg-gray-100 transition-colors">
                        <div class="inline-flex items-center justify-center rounded-full bg-primary text-white w-[34px] h-[34px] min-w-[34px] text-lg font-normal border-2 border-red-600">
                            {{ auth()->user()->getFirstLetter() }}
                        </div>
                        <div class="hidden md:block text-left leading-snug">
                            <div class="text-sm font-bold text-gray-700 leading-tight">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-500 leading-tight">{{ auth()->user()->email }}</div>
                        </div>
                        <i class="feather-chevron-down text-xs text-gray-400 hidden md:block ml-1"></i>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end min-w-[240px] !border !border-gray-200 !rounded-lg !shadow-lg !p-0 overflow-hidden">
                        <div class="flex items-center gap-3 p-4 bg-red-50 border-b border-red-100">
                            <div class="inline-flex items-center justify-center rounded-full bg-primary text-white w-[46px] h-[46px] min-w-[46px] text-2xl font-normal border-2 border-red-600">
                                {{ auth()->user()->getFirstLetter() }}
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <div class="text-sm font-bold text-gray-700">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</div>
                                <span class="inline-block mt-1 bg-red-600 text-white text-[0.65rem] font-bold px-2 py-0.5 rounded-full uppercase tracking-[0.4px]">
                                    {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                                </span>
                            </div>
                        </div>
                        <div class="p-1.5">
                            <a href="{{ route('profile.details') }}" class="flex items-center gap-2.5 px-3.5 py-2 rounded text-sm text-gray-700 no-underline hover:bg-red-50 hover:text-red-600 transition-colors my-px">
                                <i class="feather-user text-gray-500 text-[15px] w-4 hover:text-red-600"></i><span>Profile Details</span>
                            </a>
                            <a href="{{ route('settings.account') }}" class="flex items-center gap-2.5 px-3.5 py-2 rounded text-sm text-gray-700 no-underline hover:bg-red-50 hover:text-red-600 transition-colors my-px">
                                <i class="feather-settings text-gray-500 text-[15px] w-4 hover:text-red-600"></i><span>Account Settings</span>
                            </a>
                            <div class="border-t border-gray-200 my-1 mx-2.5"></div>
                            <a href="javascript:void(0);" class="flex items-center gap-2.5 px-3.5 py-2 rounded text-sm text-red-600 no-underline hover:bg-red-50 transition-colors my-px"
                                onclick="document.getElementById('logout-form').submit();">
                                <i class="feather-log-out text-red-600 text-[15px] w-4"></i><span>Logout</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display:none;">
                                @csrf</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="w-full h-[calc(100vh-70px)] mt-[70px] overflow-hidden">
        <div class="w-full h-full overflow-hidden">
            <div class="flex items-center justify-center px-6 py-3 overflow-hidden">
                @yield('content')
            </div>
        </div>
    </main>

    {{-- Template Vendors JS --}}
    <script src="{{ asset('vendors/js/vendors.min.js') }}"></script>
    {{-- daterangepicker was removed; keep layout clean --}}
    <script src="{{ asset('vendors/js/apexcharts.min.js') }}"></script>

    <script>
        (function () {
            const btn = document.getElementById('ktFullscreenBtn');
            if (!btn) return;

            const updateIcon = () => {
                const icon = btn.querySelector('i');
                if (!icon) return;
                icon.className = document.fullscreenElement ? 'feather-minimize' : 'feather-maximize';
                btn.title = document.fullscreenElement ? 'Exit full screen' : 'Full screen';
                btn.setAttribute('aria-label', btn.title);
            };

            btn.addEventListener('click', async () => {
                try {
                    if (!document.fullscreenElement) {
                        await document.documentElement.requestFullscreen();
                    } else {
                        await document.exitFullscreen();
                    }
                } catch (e) {
                    // no-op (fullscreen can be blocked by browser policy)
                } finally {
                    updateIcon();
                }
            });

            document.addEventListener('fullscreenchange', updateIcon);
            updateIcon();
        })();
    </script>
    <script>
        // Force fresh data when returning via browser back/forward cache.
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
                    position: 'fixed',
                    top: '24px',
                    left: '50%',
                    zIndex: '99999',
                    display: 'inline-flex',
                    alignItems: 'center',
                    gap: '8px',
                    padding: '8px 14px',
                    borderRadius: '10px',
                    border: '1px solid #fbcaca',
                    background: 'linear-gradient(135deg, #fff5f5 0%, #ffffff 100%)',
                    color: '#7f1d1d',
                    fontSize: '.82rem',
                    fontWeight: '600',
                    letterSpacing: '0.01em',
                    boxShadow: '0 8px 20px rgba(200, 41, 42, 0.2)',
                    backdropFilter: 'blur(3px)',
                    opacity: '0',
                    transform: 'translate(-50%, -8px) scale(0.97)',
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
                        width: '8px',
                        height: '8px',
                        borderRadius: '999px',
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
