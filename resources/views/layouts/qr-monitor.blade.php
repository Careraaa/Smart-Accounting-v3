<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Attendance Monitor - Smart Accounting</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}">

    @vite(['resources/css/tailwind.css', 'resources/css/overrides.css', 'resources/js/app.js'])

    <style>
        html, body { width: 100%; height: 100vh; margin: 0; padding: 0; overflow: hidden; }
        #mobile-collapse { display: none !important; }

        /* ── Header slide ── */
        #qr-header {
            transition: transform 0.35s cubic-bezier(0.22,1,0.36,1);
        }
        #qr-header.hidden-header {
            transform: translateY(-100%);
        }

        /* ── Main content fills gap when header hidden ── */
        #qr-main {
            transition: margin-top 0.35s cubic-bezier(0.22,1,0.36,1), height 0.35s cubic-bezier(0.22,1,0.36,1);
        }
        #qr-header.hidden-header ~ #qr-main {
            margin-top: 0;
            height: 100vh;
        }
        /* ── Dropdown animation ── */
        [data-dropdown-menu] {
            transition: opacity .15s ease-out, transform .15s ease-out, visibility .15s ease-out;
        }
        .dropdown-closed {
            visibility: hidden !important;
            opacity: 0 !important;
            transform: translateY(-4px) scale(.98) !important;
            pointer-events: none !important;
        }
    </style>
