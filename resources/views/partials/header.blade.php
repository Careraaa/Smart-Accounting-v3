<header class="nxl-header">
    <div class="header-wrapper">

        {{-- ── Left ── --}}
        <div class="header-left d-flex align-items-center gap-3">

            {{-- Mobile hamburger (shown only on mobile via d-xl-none) --}}
            <a href="javascript:void(0);" class="kt-header-btn nxl-head-mobile-toggler d-xl-none" id="mobile-collapse">
                <i class="feather-menu fs-18"></i>
            </a>

            {{-- Desktop toggle buttons: JS controls which one is visible --}}
            {{-- Both use the SAME hamburger icon so it always looks the same --}}
            <a href="javascript:void(0);" class="kt-header-btn d-none d-xl-flex" id="menu-mini-button">
                <i class="feather-menu fs-18"></i>
            </a>
            <a href="javascript:void(0);" class="kt-header-btn d-none d-xl-flex" id="menu-expend-button"
                style="display:none !important;">
                <i class="feather-menu fs-18"></i>
            </a>

            {{-- Brand --}}
            <div class="d-none d-md-flex align-items-center gap-2">
                <span class="kt-brand-name">Knights Transport</span>
                <span class="kt-role-pill">
                    {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                </span>
            </div>
        </div>

        {{-- ── Centre: Global Search ── --}}
        <div id="kt-nav-search" style="
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 420px;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f4f6f8;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
            padding: 0 16px;
            height: 40px;
            transition: border-color .15s, box-shadow .15s, background .15s;
            cursor: text;
            z-index: 10;
        ">
            <i class="feather-search" style="color:#b0b7c3;font-size:14px;flex-shrink:0;pointer-events:none;"></i>
            <input
                id="kt-search-input"
                type="text"
                placeholder="Search employees, pages…"
                autocomplete="off"
                spellcheck="false"
                style="
                    flex:1;border:none;background:transparent;outline:none;
                    font-size:0.85rem;color:#111827;min-width:0;padding:0;line-height:1;
                "
            >
        </div>

        {{-- ── Right ── --}}
        <div class="header-right ms-auto d-flex align-items-center gap-1">

            {{-- Fullscreen: single button, icon swapped by JS --}}
            <a href="javascript:void(0);" class="kt-header-btn d-none d-sm-flex" id="kt-fullscreen-btn">
                <i class="feather-maximize fs-17"></i>
            </a>

            {{-- Notifications --}}
            <div class="dropdown">
                <a class="kt-header-btn position-relative" id="notification-btn" data-bs-toggle="dropdown" href="#" role="button"
                    data-bs-auto-close="outside" data-bs-display="static">
                    <i class="feather-bell fs-17"></i>
                    @php
                        $unread_count = auth()->user()->notifications()->unread()->count();
                    @endphp
                    @if($unread_count > 0)
                        <span class="kt-notif-dot" id="notif-count">{{ $unread_count > 99 ? '99+' : $unread_count }}</span>
                    @endif
                </a>
                <div class="dropdown-menu dropdown-menu-end kt-notif-dropdown kt-notif-dropdown-under-navbar" id="notification-dropdown" style="width:680px !important; min-width:680px !important; max-width:min(96vw,680px) !important;">
                    <div class="kt-notif-header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="kt-notif-header-icon"><i class="feather-bell"></i></span>
                            <div>
                                <div class="fw-bold">Notifications</div>
                                <small class="text-muted">Latest updates</small>
                            </div>
                        </div>
                        <span class="kt-notif-badge" id="notif-badge">
                            @if($unread_count > 0)
                                {{ $unread_count }} New
                            @else
                                0 New
                            @endif
                        </span>
                    </div>
                    <div class="kt-notif-body">
                        <div id="notification-list">
                            @forelse(auth()->user()->notifications()->unread()->recent()->limit(3)->get() as $notification)
                                <div class="kt-notif-item {{ $notification->isUnread() ? 'unread' : '' }}" data-notif-id="{{ $notification->id }}" data-action-url="{{ $notification->getActionUrl() }}">
                                    <span class="kt-notif-accent"></span>
                                    <div class="kt-notif-content">
                                        <div class="kt-notif-title">{{ $notification->title }}</div>
                                        <p class="kt-notif-message">{{ $notification->message }}</p>
                                        <small class="kt-notif-time">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @empty
                                <div class="kt-notif-empty">
                                    <i class="feather-bell-off"></i>
                                    <p>No new notifications</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="kt-notif-footer d-flex align-items-center justify-content-between gap-2">
                        <a class="btn btn-sm btn-link" href="{{ route('notifications.index') }}">View all</a>
                        <button class="btn btn-sm btn-link" id="mark-all-read" style="{{ $unread_count > 0 ? '' : 'display:none;' }}">Mark all as read</button>
                    </div>
                </div>
            </div>

            {{-- Divider --}}
            <div class="kt-header-divider d-none d-sm-block"></div>

            {{-- User Profile Dropdown --}}
            <div class="dropdown">
                <a href="javascript:void(0);" class="kt-user-trigger d-flex align-items-center gap-2 text-decoration-none"
                    data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bs-display="static">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white kt-user-avatar" style="width: 40px; height: 40px; font-size: 18px; font-weight: normal; min-width: 40px;">
                        {{ auth()->user()->getFirstLetter() }}
                    </div>
                    <div class="d-none d-md-block text-start lh-sm">
                        <div class="kt-user-name">{{ auth()->user()->name }}</div>
                        <div class="kt-user-email">{{ auth()->user()->email }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end kt-user-dropdown kt-user-dropdown-under-navbar">
                    <div class="kt-user-dropdown-body">
                        <a href="{{ route('profile.details') }}" class="kt-user-dropdown-item">
                            <i class="feather-user"></i> My Profile
                        </a>
                        <a href="{{ route('employee.attachments.index') }}" class="kt-user-dropdown-item">
                            <i class="feather-user"></i> My Documents
                        </a>
                        <div class="kt-user-dropdown-divider"></div>
                        <a href="javascript:void(0);" class="kt-user-dropdown-item kt-logout-item"
                            onclick="document.getElementById('logout-form').submit();">
                            <i class="feather-log-out"></i> Logout
                        </a>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display:none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>

@push('styles')
    <style>
        .kt-user-dropdown-under-navbar {
            position: absolute !important;
            top: calc(100% + 18px) !important;
            right: 12px !important;
            left: auto !important;
            inset: auto !important;
            transform: none !important;
            margin-top: 0 !important;
        }

        .kt-notif-dropdown-under-navbar {
            position: absolute !important;
            top: calc(100% + 18px) !important;
            right: 12px !important;
            left: auto !important;
            inset: auto !important;
            transform: none !important;
            margin-top: 0 !important;
            width: 680px !important;
            min-width: 680px !important;
            max-width: min(96vw, 680px) !important;
        }

        /* Replace old template dropdown look with custom popup */
        .nxl-header #notification-dropdown.kt-notif-dropdown {
            width: 680px !important;
            min-width: 680px !important;
            max-width: min(96vw, 680px) !important;
            right: 12px !important;
            left: auto !important;
            border: 1px solid #f1d0d0 !important;
            border-radius: 18px !important;
            box-shadow: 0 20px 45px rgba(17, 24, 39, 0.18), 0 8px 22px rgba(200, 41, 42, 0.14) !important;
            overflow: hidden !important;
            padding: 0 !important;
            background: #fff !important;
        }

        .nxl-header #notification-dropdown .kt-notif-header {
            padding: 14px 16px !important;
            border-bottom: 1px solid #f3f4f6 !important;
            background: linear-gradient(135deg, #fff6f6 0%, #ffffff 72%) !important;
        }

        .nxl-header #notification-dropdown .kt-notif-header-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffe9e9;
            color: #c8292a;
            font-size: 14px;
        }

        .nxl-header #notification-dropdown .kt-notif-badge {
            background: #c8292a !important;
            color: #fff !important;
            border-radius: 999px;
            font-weight: 800;
            font-size: 0.66rem;
            padding: 4px 10px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .nxl-header #notification-dropdown .kt-notif-body {
            max-height: 470px;
            overflow-y: auto;
            padding: 10px;
            background: #ffffff;
        }

        .nxl-header #notification-dropdown .kt-notif-item {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 11px 12px;
            margin: 0 0 8px;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
            background: #fff;
            transition: all 0.16s ease;
            cursor: pointer;
        }

        .nxl-header #notification-dropdown .kt-notif-item:last-child {
            margin-bottom: 0;
        }

        .nxl-header #notification-dropdown .kt-notif-item:hover {
            background: #fff7f7;
            border-color: #f8d3d3;
            transform: translateY(-1px);
        }

        .nxl-header #notification-dropdown .kt-notif-item.unread {
            border-color: #ffd7d7;
            background: #fffafb;
        }

        .nxl-header #notification-dropdown .kt-notif-accent {
            width: 4px;
            align-self: stretch;
            border-radius: 999px;
            background: transparent;
            margin-right: 2px;
            flex-shrink: 0;
        }

        .nxl-header #notification-dropdown .kt-notif-item.unread .kt-notif-accent {
            background: #c8292a;
        }

        .nxl-header #notification-dropdown .kt-notif-content {
            flex: 1;
            min-width: 0;
        }

        .nxl-header #notification-dropdown .kt-notif-title {
            font-size: 0.87rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 2px;
        }

        .nxl-header #notification-dropdown .kt-notif-message {
            font-size: 0.79rem;
            line-height: 1.4;
            color: #6b7280;
            margin: 0 0 4px;
        }

        .nxl-header #notification-dropdown .kt-notif-time {
            font-size: 0.72rem;
            color: #9ca3af;
        }

        .nxl-header #notification-dropdown .kt-notif-footer {
            border-top: 1px solid #f3f4f6;
            background: #fcfcfd;
            padding: 10px 14px !important;
        }

        .nxl-header #notification-dropdown .kt-notif-footer .btn-link {
            text-decoration: none;
            color: #c8292a;
            font-weight: 700;
            font-size: 0.79rem;
        }

        .nxl-header #notification-dropdown .kt-notif-footer .btn-link:hover {
            color: #a81f20;
        }

        @media (max-width: 767px) {
            .kt-notif-dropdown-under-navbar,
            .kt-user-dropdown-under-navbar {
                right: 12px !important;
                left: 12px !important;
            }

            .kt-notif-dropdown-under-navbar {
                width: auto !important;
                min-width: auto !important;
                max-width: none !important;
            }

            .nxl-header #notification-dropdown.kt-notif-dropdown {
                width: auto !important;
                min-width: auto !important;
                max-width: none !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // ── Global Search ──────────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function () {

            var SEARCH_URL = @json(route('search'));
            var input = document.getElementById('kt-search-input');
            if (!input) { console.warn('[Search] #kt-search-input not found'); return; }

            /* ── Build popup and inject into <body> ── */
            var popup = document.createElement('div');
            popup.id = 'kt-search-popup';
            popup.style.cssText = [
                'display:none',
                'position:fixed',
                'z-index:99999',
                'width:520px',
                'background:#fff',
                'border:1px solid #f1d0d0',
                'border-radius:18px',
                'box-shadow:0 24px 60px rgba(17,24,39,.18),0 8px 24px rgba(200,41,42,.10)',
                'overflow:hidden',
                'font-family:inherit',
            ].join(';');

            popup.innerHTML = [
                '<div style="display:flex;align-items:center;gap:12px;padding:14px 20px;border-bottom:1px solid #f3f4f6;background:linear-gradient(135deg,#fff6f6 0%,#fff 65%)">',
                  '<span style="width:34px;height:34px;border-radius:10px;background:#ffe9e9;color:#c8292a;font-size:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="feather-search"></i></span>',
                  '<div>',
                    '<div style="font-size:.85rem;font-weight:700;color:#111827">Search</div>',
                    '<div id="kt-sp-sub" style="font-size:.72rem;color:#9ca3af">Start typing…</div>',
                  '</div>',
                  '<span style="margin-left:auto;font-size:.62rem;font-weight:700;color:#9ca3af;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:4px;padding:2px 6px;text-transform:uppercase;flex-shrink:0">Esc</span>',
                '</div>',
                '<div style="max-height:460px;overflow-y:auto;padding:8px 8px 10px">',
                  '<div id="kt-sp-state" style="padding:28px 16px;text-align:center;color:#9ca3af;font-size:.83rem"><i class="feather-search" style="display:block;font-size:24px;margin-bottom:8px;opacity:.28"></i>Type to search</div>',
                  '<ul id="kt-sp-list" style="list-style:none;margin:0;padding:0"></ul>',
                '</div>',
            ].join('');

            document.body.appendChild(popup);

            var spState = document.getElementById('kt-sp-state');
            var spList  = document.getElementById('kt-sp-list');
            var spSub   = document.getElementById('kt-sp-sub');

            var timer     = null;
            var lastQ     = '';
            var activeIdx = -1;
            var isOpen    = false;

            /* ── Position popup centred under the search pill ── */
            function reposition() {
                var pill = document.getElementById('kt-nav-search');
                if (!pill) return;
                var r   = pill.getBoundingClientRect();
                var w   = 520;
                var left = r.left + r.width / 2 - w / 2;
                left = Math.max(8, Math.min(left, window.innerWidth - w - 8));
                popup.style.top  = (r.bottom + 8) + 'px';
                popup.style.left = left + 'px';
            }

            function openPopup() {
                reposition();
                popup.style.display = 'block';
                isOpen = true;
            }

            function closePopup() {
                popup.style.display = 'none';
                isOpen    = false;
                activeIdx = -1;
            }

            function resetState() {
                spList.innerHTML = '';
                spState.style.display = '';
                spState.innerHTML = '<i class="feather-search" style="display:block;font-size:20px;margin-bottom:6px;opacity:.28"></i>Type to search';
                spSub.textContent = 'Start typing…';
            }

            /* ── Helpers ── */
            function escHtml(s) {
                return String(s)
                    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            }
            function markText(text, q) {
                if (!q) return escHtml(text);
                return escHtml(text).replace(
                    new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')', 'gi'),
                    '<strong style="color:#c8292a;font-weight:800">$1</strong>'
                );
            }
            function setActive(idx) {
                var links = spList.querySelectorAll('li a');
                links.forEach(function(a, i) {
                    a.style.background   = i === idx ? '#fff7f7' : '';
                    a.style.borderColor  = i === idx ? '#ffd7d7' : 'transparent';
                });
                activeIdx = idx;
                if (links[idx]) links[idx].scrollIntoView({ block: 'nearest' });
            }

            /* ── Render results ── */
            function render(results, q) {
                spList.innerHTML = '';
                activeIdx = -1;

                if (!results.length) {
                    spState.style.display = '';
                    spState.innerHTML = '<i class="feather-search" style="display:block;font-size:20px;margin-bottom:6px;opacity:.28"></i>No results for <strong>"' + escHtml(q) + '"</strong>';
                    spSub.textContent = '0 results';
                    return;
                }

                spState.style.display = 'none';
                spSub.textContent = results.length + ' result' + (results.length !== 1 ? 's' : '');

                // Split into pages and employees
                var pages = results.filter(function(r){ return r.type === 'page'; });
                var emps  = results.filter(function(r){ return r.type === 'employee'; });

                function addSection(label, items, renderFn) {
                    if (!items.length) return;
                    var lbl = document.createElement('div');
                    lbl.style.cssText = 'padding:8px 10px 3px;font-size:.63rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#b0b7c3';
                    lbl.textContent = label;
                    spList.appendChild(lbl);
                    items.forEach(renderFn);
                }

                function makeRow(href, left, nameHtml, subHtml) {
                    var li = document.createElement('li');
                    var a  = document.createElement('a');
                    a.href = href;
                    a.style.cssText = 'display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;text-decoration:none;color:#111827;border:1px solid transparent;transition:background .1s,border-color .1s';
                    a.innerHTML =
                        left +
                        '<span style="min-width:0">' +
                            '<div style="font-size:.88rem;font-weight:600;color:#111827;line-height:1.3;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">' + nameHtml + '</div>' +
                            (subHtml ? '<div style="font-size:.74rem;color:#6b7280;margin-top:2px">' + subHtml + '</div>' : '') +
                        '</span>';
                    a.addEventListener('mouseenter', function(){ this.style.background='#fff7f7'; this.style.borderColor='#ffd7d7'; });
                    a.addEventListener('mouseleave', function(){ this.style.background=''; this.style.borderColor='transparent'; });
                    li.appendChild(a);
                    spList.appendChild(li);
                }

                // Pages section
                addSection('Pages', pages, function(item) {
                    var iconBox = '<span style="width:36px;height:36px;min-width:36px;border-radius:9px;background:#f4f6f8;color:#6b7280;font-size:15px;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="' + escHtml(item.icon) + '"></i></span>';
                    makeRow(escHtml(item.url), iconBox, markText(item.label, q), '');
                });

                // Employees section
                addSection('Employees', emps, function(item) {
                    var avatar = '<span style="width:36px;height:36px;min-width:36px;border-radius:50%;background:#c8292a;color:#fff;font-size:.70rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0">' + escHtml(item.initials) + '</span>';
                    makeRow(escHtml(item.url), avatar, markText(item.label, q), item.subtitle ? escHtml(item.subtitle) : '');
                });
            }

            /* ── Fetch ── */
            function doSearch(q) {
                if (q === lastQ) return;
                lastQ = q;
                spState.style.display = '';
                spState.innerHTML = '<span style="display:inline-block;width:18px;height:18px;border:2px solid #f0f0f0;border-top-color:#c8292a;border-radius:50%;animation:ktSpin .5s linear infinite"></span>';
                spList.innerHTML = '';
                spSub.textContent = 'Searching…';
                openPopup();

                fetch(SEARCH_URL + '?q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(function (r) { return r.json(); })
                .then(function (d) { render(d.results || [], q); })
                .catch(function (err) {
                    console.error('[Search] fetch error', err);
                    spState.style.display = '';
                    spState.innerHTML = '<i class="feather-alert-circle" style="display:block;font-size:20px;margin-bottom:6px;opacity:.28"></i>Search unavailable';
                    spSub.textContent = 'Error';
                });
            }

            /* ── Inject spinner keyframe once ── */
            if (!document.getElementById('kt-search-style')) {
                var s = document.createElement('style');
                s.id = 'kt-search-style';
                s.textContent = '@keyframes ktSpin{to{transform:rotate(360deg)}}';
                document.head.appendChild(s);
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
            const dropdownBtn = document.getElementById('notification-btn');
            if (dropdownBtn) {
                dropdownBtn.addEventListener('show.bs.dropdown', function() {
                    refreshNotificationList();
                });
            }
        });

        // Attach click-to-read listeners on notification items
        function attachNotificationClickListeners() {
            document.querySelectorAll('.kt-notif-item').forEach(item => {
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
                        const dropdownBtn = document.getElementById('notification-btn');
                        if (dropdownBtn) {
                            const dd = bootstrap.Dropdown.getInstance(dropdownBtn) || new bootstrap.Dropdown(dropdownBtn);
                            dd.hide();
                        }
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
                if (count > 0) {
                    if (dot) {
                        dot.textContent = count > 99 ? '99+' : count;
                        dot.style.display = '';
                    } else {
                        const span = document.createElement('span');
                        span.id = 'notif-count';
                        span.className = 'kt-notif-dot';
                        span.textContent = count > 99 ? '99+' : count;
                        document.getElementById('notification-btn').appendChild(span);
                    }
                    if (badge) badge.textContent = count + ' New';
                } else {
                    if (dot) dot.style.display = 'none';
                    if (badge) badge.textContent = '0 New';
                }
                const markAllBtn = document.getElementById('mark-all-read');
                if (markAllBtn) markAllBtn.style.display = count > 0 ? '' : 'none';
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
                            <div class="kt-notif-item ${n.read_at ? '' : 'unread'}" data-notif-id="${n.id}" data-action-url="${actionUrl}">
                                <span class="kt-notif-accent"></span>
                                <div class="kt-notif-content">
                                    <div class="kt-notif-title">${n.title}</div>
                                    <p class="kt-notif-message">${n.message}</p>
                                    <small class="kt-notif-time">${timeText}</small>
                                </div>
                            </div>`;
                    }).join('');
                } else {
                    list.innerHTML = `
                        <div class="kt-notif-empty">
                            <i class="feather-bell-off"></i>
                            <p>No new notifications</p>
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
