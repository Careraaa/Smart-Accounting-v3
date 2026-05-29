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

    <title>Knights TSC</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights-icon.png') }}">

    {{-- Feather icons only (vendors.min.css replaced — all other vendor CSS was unused) --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/feather.min.css') }}">
    @stack('head_scripts')

    {{-- Vite — Tailwind + custom overrides --}}
    @vite(['resources/css/tailwind.css', 'resources/css/overrides.css', 'resources/js/app.js'])

    <style>
        /* Bootstrap CSS no longer loaded — vendors.min.css replaced with feather.min.css alone */
    </style>

    <style>
        /* ── Dark Mode ──────────────────────────────────────────── */
        html.dark { color-scheme: dark; }
        html.dark body { background: #050508; }

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

        /* Text colors — all gray shades */
        html.dark .text-gray-900 { color: #e0e0f0 !important; }
        html.dark .text-gray-800 { color: #cccce0 !important; }
        html.dark .text-gray-700 { color: #b0b0cc !important; }
        html.dark .text-gray-600 { color: #78789a !important; }
        html.dark .text-gray-500 { color: #4e4e6a !important; }
        html.dark .text-gray-400 { color: #383850 !important; }
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
        html.dark input:not([type="checkbox"]):not([type="radio"]),
        html.dark select,
        html.dark textarea { background: #0a0a14; border-color: #18182a; color: #cccce0; }
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
        html.dark #notification-dropdown .bg-gray-50\/80 { background: rgba(16,16,28,.8); }

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

        /* Sidebar collapse — main content margin + width and toggle button position */
        main { width: calc(100% - 240px); margin-left: 240px; transition: width .5s cubic-bezier(.4,0,.2,1), margin-left .5s cubic-bezier(.4,0,.2,1); }
        body.sidebar-collapsed main { width: calc(100% - 64px); margin-left: 64px; }
        @media (max-width: 1023px) { main { width: 100% !important; margin-left: 0 !important; transition: none !important; } }

        /* Prevent content elements from animating layout properties during sidebar collapse —
           override any transition-all / transition on width, height, margin, padding, etc.
           so only visual/composited properties animate, staying in sync with main's width. */
        main .main-content * { transition-property: color, background-color, border-color, outline-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter !important; transition-duration: 0.15s !important; transition-timing-function: ease !important; }
        #sidebar-collapse-btn { left: 224px; transition: left .5s cubic-bezier(.4,0,.2,1), background-color .2s ease, border-color .2s ease, box-shadow .2s ease, color .2s ease; }
        body.sidebar-collapsed #sidebar-collapse-btn { left: 48px; }

        /* Collapse button dark */
        html.dark #sidebar-collapse-btn { background: #10101c; border-color: #18182a; color: #44445a; }
        html.dark #sidebar-collapse-btn:hover { background: #18182a; }

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
        html.dark #dark-mode-toggle { background: #18182a !important; }
        html.dark #dark-mode-toggle:has(:checked) { background: #080810 !important; }
        html.dark #dark-mode-toggle span { background: #30304a; }
        html.dark #dark-mode-toggle:has(:checked) span { background: #b8b8d0 !important; }

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

    {{-- Page loader --}}
    <script>document.write('<div id="page-loader" class="fixed inset-0 z-[9999] flex items-center justify-center" style="background:'+(localStorage.getItem('dark-mode')==='true'?'#050508':'#ffffff')+'">');</script>
        <div class="loader relative w-[200px] h-[200px]" style="perspective:800px">
            <div class="crystal"></div>
            <div class="crystal"></div>
            <div class="crystal"></div>
            <div class="crystal"></div>
            <div class="crystal"></div>
            <div class="crystal"></div>
        </div>
    </div>
    <style>
        .crystal {
            position:absolute; top:50%; left:50%;
            width:60px; height:60px; opacity:0;
            transform-origin:bottom center;
            transform:translate(-50%,-50%) rotateX(45deg) rotateZ(0deg);
            animation:spin 4s linear infinite, emerge 2s ease-in-out infinite alternate, fadeIn 0.3s ease-out forwards;
            border-radius:10px; visibility:hidden;
        }
        @keyframes spin {
            from { transform:translate(-50%,-50%) rotateX(45deg) rotateZ(0deg); }
            to   { transform:translate(-50%,-50%) rotateX(45deg) rotateZ(360deg); }
        }
        @keyframes emerge {
            0%,100% { transform:translate(-50%,-50%) scale(0.5); opacity:0; }
            50%     { transform:translate(-50%,-50%) scale(1);   opacity:1; }
        }
        @keyframes fadeIn { to { visibility:visible; opacity:0.8; } }
        .crystal:nth-child(1) { background:linear-gradient(45deg,#7f1d1d,#dc2626); animation-delay:0s; }
        .crystal:nth-child(2) { background:linear-gradient(45deg,#991b1b,#ef4444); animation-delay:0.3s; }
        .crystal:nth-child(3) { background:linear-gradient(45deg,#b91c1c,#f87171); animation-delay:0.6s; }
        .crystal:nth-child(4) { background:linear-gradient(45deg,#dc2626,#fca5a5); animation-delay:0.9s; }
        .crystal:nth-child(5) { background:linear-gradient(45deg,#ef4444,#fecaca); animation-delay:1.2s; }
        .crystal:nth-child(6) { background:linear-gradient(45deg,#f87171,#fee2e2); animation-delay:1.5s; }
    </style>
    <script>
        window.addEventListener('load', function(){ document.getElementById('page-loader').style.display = 'none'; });
    </script>

    @include('partials.sidebar')
    @include('partials.header')

    <main class="bg-gray-100 pt-16 min-h-screen" data-global-datepicker="off">
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
                });
            });

            initializeMenus();
            requestAnimationFrame(() => nav.classList.remove('sidebar-initializing'));
            window.addEventListener('resize', initializeMenus);
        });
    </script>

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
                <div class="sa-confirm-icon" aria-hidden="true">
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
            const overlay = document.getElementById('sa-confirm-overlay');
            const modal = document.getElementById('sa-confirm-modal');
            const elTitle = document.getElementById('saConfirmTitle');
            const elMsg = document.getElementById('saConfirmMessage');
            const btnCancel = document.getElementById('saConfirmCancel');
            const btnOk = document.getElementById('saConfirmOk');

            if (!overlay || !modal || !btnCancel || !btnOk) return;

            const ensureStyles = () => {
                if (document.getElementById('sa-confirm-style')) return;
                const style = document.createElement('style');
                style.id = 'sa-confirm-style';
                style.textContent = `
                    #sa-confirm-overlay{position:fixed;inset:0;z-index:100000;display:flex;align-items:center;justify-content:center;background:rgba(17,24,39,.55);padding:24px;}
                    #sa-confirm-modal{width:min(520px, 100%);background:linear-gradient(180deg,#ffffff 0%,#fbfbfc 100%);border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 18px 60px rgba(0,0,0,.28);padding:18px 18px 16px;transform:translateY(6px) scale(.98);opacity:0;transition:opacity .16s ease, transform .16s ease;font-family:'Sora',system-ui,-apple-system,Segoe UI,Roboto,Arial;}
                    #sa-confirm-overlay.show #sa-confirm-modal{transform:translateY(0) scale(1);opacity:1;}
                    .sa-confirm-head{display:flex;gap:12px;align-items:flex-start;margin-bottom:14px;}
                    .sa-confirm-icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:#fff1f2;color:#c8292a;border:1px solid #fecaca;flex-shrink:0;}
                    .sa-confirm-title{font-weight:900;color:#111827;font-size:0.95rem;letter-spacing:-0.01em;margin-top:2px;}
                    .sa-confirm-msg{color:#6b7280;font-size:0.84rem;line-height:1.35;margin-top:3px;}
                    .sa-confirm-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:14px;}
                    .sa-confirm-btn{border-radius:12px;border:1px solid transparent;padding:10px 14px;font-weight:800;font-size:0.82rem;cursor:pointer;transition:transform .12s ease, box-shadow .12s ease, background .12s ease, border-color .12s ease;}
                    .sa-confirm-btn:active{transform:translateY(1px);}
                    .sa-confirm-btn.ghost{background:#fff;color:#374151;border-color:#e5e7eb;}
                    .sa-confirm-btn.ghost:hover{background:#f9fafb;border-color:#d1d5db;}
                    .sa-confirm-btn.danger{background:#c8292a;color:#fff;border-color:#c8292a;box-shadow:0 10px 26px rgba(200,41,42,.22);}
                    .sa-confirm-btn.danger:hover{background:#a81f20;border-color:#a81f20;box-shadow:0 14px 32px rgba(200,41,42,.26);}
                    .sa-confirm-btn.primary{background:#111827;color:#fff;border-color:#111827;box-shadow:0 10px 26px rgba(17,24,39,.18);}
                    .sa-confirm-btn.primary:hover{background:#000;border-color:#000;}
                `;
                document.head.appendChild(style);
            };

            let resolver = null;
            const close = (val) => {
                overlay.classList.remove('show');
                overlay.style.display = 'none';
                const r = resolver;
                resolver = null;
                if (r) r(val);
            };

            window.saConfirm = ({ title = 'Confirm action', message = 'Are you sure?', confirmText = 'Confirm', variant = 'danger' } = {}) => {
                ensureStyles();
                elTitle.textContent = title;
                elMsg.textContent = message;
                btnOk.textContent = confirmText;
                btnOk.classList.remove('danger', 'primary');
                btnOk.classList.add(variant === 'primary' ? 'primary' : 'danger');

                overlay.style.display = 'flex';
                requestAnimationFrame(() => overlay.classList.add('show'));

                return new Promise((resolve) => {
                    resolver = resolve;
                    btnCancel.onclick = () => close(false);
                    btnOk.onclick = () => close(true);
                    overlay.onclick = (e) => { if (e.target === overlay) close(false); };
                    document.addEventListener('keydown', function onKey(ev) {
                        if (!resolver) return document.removeEventListener('keydown', onKey);
                        if (ev.key === 'Escape') close(false);
                        if (ev.key === 'Enter') close(true);
                    });
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

    <script>
        // ── Dark Mode Toggle ──────────────────────────────────────────
        (function() {
            var html = document.documentElement;
            var toggle = document.getElementById('darkModeCheckbox');
            var key = 'dark-mode';

            // Restore saved preference
            if (localStorage.getItem(key) === 'true') {
                html.classList.add('dark');
                if (toggle) toggle.checked = true;
            }

            if (toggle) {
                toggle.addEventListener('change', function() {
                    var isDark = this.checked;
                    html.classList.add('dt-flash');
                    requestAnimationFrame(function() {
                        html.classList.toggle('dark', isDark);
                        localStorage.setItem(key, isDark);
                        html.offsetHeight;
                        html.classList.remove('dt-flash');
                    });
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

            window.addEventListener('pageshow', function(event) {
                if (event.persisted) { window.location.reload(); }
            });
        })();
    </script>
<script>document.addEventListener('DOMContentLoaded',function(){document.querySelectorAll('.flash-bar,.bn-flash,[class*="bg-green-50"][class*="rounded-xl"],[class*="bg-green-50"][class*="rounded-lg"],[class*="bg-red-50"][class*="rounded-xl"],[class*="bg-blue-50"][class*="rounded-xl"]').forEach(function(el){if(el.offsetParent===null)return;var c=el.className||'';var hasExact=function(n){return c.match(new RegExp('(^|\\s)'+n.replace(/[.*+?^${}()|[\]\\]/g,'\\$&')+'(\\s|$)'))};if(!hasExact('bg-green-50')&&!hasExact('bg-red-50')&&!hasExact('bg-blue-50'))return;setTimeout(function(){el.style.transition='opacity 0.5s ease,transform 0.5s ease';el.style.opacity='0';el.style.transform='translateY(-8px)';setTimeout(function(){el.remove()},500);},5000);});});</script>
</body>

</html>
