<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <meta name="author" content="flexilecode">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Knights TSC</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}">

    {{-- Vendors CSS (feather icons, Bootstrap for non-converted pages) --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/vendors.min.css') }}">
    @stack('head_scripts')

    {{-- Vite — Tailwind CSS only (no SCSS/Bootstrap/nxl) --}}
    @vite(['resources/css/tailwind.css', 'resources/js/app.js'])

    <style>
        /* Override: the vendor CSS blurs the entire page when a Bootstrap modal opens.
           This breaks our custom modals (reject, batch reject, etc.) — remove the blur. */
        body.modal-open .sidebar,
        body.modal-open main {
            filter: none !important;
        }

        /* Hide Pace.js progress bar — we have our own loader */
        .pace,
        .pace .pace-progress,
        .pace .pace-activity {
            display: none !important;
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- Page loader --}}
    <div id="page-loader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-white">
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

    <main class="lg:ml-[var(--sidebar-w)] ml-0 bg-gray-100 pt-16 min-h-screen transition-all duration-300" data-global-datepicker="off">
        <div class="bg-gray-100">

            <div class="main-content px-4 sm:px-7 lg:px-9 py-7">
                @if (session('info'))
                    <div id="flash-info" class="flex items-center gap-2.5 px-4 py-3 mb-5 rounded-xl text-sm font-medium bg-blue-50 border border-blue-200 text-blue-700" role="alert">
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

    {{-- Template Vendors JS (order matters, vendors first) --}}
    <script src="{{ asset('vendors/js/vendors.min.js') }}"></script>
    <script>if(typeof jQuery!=='undefined'){jQuery(".sidebar-list li").off("click");}</script>
    {{-- daterangepicker was removed; keep layout clean --}}
    <script src="{{ asset('vendors/js/apexcharts.min.js') }}"></script>
    {{-- circle-progress vendor removed (no longer used) --}}

    {{-- Template Init JS --}}

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
</body>

</html>
