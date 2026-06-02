<!DOCTYPE html>
<html lang="en" class="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <meta name="author" content="flexilecode">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Knights Transport</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights-icon.png') }}">

    {{-- Feather icons only (vendors.min.css replaced — all other vendor CSS was unused) --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/feather.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @stack('head_scripts')

    {{-- Vite — Tailwind + custom overrides --}}
    @vite(['resources/css/tailwind.css', 'resources/css/overrides.css', 'resources/js/app.js'])

    <style>
        /* Bootstrap CSS no longer loaded — vendors.min.css replaced with feather.min.css alone */
    </style>

    <style>
        /* ── Input Shake Animation ─────────────────────────────── */
        @keyframes shakeX {
            0%, 100% { transform: translateX(0); }
            10%, 50%, 90% { transform: translateX(-4px); }
            30%, 70% { transform: translateX(4px); }
        }
        .input-shake { animation: shakeX 0.5s ease-in-out; }
    </style>

    <style>
        /* ── Dark Mode ──────────────────────────────────────────── */
        html.dark { color-scheme: dark; }
        html.dark body { background: #050508; }

        /* Hide native page scrollbars while preserving scrolling */
        html,
        body {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        html::-webkit-scrollbar,
        body::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }

        /* Sidebar */
        html.dark .sidebar { background: #08080f; border-color: #12121e; }
        html.dark .sidebar .sidebar-logo { border-color: #12121e; }
        html.dark .sidebar .sidebar-link { color: #44445a; }
        html.dark .sidebar .sidebar-link:hover { color: #d0d0e8; background: rgba(255,255,255,.03); }
        html.dark .sidebar .sidebar-link.active { color: #e8e8f5; }
        html.dark .sidebar .sidebar-caption label { color: #30304a; }
        html.dark .sidebar .b-brand span.text-gray-900 { color: #e8e8f5; }
        html.dark .sidebar .bg-gray-50 { background: #10101c; }
        html.dark .sidebar input { background: transparent; color: #d0d0e8; }

        /* Header */
        html.dark header { background: rgba(8,8,15,.88); border-color: #12121e; backdrop-filter: blur(14px); }
        html.dark header .text-gray-500 { color: #44445a; }
        html.dark header .text-gray-700 { color: #b8b8d0; }
        html.dark header .hover\:bg-gray-100 { background: transparent !important; }
        html.dark header .hover\:bg-gray-100:hover { background: rgba(255,255,255,.05) !important; }
        html.dark header .bg-white { background: #10101c; }
        html.dark header .border-gray-100 { border-color: #12121e; }
        html.dark header .border-gray-200 { border-color: #18182a; }

        /* Main content area */
        html.dark .bg-gray-100 { background: #050508 !important; }
        html.dark .bg-gray-50 { background: #0c0c18; }
        html.dark main.bg-gray-100 { background: #050508 !important; }
        html.dark main > .bg-gray-100 { background: #050508 !important; }
        html.dark .main-content { background: #050508; }

        /* Cards / surfaces — override all bg-white */
        html.dark .bg-white,
        html.dark .table-wrap,
        html.dark .stat-card,
        html.dark .modal-card,
        html.dark .net-pop,
        html.dark .hrd-slide-bounce,
        html.dark .hrd-stat-card,
        html.dark .hrd-fade-up,
        html.dark .hrd-slide-right,
        html.dark .pf2-card { background: #0a0a14 !important; }
        html.dark .bg-white\/80 { background: rgba(10,10,20,.82); }
        html.dark .bg-white\/95 { background: rgba(10,10,20,.95); }

        /* Bar chart & To-Do icon dark mode overrides */
        html.dark .hrd-bar.bg-gray-200 { background: #2a2a48 !important; }
        html.dark .hrd-bar.bg-emerald-500 { background: #065f46 !important; }
        html.dark .bg-amber-100 { background: rgba(69,26,3,0.6) !important; }
        html.dark .text-amber-700 { color: #fcd34d !important; }
        html.dark .ring-gray-200 { --tw-ring-color: #1a1a30 !important; }
        html.dark .ring-emerald-100 { --tw-ring-color: rgba(6,95,70,0.6) !important; }
        /* To-Do item icon backgrounds (inline styles) */
        html.dark [style*="background:#fef3c7"] { background: #3d2e00 !important; }
        html.dark [style*="background:#e0f2fe"] { background: #002e4d !important; }
        html.dark [style*="background:#f5f3ff"] { background: #1a0050 !important; }
        html.dark [style*="background:#f0fdf4"] { background: #003d1a !important; }
        html.dark [style*="background:#fff0f0"] { background: #4d0000 !important; }
        /* To-Do item icon colors (inline styles) */
        html.dark [style*="color:#d97706"] { color: #fbbf24 !important; }
        html.dark [style*="color:#0284c7"] { color: #38bdf8 !important; }
        html.dark [style*="color:#7c3aed"] { color: #a78bfa !important; }
        html.dark [style*="color:#16a34a"] { color: #4ade80 !important; }
        html.dark [style*="color:#c8292a"] { color: #f87171 !important; }

        /* Text colors — all gray shades */
        html.dark .text-gray-900 { color: #e0e0f0 !important; }
        html.dark .text-gray-800 { color: #cccce0 !important; }
        html.dark .text-gray-700 { color: #b0b0cc !important; }
        html.dark .text-gray-600 { color: #78789a !important; }
        html.dark .text-gray-500 { color: #4e4e6a !important; }
        html.dark .text-gray-400 { color: #5a5a7a !important; }
        html.dark .text-gray-300 { color: #24243a !important; }

        /* Borders */
        html.dark .border-gray-50 { border-color: #0c0c18 !important; }
        html.dark .divide-gray-50,
        html.dark .divide-gray-50 > :not([hidden]) ~ :not([hidden]) { border-color: #0c0c18 !important; }
        html.dark .border-gray-100 { border-color: #12121e !important; }
        html.dark .divide-gray-100,
        html.dark .divide-gray-100 > :not([hidden]) ~ :not([hidden]) { border-color: #12121e !important; }
        html.dark .border-gray-200 { border-color: #18182a !important; }
        html.dark .divide-gray-200,
        html.dark .divide-gray-200 > :not([hidden]) ~ :not([hidden]) { border-color: #18182a !important; }
        html.dark .border-gray-300 { border-color: #24243a !important; }

        /* Shadows — darken them (no white glow) */
        html.dark .shadow-sm { box-shadow: 0 1px 3px rgba(0,0,0,.5); }
        html.dark .shadow-md { box-shadow: 0 4px 12px rgba(0,0,0,.55); }
        html.dark .shadow-lg { box-shadow: 0 8px 24px rgba(0,0,0,.6); }
        html.dark .shadow-xl { box-shadow: 0 20px 60px rgba(0,0,0,.7); }
        html.dark .shadow-2xl { box-shadow: 0 30px 80px rgba(0,0,0,.8); }

        /* Tables */
        html.dark table { background: transparent; }
        html.dark thead { background: #0a0a14; }
        html.dark th { color: #78789a; border-color: #18182a; }
        html.dark td { color: #b0b0cc; border-color: #18182a; }
        html.dark tr { background: transparent; }
        html.dark tr:hover { background: rgba(255,255,255,.015) !important; }

        /* Kill all gray-50 opacity variant backgrounds (commonly used on table rows) */
        html.dark [class*="bg-gray-50"] { background: #0c0c18 !important; }
        html.dark [class*="hover:bg-gray-50"] { background: transparent !important; }
        html.dark .sidebar .sidebar-link { background: transparent !important; }
        html.dark .sidebar .sidebar-link:hover { background: rgba(255,255,255,.035) !important; }

        /* Invert status/colored badges for dark mode */
        html.dark [class*="bg-emerald-50"] { background: rgba(2,44,34,0.6) !important; }
        html.dark [class*="bg-emerald-50"][class*="text-emerald-"] { color: #6ee7b7 !important; }
        html.dark [class*="bg-emerald-50"][class*="border-emerald-"] { border-color: #065f46 !important; }

        html.dark [class*="bg-red-50"] { background: rgba(69,10,10,0.6) !important; }
        html.dark [class*="bg-red-50"][class*="text-red-"] { color: #fca5a5 !important; }
        html.dark [class*="bg-red-50"][class*="border-red-"] { border-color: #991b1b !important; }

        html.dark [class*="bg-amber-50"] { background: rgba(69,26,3,0.6) !important; }
        html.dark [class*="bg-amber-50"][class*="text-amber-"] { color: #fcd34d !important; }
        html.dark [class*="bg-amber-50"][class*="border-amber-"] { border-color: #92400e !important; }

        html.dark [class*="bg-blue-50"] { background: rgba(12,35,102,0.6) !important; }
        html.dark [class*="bg-blue-50"][class*="text-blue-"] { color: #93c5fd !important; }
        html.dark [class*="bg-blue-50"][class*="border-blue-"] { border-color: #1d4ed8 !important; }

        html.dark [class*="bg-violet-50"] { background: rgba(46,16,91,0.6) !important; }
        html.dark [class*="bg-violet-50"][class*="text-violet-"] { color: #c4b5fd !important; }
        html.dark [class*="bg-violet-50"][class*="border-violet-"] { border-color: #6d28d9 !important; }

        html.dark [class*="bg-indigo-50"] { background: rgba(31,27,85,0.6) !important; }
        html.dark [class*="bg-indigo-50"][class*="text-indigo-"] { color: #a5b4fc !important; }
        html.dark [class*="bg-indigo-50"][class*="border-indigo-"] { border-color: #4338ca !important; }

        html.dark [class*="bg-sky-50"] { background: rgba(3,48,73,0.6) !important; }
        html.dark [class*="bg-sky-50"][class*="text-sky-"] { color: #7dd3fc !important; }
        html.dark [class*="bg-sky-50"][class*="border-sky-"] { border-color: #0369a1 !important; }

        html.dark [class*="bg-green-50"] { background: rgba(8,52,22,0.6) !important; }
        html.dark [class*="bg-green-50"][class*="text-green-"] { color: #86efac !important; }
        html.dark [class*="bg-green-50"][class*="border-green-"] { border-color: #15803d !important; }

        html.dark [class*="bg-yellow-50"] { background: rgba(63,32,4,0.6) !important; }
        html.dark [class*="bg-yellow-50"][class*="text-yellow-"] { color: #fde047 !important; }
        html.dark [class*="bg-yellow-50"][class*="border-yellow-"] { border-color: #a16207 !important; }

        html.dark [class*="bg-cyan-50"] { background: rgba(10,55,92,0.6) !important; }
        html.dark [class*="bg-cyan-50"][class*="text-cyan-"] { color: #67e8f9 !important; }
        html.dark [class*="bg-cyan-50"][class*="border-cyan-"] { border-color: #0891b2 !important; }

        html.dark [class*="bg-purple-50"] { background: rgba(59,7,100,0.6) !important; }
        html.dark [class*="bg-purple-50"][class*="text-purple-"] { color: #d8b4fe !important; }
        html.dark [class*="bg-purple-50"][class*="border-purple-"] { border-color: #7e22ce !important; }

        html.dark [class*="bg-rose-50"] { background: rgba(69,10,26,0.6) !important; }
        html.dark [class*="bg-rose-50"][class*="text-rose-"] { color: #fb7185 !important; }
        html.dark [class*="bg-rose-50"][class*="border-rose-"] { border-color: #be123c !important; }

        html.dark [class*="bg-slate-50"] { background: rgba(30,41,59,0.6) !important; }
        html.dark [class*="bg-slate-50"][class*="text-slate-"] { color: #94a3b8 !important; }
        html.dark [class*="bg-slate-50"][class*="border-slate-"] { border-color: #334155 !important; }

        /* Invert bg-*-100 badges (tab active states, count badges) */
        html.dark [class*="bg-emerald-100"] { background: rgba(2,44,34,0.8) !important; }
        html.dark [class*="bg-emerald-100"][class*="text-emerald-"] { color: #34d399 !important; }

        html.dark [class*="bg-amber-100"] { background: rgba(69,26,3,0.8) !important; }
        html.dark [class*="bg-amber-100"][class*="text-amber-"] { color: #fbbf24 !important; }

        html.dark [class*="bg-red-100"] { background: rgba(69,10,10,0.8) !important; }
        html.dark [class*="bg-red-100"][class*="text-red-"] { color: #f87171 !important; }

        html.dark [class*="bg-blue-100"] { background: rgba(12,35,102,0.8) !important; }
        html.dark [class*="bg-blue-100"][class*="text-blue-"] { color: #60a5fa !important; }

        html.dark [class*="bg-indigo-100"] { background: rgba(31,27,85,0.8) !important; }
        html.dark [class*="bg-indigo-100"][class*="text-indigo-"] { color: #818cf8 !important; }

        html.dark [class*="bg-violet-100"] { background: rgba(46,16,91,0.8) !important; }
        html.dark [class*="bg-violet-100"][class*="text-violet-"] { color: #a78bfa !important; }

        html.dark [class*="bg-cyan-100"] { background: rgba(10,55,92,0.8) !important; }
        html.dark [class*="bg-cyan-100"][class*="text-cyan-"] { color: #22d3ee !important; }

        html.dark [class*="bg-purple-100"] { background: rgba(59,7,100,0.8) !important; }
        html.dark [class*="bg-purple-100"][class*="text-purple-"] { color: #d8b4fe !important; }

        html.dark [class*="bg-gray-200"] { background: #1c1c30 !important; }
        html.dark [class*="bg-gray-200"][class*="text-gray-"] { color: #9ca3af !important; }

        /* Department/gray badges — subtle dark pill */
        html.dark [class*="bg-gray-100"] { background: #14142a !important; }
        html.dark [class*="bg-gray-100"][class*="text-gray-"] { color: #9ca3af !important; }
        html.dark [class*="bg-gray-100"][class*="border-gray-200"] { border-color: #1e1e34 !important; }

        /* Calendar legend boxes */
        html.dark [class*="bg-emerald-100"][class*="border-emerald-300"] { background: rgba(2,44,34,0.8) !important; border-color: #065f46 !important; }
        html.dark [class*="bg-red-100"][class*="border-red-300"] { background: rgba(69,10,10,0.8) !important; border-color: #991b1b !important; }
        html.dark [class*="bg-yellow-100"][class*="border-yellow-300"] { background: rgba(63,32,4,0.8) !important; border-color: #a16207 !important; }
        html.dark [class*="bg-blue-100"][class*="border-blue-300"] { background: rgba(12,35,102,0.8) !important; border-color: #1d4ed8 !important; }
        html.dark [class*="bg-violet-100"][class*="border-violet-300"] { background: rgba(46,16,91,0.8) !important; border-color: #6d28d9 !important; }

        /* Restore group-hover invert animations on stat cards (override [class*="bg-*-50"] catch-all) */
        html.dark .group:hover [class*="group-hover:bg-emerald-500"] { background: #10b981 !important; }
        html.dark .group:hover [class*="group-hover:bg-red-500"] { background: #ef4444 !important; }
        html.dark .group:hover [class*="group-hover:bg-amber-500"] { background: #f59e0b !important; }
        html.dark .group:hover [class*="group-hover:bg-blue-500"] { background: #3b82f6 !important; }
        html.dark .group:hover [class*="group-hover:bg-indigo-500"] { background: #6366f1 !important; }
        html.dark .group:hover [class*="group-hover:bg-violet-500"] { background: #8b5cf6 !important; }
        html.dark .group:hover [class*="group-hover:bg-cyan-500"] { background: #06b6d4 !important; }
        html.dark .group:hover [class*="group-hover:bg-rose-500"] { background: #f43f5e !important; }
        html.dark .group:hover [class*="group-hover:bg-yellow-500"] { background: #eab308 !important; }
        html.dark .group:hover [class*="group-hover:bg-green-500"] { background: #22c55e !important; }
        html.dark .group:hover [class*="group-hover:bg-sky-500"] { background: #0ea5e9 !important; }
        html.dark .group:hover [class*="group-hover:bg-teal-500"] { background: #14b8a6 !important; }
        html.dark .group:hover [class*="group-hover:bg-purple-500"] { background: #a855f7 !important; }
        html.dark .group:hover [class*="group-hover:bg-pink-500"] { background: #ec4899 !important; }
        html.dark .group:hover [class*="group-hover:bg-gray-500"] { background: #6b7280 !important; }
        html.dark .group:hover [class*="group-hover:bg-gray-900"] { background: #111827 !important; }
        html.dark .group:hover [class*="group-hover:text-white"] { color: #fff !important; }

        /* Forms */
        html.dark input:not([type="checkbox"]):not([type="radio"]):not([type="file"]),
        html.dark select,
        html.dark textarea { background: #0a0a14; border-color: #18182a; color: #cccce0; }
        html.dark input[type="file"] { background: transparent; color: #9ca3af; }
        html.dark input:focus,
        html.dark select:focus,
        html.dark textarea:focus { border-color: #c8292a; }
        html.dark label { color: #78789a; }
        html.dark select option { background: #0a0a14; color: #cccce0; }

        /* Dropdowns */
        html.dark [data-dropdown-menu],
        html.dark #kt-search-popup,
        html.dark #notification-dropdown,
        html.dark #user-dropdown { background: #0a0a14; border-color: #18182a; }
        html.dark #kt-search-popup .text-gray-800 { color: #cccce0; }
        html.dark #notification-dropdown .text-gray-900 { color: #e0e0f0; }
        html.dark #notification-dropdown .text-gray-600 { color: #78789a; }
        html.dark #notification-dropdown .bg-gray-50\/80 { background: transparent !important; }
        html.dark #notification-dropdown [data-notif-id]:hover { background: transparent !important; }

        /* Hover fixes — remove white glow */
        html.dark .hover\:bg-gray-50:hover,
        html.dark .hover\:bg-gray-100:hover,
        html.dark #user-dropdown .hover\:bg-gray-50:hover { background: rgba(255,255,255,.035) !important; }
        html.dark .hover\:border-gray-300:hover { border-color: #24243a !important; }
        html.dark .hover\:shadow-sm:hover { box-shadow: 0 1px 3px rgba(0,0,0,.5); }
        html.dark .hover\:shadow-md:hover { box-shadow: 0 4px 12px rgba(0,0,0,.55); }

        /* Buttons */
        html.dark .btn-uv-pill { color: #78789a; }

        /* Sidebar search */
        html.dark #kt-nav-search .bg-gray-50 { background: #10101c; }
        html.dark #kt-nav-search .border-gray-200 { border-color: #18182a; }
        html.dark #kt-nav-search input { color: #cccce0; }
        html.dark #kt-nav-search .focus-within\:bg-white:focus-within { background: #0a0a14 !important; }

        /* Flash messages */
        html.dark .bg-blue-50 { background: rgba(20,50,140,.25); border-color: rgba(20,50,140,.35); }
        html.dark .text-blue-700 { color: #7aabf0; }

        /* Submenu */
        html.dark .sidebar-sub .sidebar-link { color: #44445a; }
        html.dark .sidebar-sub .sidebar-link:hover { color: #b0b0cc; }

        /* Sidebar collapse — main content and header positions sync via direct CSS */
        main { transition: margin-left .5s cubic-bezier(.4,0,.2,1), background-color .15s ease, color .15s ease, border-color .15s ease !important; }
        header { transition: left .5s cubic-bezier(.4,0,.2,1), background-color .15s ease, color .15s ease, border-color .15s ease !important; }
        @media (min-width: 1024px) {
            body:not(.sidebar-collapsed) main { margin-left: 240px !important; }
            body.sidebar-collapsed main { margin-left: 64px !important; }
            body:not(.sidebar-collapsed) header { left: 240px !important; }
            body.sidebar-collapsed header { left: 64px !important; }
        }
        /* Prevent content elements from animating layout properties during sidebar collapse —
           override any transition-all / transition on width, height, margin, padding, etc.
           so only visual/composited properties animate, staying in sync with the main area. */
        main .main-content * { transition-property: color, background-color, border-color, outline-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter !important; transition-duration: 0.15s !important; transition-timing-function: ease !important; }
        #sidebar-collapse-btn { left: 224px; transition: left .5s cubic-bezier(.4,0,.2,1), background-color .2s ease, border-color .2s ease, box-shadow .2s ease, color .2s ease; }
        body.sidebar-collapsed #sidebar-collapse-btn { left: 48px; }

        /* ── Mobile: table horizontal scroll ── */
        @media (max-width: 639px) {
            .overflow-x-auto { -webkit-overflow-scrolling: touch; }
            .main-content .overflow-x-auto > table.w-full { min-width: 580px; }
        }

        /* Collapse button dark */
        html.dark #sidebar-collapse-btn { background: #10101c; border-color: #18182a; color: #44445a; }
        html.dark #sidebar-collapse-btn:hover { background: #18182a; }

        /* ── Right sidebar ── */
        #right-sidebar { width: 280px; transform: translateX(100%); transition: transform .5s cubic-bezier(.4,0,.2,1); }
        body.right-sidebar-open #right-sidebar { transform: translateX(0); }
        #right-sidebar-toggle { transition: right .5s cubic-bezier(.4,0,.2,1), opacity .3s ease; }
        body.right-sidebar-open #right-sidebar-toggle { right: 280px; opacity: 1; pointer-events: auto; }
        @media (max-width: 1023px) {
            #right-sidebar { display: none; }
            #right-sidebar-toggle { display: none; }
        }
        /* Animate activity items on sidebar open — staggered entrance */
        .rs-activity-item { opacity: 0; transform: translateX(16px); transition: opacity .45s cubic-bezier(.21,.98,.35,1), transform .45s cubic-bezier(.21,.98,.35,1), background-color .15s ease, border-color .15s ease; }
        body.right-sidebar-open .rs-activity-item { opacity: 1; transform: translateX(0); }
        /* Pinned item entrance animation */
        @keyframes rsSlideIn { from { opacity: 0; transform: translateX(-12px) scale(.96); } to { opacity: 1; transform: translateX(0) scale(1); } }
        .rs-pinned-item { transition: background-color .15s ease, transform .15s ease, opacity .15s ease; }
        .rs-pinned-item:hover { transform: scale(1.02); }
        .rs-pinned-item:hover .rs-pin-icon { animation: rsIconWiggle 0.3s ease-in-out; }
        @keyframes rsIconWiggle { 0%,100% { transform: rotate(0deg); } 25% { transform: rotate(-10deg); } 75% { transform: rotate(10deg); } }
        /* Star bounce on hover */
        .rs-pin-toggle svg, .rs-result-star { transition: transform .2s cubic-bezier(.34,1.56,.64,1), fill .15s ease, color .15s ease; }
        .rs-pin-toggle:hover svg { transform: scale(1.3) rotate(-10deg); }
        .rs-search-result:hover .rs-result-star { transform: scale(1.25) rotate(-10deg); }
        /* Search results dropdown */
        #rs-pin-search-results { transition: opacity .15s ease, transform .15s ease; transform-origin: top center; }
        #rs-pin-search-results:not([style*="display: none"]) { animation: rsDropIn .15s ease-out; }
        @keyframes rsDropIn { from { opacity: 0; transform: translateY(-6px) scale(.96); } to { opacity: 1; transform: translateY(0) scale(1); } }
        /* Search result rows */
        .rs-search-result { transition: background .12s ease, transform .12s ease; }
        .rs-search-result:hover { transform: translateX(2px); }
        /* Collapsible activity section */
        #rs-activity-body { max-height: 2000px; overflow-y: auto; transition: max-height .4s cubic-bezier(.4,0,.2,1), opacity .3s ease, margin .3s ease; }
        .rs-activity-collapsed #rs-activity-body { max-height: 0 !important; opacity: 0; overflow-y: hidden; margin-bottom: 0; }
        #rs-activity-toggle { cursor: pointer; user-select: none; }
        #rs-activity-toggle .rs-activity-arrow { transition: transform .35s cubic-bezier(.4,0,.2,1); }
        .rs-activity-collapsed .rs-activity-arrow { transform: rotate(-90deg); }
        /* Pin animation — flash on search result row when pinned */
        @keyframes rsPinFlash { 0% { background-color: rgba(251,191,36,.2); box-shadow: inset 0 0 0 1px rgba(251,191,36,.3); } 50% { background-color: rgba(251,191,36,.12); } 100% { background-color: transparent; box-shadow: inset 0 0 0 1px transparent; } }
        .rs-pin-flash { animation: rsPinFlash .6s ease-out forwards; border-radius: 0.5rem; }
        /* Star pop animation when pinning */
        @keyframes rsStarPop { 0% { transform: scale(1); } 30% { transform: scale(1.7); filter: brightness(1.5); } 60% { transform: scale(.82); } 100% { transform: scale(1); filter: brightness(1); } }
        .rs-star-pop svg { animation: rsStarPop .5s cubic-bezier(.34,1.56,.64,1) forwards; }
        /* Drag resize cursor */
        body.cursor-row-resize, body.cursor-row-resize * { cursor: row-resize !important; user-select: none !important; }
        /* Resize handle grip dots */
        #rs-resize-handle .rs-grip { display: flex; align-items: center; gap: 4px; }
        #rs-resize-handle .rs-grip span { width: 3px; height: 3px; border-radius: 50%; background: #d1d5db; transition: background .2s, box-shadow .2s; }
        #rs-resize-handle:hover .rs-grip span { background: #9ca3af; box-shadow: 0 0 6px rgba(156,163,175,.4); }
        /* Drag handle glow line */
        #rs-resize-handle::before { content: ''; position: absolute; inset: 4px 12px; border-radius: 999px; background: transparent; transition: background .25s; }
        #rs-resize-handle:hover::before { background: rgba(0,0,0,.03); }
        #rs-resize-handle:active::before { background: rgba(0,0,0,.06); }
        /* Sidebar header fade on open */
        body.right-sidebar-open #right-sidebar .rs-section-header { animation: rsSlideIn .35s ease-out both; }

        /* Confirm modal */
        html.dark #sa-confirm-modal { background: #0a0a14; border-color: #18182a; }
        html.dark .sa-confirm-title { color: #e0e0f0; }
        html.dark .sa-confirm-msg { color: #78789a; }
        html.dark .sa-confirm-btn.ghost { background: #10101c; color: #cccce0; border-color: #18182a; }
        html.dark .sa-confirm-btn.ghost:hover { background: #18182a; }

        /* Smooth dark/light mode transition — lightweight, only color/bg/border on key containers */
        body, .sidebar, header, main, .bg-white, .bg-gray-100, .bg-gray-50,
        table, thead, th, td, tr, input, select, textarea,
        [class*="border-"], [class*="divide-"], [class*="text-"],
        .main-content, .card, [class*="bg-emerald"], [class*="bg-red"],
        [class*="bg-amber"], [class*="bg-blue"], [class*="bg-indigo"],
        [class*="bg-violet"], [class*="bg-cyan"], [class*="bg-rose"],
        [class*="bg-gray"], [class*="bg-sky"], [class*="bg-green"],
        [class*="bg-yellow"], [class*="bg-purple"], [class*="bg-slate"] {
            transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        }

        /* Dark mode toggle tooltip (calendar-style, bottom) */
        .dark-tip {
            position: relative;
        }
        .dark-tip::before,
        .dark-tip::after {
            position: absolute;
            top: 100%;
            left: 50%;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.15s ease, transform 0.15s ease;
            transform: translateX(-50%) translateY(-4px);
            z-index: 9999;
        }
        .dark-tip::before {
            content: attr(data-tip);
            top: calc(100% + 5px);
            white-space: nowrap;
            padding: 5px 10px;
            font-size: 0.65rem;
            font-weight: 600;
            color: #374151;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,.08);
            line-height: 1.4;
        }
        .dark-tip::after {
            content: '';
            top: calc(100% - 1px);
            width: 8px;
            height: 8px;
            background: #fff;
            border-left: 1px solid #e5e7eb;
            border-top: 1px solid #e5e7eb;
            transform: translateX(-50%) translateY(-4px) rotate(45deg);
            box-shadow: -2px -2px 4px rgba(0,0,0,.04);
        }
        .dark-tip:hover::before,
        .dark-tip:hover::after {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
        .dark-tip:hover::after {
            transform: translateX(-50%) translateY(0) rotate(45deg);
        }

        /* Dark mode toggle tooltip — dark variant */
        html.dark .dark-tip::before {
            background: #0a0a14;
            border-color: #18182a;
            color: #b0b0cc;
            box-shadow: 0 4px 12px rgba(0,0,0,.4);
        }
        html.dark .dark-tip::after {
            background: #0a0a14;
            border-left-color: #18182a;
            border-top-color: #18182a;
            box-shadow: -2px -2px 4px rgba(0,0,0,.3);
        }

        /* Dark mode toggle */
        html.dark #dark-mode-toggle { color: #b8b8d0 !important; }
        html.dark #dark-mode-toggle:hover { background: rgba(255,255,255,.05) !important; color: #e0e0f0 !important; }

        /* Profile page (pf2 classes) */
        html.dark .pf2-page { background: #050508; }
        html.dark .pf2-card { border-color: #18182a; }
        html.dark .pf2-card-head { border-color: #18182a; }
        html.dark .pf2-input { background: #0a0a14; border-color: #18182a; color: #cccce0; }
        html.dark .pf2-input:focus { border-color: #c8292a; }
        html.dark .pf2-field-label { color: #78789a; }
        html.dark .pf2-title { color: #e0e0f0; }
        html.dark .pf2-sub { color: #78789a; }
        html.dark .pf2-hero-name { color: #e0e0f0; }
        html.dark .pf2-hero-meta { color: #78789a; }
        html.dark .pf2-avatar { background: #0a0a14; color: #b8b8d0; border-color: #18182a; }
        html.dark .pf2-btn { background: #0a0a14; color: #b0b0cc; border-color: #18182a; }
        html.dark .pf2-btn:hover { background: #101020; }
        html.dark .pf2-btn-primary { background: #c8292a; }
        html.dark .pf2-btn-primary:hover { background: #a81f20; }
        html.dark .pf2-help { color: #4e4e6a; }
        html.dark .pf2-divider { border-color: #18182a; }
        html.dark .pf2-chip { background: #10101c; color: #78789a; border-color: #18182a; }
        html.dark .pf2-backdrop .pf2-grid div { background: rgba(255,255,255,.015); }

        /* Calendar (prl-calendar classes) */
        html.dark .prl-calendar-container { background: transparent; }
        html.dark .prl-calendar { background: #0a0a14; border-color: #18182a; }
        html.dark .prl-calendar-header { border-color: #18182a; }
        html.dark .prl-calendar-title { color: #e0e0f0; }
        html.dark .prl-calendar-btn { background: #10101c; color: #78789a; border-color: #18182a; }
        html.dark .prl-calendar-btn:hover { background: #18182a; color: #b0b0cc; }
        html.dark .prl-calendar-weekday { color: #4e4e6a; }
        html.dark .prl-calendar-date { color: #b0b0cc; }
        html.dark .prl-calendar-date:hover { background: rgba(255,255,255,.03); }
        html.dark .prl-calendar-date.other-month { color: #24243a; }
        html.dark .prl-calendar-date.today { background: rgba(200,41,42,.15); color: #e0e0f0; }
        html.dark .prl-calendar-date.holiday { color: #f87171; }
        html.dark .prl-calendar-date.selected { background: #c8292a; color: #fff; }
        html.dark .prl-calendar-footer { border-color: #18182a; }
        html.dark .prl-calendar-link { color: #4e4e6a; }
        html.dark .prl-calendar-link:hover { color: #b0b0cc; }

        /* Employee show page cards */
        html.dark .rounded-2xl.overflow-hidden.shadow-sm,
        html.dark [class*="rounded-2xl"][class*="shadow-sm"] { background: #0a0a14; border-color: #18182a; }

        /* General hover:bg-gray-50  remap */
        html.dark [class*="hover:bg-gray-50"]:hover { background: rgba(255,255,255,.03) !important; }

        /* Attendance calendar page */
        html.dark .bg-gray-50.border { background: #0c0c18; border-color: #18182a; }

        /* Badge/pill backgrounds */
        html.dark .bg-gray-100 { background: #10101c; }

        /* Pagination */
        html.dark .page-link { background: #0a0a14; border-color: #18182a; color: #b0b0cc; }
        html.dark .page-link:hover { background: #10101c; }
        html.dark .page-item.active .page-link { background: #c8292a; border-color: #c8292a; }
    </style>

    @stack('styles')
</head>

<body>

    {{-- Skeleton loader for page refreshes --}}
    <style>
        .sk-item { background: #e5e7eb; border-radius: 8px; position: relative; overflow: hidden; }
        .sk-item::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.35) 50%, transparent 100%);
            animation: skShimmer 1.5s ease-in-out infinite;
        }
        @keyframes skShimmer { 0%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }
        html.dark .sk-item { background: #1a1a30; }
        html.dark .sk-item::after { background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.06) 50%, transparent 100%); }
        #page-skeleton { transition: opacity .3s ease; }
        #page-skeleton.loaded { opacity: 0; pointer-events: none; }
        .sk-sidebar { width:240px; border-right:1px solid #f3f4f6; }
        html.dark .sk-sidebar { border-color:#12121e; }
        .sk-crystal { --cc: 0.08; }
    </style>
    <div id="page-skeleton" class="fixed inset-0 z-[9999] bg-white dark:bg-[#050508] flex">
        {{-- Sidebar skeleton (desktop only) --}}
        <div class="sk-sidebar shrink-0 flex-col h-full" id="sk-sidebar-cont">
            {{-- Brand --}}
            <div class="flex items-center px-3 h-16 border-b border-gray-100 dark:border-[#12121e]">
                <div class="sk-item h-4 w-28 rounded-md"></div>
            </div>
            {{-- Search --}}
            <div class="mx-3 mt-3 mb-1">
                <div class="sk-item h-9 rounded-xl"></div>
            </div>
            {{-- Nav items --}}
            @php
                $skRole = auth()->user()->role;
                $skSections = match ($skRole) {
                    'superadmin' => [['caption'=>'SYSTEM ADMIN','count'=>4],['caption'=>'HR MANAGEMENT','count'=>5],['caption'=>'OPERATIONS','count'=>2],['caption'=>'FINANCIALS','count'=>5]],
                    'hr' => [['caption'=>'HR MANAGEMENT','count'=>4],['caption'=>'PAYROLL','count'=>4],['caption'=>'REPORTS','count'=>2],['caption'=>'MY FINANCES','count'=>2]],
                    default => [['caption'=>'MENU','count'=>2],['caption'=>'OPERATIONS','count'=>2],['caption'=>'REPORTS','count'=>1],['caption'=>'MY FINANCES','count'=>3],['caption'=>'TIME OFF','count'=>2],['caption'=>'MY ACCOUNT','count'=>2]],
                };
            @endphp
            <div class="flex-1 overflow-hidden px-3 py-2 space-y-0.5">
                @foreach($skSections as $skSec)
                <div class="sk-item h-3 w-{{ strlen($skSec['caption']) > 10 ? '24' : '20' }} mb-2 ml-2 mt-{{ $loop->first ? '0' : '3' }}"></div>
                @for($i=0;$i<$skSec['count'];$i++)
                <div class="flex items-center gap-1.5 px-3 py-1.5">
                    <div class="sk-item h-[30px] w-[30px] rounded-lg shrink-0"></div>
                    <div class="sk-item h-3.5 flex-1 max-w-[120px] rounded-md"></div>
                </div>
                @endfor
                @endforeach
            </div>
        </div>
        <style>#sk-sidebar-cont{display:none}@media(min-width:1024px){#sk-sidebar-cont{display:flex!important}}</style>
        {{-- Main area skeleton --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Navbar skeleton --}}
            <div class="h-16 border-b border-gray-100 dark:border-[#12121e] flex items-center px-4 sm:px-6">
                <div class="sk-item h-5 w-6 sm:w-48 rounded-md lg:hidden"></div>
                <div class="sk-item h-4 w-48 rounded-md hidden lg:block"></div>
                <div class="flex-1"></div>
                <div class="flex items-center gap-2 sm:gap-3">
                    <div class="sk-item h-7 w-7 sm:h-8 sm:w-8 rounded-lg"></div>
                    <div class="sk-item h-7 w-7 sm:h-8 sm:w-8 rounded-lg hidden sm:block"></div>
                    <div class="sk-item h-7 w-7 sm:h-8 sm:w-8 rounded-lg"></div>
                    <div class="sk-item h-7 w-7 sm:h-8 sm:w-8 rounded-full"></div>
                </div>
            </div>
            {{-- Crystal spinner --}}
            <div class="flex-1 flex items-center justify-center bg-gray-50 dark:bg-[#0c0c18]">
                <style>
                    @keyframes skSpin {
                        from{transform:translate(-50%,-50%) rotateX(45deg) rotateZ(0deg)}
                        to{transform:translate(-50%,-50%) rotateX(45deg) rotateZ(360deg)}
                    }
                    @keyframes skEmerg {
                        0%,100%{transform:translate(-50%,-50%) scale(0.5);opacity:0}
                        50%{transform:translate(-50%,-50%) scale(1);opacity:1}
                    }
                    @keyframes skFade { to{visibility:visible;opacity:0.35} }
                    .sk-crystal {
                        position:absolute; top:50%; left:50%;
                        width:48px; height:48px; opacity:0;
                        transform-origin:bottom center;
                        border-radius:8px; visibility:hidden;
                    }
                    .sk-crystal:nth-child(1){background:linear-gradient(45deg,#6366f1,#818cf8);animation-delay:0s}
                    .sk-crystal:nth-child(2){background:linear-gradient(45deg,#4f46e5,#6366f1);animation-delay:0.15s}
                    .sk-crystal:nth-child(3){background:linear-gradient(45deg,#4338ca,#4f46e5);animation-delay:0.3s}
                    .sk-crystal:nth-child(4){background:linear-gradient(45deg,#6366f1,#a5b4fc);animation-delay:0.45s}
                    .sk-crystal:nth-child(5){background:linear-gradient(45deg,#818cf8,#6366f1);animation-delay:0.6s}
                    .sk-crystal:nth-child(6){background:linear-gradient(45deg,#4f46e5,#4338ca);animation-delay:0.75s}
                    .sk-crystal { animation:skSpin 2s linear infinite, skEmerg 0.8s ease-in-out infinite alternate, skFade 0.25s ease-out forwards; }
                </style>
                <div class="relative w-[160px] h-[160px]" style="perspective:600px">
                    <div class="sk-crystal"></div>
                    <div class="sk-crystal"></div>
                    <div class="sk-crystal"></div>
                    <div class="sk-crystal"></div>
                    <div class="sk-crystal"></div>
                    <div class="sk-crystal"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.addEventListener('load', function(){
            var sk = document.getElementById('page-skeleton');
            if (sk) sk.classList.add('loaded');
        });
        window.addEventListener('beforeunload', function(){
            var sk = document.getElementById('page-skeleton');
            if (sk) sk.classList.remove('loaded');
        });
    </script>

    @include('partials.sidebar')
    @include('partials.header')

    <main class="ml-0 bg-gray-100 pt-16 min-h-screen" data-global-datepicker="off">
        <div class="bg-gray-100">

            <div class="main-content px-4 sm:px-7 lg:px-9 py-7">
                @if (session('info'))
                    <div class="flash-bar flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium bg-blue-50 border border-blue-200 text-blue-700" role="alert">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
                        <span class="flex-1">{{ session('info') }}</span>
                        <button type="button" onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700 cursor-pointer bg-transparent border-none p-0 leading-none">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                <div class="w-full max-w-full">
                    @yield('content')
                </div>
            </div>

        </div>
    </main>

    @include('partials.right-sidebar')

    {{-- Sidebar Collapse / Mobile Toggle --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('sidebar-collapse-btn');
            if (!btn) return;
            var icon = btn.querySelector('svg');
            var storageKey = 'sidebar-collapsed';
            var isMobile = function() { return window.innerWidth < 1024; };

            // Restore saved state (desktop only)
            if (!isMobile()) {
                var saved = localStorage.getItem(storageKey);
                if (saved === 'true') {
                    document.body.classList.add('sidebar-collapsed');
                    if (icon) icon.style.transform = 'rotate(180deg)';
                }
            }

            btn.addEventListener('click', function() {
                if (isMobile()) {
                    // Mobile: toggle overlay
                    document.body.classList.toggle('sidebar-open');
                } else {
                    // Desktop: collapse/expand
                    var collapsed = document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem(storageKey, collapsed);
                    if (icon) icon.style.transform = collapsed ? 'rotate(180deg)' : '';
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nav = document.querySelector('.sidebar');
            if (!nav) return;

            const menuItems = Array.from(nav.querySelectorAll('.sidebar-item.has-sub'));
            if (!menuItems.length) return;

            // Prevent visible flicker while the menu state is initialized.
            nav.classList.add('sidebar-initializing');

            const isExpandedDesktop = () =>
                window.innerWidth >= 1200 && !document.body.classList.contains('sidebar-collapsed');

            const getDirectTrigger = (item) => {
                for (const child of item.children) {
                    if (child.classList && child.classList.contains('sidebar-link')) return child;
                }
                return null;
            };

            const setOpen = (item, open) => {
                const trigger = getDirectTrigger(item);
                if (!trigger) return;
                item.classList.toggle('open', open);
                trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
            };

            const initializeMenus = () => {
                const allowOpen = isExpandedDesktop();
                menuItems.forEach((item) => {
                    const hasActiveChild = !!item.querySelector('.sidebar-sub .sidebar-item.active');
                    setOpen(item, allowOpen && (item.classList.contains('active') || hasActiveChild));
                });
            };

            menuItems.forEach((item) => {
                const trigger = getDirectTrigger(item);
                if (!trigger) return;

                trigger.addEventListener('click', function(e) {
                    const href = (trigger.getAttribute('href') || '').trim().toLowerCase();
                    const isToggleLink = href === '' || href === '#' || href === 'javascript:void(0);';
                    if (!isToggleLink) return;

                    e.preventDefault();
                    e.stopPropagation();

                    const next = !item.classList.contains('open');

                    if (isExpandedDesktop()) {
                        menuItems.forEach((other) => {
                            if (other !== item) setOpen(other, false);
                        });
                    }

                    setOpen(item, next);

                    // Scroll sidebar to keep the opened dropdown visible
                    if (next) {
                        requestAnimationFrame(function() {
                            var sub = item.querySelector('.sidebar-sub');
                            var scrollEl = nav.querySelector('.overflow-y-auto');
                            if (sub && scrollEl) {
                                var itemBottom = item.offsetTop + item.offsetHeight;
                                var scrollBottom = scrollEl.scrollTop + scrollEl.clientHeight;
                                if (itemBottom > scrollBottom) {
                                    scrollEl.scrollTop = itemBottom - scrollEl.clientHeight;
                                }
                            }
                        });
                    }
                });
            });

            initializeMenus();
            requestAnimationFrame(() => nav.classList.remove('sidebar-initializing'));
            window.addEventListener('resize', initializeMenus);
        });
    </script>

    @if(session('show_sidebar_peek'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth < 1024) return;
            setTimeout(function() {
                document.body.classList.add('right-sidebar-open');
                var icon = document.querySelector('#right-sidebar-toggle svg');
                if (icon) icon.style.transform = 'rotate(180deg)';
                setTimeout(function() {
                    document.body.classList.remove('right-sidebar-open');
                    if (icon) icon.style.transform = '';
                }, 1500);
            }, 400);
        });
    </script>
    @endif

    <script>
        window.attendanceRowsUrl = "{{ route('api.attendance.table-rows') }}";
        window.notificationsCountUrl = "{{ route('notifications.count') }}";
    </script>


    {{-- Page-specific scripts --}}
    @yield('scripts')
    @stack('scripts')

    {{-- Global confirm modal (replaces browser confirm for payroll flows) --}}
    <div id="sa-confirm-overlay" style="display:none;">
        <div id="sa-confirm-modal" role="dialog" aria-modal="true" aria-labelledby="saConfirmTitle">
            <div class="sa-confirm-head">
                <div id="saConfirmIcon" class="sa-confirm-icon" aria-hidden="true">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86l-7.4 13.3A2 2 0 004.62 20h14.76a2 2 0 001.73-2.84l-7.4-13.3a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <div id="saConfirmTitle" class="sa-confirm-title">Confirm action</div>
                    <div id="saConfirmMessage" class="sa-confirm-msg">Are you sure?</div>
                </div>
            </div>
            <div class="sa-confirm-actions">
                <button type="button" id="saConfirmCancel" class="sa-confirm-btn ghost">Cancel</button>
                <button type="button" id="saConfirmOk" class="sa-confirm-btn danger">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        // Global input normalizers (used by multiple forms across the app)
        (() => {
            const normalizeDigitsOnly = (el) => {
                const digits = (el.value || '').replace(/[^\d]/g, '');
                if (el.value !== digits) el.value = digits;
            };

            const normalizeDecimalOnly = (el) => {
                const raw = (el.value || '');
                let cleaned = raw.replace(/[^\d.]/g, '');
                const firstDot = cleaned.indexOf('.');
                if (firstDot !== -1) {
                    cleaned = cleaned.slice(0, firstDot + 1) + cleaned.slice(firstDot + 1).replace(/\./g, '');
                }
                if (el.value !== cleaned) el.value = cleaned;
            };

            document.addEventListener('input', (e) => {
                const el = e.target;
                if (!(el instanceof HTMLInputElement)) return;
                if (el.hasAttribute('data-digits-only')) normalizeDigitsOnly(el);
                if (el.hasAttribute('data-decimal-only')) normalizeDecimalOnly(el);
            });

            document.addEventListener('keydown', (e) => {
                const el = e.target;
                if (!(el instanceof HTMLInputElement)) return;
                if (!el.hasAttribute('data-decimal-only')) return;
                if (e.key === 'e' || e.key === 'E' || e.key === '+' || e.key === '-') e.preventDefault();
            });
        })();
    </script>

    <script>
        (() => {
            var popSnd = new Audio('{{ asset('sounds/pop-sound.mp3') }}');
            popSnd.preload = 'auto';
            var errSnd = new Audio('{{ asset('sounds/error-sound.mp3') }}');
            errSnd.preload = 'auto';

            window.sndPlay = function(){ try { popSnd.currentTime=0; popSnd.play(); } catch(e){} };
            window.errPlay = function(){ try { errSnd.currentTime=0; errSnd.play(); } catch(e){} };

            // Play pop sound whenever any modal becomes visible (hidden class removed)
            var popObs = new MutationObserver(function(muts){
                muts.forEach(function(m){
                    if (m.type === 'attributes' && m.attributeName === 'class') {
                        var el = m.target;
                        if (el.matches && el.matches('[id$="Modal"], .modal-overlay') && !el.classList.contains('hidden')) {
                            window.sndPlay();
                        }
                    }
                });
            });
            popObs.observe(document.body, { attributes: true, subtree: true, attributeFilter: ['class'] });

            const overlay = document.getElementById('sa-confirm-overlay');
            const modal = document.getElementById('sa-confirm-modal');
            const elIcon = document.getElementById('saConfirmIcon');
            const elTitle = document.getElementById('saConfirmTitle');
            const elMsg = document.getElementById('saConfirmMessage');
            const btnCancel = document.getElementById('saConfirmCancel');
            const btnOk = document.getElementById('saConfirmOk');

            if (!overlay || !modal || !btnCancel || !btnOk) return;

            var iconSvgs = {
                danger: '<svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01"/><path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86l-7.4 13.3A2 2 0 004.62 20h14.76a2 2 0 001.73-2.84l-7.4-13.3a2 2 0 00-3.42 0z"/></svg>',
                primary: '<svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                success: '<svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                warning: '<svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            };

            var iconStyles = {
                danger:  'background:#fff1f2;color:#c8292a;border-color:#fecaca',
                primary: 'background:#eef2ff;color:#4f46e5;border-color:#c7d2fe',
                success: 'background:#ecfdf5;color:#059669;border-color:#a7f3d0',
                warning: 'background:#fffbeb;color:#d97706;border-color:#fde68a',
            };

            const ensureStyles = () => {
                if (document.getElementById('sa-confirm-style')) return;
                var darkBg = getComputedStyle(document.documentElement).getPropertyValue('--sa-modal-dark-bg').trim() || '#0a0a14';
                var darkBorder = getComputedStyle(document.documentElement).getPropertyValue('--sa-modal-dark-border').trim() || '#1a1a30';
                var darkText = getComputedStyle(document.documentElement).getPropertyValue('--sa-modal-dark-text').trim() || '#e5e7eb';
                var darkMuted = getComputedStyle(document.documentElement).getPropertyValue('--sa-modal-dark-muted').trim() || '#9ca3af';
                const style = document.createElement('style');
                style.id = 'sa-confirm-style';
                style.textContent = `
                    #sa-confirm-overlay{position:fixed;inset:0;z-index:100000;display:flex;align-items:center;justify-content:center;background:rgba(17,24,39,.55);padding:24px;}
                    #sa-confirm-modal{width:min(520px, 100%);background:linear-gradient(180deg,#ffffff 0%,#fbfbfc 100%);border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 18px 60px rgba(0,0,0,.28);padding:18px 18px 16px;transform:translateY(6px) scale(.98);opacity:0;transition:opacity .16s ease, transform .16s ease;font-family:inherit;}
                    html.dark #sa-confirm-modal{background:linear-gradient(180deg,`+darkBg+` 0%,#0e0e1c 100%);border-color:`+darkBorder+`;}
                    #sa-confirm-overlay.show #sa-confirm-modal{transform:translateY(0) scale(1);opacity:1;}
                    .sa-confirm-head{display:flex;gap:12px;align-items:flex-start;margin-bottom:14px;}
                    .sa-confirm-icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;border:1px solid;flex-shrink:0;}
                    .sa-confirm-title{font-weight:900;color:#111827;font-size:0.95rem;letter-spacing:-0.01em;margin-top:2px;}
                    html.dark .sa-confirm-title{color:`+darkText+`;}
                    .sa-confirm-msg{color:#6b7280;font-size:0.84rem;line-height:1.35;margin-top:3px;}
                    html.dark .sa-confirm-msg{color:`+darkMuted+`;}
                    .sa-confirm-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:14px;}
                    .sa-confirm-btn{border-radius:12px;border:1px solid transparent;padding:10px 14px;font-weight:800;font-size:0.82rem;cursor:pointer;transition:transform .12s ease, box-shadow .12s ease, background .12s ease, border-color .12s ease;}
                    .sa-confirm-btn:active{transform:translateY(1px);}
                    .sa-confirm-btn.ghost{background:#fff;color:#374151;border-color:#e5e7eb;}
                    html.dark .sa-confirm-btn.ghost{background:transparent;color:`+darkText+`;border-color:`+darkBorder+`;}
                    html.dark .sa-confirm-btn.ghost:hover{background:`+darkBg+`;}
                    .sa-confirm-btn.danger{background:#c8292a;color:#fff;border-color:#c8292a;box-shadow:0 10px 26px rgba(200,41,42,.22);}
                    .sa-confirm-btn.danger:hover{background:#a81f20;border-color:#a81f20;box-shadow:0 14px 32px rgba(200,41,42,.26);}
                    .sa-confirm-btn.primary{background:#111827;color:#fff;border-color:#111827;box-shadow:0 10px 26px rgba(17,24,39,.18);}
                    .sa-confirm-btn.primary:hover{background:#000;border-color:#000;}
                    html.dark .sa-confirm-btn.primary{background:#4f46e5;border-color:#4f46e5;}
                    html.dark .sa-confirm-btn.primary:hover{background:#4338ca;border-color:#4338ca;}
                    .sa-confirm-btn.success{background:#059669;color:#fff;border-color:#059669;box-shadow:0 10px 26px rgba(5,150,105,.22);}
                    .sa-confirm-btn.success:hover{background:#047857;border-color:#047857;}
                    .sa-confirm-btn.warning{background:#d97706;color:#fff;border-color:#d97706;box-shadow:0 10px 26px rgba(217,119,6,.22);}
                    .sa-confirm-btn.warning:hover{background:#b45309;border-color:#b45309;}
                `;
                document.head.appendChild(style);
            };

            let resolver = null;
            var isAlert = false;
            const close = (val) => {
                overlay.classList.remove('show');
                overlay.style.display = 'none';
                btnCancel.style.display = '';
                const r = resolver;
                resolver = null;
                if (r) r(val);
            };

            var showModal = function(title, message, variant, okText, showCancel) {
                ensureStyles();
                sndPlay();
                if (variant === 'danger' || variant === 'warning') errPlay();
                elTitle.textContent = title;
                elMsg.textContent = message;
                btnOk.textContent = okText;
                btnOk.className = 'sa-confirm-btn ' + variant;
                if (elIcon) {
                    elIcon.innerHTML = iconSvgs[variant] || iconSvgs.danger;
                    elIcon.style.cssText = 'width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;border:1px solid;flex-shrink:0;' + (iconStyles[variant] || iconStyles.danger);
                }
                btnCancel.style.display = showCancel ? '' : 'none';
                overlay.style.display = 'flex';
                requestAnimationFrame(function(){ overlay.classList.add('show'); });
            };

            window.saConfirm = function(opts) {
                if (!opts) opts = {};
                var title = opts.title || 'Confirm action';
                var message = opts.message || 'Are you sure?';
                var confirmText = opts.confirmText || 'Confirm';
                var variant = opts.variant || 'danger';
                showModal(title, message, variant, confirmText, true);
                return new Promise(function(resolve) {
                    resolver = resolve;
                    btnCancel.onclick = function(){ close(false); };
                    btnOk.onclick = function(){ close(true); };
                    overlay.onclick = function(e) { if (e.target === overlay) close(false); };
                    var onKey = function(ev) {
                        if (!resolver) { document.removeEventListener('keydown', onKey); return; }
                        if (ev.key === 'Escape') close(false);
                        if (ev.key === 'Enter') close(true);
                    };
                    document.addEventListener('keydown', onKey);
                });
            };

            window.saAlert = function(opts) {
                if (!opts) opts = {};
                var title = opts.title || 'Notice';
                var message = opts.message || '';
                var variant = opts.variant || 'primary';
                var okText = opts.okText || 'OK';
                showModal(title, message, variant, okText, false);
                return new Promise(function(resolve) {
                    resolver = resolve;
                    btnOk.onclick = function(){ close(true); };
                    overlay.onclick = function(e) { if (e.target === overlay) close(true); };
                    var onKey = function(ev) {
                        if (!resolver) { document.removeEventListener('keydown', onKey); return; }
                        if (ev.key === 'Escape' || ev.key === 'Enter') close(true);
                    };
                    document.addEventListener('keydown', onKey);
                });
            };

            document.addEventListener('submit', async (e) => {
                const form = e.target;
                if (!(form instanceof HTMLFormElement)) return;
                const msg = form.getAttribute('data-sa-confirm');
                if (!msg) return;
                e.preventDefault();
                const ok = await window.saConfirm({ message: msg });
                if (ok) form.submit();
            }, true);

            document.addEventListener('click', async (e) => {
                const btn = e.target.closest('[data-sa-confirm]');
                if (!btn) return;
                if (btn instanceof HTMLFormElement) return;
                const msg = btn.getAttribute('data-sa-confirm');
                if (!msg) return;
                // For buttons not inside forms (rare), just confirm and let default proceed.
                if (btn.tagName === 'BUTTON' && btn.type === 'submit') return;
                e.preventDefault();
                const ok = await window.saConfirm({ message: msg });
                if (ok && btn.href) window.location.href = btn.href;
            }, true);
        })();
    </script>

    {{-- Error sound wiring — plays error-sound.mp3 for validation errors only --}}
    <script>
        (function(){
            var errSnd = new Audio('{{ asset('sounds/error-sound.mp3') }}');
            errSnd.preload = 'auto';
            var playErr = function(){ try { errSnd.currentTime=0; errSnd.play(); } catch(e){} };
            window._errSnd = errSnd;

            // HTML5 form validation (required, pattern, etc.)
            document.addEventListener('invalid', function(){ playErr(); }, true);

            // Server-side validation errors from last form submission only
            @if ($errors->any())
            (function(){
                playErr();

                // Shake inputs that have validation errors
                setTimeout(function(){
                    document.querySelectorAll('.text-red-500, .invalid-feedback, .prl-err, .prl-invalid-feedback').forEach(function(el) {
                        if (el.offsetParent === null) return;
                        var input = el.closest('.form-group, .mb-4, .mb-5, .mb-6, .flex-col, [class*="form-group"]')
                            ?.querySelector('input, select, textarea');
                        if (!input) {
                            input = el.previousElementSibling;
                            if (input && !input.matches('input, select, textarea')) {
                                input = el.closest('div')?.querySelector('input, select, textarea');
                            }
                        }
                        if (input && input.matches('input, select, textarea')) {
                            input.classList.add('input-shake');
                            setTimeout(function() { input.classList.remove('input-shake'); }, 600);
                        }
                    });
                }, 50);
            })();
            @endif
        })();
    </script>

    {{-- Server-side sound triggers — plays sounds based on flash message content --}}
    @php
        $playSuccess = session('_sound_success');
        $playError = session('_sound_error') || session('error');

        if (!$playSuccess) {
            $msg = session('success');
            if ($msg) {
                $actionWords = ['created', 'updated', 'deleted', 'added', 'removed', 'saved', 'submitted', 'cancelled', 'approved', 'rejected', 'released', 'restored', 'cleared', 'resolved', 'recomputed', 'changed'];
                foreach ($actionWords as $word) {
                    if (str_contains(strtolower($msg), $word)) { $playSuccess = true; break; }
                }
            }
        }
    @endphp
    @if($playSuccess || $playError)
    <script>
        (function(){
            @if($playSuccess)
            var okSnd = new Audio('{{ asset('sounds/success_created-sound.mp3') }}');
            okSnd.preload = 'auto';
            var playOk = function(){ try { okSnd.currentTime=0; okSnd.play(); } catch(e){} };
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', playOk);
            } else {
                playOk();
            }
            @endif
            @if($playError)
            var errSnd = new Audio('{{ asset('sounds/error-sound.mp3') }}');
            errSnd.preload = 'auto';
            var playErr = function(){ try { errSnd.currentTime=0; errSnd.play(); } catch(e){} };
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', playErr);
            } else {
                playErr();
            }
            @endif
        })();
    </script>
    @endif

    <script>
        // ── Dark Mode Toggle ──────────────────────────────────────────
        (function() {
            var html = document.documentElement;
            var btn = document.getElementById('dark-mode-toggle');
            var key = 'dark-mode';

            function setDark(isDark) {
                html.classList.add('dt-flash');
                requestAnimationFrame(function() {
                    html.classList.toggle('dark', isDark);
                    localStorage.setItem(key, isDark);
                    var sun = btn?.querySelector('.dark-mode-sun');
                    var moon = btn?.querySelector('.dark-mode-moon');
                    if (sun && moon) {
                        sun.classList.toggle('hidden', isDark);
                        moon.classList.toggle('hidden', !isDark);
                    }
                    html.offsetHeight;
                    html.classList.remove('dt-flash');
                });
            }

            // Restore saved preference — default to light mode
            var saved = localStorage.getItem(key);
            if (saved === 'true') {
                html.classList.add('dark');
            }

            // Sync icons on load
            (function syncIcons() {
                var isDark = html.classList.contains('dark');
                var sun = btn?.querySelector('.dark-mode-sun');
                var moon = btn?.querySelector('.dark-mode-moon');
                if (sun && moon) {
                    sun.classList.toggle('hidden', isDark);
                    moon.classList.toggle('hidden', !isDark);
                }
            })();

            if (btn) {
                btn.addEventListener('click', function() {
                    var isDark = !html.classList.contains('dark');
                    setDark(isDark);
                });
            }
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
                    style.textContent = [
                        '@@keyframes saRefreshPulse {',
                        '    0% { box-shadow: 0 0 0 0 rgba(200, 41, 42, 0.4); }',
                        '    70% { box-shadow: 0 0 0 8px rgba(200, 41, 42, 0); }',
                        '    100% { box-shadow: 0 0 0 0 rgba(200, 41, 42, 0); }',
                        '}',
                    ].join('\n');
                    document.head.appendChild(style);
                }
            };

            window.addEventListener('pageshow', function(event) {
                if (event.persisted) { window.location.reload(); }
            });
        })();
    </script>
<script>document.addEventListener('DOMContentLoaded',function(){document.querySelectorAll('.flash-bar,.bn-flash').forEach(function(el){if(el.offsetParent===null)return;setTimeout(function(){el.style.transition='opacity 0.5s ease,transform 0.5s ease';el.style.opacity='0';el.style.transform='translateY(-8px)';setTimeout(function(){el.remove()},500);},5000);});});</script>
</body>

</html>
