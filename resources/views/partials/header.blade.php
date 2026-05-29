<header class="fixed top-0 lg:left-[var(--sidebar-w)] left-0 right-0 z-30 h-16 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center transition-all duration-300">
    <div class="flex items-center w-full h-full px-4 sm:px-6">

        {{-- Mobile menu toggle (hidden on desktop) --}}
        <button class="lg:hidden flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors" id="mobile-menu-btn" type="button" aria-label="Toggle menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Typewriter greeting --}}
        @php
            $user = auth()->user();
            $name = $user->first_name ?? $user->name ?? 'User';
            $greetings = [
                "Welcome, $name",
                "Hello, $name",
                "Good to see you, $name!",
            ];
            $greeting = $greetings[array_rand($greetings)];
        @endphp
        <div class="flex-1 min-w-0 flex items-center h-full ml-2 sm:ml-4">
            <span id="typewriter" class="text-sm sm:text-base font-semibold text-gray-700 truncate"></span>
            <span class="inline-block w-[2px] h-4 bg-gray-700 ml-0.5 animate-pulse" id="typewriter-cursor"></span>
        </div>

        {{-- Right side icons --}}
        <div class="flex items-center gap-0.5 ml-auto">

            {{-- Dark mode toggle --}}
            <label class="dark-tip relative inline-block h-5 w-9 cursor-pointer rounded-full bg-gray-300 transition [-webkit-tap-highlight-color:_transparent] has-[:checked]:bg-gray-900" id="dark-mode-toggle" data-tip="Toggle dark mode">
                <input class="peer sr-only" id="darkModeCheckbox" type="checkbox" />
                <span class="absolute inset-y-0 start-0 m-[3px] size-3.5 rounded-full bg-gray-300 ring-[3px] ring-inset ring-white transition-all peer-checked:start-[18px] peer-checked:w-1 peer-checked:bg-white peer-checked:ring-transparent"></span>
            </label>

            {{-- Fullscreen --}}
            <a href="javascript:void(0);" class="hidden sm:flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors" id="kt-fullscreen-btn">
                <i class="feather-maximize" style="font-size:16px"></i>
            </a>

            {{-- Notifications --}}
            @php
                $notifData = cache()->remember('notif.'.auth()->id(), 30, function() {
                    $u = auth()->user();
                    return [
                        'count' => $u->notifications()->unread()->count(),
                        'recent' => $u->notifications()->unread()->recent()->limit(3)->get(),
                    ];
                });
                $unread_count = $notifData['count'];
            @endphp
            <div class="relative" data-dropdown>
                <button class="relative flex items-center justify-center w-9 h-9 rounded-lg transition-colors hover:bg-gray-100" id="notification-btn" type="button">
                    <svg viewBox="0 0 24 24" fill="none" height="20" width="20" xmlns="http://www.w3.org/2000/svg" class="text-gray-500">
                        <path d="M12 5.365V3m0 2.365a5.338 5.338 0 0 1 5.133 5.368v1.8c0 2.386 1.867 2.982 1.867 4.175 0 .593 0 1.292-.538 1.292H5.538C5 18 5 17.301 5 16.708c0-1.193 1.867-1.789 1.867-4.175v-1.8A5.338 5.338 0 0 1 12 5.365ZM8.733 18c.094.852.306 1.54.944 2.112a3.48 3.48 0 0 0 4.646 0c.638-.572 1.236-1.26 1.33-2.112h-6.92Z" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" stroke="currentColor"></path>
                    </svg>
                    @if($unread_count > 0)
                        <span class="notif-blip"></span>
                    @endif
                </button>
                <div id="notification-dropdown" class="dropdown-closed sm:absolute sm:top-full sm:right-[-8px] sm:left-auto sm:mt-3 sm:w-[780px] sm:max-w-[96vw] fixed top-16 right-4 left-4 w-auto bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden z-50" data-dropdown-menu>
                    <div class="flex items-center justify-between px-4 py-3.5 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center"><i class="feather-bell" style="font-size:15px"></i></span>
                            <div>
                                <div class="text-sm font-bold text-gray-900">Notifications</div>
                                <div class="text-xs text-gray-500">Latest updates</div>
                            </div>
                        </div>
                        @if($unread_count > 0)
                            <span class="inline-block text-[11px] font-bold text-white uppercase tracking-wide px-2.5 py-1 rounded-full bg-red-500" id="notif-badge">{{ $unread_count }} New</span>
                        @endif
                    </div>
                    <div class="max-h-[420px] overflow-y-auto p-1.5">
                        <div id="notification-list">
                            @forelse($notifData['recent'] as $notification)
                                <div class="flex items-start gap-3 px-4 py-3.5 rounded-lg cursor-pointer transition-colors duration-100 hover:bg-red-50 {{ $notification->isUnread() ? 'bg-gray-50/80 font-medium' : '' }}" data-notif-id="{{ $notification->id }}" data-action-url="{{ $notification->getActionUrl() }}">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-bold text-gray-900 mb-1">{{ $notification->title }}</div>
                                        <p class="text-sm text-gray-600 mb-1.5 leading-relaxed">{{ $notification->message }}</p>
                                        <small class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @empty
                                <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                                    <i class="feather-bell-off" style="font-size:40px;opacity:0.4"></i>
                                    <p class="text-sm mt-2">No new notifications</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50/80 border-t border-gray-100">
                        <a class="btn-uv-pill text-sm" href="{{ route('notifications.index') }}">View all</a>
                        <button class="text-sm font-bold text-red-500 no-underline hover:underline bg-transparent border-none cursor-pointer {{ $unread_count > 0 ? '' : 'hidden' }}" id="mark-all-read">Mark all as read</button>
                    </div>
                </div>
            </div>

            {{-- User Profile Dropdown --}}
            <div class="relative" data-dropdown>
                <button class="flex items-center gap-2.5 no-underline rounded-lg py-1.5 pl-2 pr-1.5 transition-colors hover:bg-rose-50" id="user-dropdown-btn" type="button">
                    <div class="w-8 h-8 min-w-[32px] rounded-full bg-gradient-to-br from-rose-800 to-rose-900 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                        {{ auth()->user()->getFirstLetter() }}
                    </div>
                    <svg class="hidden md:block text-gray-400" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>
                <div id="user-dropdown" class="dropdown-closed sm:absolute sm:top-full sm:right-0 sm:mt-2 sm:w-56 sm:left-auto fixed top-16 right-4 left-4 w-auto bg-white border border-gray-200 rounded-xl shadow-xl overflow-hidden z-50" data-dropdown-menu>
                    <div class="py-1.5">
                        <a href="{{ route('profile.details') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 no-underline transition-colors duration-100 hover:bg-gray-50 hover:text-gray-900 group">
                            <i class="feather-user w-4 text-center text-gray-400 group-hover:text-gray-600 transition-colors"></i> My Profile
                        </a>
                        <a href="{{ route('employee.attachments.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-600 no-underline transition-colors duration-100 hover:bg-gray-50 hover:text-gray-900 group">
                            <i class="feather-folder w-4 text-center text-gray-400 group-hover:text-gray-600 transition-colors"></i> Documents
                        </a>
                        <div class="border-t border-gray-100 my-1 mx-4"></div>
                        <a href="javascript:void(0);" class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 no-underline transition-colors duration-100 hover:bg-red-50 group"
                            onclick="document.getElementById('logout-form').submit();">
                            <i class="feather-log-out w-4 text-center text-red-400 group-hover:text-red-500 transition-colors"></i> Logout
                        </a>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>

