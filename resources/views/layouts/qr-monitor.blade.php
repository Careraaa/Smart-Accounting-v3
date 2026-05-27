<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Attendance Monitor - Smart Accounting</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}">

    @vite(['resources/css/tailwind.css', 'resources/js/app.js'])

    <style>
        html, body { width: 100%; height: 100vh; margin: 0; padding: 0; overflow: hidden; }
        #mobile-collapse { display: none !important; }
        @keyframes fadeSlideUp {
            0% { opacity: 0; transform: translateY(12px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        @keyframes scaleIn {
            0% { opacity: 0; transform: scale(0.92); }
            100% { opacity: 1; transform: scale(1); }
        }
        .anim-header { animation: fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
        .anim-card { animation: scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
        .anim-card:nth-child(1) { animation-delay: 0.05s; }
        .anim-card:nth-child(2) { animation-delay: 0.1s; }
    </style>
</head>
<body>
    {{-- Header --}}
    <header class="fixed top-0 left-0 right-0 h-[70px] bg-white border-b border-gray-200 shadow-sm z-50">
        <div class="flex items-center h-full px-5 gap-1">

            {{-- Left --}}
            <div class="flex items-center gap-3">
                <div class="hidden md:flex items-center gap-2">
                    <span class="font-bold text-sm text-gray-700 tracking-[0.2px]">Knights Transport</span>
                    <span class="bg-rose-50 text-rose-600 border border-rose-200 text-[0.67rem] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-[0.5px]">
                        {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                    </span>
                </div>
            </div>

            {{-- Right --}}
            <div class="ml-auto flex items-center gap-1">

                {{-- Fullscreen --}}
                <button type="button" id="ktFullscreenBtn"
                    class="w-9 h-9 rounded-md flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors cursor-pointer bg-transparent border-none"
                    title="Full screen" aria-label="Full screen">
                    <svg class="w-4.5 h-4.5" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" id="fs-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                    </svg>
                </button>

                {{-- Divider --}}
                <div class="w-px h-[22px] bg-gray-200 mx-1.5 hidden sm:block"></div>

                {{-- User menu --}}
                <div class="relative" id="userMenu">
                    <button type="button" id="userMenuBtn"
                        class="flex items-center gap-2 cursor-pointer px-1.5 py-1 rounded-md hover:bg-gray-100 transition-colors bg-transparent border-none">
                        <div class="inline-flex items-center justify-center rounded-full w-[34px] h-[34px] text-lg font-bold text-white bg-gray-900 shrink-0">
                            {{ auth()->user()->getFirstLetter() }}
                        </div>
                        <div class="hidden md:block text-left leading-snug">
                            <div class="text-sm font-bold text-gray-700 leading-tight">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-gray-500 leading-tight">{{ auth()->user()->email }}</div>
                        </div>
                        <svg class="w-3 h-3 text-gray-400 hidden md:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    {{-- Dropdown --}}
                    <div id="userDropdown" class="hidden absolute right-0 top-full mt-1 min-w-[240px] bg-white border border-gray-200 rounded-lg shadow-lg z-50 overflow-hidden">
                        <div class="flex items-center gap-3 p-4 bg-rose-50 border-b border-rose-100">
                            <div class="inline-flex items-center justify-center rounded-full w-[46px] h-[46px] text-2xl font-bold text-white bg-gray-900 shrink-0">
                                {{ auth()->user()->getFirstLetter() }}
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <div class="text-sm font-bold text-gray-700">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</div>
                                <span class="inline-block mt-1 bg-rose-600 text-white text-[0.65rem] font-bold px-2 py-0.5 rounded-full uppercase tracking-[0.4px]">
                                    {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                                </span>
                            </div>
                        </div>
                        <div class="p-1.5">
                            <a href="{{ route('profile.details') }}" class="flex items-center gap-2.5 px-3.5 py-2 rounded text-sm text-gray-700 no-underline hover:bg-rose-50 hover:text-rose-600 transition-colors my-px">
                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profile Details
                            </a>
                            <a href="{{ route('settings.account') }}" class="flex items-center gap-2.5 px-3.5 py-2 rounded text-sm text-gray-700 no-underline hover:bg-rose-50 hover:text-rose-600 transition-colors my-px">
                                <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Account Settings
                            </a>
                            <div class="border-t border-gray-200 my-1 mx-2.5"></div>
                            <a href="javascript:void(0);" onclick="document.getElementById('logout-form').submit();"
                               class="flex items-center gap-2.5 px-3.5 py-2 rounded text-sm text-rose-600 no-underline hover:bg-rose-50 transition-colors my-px">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Logout
                            </a>
                            <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">@csrf</form>
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

    <script>
    (function () {
        {{-- Fullscreen toggle --}}
        const btn = document.getElementById('ktFullscreenBtn');
        if (btn) {
            const icon = btn.querySelector('svg');
            const updateIcon = () => {
                if (!icon) return;
                const isFs = document.fullscreenElement;
                icon.innerHTML = isFs
                    ? '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>'
                    : '<path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>';
                btn.title = isFs ? 'Exit full screen' : 'Full screen';
            };
            btn.addEventListener('click', async () => {
                try {
                    if (!document.fullscreenElement) await document.documentElement.requestFullscreen();
                    else await document.exitFullscreen();
                } catch (e) {}
                updateIcon();
            });
            document.addEventListener('fullscreenchange', updateIcon);
            updateIcon();
        }

        {{-- User dropdown toggle --}}
        const menuBtn = document.getElementById('userMenuBtn');
        const dropdown = document.getElementById('userDropdown');
        if (menuBtn && dropdown) {
            menuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });
            document.addEventListener('click', (e) => {
                if (!document.getElementById('userMenu').contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') dropdown.classList.add('hidden');
            });
        }

        {{-- Force fresh data on back/forward --}}
        window.addEventListener('pageshow', (event) => {
            const navEntry = performance.getEntriesByType?.('navigation')?.[0];
            if (event.persisted || navEntry?.type === 'back_forward') {
                window.location.reload();
            }
        });
    })();
    </script>
</body>
</html>