</head>
<body>
    {{-- Trigger strip for showing header on hover --}}
    <div id="header-trigger" style="display:none; position:fixed; top:0; left:0; right:0; height:12px; z-index:49; background:transparent;"></div>

    {{-- Header (matches main layout style) --}}
    <header id="qr-header" class="fixed top-0 left-0 right-0 h-16 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center z-50">
        <div class="flex items-center w-full h-full px-4 sm:px-6">

            {{-- Left: Brand --}}
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 min-w-[32px] rounded-lg bg-gradient-to-br from-rose-800 to-rose-900 flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <div class="text-sm font-bold text-gray-900 leading-tight truncate">Knights Transport</div>
                    <div class="text-[0.6rem] text-gray-500 font-medium leading-tight -mt-px">Fleet Attendance</div>
                </div>
            </div>

            {{-- Right --}}
            <div class="flex items-center gap-0.5 ml-auto">

                {{-- Fullscreen --}}
                <button type="button" id="ktFullscreenBtn"
                    class="hidden sm:flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors cursor-pointer bg-transparent border-none"
                    title="Full screen" aria-label="Full screen">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" id="fs-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                </button>

                {{-- User Profile --}}
                <div class="relative" data-dropdown>
                    <button class="flex items-center gap-2.5 no-underline rounded-lg py-1.5 pl-2 pr-1.5 transition-colors hover:bg-rose-50 cursor-pointer bg-transparent border-none" id="user-dropdown-btn" type="button">
                        <div class="w-8 h-8 min-w-[32px] rounded-full bg-gradient-to-br from-rose-800 to-rose-900 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                            {{ auth()->user()->getFirstLetter() }}
                        </div>
                        <svg class="hidden md:block text-gray-400" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                        </svg>
                    </button>

                    {{-- Dropdown (animated with scale+fade) --}}
                    <div id="user-dropdown" class="dropdown-closed absolute right-0 top-full mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden z-50" data-dropdown-menu>
                        <div class="py-1.5">
                            <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 mx-2 mb-1">
                                <div class="w-9 h-9 min-w-[36px] rounded-full bg-gradient-to-br from-rose-800 to-rose-900 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                    {{ auth()->user()->getFirstLetter() }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-bold text-gray-900 leading-tight truncate">{{ auth()->user()->name }}</div>
                                    <div class="text-[11px] text-gray-500 truncate">{{ auth()->user()->email }}</div>
                                </div>
                            </div>
                            <div class="border-t border-gray-100 my-1 mx-4"></div>
                            <a href="javascript:void(0);" onclick="document.getElementById('logout-form').submit();"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 no-underline transition-colors duration-100 hover:bg-red-50 group">
                                <svg class="w-4 h-4 text-red-400 group-hover:text-red-500 transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Logout
                            </a>
                            <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">@csrf</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Main content area --}}
    <main id="qr-main" class="w-full h-[calc(100vh-4rem)] mt-16 overflow-hidden">
        <div class="w-full h-full overflow-hidden">
            <div class="w-full h-full overflow-hidden">
                @yield('content')
            </div>
        </div>
    </main>

    <script>
    (function () {
        // ── Auto-hide header on idle ──────────────────────────────
        var header   = document.getElementById('qr-header');
        var trigger  = document.getElementById('header-trigger');
        var idleTimer = null;
        var IDLE_MS   = 5000;

        function showHeader() {
            header.classList.remove('hidden-header');
            // keep a slim trigger invisible so hover on edge still works
            trigger.style.display = 'none';
            resetIdleTimer();
        }

        function hideHeader() {
            // only hide if dropdown is closed
            var dd = document.getElementById('user-dropdown');
            if (dd && !dd.classList.contains('dropdown-closed')) {
                resetIdleTimer();
                return;
            }
            header.classList.add('hidden-header');
            trigger.style.display = 'block';
        }

        function resetIdleTimer() {
            clearTimeout(idleTimer);
            idleTimer = setTimeout(hideHeader, IDLE_MS);
        }

        // Show on any activity
        ['mousemove', 'mousedown', 'touchstart', 'keydown'].forEach(function (ev) {
            document.addEventListener(ev, function () {
                if (header.classList.contains('hidden-header')) {
                    showHeader();
                } else {
                    resetIdleTimer();
                }
            });
        });

        // Show when hovering the trigger strip
        trigger.addEventListener('mouseenter', showHeader);

        // Also reset idle when dropdown opens (keep header visible)
        var ddBtn = document.getElementById('user-dropdown-btn');
        if (ddBtn) {
            ddBtn.addEventListener('click', function () {
                // dropdown toggled open — reset idle so header stays
                resetIdleTimer();
            });
        }

        // Start idle timer
        resetIdleTimer();

        // ── Fullscreen toggle ──
        const fsBtn = document.getElementById('ktFullscreenBtn');
        if (fsBtn) {
            const fsIcon = fsBtn.querySelector('svg');
            const updateFsIcon = function () {
                if (!fsIcon) return;
                var isFs = document.fullscreenElement;
                fsIcon.innerHTML = isFs
                    ? '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>';
                fsBtn.title = isFs ? 'Exit full screen' : 'Full screen';
            };
            fsBtn.addEventListener('click', async function () {
                try {
                    if (!document.fullscreenElement) await document.documentElement.requestFullscreen();
                    else await document.exitFullscreen();
                } catch (e) {}
                updateFsIcon();
            });
            document.addEventListener('fullscreenchange', updateFsIcon);
            updateFsIcon();
        }

        // ── User dropdown (animated) ──
        var userBtn  = document.getElementById('user-dropdown-btn');
        var userDrop = document.getElementById('user-dropdown');
        if (userBtn && userDrop) {
            userBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                var isOpen = !userDrop.classList.contains('dropdown-closed');
                // close all other dropdowns
                document.querySelectorAll('[data-dropdown-menu]').forEach(function (el) {
                    el.classList.add('dropdown-closed');
                });
                if (!isOpen) userDrop.classList.remove('dropdown-closed');
            });
            document.addEventListener('click', function (e) {
                var parent = userBtn.closest('[data-dropdown]');
                if (parent && !parent.contains(e.target)) {
                    userDrop.classList.add('dropdown-closed');
                }
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') userDrop.classList.add('dropdown-closed');
            });
        }

        // ── Force fresh data on back/forward ──
        window.addEventListener('pageshow', function (event) {
            var navEntry = performance.getEntriesByType?.('navigation')?.[0];
            if (event.persisted || navEntry?.type === 'back_forward') {
                window.location.reload();
            }
        });
    })();
    </script>
</body>
</html>
