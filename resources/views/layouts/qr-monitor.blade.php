<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>QR Attendance Monitor - Smart Accounting</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}">
    
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('vendors/css/vendors.min.css') }}">
    {{-- daterangepicker assets removed (no longer used) --}}

    <!-- Vite compiled assets -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <style>
        /* ═══════════════════════════════════════════════════════════════ */
        /* PAGE LAYOUT - SINGLE VIEWPORT, NO SCROLL */
        /* ═══════════════════════════════════════════════════════════════ */

        html, body {
            width: 100%;
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        /* ═══════════════════════════════════════════════════════════════ */
        /* HEADER STYLES */
        /* ═══════════════════════════════════════════════════════════════ */

        .nxl-header {
            background: #fff !important;
            border-bottom: 1px solid #e8e8e8 !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
            height: 70px !important;
            transition: all 0.3s ease !important;
            width: 100% !important;
            left: 0 !important;
            right: 0 !important;
            position: relative;
        }

        /* Override all header media queries */
        @media (max-width: 767px) {
            .nxl-header {
                width: 100% !important;
                left: 0 !important;
            }
        }

        @media (min-width: 768px) {
            .nxl-header {
                width: 100% !important;
                left: 0 !important;
            }
        }

        @media (min-width: 992px) {
            .nxl-header {
                width: 100% !important;
                left: 0 !important;
            }
        }

        @media (min-width: 1200px) {
            .nxl-header {
                width: 100% !important;
                left: 0 !important;
            }
            html:not(.minimenu) .nxl-header {
                width: 100% !important;
                left: 0 !important;
            }
            html.minimenu .nxl-header {
                width: 100% !important;
                left: 0 !important;
            }
        }

        @media (min-width: 1400px) {
            .nxl-header {
                width: 100% !important;
                left: 0 !important;
            }
        }

        .nxl-header .header-wrapper {
            height: 100%;
            display: flex;
            align-items: center;
            padding: 0 20px;
            gap: 4px;
        }

        /* Header Button */
        .kt-header-btn {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #757575;
            text-decoration: none;
            cursor: pointer;
            background: transparent;
            border: none;
            transition: background 0.15s, color 0.15s;
        }

        .kt-header-btn:hover {
            background: #f5f5f5;
            color: #333;
        }

        /* Header Elements */
        .kt-brand-name {
            font-weight: 700;
            font-size: 0.9rem;
            color: #333;
            letter-spacing: 0.2px;
        }

        .kt-role-pill {
            background: #fff0f0;
            color: #d32f2f;
            border: 1px solid #fcd0d0;
            font-size: 0.67rem;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .kt-header-divider {
            width: 1px;
            height: 22px;
            background: #e8e8e8;
            margin: 0 6px;
        }

        /* User Trigger */
        .kt-user-trigger {
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 6px;
            transition: background 0.15s;
        }

        .kt-user-trigger:hover {
            background: #f5f5f5;
        }

        .kt-user-avatar {
            width: 34px;
            height: 34px;
            min-width: 34px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #d32f2f;
        }

        .kt-user-name {
            font-size: 0.82rem;
            font-weight: 700;
            color: #333;
            line-height: 1.2;
        }

        .kt-user-email {
            font-size: 0.72rem;
            color: #757575;
            line-height: 1.2;
        }

        /* User Dropdown */
        .kt-user-dropdown {
            min-width: 240px;
            border: 1px solid #e8e8e8 !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            padding: 0 !important;
            overflow: hidden;
        }

        .kt-user-dropdown-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: #fff5f5;
            border-bottom: 1px solid #fcdcdc;
        }

        .kt-user-dropdown-avatar {
            width: 46px;
            height: 46px;
            min-width: 46px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #d32f2f;
        }

        .kt-user-dropdown-name {
            font-size: 0.875rem;
            font-weight: 700;
            color: #333;
        }

        .kt-user-dropdown-email {
            font-size: 0.75rem;
            color: #757575;
        }

        .kt-user-dropdown-role {
            display: inline-block;
            margin-top: 4px;
            background: #d32f2f;
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .kt-user-dropdown-body {
            padding: 6px 4px;
        }

        .kt-user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            border-radius: 4px;
            margin: 1px 0;
            color: #333;
            font-size: 0.845rem;
            text-decoration: none;
            transition: background 0.13s, color 0.13s;
        }

        .kt-user-dropdown-item i {
            color: #757575;
            font-size: 15px;
            width: 16px;
        }

        .kt-user-dropdown-item:hover {
            background: #fff5f5;
            color: #d32f2f;
        }

        .kt-user-dropdown-item:hover i {
            color: #d32f2f;
        }

        .kt-logout-item {
            color: #d32f2f;
        }

        .kt-logout-item i {
            color: #d32f2f;
        }

        .kt-logout-item:hover {
            background: #fff0f0 !important;
        }

        .kt-user-dropdown-divider {
            border-top: 1px solid #e8e8e8;
            margin: 4px 10px;
        }

        /* Dropdown animation */
        .nxl-header .dropdown-menu {
            animation: 0.4s ease-in-out 0s normal forwards 1 fadein;
        }

        @keyframes fadein {
            from {
                opacity: 0;
                transform: translate3d(0, 8px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        /* ═══════════════════════════════════════════════════════════════ */
        /* QR MONITOR LAYOUT STYLES */
        /* ═══════════════════════════════════════════════════════════════ */

        /* Hide sidebar and navigation elements */
        #mobile-collapse {
            display: none !important;
        }
        
        .nxl-navigation {
            display: none !important;
        }

        /* Reset container to full width without sidebar offset */
        .nxl-container {
            width: 100% !important;
            height: calc(100vh - 70px) !important;
            margin: 0 !important;
            padding: 0 !important;
            margin-left: 0 !important;
            margin-top: 0 !important;
            max-width: 100% !important;
            overflow: hidden;
        }

        /* Override all media query responsive margin rules */
        @media (max-width: 767px) {
            .nxl-container {
                margin-left: 0 !important;
            }
        }

        @media (min-width: 768px) {
            .nxl-container {
                margin-left: 0 !important;
            }
        }

        @media (min-width: 992px) {
            .nxl-container {
                margin-left: 0 !important;
            }
        }

        @media (min-width: 1200px) {
            html:not(.minimenu) .nxl-container {
                margin-left: 0 !important;
            }
            html.minimenu .nxl-container {
                margin-left: 0 !important;
            }
        }

        @media (min-width: 1400px) {
            .nxl-container {
                margin-left: 0 !important;
            }
        }

        /* Reset content to fill available space */
        .nxl-content {
            width: 100% !important;
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            padding-left: 0 !important;
            padding-top: 0 !important;
            overflow: hidden;
        }

        /* Main content container with centered display */
        .main-content {
            display: flex;
            align-items: center;
            justify-content: center;
            height: auto;
            padding: 12px 24px !important;
            margin: 0 !important;
            margin-top: 0 !important;
            box-sizing: border-box;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <!-- Header recreated inline -->
    <header class="nxl-header">
        <div class="header-wrapper">

            {{-- ── Left ── --}}
            <div class="header-left d-flex align-items-center gap-3">
                {{-- Brand --}}
                <div class="d-none d-md-flex align-items-center gap-2">
                    <span class="kt-brand-name">Knights Transport</span>
                    <span class="kt-role-pill">
                        {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                    </span>
                </div>
            </div>

            {{-- ── Right ── --}}
            <div class="header-right ms-auto d-flex align-items-center gap-1">

                {{-- Fullscreen (monitor display) --}}
                <button type="button" class="kt-header-btn" id="ktFullscreenBtn" title="Full screen" aria-label="Full screen">
                    <i class="feather-maximize"></i>
                </button>

                {{-- Divider --}}
                <div class="kt-header-divider d-none d-sm-block"></div>

                {{-- User Dropdown --}}
                <div class="dropdown">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside"
                        class="kt-user-trigger d-flex align-items-center gap-2 text-decoration-none">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white kt-user-avatar" style="width: 40px; height: 40px; font-size: 18px; font-weight: normal; min-width: 40px;">
                            {{ auth()->user()->getFirstLetter() }}
                        </div>
                        <div class="d-none d-md-block text-start lh-sm">
                            <div class="kt-user-name">{{ auth()->user()->name }}</div>
                            <div class="kt-user-email">{{ auth()->user()->email }}</div>
                        </div>
                        <i class="feather-chevron-down fs-12 text-muted d-none d-md-block ms-1"></i>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end kt-user-dropdown">
                        <div class="kt-user-dropdown-header">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white kt-user-dropdown-avatar" style="width: 50px; height: 50px; font-size: 24px; font-weight: normal; min-width: 50px;">
                                {{ auth()->user()->getFirstLetter() }}
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="kt-user-dropdown-name">{{ auth()->user()->name }}</div>
                                <div class="kt-user-dropdown-email text-truncate">{{ auth()->user()->email }}</div>
                                <span class="kt-user-dropdown-role">
                                    {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                                </span>
                            </div>
                        </div>
                        <div class="kt-user-dropdown-body">
                            <a href="{{ route('profile.details') }}" class="kt-user-dropdown-item">
                                <i class="feather-user"></i><span>Profile Details</span>
                            </a>
                            <a href="{{ route('settings.account') }}" class="kt-user-dropdown-item">
                                <i class="feather-settings"></i><span>Account Settings</span>
                            </a>
                            <div class="kt-user-dropdown-divider"></div>
                            <a href="javascript:void(0);" class="kt-user-dropdown-item kt-logout-item"
                                onclick="document.getElementById('logout-form').submit();">
                                <i class="feather-log-out"></i><span>Logout</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display:none;">
                                @csrf</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="nxl-container">
        <div class="nxl-content">
            <div class="main-content">
                @yield('content')
            </div>
        </div>
    </main>

    {{-- Template Vendors JS --}}
    <script src="{{ asset('vendors/js/vendors.min.js') }}"></script>
    {{-- daterangepicker was removed; keep layout clean --}}
    <script src="{{ asset('vendors/js/apexcharts.min.js') }}"></script>

    <script>
        (function () {
            const btn = document.getElementById('ktFullscreenBtn');
            if (!btn) return;

            const updateIcon = () => {
                const icon = btn.querySelector('i');
                if (!icon) return;
                icon.className = document.fullscreenElement ? 'feather-minimize' : 'feather-maximize';
                btn.title = document.fullscreenElement ? 'Exit full screen' : 'Full screen';
                btn.setAttribute('aria-label', btn.title);
            };

            btn.addEventListener('click', async () => {
                try {
                    if (!document.fullscreenElement) {
                        await document.documentElement.requestFullscreen();
                    } else {
                        await document.exitFullscreen();
                    }
                } catch (e) {
                    // no-op (fullscreen can be blocked by browser policy)
                } finally {
                    updateIcon();
                }
            });

            document.addEventListener('fullscreenchange', updateIcon);
            updateIcon();
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