<style>
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

    /* ── Notification blinking dot ── */
    .notif-blip {
        position: absolute;
        bottom: 6px;
        left: 6px;
        width: 8px;
        height: 8px;
        background: #22c55e;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 4px rgba(34,197,94,.4);
    }
    .notif-blip::before {
        content: '';
        position: absolute;
        inset: 50%;
        translate: -50% -50%;
        width: 2px;
        height: 2px;
        background: #22c55e;
        border-radius: 50%;
        animation: notif-pulse 1.2s ease-in-out infinite;
    }
    @keyframes notif-pulse {
        0%   { width: 2px; height: 2px; opacity: 1; }
        100% { width: 28px; height: 28px; opacity: 0; }
    }

    /* ── Search loading spinner ── */
    .three-body {
        --uib-size: 35px;
        --uib-speed: 0.8s;
        --uib-color: #f43f5e;
        position: relative;
        display: inline-block;
        height: var(--uib-size);
        width: var(--uib-size);
        animation: spin78236 calc(var(--uib-speed) * 2.5) infinite linear;
    }
    .three-body__dot {
        position: absolute;
        height: 100%;
        width: 30%;
    }
    .three-body__dot::after {
        content: '';
        position: absolute;
        height: 0%;
        width: 100%;
        padding-bottom: 100%;
        background-color: var(--uib-color);
        border-radius: 50%;
    }
    .three-body__dot:nth-child(1) {
        bottom: 5%;
        left: 0;
        transform: rotate(60deg);
        transform-origin: 50% 85%;
    }
    .three-body__dot:nth-child(1)::after {
        bottom: 0;
        left: 0;
        animation: wobble1 var(--uib-speed) infinite ease-in-out;
        animation-delay: calc(var(--uib-speed) * -0.3);
    }
    .three-body__dot:nth-child(2) {
        bottom: 5%;
        right: 0;
        transform: rotate(-60deg);
        transform-origin: 50% 85%;
    }
    .three-body__dot:nth-child(2)::after {
        bottom: 0;
        left: 0;
        animation: wobble1 var(--uib-speed) infinite calc(var(--uib-speed) * -0.15) ease-in-out;
    }
    .three-body__dot:nth-child(3) {
        bottom: -5%;
        left: 0;
        transform: translateX(116.666%);
    }
    .three-body__dot:nth-child(3)::after {
        top: 0;
        left: 0;
        animation: wobble2 var(--uib-speed) infinite ease-in-out;
    }
    @keyframes spin78236 {
        0%   { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    @keyframes wobble1 {
        0%, 100% { transform: translateY(0%) scale(1); opacity: 1; }
        50%      { transform: translateY(-66%) scale(0.65); opacity: 0.8; }
    }
    @keyframes wobble2 {
        0%, 100% { transform: translateY(0%) scale(1); opacity: 1; }
        50%      { transform: translateY(66%) scale(0.65); opacity: 0.8; }
    }

</style>

@push('scripts')
    <script>
        // ── Global Search ──────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function () {

            var SEARCH_URL = @json(route('search'));
            var input = document.getElementById('kt-search-input');
            var popup = document.getElementById('kt-search-popup');
            if (!input || !popup) { console.warn('[Search] input/popup not found'); return; }

            var spState = document.getElementById('kt-sp-state');
            var spList  = document.getElementById('kt-sp-list');

            var timer     = null;
            var lastQ     = '';
            var activeIdx = -1;
            var isOpen    = false;

            var ANIM_DURATION = 200;

            function reposition() {
                var pill = document.getElementById('kt-nav-search');
                if (!pill) return;
                var r = pill.getBoundingClientRect();
                var gap = 8;
                var maxW = window.innerWidth - gap * 2;
                var w = Math.min(300, maxW);
                var l = Math.min(r.left, window.innerWidth - w - gap);
                l = Math.max(gap, l);
                popup.style.top   = (r.bottom + 4) + 'px';
                popup.style.left  = l + 'px';
                popup.style.width = w + 'px';
            }

            function openPopup() {
                if (isOpen) return;
                reposition();
                popup.style.visibility = 'visible';
                popup.style.pointerEvents = 'auto';
                void popup.offsetWidth;
                popup.style.opacity = '1';
                popup.style.transform = 'translateY(0) scale(1)';
                isOpen = true;
            }

            function closePopup() {
                if (!isOpen) return;
                popup.style.opacity = '0';
                popup.style.transform = 'translateY(-8px) scale(0.97)';
                popup.style.pointerEvents = 'none';
                popup.style.visibility = 'hidden';
                isOpen = false;
                activeIdx = -1;
            }

            function resetState() {
                spList.innerHTML = '';
                spState.style.display = '';
                spState.innerHTML =
                    '<svg class="w-6 h-6 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>' +
                    '</svg>' +
                    '<span class="text-sm text-gray-400">Type to search</span>';
            }

            function escHtml(s) {
                return String(s)
                    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            }
            function markText(text, q) {
                if (!q) return escHtml(text);
                return escHtml(text).replace(
                    new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')', 'gi'),
                    '<strong class="text-gray-900 font-extrabold">$1</strong>'
                );
            }

            var iconColorMap = {
                'feather-airplay': 'text-blue-500 bg-blue-50',
                'feather-camera': 'text-cyan-500 bg-cyan-50',
                'feather-user': 'text-indigo-500 bg-indigo-50',
                'feather-folder': 'text-sky-500 bg-sky-50',
                'feather-bell': 'text-amber-500 bg-amber-50',
                'feather-user-plus': 'text-emerald-500 bg-emerald-50',
                'feather-tag': 'text-violet-500 bg-violet-50',
                'feather-calendar': 'text-violet-500 bg-violet-50',
                'feather-clock': 'text-cyan-500 bg-cyan-50',
                'feather-dollar-sign': 'text-green-600 bg-green-50',
                'feather-inbox': 'text-emerald-500 bg-emerald-50',
                'feather-file-text': 'text-sky-500 bg-sky-50',
                'feather-bar-chart-2': 'text-sky-500 bg-sky-50',
                'feather-users': 'text-orange-500 bg-orange-50',
                'feather-map': 'text-orange-500 bg-orange-50',
                'feather-truck': 'text-orange-500 bg-orange-50',
                'feather-activity': 'text-teal-500 bg-teal-50',
                'feather-check-circle': 'text-indigo-500 bg-indigo-50',
                'feather-credit-card': 'text-emerald-500 bg-emerald-50',
                'feather-briefcase': 'text-amber-500 bg-amber-50',
                'feather-settings': 'text-slate-500 bg-slate-50',
                'feather-shield': 'text-pink-500 bg-pink-50',
                'feather-monitor': 'text-rose-500 bg-rose-50',
                'feather-gift': 'text-rose-500 bg-rose-50',
                'feather-award': 'text-yellow-500 bg-yellow-50',
            };

            function getIconColors(icon) {
                return iconColorMap[icon] || 'text-gray-500 bg-gray-100';
            }

            function setActive(idx) {
                var links = spList.querySelectorAll('li a');
                links.forEach(function(a, i) {
                    a.classList.toggle('bg-rose-50', i === idx);
                    a.classList.toggle('bg-transparent', i !== idx);
                });
                activeIdx = idx;
                if (links[idx]) links[idx].scrollIntoView({ block: 'nearest' });
            }

            function render(results, q) {
                spList.innerHTML = '';
                activeIdx = -1;

                if (!results.length) {
                    spState.style.display = '';
                    spState.innerHTML =
                        '<svg class="w-8 h-8 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>' +
                        '</svg>' +
                        '<span class="text-sm text-gray-400">No results for <span class="font-semibold text-gray-600">"' + escHtml(q) + '"</span></span>';
                    return;
                }

                spState.style.display = 'none';

                function makeRow(href, left, nameHtml, subHtml) {
                    var li = document.createElement('li');
                    var a  = document.createElement('a');
                    a.href = href;
                    a.className = 'flex items-center gap-3 px-3 py-2.5 no-underline transition-all duration-150 hover:bg-rose-50 group';
                    a.innerHTML =
                        left +
                        '<span class="min-w-0 flex-1">' +
                            '<div class="text-sm font-semibold text-gray-800 leading-tight truncate group-hover:text-rose-700">' + nameHtml + '</div>' +
                            (subHtml ? '<div class="text-xs text-gray-400 mt-0.5 leading-tight truncate">' + subHtml + '</div>' : '') +
                        '</span>';
                    li.appendChild(a);
                    spList.appendChild(li);
                }

                results.forEach(function(item) {
                    if (item.type === 'page') {
                        var colors = getIconColors(item.icon).split(' ');
                        var iconBox = '<span style="width:32px;height:32px;min-width:32px" class="rounded-lg flex items-center justify-center shrink-0 ' + colors.join(' ') + '"><i class="' + escHtml(item.icon) + '" style="font-size:14px"></i></span>';
                        makeRow(escHtml(item.url), iconBox, markText(item.label, q), '');
                    } else {
                        var avatar = '<span style="width:32px;height:32px;min-width:32px" class="rounded-full bg-gradient-to-br from-gray-700 to-gray-600 text-white text-[11px] font-bold flex items-center justify-center shrink-0 tracking-tight shadow-sm">' + escHtml(item.initials) + '</span>';
                        makeRow(escHtml(item.url), avatar, markText(item.label, q), item.subtitle ? escHtml(item.subtitle) : '');
                    }
                });
            }

            function doSearch(q) {
                if (q === lastQ) return;
                lastQ = q;
                spState.style.display = '';
                spState.innerHTML = '<div class="three-body"><div class="three-body__dot"></div><div class="three-body__dot"></div><div class="three-body__dot"></div></div>';
                spList.innerHTML = '';
                openPopup();

                fetch(SEARCH_URL + '?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (r) { return r.json(); })
                .then(function (d) { render(d.results || [], q); })
                .catch(function (err) {
                    console.error('[Search] fetch error', err);
                    spState.style.display = '';
                    spState.innerHTML = 'Search unavailable';
                });
            }

            /* ── Events ── */
            input.addEventListener('focus', function () { resetState(); openPopup(); });

            input.addEventListener('input', function () {
                var q = this.value.trim();
                clearTimeout(timer);
                if (!q) { lastQ = ''; resetState(); openPopup(); return; }
                timer = setTimeout(function () { doSearch(q); }, 220);
            });

            input.addEventListener('keydown', function (e) {
                var links = spList.querySelectorAll('li a');
                if (e.key === 'Escape') { closePopup(); input.blur(); return; }
                if (!links.length) return;
                if (e.key === 'ArrowDown') { e.preventDefault(); setActive(Math.min(activeIdx + 1, links.length - 1)); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); setActive(Math.max(activeIdx - 1, 0)); }
                else if (e.key === 'Enter' && activeIdx >= 0) { e.preventDefault(); links[activeIdx].click(); }
            });

            document.addEventListener('mousedown', function (e) {
                if (!popup.contains(e.target) && !document.getElementById('kt-nav-search').contains(e.target)) {
                    closePopup();
                }
            });

            window.addEventListener('resize', function () { if (isOpen) reposition(); });

        }); // end DOMContentLoaded

        // Global polling interval ID
        let notificationPollingInterval = null;
        
        const notificationReadUrlTemplate = @json(route('notifications.read', ['notification' => '__ID__']));

        // ── Notification functionality ────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function() {
            const isNotificationsPage = window.location.pathname.includes('/notifications');

            attachMarkAllAsReadListener();
            attachNotificationClickListeners();

            // Auto-refresh every 30 seconds (not on the notifications page itself)
            if (!isNotificationsPage) {
                startNotificationPolling();
            }

            // Refresh list when dropdown opens
            const notifBtn = document.getElementById('notification-btn');
            if (notifBtn) {
                notifBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleDropdown('notification-dropdown', notifBtn);
                    refreshNotificationList();
                });
            }
        });

        // ── Dropdown helpers ───────────────────────────────────────────────
        function toggleDropdown(id, btn) {
            var menu = document.getElementById(id);
            if (!menu) return;
            var isOpen = !menu.classList.contains('dropdown-closed');
            document.querySelectorAll('[data-dropdown-menu]').forEach(function(el) {
                el.classList.add('dropdown-closed');
            });
            if (!isOpen) {
                menu.classList.remove('dropdown-closed');
            }
        }

        // Close on outside click
        document.addEventListener('click', function(e) {
            document.querySelectorAll('[data-dropdown-menu]:not(.dropdown-closed)').forEach(function(menu) {
                var parent = menu.closest('[data-dropdown]');
                if (parent && !parent.contains(e.target)) {
                    menu.classList.add('dropdown-closed');
                }
            });
        });

        // User dropdown toggle
        var userBtn = document.getElementById('user-dropdown-btn');
        if (userBtn) {
            userBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleDropdown('user-dropdown', userBtn);
            });
        }

        // Mobile menu toggle
        var mobBtn = document.getElementById('mobile-menu-btn');
        if (mobBtn) {
            mobBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                document.body.classList.toggle('sidebar-open');
            });
        }

        // Close mobile sidebar on backdrop click / Escape key
        document.addEventListener('click', function(e) {
            if (document.body.classList.contains('sidebar-open')) {
                var sidebar = document.querySelector('.sidebar');
                if (sidebar && !sidebar.contains(e.target) && e.target !== mobBtn && !mobBtn?.contains(e.target)) {
                    document.body.classList.remove('sidebar-open');
                }
            }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.body.classList.contains('sidebar-open')) {
                document.body.classList.remove('sidebar-open');
            }
        });

        // ── Typewriter effect (single line, no loop) ──────────────────────
        (function() {
            var el = document.getElementById('typewriter');
            if (!el) return;
            var text = @json($greeting);
            var j = 0;
            (function type() {
                el.textContent = text.substring(0, j + 1);
                j++;
                if (j < text.length) { setTimeout(type, 100); }
            })();
        })();

        function attachNotificationClickListeners() {
            document.querySelectorAll('#notification-list > div').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const notifId  = this.dataset.notifId;
                    const actionUrl = this.dataset.actionUrl;

                    fetch(notificationReadUrlTemplate.replace('__ID__', notifId), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(() => {
                        var menu = document.getElementById('notification-dropdown');
                        if (menu) { menu.classList.add('dropdown-closed'); }
                        if (actionUrl && actionUrl !== 'null') {
                            window.location.href = actionUrl;
                        } else {
                            updateNotificationCount();
                            refreshNotificationList();
                        }
                    })
                    .catch(err => console.error('Error marking notification as read:', err));
                });
            });
        }

        // Mark all as read
        function attachMarkAllAsReadListener() {
            const btn = document.getElementById('mark-all-read');
            if (!btn) return;
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fetch(@json(route('notifications.mark-all-as-read')), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(() => refreshNotificationList())
                .catch(err => console.error('Error marking all as read:', err));
            });
        }

        // Update the bell badge count
        function updateNotificationCount() {
            fetch(@json(route('notifications.count')), { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                const count = data.unread_count || 0;
                const dot   = document.getElementById('notif-count');
                const badge = document.getElementById('notif-badge');
                let   blip  = document.querySelector('#notification-btn .notif-blip');
                if (count > 0) {
                    if (dot) {
                        dot.textContent = count > 99 ? '99+' : count;
                        dot.classList.remove('hidden');
                    } else {
                        const span = document.createElement('span');
                        span.id = 'notif-count';
                        span.className = 'absolute top-1 right-1 bg-red-500 text-white text-[9px] font-bold min-w-[15px] h-[15px] rounded-full flex items-center justify-center border-2 border-white leading-none';
                        span.textContent = count > 99 ? '99+' : count;
                        document.getElementById('notification-btn').appendChild(span);
                    }
                    if (badge) badge.textContent = count + ' New';
                    if (blip) { blip.classList.remove('hidden'); }
                } else {
                    if (dot) dot.classList.add('hidden');
                    if (badge) badge.textContent = '0 New';
                    if (blip) blip.classList.add('hidden');
                }
                const markAllBtn = document.getElementById('mark-all-read');
                if (markAllBtn) markAllBtn.classList.toggle('hidden', count === 0);
            })
            .catch(err => console.error('Error updating count:', err));
        }

        // Refresh the notification list in the dropdown (no delete button, max 3)
        function refreshNotificationList() {
            fetch(@json(route('notifications.index')) + '?view=unread&limit=3', {
                headers: { 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                const payload       = data && data.status === 'success' ? data.data : null;
                const notifications = payload && Array.isArray(payload.data) ? payload.data : [];
                const list          = document.getElementById('notification-list');
                if (!list) return;

                if (notifications.length > 0) {
                    list.innerHTML = notifications.slice(0, 3).map(n => {
                        const created = new Date(n.created_at);
                        const mins    = Math.ceil(Math.abs(new Date() - created) / 60000);
                        const timeText = mins >= 60
                            ? Math.ceil(mins / 60) + ' hr' + (Math.ceil(mins / 60) > 1 ? 's' : '') + ' ago'
                            : mins + ' min ago';
                        const actionUrl = n.action_url || '';
                        return `
                            <div class="flex items-start gap-2.5 px-3.5 py-2.5 rounded-lg cursor-pointer transition-colors duration-100 hover:bg-red-50 ${n.read_at ? '' : 'bg-gray-50/80 font-medium'}" data-notif-id="${n.id}" data-action-url="${actionUrl}">
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-bold text-gray-900 mb-1">${n.title}</div>
                                    <p class="text-sm text-gray-600 mb-1 leading-snug">${n.message}</p>
                                    <small class="text-xs text-gray-400">${timeText}</small>
                                </div>
                            </div>`;
                    }).join('');
                } else {
                    list.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                            <i class="feather-bell-off" style="font-size:40px;opacity:0.4"></i>
                            <p class="text-sm mt-2">No new notifications</p>
                        </div>`;
                }

                // Re-attach click listeners to freshly rendered items
                attachNotificationClickListeners();
                updateNotificationCount();
            })
            .catch(err => console.error('Error refreshing notifications:', err));
        }

        // Start polling
        function startNotificationPolling() {
            if (notificationPollingInterval) clearInterval(notificationPollingInterval);
            notificationPollingInterval = setInterval(refreshNotificationList, 30000);
        }

        // Stop polling when page unloads
        window.addEventListener('beforeunload', function() {
            if (notificationPollingInterval) {
                clearInterval(notificationPollingInterval);
            }
        });

        // Fullscreen — swap icon only, no show/hide logic
        const ktFsBtn = document.getElementById('kt-fullscreen-btn');
        if (ktFsBtn) {
            ktFsBtn.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(() => {});
                } else {
                    document.exitFullscreen().catch(() => {});
                }
            });
            document.addEventListener('fullscreenchange', function() {
                const icon = ktFsBtn.querySelector('i');
                if (!icon) return;
                icon.className = document.fullscreenElement ?
                    'feather-minimize fs-17' :
                    'feather-maximize fs-17';
            });
        }
    </script>

@endpush
