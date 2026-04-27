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

    <title>Smart Accounting v3</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('vendors/css/vendors.min.css') }}">
    {{-- daterangepicker assets removed (no longer used) --}}
    @stack('head_scripts')

    <!-- Vite compiled assets (CSS & JS) – placed last so it overrides previous styles -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    @stack('styles')
</head>

<body>
    @include('partials.sidebar')
    @include('partials.header')

    <main class="nxl-container" data-global-datepicker="off">
        <div class="nxl-content">
            @include('partials.page-header')

            <div class="main-content">
                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    @yield('content')
                </div>
            </div>

            @include('partials.footer')
        </div>
    </main>

    {{-- Template Vendors JS (order matters, vendors first) --}}
    <script src="{{ asset('vendors/js/vendors.min.js') }}"></script>
    {{-- daterangepicker was removed; keep layout clean --}}
    <script src="{{ asset('vendors/js/apexcharts.min.js') }}"></script>
    {{-- circle-progress vendor removed (no longer used) --}}

    {{-- Template Init JS --}}

    {{-- Desktop/Mobile Menu Handler --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const miniBtn = document.getElementById('menu-mini-button');
            const expendBtn = document.getElementById('menu-expend-button');
            const htmlElement = document.documentElement;
            const storageKey = 'nexel-classic-dashboard-menu-mini-theme';

            // Initialize menu state from localStorage
            const savedState = localStorage.getItem(storageKey);
            if (savedState === 'menu-mini-theme') {
                htmlElement.classList.add('minimenu');
                if (miniBtn) miniBtn.style.display = 'none';
                if (expendBtn) expendBtn.style.display = 'flex';
            } else if (savedState === 'menu-expend-theme') {
                htmlElement.classList.remove('minimenu');
                if (miniBtn) miniBtn.style.display = 'flex';
                if (expendBtn) expendBtn.style.display = 'none';
            }

            // Mini button click handler
            if (miniBtn) {
                miniBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    miniBtn.style.display = 'none';
                    if (expendBtn) expendBtn.style.display = 'flex';
                    htmlElement.classList.add('minimenu');
                    localStorage.setItem(storageKey, 'menu-mini-theme');
                });
            }

            // Expend button click handler
            if (expendBtn) {
                expendBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (miniBtn) miniBtn.style.display = 'flex';
                    expendBtn.style.display = 'none';
                    htmlElement.classList.remove('minimenu');
                    localStorage.setItem(storageKey, 'menu-expend-theme');
                });
            }

            // Handle window resize
            window.addEventListener('resize', function() {
                const width = window.innerWidth;
                // Desktop: 1024px <= width <= 1600px: show mini by default
                // Laptop: width > 1600px: show full by default
                if (1024 <= width && width <= 1600) {
                    if (!htmlElement.classList.contains('minimenu')) {
                        htmlElement.classList.add('minimenu');
                        if (miniBtn) miniBtn.style.display = 'none';
                        if (expendBtn) expendBtn.style.display = 'flex';
                    }
                } else if (width > 1600) {
                    if (htmlElement.classList.contains('minimenu')) {
                        htmlElement.classList.remove('minimenu');
                        if (miniBtn) miniBtn.style.display = 'flex';
                        if (expendBtn) expendBtn.style.display = 'none';
                    }
                }
            });
        });
    </script>

    {{-- Mobile Sidebar Handler --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('mobile-collapse');
            const nav = document.querySelector('.nxl-navigation');
            const closeBtn = document.querySelector('.kt-mob-close');

            if (!toggle || !nav) return;

            // Open/close on hamburger click
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                nav.classList.toggle('active');
                document.body.classList.toggle('sidebar-open', nav.classList.contains('active'));
            });

            // Close on close button click
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    nav.classList.remove('active', 'mob-navigation-active');
                    document.body.classList.remove('sidebar-open');
                });
            }

            // Close on backdrop click (outside nav/toggle)
            document.addEventListener('click', function(e) {
                if (window.innerWidth >= 1199) return;
                if (!nav.classList.contains('active') && !nav.classList.contains('mob-navigation-active'))
                    return;

                const isClickOnNav = nav.contains(e.target);
                const isClickOnToggle = toggle.contains(e.target);

                if (!isClickOnNav && !isClickOnToggle) {
                    nav.classList.remove('active', 'mob-navigation-active'); // critical fix
                    document.body.classList.remove('sidebar-open');
                }
            }, true);

            // Close on nav link click (except submenu toggles)
            nav.addEventListener('click', function(e) {
                const link = e.target.closest('a.nxl-link');
                if (!link) return;
                if (link.getAttribute('href') === 'javascript:void(0);') return;

                if (window.innerWidth < 1200) {
                    nav.classList.remove('active', 'mob-navigation-active');
                    document.body.classList.remove('sidebar-open');
                }
            });

            // Reset on resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1200) {
                    nav.classList.remove('active', 'mob-navigation-active');
                    document.body.classList.remove('sidebar-open');
                }
            });
        });
    </script>

    <script>
        window.attendanceRowsUrl = "{{ route('api.attendance.table-rows') }}";
        window.notificationsCountUrl = "{{ route('notifications.count') }}";
    </script>


    {{-- Page-specific scripts --}}
    @yield('scripts')
    @stack('scripts')

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
                    top: '18px',
                    right: '18px',
                    zIndex: '99999',
                    display: 'inline-flex',
                    alignItems: 'center',
                    gap: '10px',
                    padding: '10px 14px',
                    borderRadius: '10px',
                    border: '1px solid #fbcaca',
                    background: 'linear-gradient(135deg, #fff5f5 0%, #ffffff 100%)',
                    color: '#7f1d1d',
                    fontSize: '0.8rem',
                    fontWeight: '700',
                    letterSpacing: '0.01em',
                    boxShadow: '0 8px 22px rgba(200, 41, 42, 0.2)',
                    backdropFilter: 'blur(3px)',
                    opacity: '0',
                    transform: 'translateY(-6px)',
                    transition: 'opacity .2s ease, transform .2s ease',
                });

                document.body.appendChild(toast);
                requestAnimationFrame(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateY(0)';
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
