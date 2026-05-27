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

            {{-- Fullscreen --}}
            <a href="javascript:void(0);" class="hidden sm:flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors" id="kt-fullscreen-btn">
                <i class="feather-maximize" style="font-size:16px"></i>
            </a>

            {{-- Notifications --}}
            @php
                $unread_count = auth()->user()->notifications()->unread()->count();
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
                            @forelse(auth()->user()->notifications()->unread()->recent()->limit(3)->get() as $notification)
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
                        <a class="text-sm font-bold text-red-500 no-underline hover:underline" href="{{ route('notifications.index') }}">View all</a>
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

            function reposition() {
                var pill = document.getElementById('kt-nav-search');
                if (!pill) return;
                var r = pill.getBoundingClientRect();
                popup.style.top  = (r.bottom + 4) + 'px';
                popup.style.left = r.left + 'px';
                popup.style.width = r.width + 'px';
            }

            function openPopup() {
                reposition();
                popup.classList.remove('hidden');
                isOpen = true;
            }

            function closePopup() {
                popup.classList.add('hidden');
                isOpen    = false;
                activeIdx = -1;
            }

            function resetState() {
                spList.innerHTML = '';
                spState.classList.remove('hidden');
                spState.textContent = 'Type to search';
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
            function setActive(idx) {
                var links = spList.querySelectorAll('li a');
                links.forEach(function(a, i) {
                    a.classList.toggle('bg-gray-100', i === idx);
                    a.classList.toggle('bg-transparent', i !== idx);
                });
                activeIdx = idx;
                if (links[idx]) links[idx].scrollIntoView({ block: 'nearest' });
            }

            function render(results, q) {
                spList.innerHTML = '';
                activeIdx = -1;

                if (!results.length) {
                    spState.classList.remove('hidden');
                    spState.innerHTML = 'No results for <span class="font-semibold text-gray-700">"' + escHtml(q) + '"</span>';
                    return;
                }

                spState.classList.add('hidden');

                function makeRow(href, left, nameHtml, subHtml) {
                    var li = document.createElement('li');
                    var a  = document.createElement('a');
                    a.href = href;
                    a.className = 'flex items-center gap-2 px-2.5 py-1.5 rounded-lg no-underline text-gray-900 transition-colors duration-100 hover:bg-gray-100';
                    a.innerHTML =
                        left +
                        '<span class="min-w-0">' +
                            '<div class="text-xs font-semibold text-gray-900 leading-tight truncate">' + nameHtml + '</div>' +
                            (subHtml ? '<div class="text-[11px] text-gray-500 mt-0.5">' + subHtml + '</div>' : '') +
                        '</span>';
                    li.appendChild(a);
                    spList.appendChild(li);
                }

                results.forEach(function(item) {
                    if (item.type === 'page') {
                        var iconBox = '<span class="w-7 h-7 min-w-[28px] rounded-lg bg-gray-100 text-gray-500 text-xs flex items-center justify-center shrink-0"><i class="' + escHtml(item.icon) + '"></i></span>';
                        makeRow(escHtml(item.url), iconBox, markText(item.label, q), '');
                    } else {
                        var avatar = '<span class="w-7 h-7 min-w-[28px] rounded-full bg-gray-700 text-white text-[10px] font-bold flex items-center justify-center shrink-0">' + escHtml(item.initials) + '</span>';
                        makeRow(escHtml(item.url), avatar, markText(item.label, q), item.subtitle ? escHtml(item.subtitle) : '');
                    }
                });
            }

            function doSearch(q) {
                if (q === lastQ) return;
                lastQ = q;
                spState.classList.remove('hidden');
                spState.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-gray-200 border-t-gray-500 rounded-full animate-spin"></span>';
                spList.innerHTML = '';
                openPopup();

                fetch(SEARCH_URL + '?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (r) { return r.json(); })
                .then(function (d) { render(d.results || [], q); })
                .catch(function (err) {
                    console.error('[Search] fetch error', err);
                    spState.classList.remove('hidden');
                    spState.innerHTML = 'Search unavailable';
                });
            }

            /* ── Events ── */
            input.addEventListener('focus', function () { openPopup(); });

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
