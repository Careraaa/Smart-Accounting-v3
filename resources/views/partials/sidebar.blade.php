<nav class="sidebar fixed top-0 left-0 z-40 h-dvh bg-white border-r border-gray-200 flex flex-col">
    @php $activeRole = session('view_as_role') ?? auth()->user()->role; @endphp
    <style>
        /* ── Submenu slide animation ── */
        .sidebar-sub {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height .55s ease, opacity .4s ease;
        }
        .has-sub.open > .sidebar-sub {
            max-height: 300px;
            opacity: 1;
        }

        .has-sub.open > .sidebar-link .sidebar-arrow {
            transform: rotate(90deg);
        }
        .sidebar-arrow {
            transition: transform .35s ease;
        }

        /* ── Submenu dot indicators ── */
        .sidebar-sub .sidebar-link {
            position: relative;
            transition: color .2s ease;
        }
        .sidebar-sub .sidebar-link::before {
            content: '';
            position: absolute;
            left: 1.1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: transparent;
            transition: background .2s ease, transform .2s ease;
        }
        .sidebar-sub .sidebar-link:hover { color: #374151; }
        .sidebar-sub .sidebar-link:hover::before { background: #9ca3af; }
        .sidebar-sub .sidebar-link.active { color: #111827; font-weight: 500; }
        .sidebar-sub .sidebar-link.active::before { background: #c8292a; transform: translateY(-50%) scale(1.35); }

        /* ── Link transitions ── */
        .sidebar-link { transition: background .18s ease, color .18s ease; }

        /* ── Icon invert on hover ── */
        .sidebar-icon {
            padding: 6px; border-radius: 8px;
            transition: background .25s ease;
        }
        .sidebar-link:hover .sidebar-icon { background: currentColor; }
        .sidebar-link:hover .sidebar-icon i { color: #fff !important; }
        .sidebar-icon i { transition: color .25s ease; }

        /* ── Submenu icons ── */
        .sidebar-sub .sidebar-link .sidebar-icon i { transition: color .2s ease; }

        /* ── Hide scrollbar while keeping scrollable ── */
        .sidebar .overflow-y-auto { scrollbar-width: none; -ms-overflow-style: none; }
        .sidebar .overflow-y-auto::-webkit-scrollbar { display: none; }

        /* ── Logo icon gentle pulse ── */
        .sidebar-logo img {
            animation: logoPulse 3s ease-in-out infinite;
        }
        @keyframes logoPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.04); opacity: 0.85; }
        }

        /* ── Brand title typewriter reveal ── */
        .brand-title-typing {
            display: inline-block;
            overflow: hidden;
            white-space: nowrap;
            animation: brandReveal 0.8s steps(17) forwards;
            width: 0;
        }
        @keyframes brandReveal {
            from { width: 0; }
            to { width: 100%; }
        }

        /* ── Staggered item entrance ── */
        .sidebar-item {
            opacity: 0;
            transform: translateX(-10px);
            animation: slideInItem 0.4s cubic-bezier(0.16,1,0.3,1) forwards;
        }
        .sidebar-item:nth-child(1) { animation-delay: 0.05s; }
        .sidebar-item:nth-child(2) { animation-delay: 0.10s; }
        .sidebar-item:nth-child(3) { animation-delay: 0.15s; }
        .sidebar-item:nth-child(4) { animation-delay: 0.20s; }
        .sidebar-item:nth-child(5) { animation-delay: 0.25s; }
        .sidebar-item:nth-child(6) { animation-delay: 0.30s; }
        .sidebar-item:nth-child(7) { animation-delay: 0.35s; }
        .sidebar-item:nth-child(8) { animation-delay: 0.40s; }
        .sidebar-item:nth-child(9) { animation-delay: 0.45s; }
        .sidebar-item:nth-child(10) { animation-delay: 0.50s; }
        .sidebar-item:nth-child(11) { animation-delay: 0.55s; }
        .sidebar-item:nth-child(12) { animation-delay: 0.60s; }
        .sidebar-item:nth-child(13) { animation-delay: 0.65s; }
        .sidebar-item:nth-child(14) { animation-delay: 0.70s; }
        .sidebar-item:nth-child(15) { animation-delay: 0.75s; }
        .sidebar-item:nth-child(16) { animation-delay: 0.80s; }
        .sidebar-item:nth-child(17) { animation-delay: 0.85s; }
        .sidebar-item:nth-child(18) { animation-delay: 0.90s; }
        .sidebar-item:nth-child(19) { animation-delay: 0.95s; }
        .sidebar-item:nth-child(20) { animation-delay: 1.00s; }
        @keyframes slideInItem {
            to { opacity: 1; transform: translateX(0); }
        }

        /* ── White theme link overrides ── */
        .sidebar .sidebar-link { color: #6b7280; }
        .sidebar .sidebar-link:hover { color: #111827; background: rgba(0,0,0,.04); }
        .sidebar .sidebar-link.active { color: #111827; font-weight: 600; }
        .sidebar .sidebar-caption label { color: #9ca3af; }

        /* ── Sidebar collapse ── */
        :root { --sidebar-w: 240px; }
        body.sidebar-collapsed { --sidebar-w: 64px; }
        .sidebar { width: 240px; transition: width .5s cubic-bezier(.4,0,.2,1), transform .5s cubic-bezier(.4,0,.2,1); }
        body.sidebar-collapsed .sidebar { width: 64px; }
        body.sidebar-collapsed .sidebar-text,
        body.sidebar-collapsed .sidebar-caption,
        body.sidebar-collapsed .sidebar-arrow,
        body.sidebar-collapsed .b-brand span:not(.flex-shrink-0),
        body.sidebar-collapsed #kt-nav-search { display: none; }
        body.sidebar-collapsed .brand-icon { display: block; }
        body.sidebar-collapsed #kt-search-popup,
        body.sidebar-collapsed .sidebar-sub { display: none; }
        body.sidebar-collapsed .sidebar .sidebar-link { justify-content: center; padding: 10px 0; gap: 0; }
        body.sidebar-collapsed .sidebar .sidebar-icon { margin: 0; }
        body.sidebar-collapsed .sidebar .sidebar-logo { justify-content: center; padding-left: 0; padding-right: 0; }
        @media (max-width: 1023px) {
            .sidebar { transform: translateX(-100%); }
            body.sidebar-open .sidebar { transform: translateX(0); }
            body.sidebar-collapsed .sidebar { width: 240px; transform: translateX(-100%); }
            body.sidebar-collapsed.sidebar-open .sidebar { transform: translateX(0); }
        }

        /* ── Mobile sidebar backdrop ── */
        body.sidebar-open::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 35;
            background: rgba(0,0,0,.5);
            opacity: 0;
            transition: opacity .3s ease;
            pointer-events: none;
        }
        body.sidebar-open::before {
            opacity: 1;
            pointer-events: auto;
        }
        @media (min-width: 1024px) {
            body:not(.sidebar-collapsed) .brand-icon { display: none; }
            body.sidebar-open::before { display: none; }
        }
    </style>
    <button id="sidebar-collapse-btn" class="fixed top-[12px] z-50 w-8 h-8 rounded-full bg-white border-2 border-gray-200 shadow-sm text-gray-400 hover:text-gray-700 hover:border-gray-300 hover:shadow-md hidden lg:flex lg:items-center lg:justify-center" type="button">
        <svg class="w-3.5 h-3.5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.5 19l-7-7 7-7"/>
        </svg>
    </button>
    <div class="flex flex-col h-full">

        {{-- ── Logo / Brand ── --}}
        <div class="sidebar-logo shrink-0 flex items-center justify-center px-3 h-16 border-b border-gray-100">
            <a href="{{ route('dashboard') }}" class="b-brand flex items-center gap-2.5 no-underline min-w-0">
                <span class="brand-icon flex-shrink-0 hidden">
                    <img src="{{ asset('images/knights-icon.png') }}" alt="Knights Transport" width="30" height="30" class="si-logo-light block rounded-lg">
                    <img src="{{ asset('images/darkmode-icon.png') }}" alt="Knights Transport" width="30" height="30" class="si-logo-dark hidden rounded-lg">
                </span>
                <style>
                    html.dark .si-logo-light { display: none !important; }
                    html.dark .si-logo-dark { display: block !important; }
                </style>
                <span class="brand-title brand-title-typing text-sm font-bold text-gray-900">Knights Transport</span>
            </a>
        </div>

        {{-- ── Search ── --}}
        <div id="kt-nav-search" class="relative mx-3 mt-3 mb-1 cursor-text">
            <div class="flex items-center gap-2.5 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 transition-all duration-200 ring-2 ring-transparent focus-within:ring-rose-500/20 focus-within:border-rose-300 focus-within:bg-white focus-within:shadow-sm">
                <svg class="w-4 h-4 shrink-0 text-gray-400 transition-colors duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input id="kt-search-input" type="text" placeholder="Search pages, employees…" autocomplete="off" spellcheck="false" class="flex-1 border-none bg-transparent outline-none text-sm text-gray-700 min-w-0 p-0 leading-tight placeholder:text-gray-400">
            </div>
        </div>

        {{-- ── Search results popup ── --}}
        <div id="kt-search-popup" class="fixed z-[9999] bg-white rounded-xl border border-gray-200 shadow-xl shadow-gray-200/50 overflow-hidden" style="transition:all 0.2s cubic-bezier(0.16, 1, 0.3, 1);opacity:0;transform:translateY(-8px) scale(0.97);pointer-events:none;visibility:hidden;width:300px">
            <div class="max-h-[400px] overflow-y-auto">
                <div id="kt-sp-state" class="px-4 py-5 text-center text-sm text-gray-400">Type to search</div>
                <ul id="kt-sp-list" class="list-none m-0 p-0"></ul>
            </div>
        </div>

        {{-- ── Scrollable Nav ── --}}
        <div class="flex-1 overflow-y-auto px-3 py-2">
            <ul class="sidebar-list space-y-0.5 p-0 list-none">

                {{-- ── SUPERADMIN ── --}}
@if ($activeRole === 'superadmin')
                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-2 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">System Administration</label></li>

                    <li class="sidebar-item">
                        <a href="{{ route('superadmin.dashboard') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('superadmin.dashboard') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-indigo-500"><i class="feather-home" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Dashboard</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('superadmin.accounts.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('superadmin.accounts.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-pink-500"><i class="feather-shield" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Manage Accounts</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('configuration.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('configuration.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-slate-500"><i class="feather-settings" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">System Configuration</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('admin.dashboard') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('admin.dashboard') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-rose-500"><i class="feather-monitor" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">QR Monitor</span>
                        </a>
                    </li>

                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-3 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">HR Management</label></li>

                    <li class="sidebar-item">
                        <a href="{{ route('employees.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employees.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-emerald-500"><i class="feather-user-plus" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Employee Management</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('leave.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('leave.*', 'leave-type.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-violet-500"><i class="feather-calendar" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Leave Management</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('holiday.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('holiday.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-rose-500"><i class="feather-gift" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Holiday Management</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('attendance.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('attendance.*', 'overtime.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-cyan-500"><i class="feather-clock" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Attendance</span>
                        </a>
                    </li>

                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-3 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Operations</label></li>

                    <li class="sidebar-item has-sub">
                        <a href="javascript:void(0);" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('drivers.*', 'paos.*', 'routes.*', 'vehicles.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-orange-500"><i class="feather-truck" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Operational Records</span>
                            <span class="sidebar-arrow ml-auto"><i class="feather-chevron-right" style="font-size:12px"></i></span>
                        </a>
                        <ul class="sidebar-sub">
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('drivers.*') ? 'active' : 'text-gray-500' }}" href="{{ route('drivers.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-orange-500"><i class="feather-user" style="font-size:13px"></i></span><span>List of Drivers</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('paos.*') ? 'active' : 'text-gray-500' }}" href="{{ route('paos.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-orange-500"><i class="feather-users" style="font-size:13px"></i></span><span>List of PAO / Conductors</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('routes.*') ? 'active' : 'text-gray-500' }}" href="{{ route('routes.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-orange-500"><i class="feather-map-pin" style="font-size:13px"></i></span><span>Manage Routes</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('vehicles.*') ? 'active' : 'text-gray-500' }}" href="{{ route('vehicles.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-orange-500"><i class="feather-truck" style="font-size:13px"></i></span><span>Manage Vehicles</span></a></li>
                        </ul>
                    </li>

                    <li class="sidebar-item has-sub">
                        <a href="javascript:void(0);" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('remittances.*', 'short-remittances.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-teal-500"><i class="feather-activity" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Daily Remittance</span>
                            <span class="sidebar-arrow ml-auto"><i class="feather-chevron-right" style="font-size:12px"></i></span>
                        </a>
                        <ul class="sidebar-sub">
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('remittances.index') ? 'active' : 'text-gray-500' }}" href="{{ route('remittances.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-teal-500"><i class="feather-edit" style="font-size:13px"></i></span><span>Record Remittance</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('short-remittances.index') ? 'active' : 'text-gray-500' }}" href="{{ route('short-remittances.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-teal-500"><i class="feather-alert-triangle" style="font-size:13px"></i></span><span>Short Remittance</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('reports.index') ? 'active' : 'text-gray-500' }}" href="{{ route('reports.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-teal-500"><i class="feather-file-text" style="font-size:13px"></i></span><span>Remittance Report</span></a></li>
                        </ul>
                    </li>

                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-3 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Financials &amp; Controls</label></li>

                    <li class="sidebar-item has-sub">
                        <a href="javascript:void(0);" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('payroll.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-green-600"><i class="feather-dollar-sign" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Payroll Processing</span>
                            <span class="sidebar-arrow ml-auto"><i class="feather-chevron-right" style="font-size:12px"></i></span>
                        </a>
                        <ul class="sidebar-sub">
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('payroll.salary-computation.*') ? 'active' : 'text-gray-500' }}" href="{{ route('payroll.salary-computation.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-green-600"><i class="feather-settings" style="font-size:13px"></i></span><span>Payroll Management</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('payroll.statutory-deductions.*') ? 'active' : 'text-gray-500' }}" href="{{ route('payroll.statutory-deductions.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-green-600"><i class="feather-shield" style="font-size:13px"></i></span><span>Statutory Deductions</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('payroll.receivables.*') ? 'active' : 'text-gray-500' }}" href="{{ route('payroll.receivables.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-green-600"><i class="feather-inbox" style="font-size:13px"></i></span><span>Payroll Receivables</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('payroll.generate-payslip.*') ? 'active' : 'text-gray-500' }}" href="{{ route('payroll.generate-payslip.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-green-600"><i class="feather-file" style="font-size:13px"></i></span><span>Pay Slips</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('payroll.history.*') ? 'active' : 'text-gray-500' }}" href="{{ route('payroll.history.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-green-600"><i class="feather-bar-chart-2" style="font-size:13px"></i></span><span>Payroll Summary</span></a></li>
                        </ul>
                    </li>

                    <li class="sidebar-item has-sub">
                        <a href="javascript:void(0);" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('payroll-approval.*', 'remittance-approval.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-indigo-500"><i class="feather-check-circle" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Approvals</span>
                            <span class="sidebar-arrow ml-auto"><i class="feather-chevron-right" style="font-size:12px"></i></span>
                        </a>
                        <ul class="sidebar-sub">
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('payroll-approval.index') ? 'active' : 'text-gray-500' }}" href="{{ route('payroll-approval.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-indigo-500"><i class="feather-check-circle" style="font-size:13px"></i></span><span>Payroll Release Approval</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('remittance-approval.index') ? 'active' : 'text-gray-500' }}" href="{{ route('remittance-approval.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-indigo-500"><i class="feather-check-circle" style="font-size:13px"></i></span><span>Remittance Approval</span></a></li>
                        </ul>
                    </li>

                    <li class="sidebar-item has-sub">
                        <a href="javascript:void(0);" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('reports.remittance', 'reports.payroll') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-sky-500"><i class="feather-file-text" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Accountant Reports</span>
                            <span class="sidebar-arrow ml-auto"><i class="feather-chevron-right" style="font-size:12px"></i></span>
                        </a>
                        <ul class="sidebar-sub">
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('reports.remittance') ? 'active' : 'text-gray-500' }}" href="{{ route('reports.remittance') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-sky-500"><i class="feather-file-text" style="font-size:13px"></i></span><span>Remittance Reports</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('reports.payroll') ? 'active' : 'text-gray-500' }}" href="{{ route('reports.payroll') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-sky-500"><i class="feather-file-text" style="font-size:13px"></i></span><span>Payroll Reports</span></a></li>
                        </ul>
                    </li>
                @endif

                {{-- non-superadmin base items --}}
                @if ($activeRole !== 'superadmin')
                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-2 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Menu</label></li>

                    <li class="sidebar-item">
                        <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('dashboard') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-indigo-500"><i class="feather-home" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Dashboard</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('attendance.scan') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('attendance.scan') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center {{ request()->routeIs('attendance.scan') ? 'text-cyan-500' : 'text-cyan-500' }}"><i class="feather-camera" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Scan QR Attendance</span>
                        </a>
                    </li>
                @endif

                {{-- ── Remittance Clerk ── --}}
                @if ($activeRole === 'remittance_clerk')
                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-2 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Operations</label></li>

                    <li class="sidebar-item has-sub">
                        <a href="javascript:void(0);" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('drivers.*', 'paos.*', 'routes.*', 'vehicles.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-orange-500"><i class="feather-truck" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Operational Records</span>
                            <span class="sidebar-arrow ml-auto"><i class="feather-chevron-right" style="font-size:12px"></i></span>
                        </a>
                        <ul class="sidebar-sub">
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('drivers.*') ? 'active' : 'text-gray-500' }}" href="{{ route('drivers.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-orange-500"><i class="feather-user" style="font-size:13px"></i></span><span>List of Drivers</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('paos.*') ? 'active' : 'text-gray-500' }}" href="{{ route('paos.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-orange-500"><i class="feather-users" style="font-size:13px"></i></span><span>List of PAO / Conductors</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('routes.*') ? 'active' : 'text-gray-500' }}" href="{{ route('routes.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-orange-500"><i class="feather-map-pin" style="font-size:13px"></i></span><span>Manage Routes</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('vehicles.*') ? 'active' : 'text-gray-500' }}" href="{{ route('vehicles.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-orange-500"><i class="feather-truck" style="font-size:13px"></i></span><span>Manage Vehicles</span></a></li>
                        </ul>
                    </li>

                    <li class="sidebar-item has-sub">
                        <a href="javascript:void(0);" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('remittances.*', 'short-remittances.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-teal-500"><i class="feather-activity" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Daily Remittance</span>
                            <span class="sidebar-arrow ml-auto"><i class="feather-chevron-right" style="font-size:12px"></i></span>
                        </a>
                        <ul class="sidebar-sub">
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('remittances.index') ? 'active' : 'text-gray-500' }}" href="{{ route('remittances.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-teal-500"><i class="feather-edit" style="font-size:13px"></i></span><span>Record Remittance</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('short-remittances.index') ? 'active' : 'text-gray-500' }}" href="{{ route('short-remittances.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-teal-500"><i class="feather-alert-triangle" style="font-size:13px"></i></span><span>Short Remittance</span></a></li>
                        </ul>
                    </li>

                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-3 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Reports</label></li>

                    <li class="sidebar-item">
                        <a href="{{ route('reports.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('reports.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-sky-500"><i class="feather-file-text" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Remittance Report</span>
                        </a>
                    </li>

                    {{-- Employee Self-Service --}}
                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-3 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">My Finances</label></li>

                    <li class="sidebar-item">
                        <a href="{{ route('employee.cash-advances.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employee.cash-advances.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-emerald-500"><i class="feather-credit-card" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Cash Advances</span>
                        </a>
                    </li>

                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-3 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Time Off</label></li>

                    <li class="sidebar-item">
                        <a href="{{ route('employee.leaves.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employee.leaves.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-violet-500"><i class="feather-calendar" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">My Leaves</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('employee.attendance.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employee.attendance.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-blue-500"><i class="feather-check-square" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Attendance</span>
                        </a>
                    </li>

                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-3 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">My Account</label></li>

                    <li class="sidebar-item">
                        <a href="{{ route('profile.details') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('profile.details') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-indigo-500"><i class="feather-user" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">My Profile</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('employee.attachments.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employee.attachments.index') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-sky-500"><i class="feather-folder" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">My Documents</span>
                        </a>
                    </li>
                @endif

                {{-- ── HR ── --}}
                @if ($activeRole === 'hr')
                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-2 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">HR Management</label></li>

                    <li class="sidebar-item">
                        <a href="{{ route('employees.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employees.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center {{ request()->routeIs('employees.*') ? 'text-emerald-500' : 'text-emerald-500' }}"><i class="feather-user-plus" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Employee Management</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('leave.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('leave.*', 'leave-type.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center {{ request()->routeIs('leave.*', 'leave-type.*') ? 'text-violet-500' : 'text-violet-500' }}"><i class="feather-calendar" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Leave Management</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('holiday.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('holiday.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center {{ request()->routeIs('holiday.*') ? 'text-rose-500' : 'text-rose-500' }}"><i class="feather-gift" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Holiday Management</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('attendance.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('attendance.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center {{ request()->routeIs('attendance.*') ? 'text-cyan-500' : 'text-cyan-500' }}"><i class="feather-clock" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Attendance</span>
                        </a>
                    </li>

                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-3 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Payroll</label></li>

                    <li class="sidebar-item has-sub">
                        <a href="javascript:void(0);" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('payroll.salary-computation.*', 'payroll.statutory-deductions.*', 'payroll.receivables.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center {{ request()->routeIs('payroll.salary-computation.*', 'payroll.statutory-deductions.*', 'payroll.receivables.*') ? 'text-green-600' : 'text-green-600' }}"><i class="feather-dollar-sign" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Payroll Processing</span>
                            <span class="sidebar-arrow ml-auto"><i class="feather-chevron-right" style="font-size:12px"></i></span>
                        </a>
                        <ul class="sidebar-sub">
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('payroll.salary-computation.*') ? 'active' : 'text-gray-500' }}" href="{{ route('payroll.salary-computation.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-green-600"><i class="feather-settings" style="font-size:13px"></i></span><span>Payroll Management</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('payroll.statutory-deductions.*') ? 'active' : 'text-gray-500' }}" href="{{ route('payroll.statutory-deductions.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-green-600"><i class="feather-shield" style="font-size:13px"></i></span><span>Statutory Deductions</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('payroll.receivables.*') ? 'active' : 'text-gray-500' }}" href="{{ route('payroll.receivables.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-green-600"><i class="feather-inbox" style="font-size:13px"></i></span><span>Employee Receivables</span></a></li>
                        </ul>
                    </li>

                    <li class="sidebar-item has-sub">
                        <a href="javascript:void(0);" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('reports.payslips', 'reports.payroll*', 'reports.deduction*', 'reports.government*', 'payroll.generate-payslip.*', 'payroll.history.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center {{ request()->routeIs('reports.payslips', 'reports.payroll*', 'reports.deduction*', 'reports.government*', 'payroll.generate-payslip.*', 'payroll.history.*') ? 'text-sky-500' : 'text-sky-500' }}"><i class="feather-bar-chart-2" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Reports</span>
                            <span class="sidebar-arrow ml-auto"><i class="feather-chevron-right" style="font-size:12px"></i></span>
                        </a>
                        <ul class="sidebar-sub">
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('payroll.generate-payslip.*') ? 'active' : 'text-gray-500' }}" href="{{ route('payroll.generate-payslip.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-sky-500"><i class="feather-file" style="font-size:13px"></i></span><span>Payslips</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('payroll.history.*') ? 'active' : 'text-gray-500' }}" href="{{ route('payroll.history.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-green-600"><i class="feather-bar-chart-2" style="font-size:13px"></i></span><span>Payroll Summary</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('reports.government-contribution') ? 'active' : 'text-gray-500' }}" href="{{ route('reports.government-contribution') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-sky-500"><i class="feather-file-text" style="font-size:13px"></i></span><span>Government Contribution Summary</span></a></li>
                        </ul>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('settings.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('settings.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center {{ request()->routeIs('settings.*') ? 'text-slate-500' : 'text-slate-500' }}"><i class="feather-settings" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Settings</span>
                        </a>
                    </li>

                    {{-- Employee Self-Service --}}
                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-3 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">My Finances</label></li>

                    <li class="sidebar-item">
                        <a href="{{ route('employee.cash-advances.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employee.cash-advances.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-emerald-500"><i class="feather-credit-card" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Cash Advances</span>
                        </a>
                    </li>

                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-3 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Time Off</label></li>

                    <li class="sidebar-item">
                        <a href="{{ route('employee.leaves.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employee.leaves.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-violet-500"><i class="feather-calendar" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">My Leaves</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('employee.attendance.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employee.attendance.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-blue-500"><i class="feather-check-square" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Attendance</span>
                        </a>
                    </li>

                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-3 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">My Account</label></li>

                    <li class="sidebar-item">
                        <a href="{{ route('profile.details') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('profile.details') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-indigo-500"><i class="feather-user" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">My Profile</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('employee.attachments.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employee.attachments.index') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-sky-500"><i class="feather-folder" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">My Documents</span>
                        </a>
                    </li>
                @endif

                {{-- ── Employee ── --}}
                @if ($activeRole === 'employee')
                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-2 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">My Finances</label></li>

                    <li class="sidebar-item">
                        <a href="{{ route('employee.cash-advances.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employee.cash-advances.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-emerald-500"><i class="feather-credit-card" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Cash Advances</span>
                        </a>
                    </li>

                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-3 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Time Off</label></li>

                    <li class="sidebar-item">
                        <a href="{{ route('employee.leaves.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employee.leaves.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-violet-500"><i class="feather-calendar" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">My Leaves</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('employee.attendance.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employee.attendance.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-blue-500"><i class="feather-check-square" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Attendance</span>
                        </a>
                    </li>

                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-3 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">My Account</label></li>

                    <li class="sidebar-item">
                        <a href="{{ route('profile.details') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('profile.details') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-indigo-500"><i class="feather-user" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">My Profile</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('employee.attachments.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employee.attachments.index') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-sky-500"><i class="feather-folder" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">My Documents</span>
                        </a>
                    </li>
                @endif

                {{-- ── Accountant ── --}}
                @if ($activeRole === 'accountant')
                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-2 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Approvals &amp; Receivables</label></li>

                    <li class="sidebar-item has-sub">
                        <a href="javascript:void(0);" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('payroll-approval.*', 'remittance-approval.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-indigo-500"><i class="feather-check-circle" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Approvals</span>
                            <span class="sidebar-arrow ml-auto"><i class="feather-chevron-right" style="font-size:12px"></i></span>
                        </a>
                        <ul class="sidebar-sub">
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('payroll-approval.index') ? 'active' : 'text-gray-500' }}" href="{{ route('payroll-approval.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-indigo-500"><i class="feather-check-circle" style="font-size:13px"></i></span><span>Payroll Release Approval</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('remittance-approval.index') ? 'active' : 'text-gray-500' }}" href="{{ route('remittance-approval.index') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-indigo-500"><i class="feather-check-circle" style="font-size:13px"></i></span><span>Remittance Approval</span></a></li>
                        </ul>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('payroll.receivables.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('payroll.receivables.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-emerald-500"><i class="feather-inbox" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Receivables &amp; Loans</span>
                        </a>
                    </li>

                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-3 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Reports</label></li>

                    <li class="sidebar-item has-sub">
                        <a href="javascript:void(0);" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('reports.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-sky-500"><i class="feather-file-text" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Reports</span>
                            <span class="sidebar-arrow ml-auto"><i class="feather-chevron-right" style="font-size:12px"></i></span>
                        </a>
                        <ul class="sidebar-sub">
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('reports.remittance') ? 'active' : 'text-gray-500' }}" href="{{ route('reports.remittance') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-sky-500"><i class="feather-file-text" style="font-size:13px"></i></span><span>Remittance Reports</span></a></li>
                            <li class="sidebar-item"><a class="sidebar-link flex items-center gap-2 px-7 py-1.5 text-sm no-underline {{ request()->routeIs('reports.payroll') ? 'active' : 'text-gray-500' }}" href="{{ route('reports.payroll') }}"><span class="sidebar-icon submenu-icon flex items-center justify-center text-sky-500"><i class="feather-file-text" style="font-size:13px"></i></span><span>Payroll Reports</span></a></li>
                        </ul>
                    </li>

                    {{-- Employee Self-Service --}}
                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-3 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">My Finances</label></li>

                    <li class="sidebar-item">
                        <a href="{{ route('employee.cash-advances.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employee.cash-advances.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-emerald-500"><i class="feather-credit-card" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Cash Advances</span>
                        </a>
                    </li>

                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-3 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">Time Off</label></li>

                    <li class="sidebar-item">
                        <a href="{{ route('employee.leaves.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employee.leaves.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-violet-500"><i class="feather-calendar" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">My Leaves</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('employee.attendance.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employee.attendance.*') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-blue-500"><i class="feather-check-square" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">Attendance</span>
                        </a>
                    </li>

                    <li class="sidebar-item sidebar-caption pl-2 pr-2 pt-3 pb-1"><label class="text-[10px] font-semibold uppercase tracking-wider text-gray-500">My Account</label></li>

                    <li class="sidebar-item">
                        <a href="{{ route('profile.details') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('profile.details') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-indigo-500"><i class="feather-user" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">My Profile</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="{{ route('employee.attachments.index') }}" class="sidebar-link flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm no-underline {{ request()->routeIs('employee.attachments.index') ? 'active text-gray-900 font-semibold' : 'text-gray-500 hover:text-gray-600 hover:bg-gray-50' }}">
                            <span class="sidebar-icon flex items-center justify-center text-sky-500"><i class="feather-folder" style="font-size:18px"></i></span>
                            <span class="sidebar-text truncate min-w-0">My Documents</span>
                        </a>
                    </li>
                @endif

            </ul>
        </div>

    </div>
</nav>
<form method="POST" action="{{ route('logout') }}" id="logout-form" style="display:none;">@csrf</form>
