<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Smart Accounting System">
    <title>@yield('title', 'Smart Accounting')</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}">


    <!-- Your custom inline styles (high specificity – Vite will override where it can) -->
    <style>
        body {
            margin: 0;
            background:
                radial-gradient(circle at 12% 18%, rgba(200,41,42,0.08), transparent 34%),
                radial-gradient(circle at 88% 12%, rgba(37,99,235,0.08), transparent 28%),
                linear-gradient(160deg, #f5f7fb 0%, #eff3f9 56%, #f7f9fc 100%);
            color: #111827;
            font-family: 'Sora', sans-serif;
        }

        .auth-wrap {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            position: relative;
        }

        .auth-brand-panel {
            background:
                linear-gradient(140deg, rgba(17,24,39,0.95) 0%, rgba(30,41,59,0.95) 58%, rgba(15,23,42,0.95) 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 48px;
            position: relative;
            overflow: hidden;
        }
        .auth-brand-panel .auth-brand-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(to right, rgba(255,255,255,0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 46px 46px;
            mask-image: radial-gradient(closest-side at 50% 30%, rgba(0,0,0,0.75), transparent 80%);
            opacity: 0.4;
            pointer-events: none;
        }
        .auth-brand-panel::before {
            content: '';
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 360px;
            height: 360px;
            background: radial-gradient(circle, rgba(200,41,42,0.24) 0%, transparent 70%);
            pointer-events: none;
        }
        .auth-brand-panel::after {
            content: '';
            position: absolute;
            top: -60px;
            left: -60px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(56,189,248,0.12) 0%, transparent 72%);
            pointer-events: none;
        }

        .auth-brand-logo {
            width: 110px;
            height: 110px;
            background: rgba(255,255,255,0.98);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            margin-bottom: 28px;
            box-shadow: 0 10px 38px rgba(15,23,42,0.5), 0 0 0 1px rgba(255,255,255,0.18);
        }
        .auth-brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .auth-brand-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.3px;
            margin-bottom: 6px;
            text-align: center;
        }
        .auth-brand-tagline {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.52);
            text-transform: uppercase;
            letter-spacing: 2px;
            text-align: center;
        }
        .auth-brand-accent {
            width: 32px;
            height: 2px;
            background: #c8292a;
            border-radius: 2px;
            margin: 20px auto 0;
        }
        .auth-brand-copy {
            position: absolute;
            bottom: 28px;
            font-size: 0.7rem;
            color: rgba(255,255,255,0.34);
            text-align: center;
            letter-spacing: 0.3px;
        }

        .auth-form-panel {
            background: transparent;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 48px;
            overflow-y: auto;
            position: relative;
        }
        .auth-form-inner {
            width: 100%;
            max-width: 420px;
            background: rgba(255,255,255,0.88);
            border: 1px solid rgba(255,255,255,0.7);
            border-radius: 20px;
            padding: 30px 26px;
            box-shadow: 0 16px 44px rgba(15,23,42,0.14);
            backdrop-filter: blur(14px);
            position: relative;
            overflow: hidden;
        }
        .auth-form-inner::before {
            content: '';
            position: absolute;
            top: -95px;
            right: -95px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(200,41,42,0.15) 0%, transparent 72%);
            pointer-events: none;
        }
        .auth-form-inner > * {
            position: relative;
            z-index: 1;
        }

        .auth-page-title {
            font-size: 1.52rem;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.02em;
            margin-bottom: 6px;
        }
        .auth-page-sub {
            font-size: 0.83rem;
            color: #6b7280;
            margin-bottom: 28px;
        }

        .auth-form-inner .form-label {
            font-size: 0.815rem !important;
            font-weight: 600 !important;
            color: #111827 !important;
            margin-bottom: 6px;
        }
        .auth-form-inner .form-control,
        .auth-form-inner .form-select {
            border-radius: 10px !important;
            padding: 11px 14px !important;
            border-color: #dbe2ea !important;
            font-size: 0.875rem !important;
            color: #111827 !important;
            background: #f9fbff !important;
            transition: border-color 0.15s, box-shadow 0.15s !important;
        }
        .auth-form-inner .form-control:focus,
        .auth-form-inner .form-select:focus {
            border-color: #c8292a !important;
            box-shadow: 0 0 0 3px rgba(200,41,42,0.11) !important;
            background: #fff !important;
            outline: none !important;
        }
        .auth-form-inner .form-control::placeholder { color: #98a2b3 !important; }

        .btn-auth {
            background: #c8292a;
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 0.875rem;
            font-weight: 700;
            padding: 12px;
            width: 100%;
            cursor: pointer;
            transition: background 0.15s, transform 0.12s, box-shadow 0.15s;
            letter-spacing: 0.24px;
            box-shadow: 0 10px 28px rgba(200,41,42,0.34);
        }
        .btn-auth:hover  { background: #a81f20; color: #fff; transform: translateY(-1px); }
        .btn-auth:focus  { outline: none; box-shadow: 0 0 0 3px rgba(200,41,42,0.2); }

        .auth-form-inner .btn-primary,
        .auth-form-inner .btn-lg.btn-primary {
            background: #c8292a !important;
            border-color: #c8292a !important;
            border-radius: 10px !important;
            font-size: 0.875rem !important;
            font-weight: 700 !important;
            padding: 12px !important;
            transition: background 0.15s !important;
            box-shadow: none !important;
        }
        .auth-form-inner .btn-primary:hover {
            background: #a81f20 !important;
            border-color: #a81f20 !important;
        }

        .auth-form-inner a {
            color: #c8292a !important;
            text-decoration: none;
            font-weight: 600;
        }
        .auth-form-inner a:hover { text-decoration: underline; }
        .auth-form-inner .link-muted {
            color: #9898a8 !important;
            font-weight: 400;
        }
        .auth-form-inner .link-muted:hover { color: #4a4a58 !important; }

        .auth-divider {
            width: 44px;
            height: 3px;
            background: #c8292a;
            border-radius: 2px;
            margin: 12px 0 0;
        }

        .auth-form-inner .form-check-input:checked {
            background-color: #1c1c1e !important;
            border-color: #1c1c1e !important;
        }

        .auth-form-inner .alert-danger {
            background: #fff5f5 !important;
            border-color: #fcd0d0 !important;
            color: #a81f20 !important;
            border-radius: 8px !important;
            font-size: 0.845rem !important;
        }
        .auth-form-inner .alert-success {
            background: #f0fdf4 !important;
            border-color: #bbf7d0 !important;
            color: #15803d !important;
            border-radius: 8px !important;
            font-size: 0.845rem !important;
        }

        .auth-footer-text {
            margin-top: 24px;
            font-size: 0.82rem;
            color: #6b7280;
            text-align: center;
        }

        @media (max-width: 767px) {
            .auth-wrap { grid-template-columns: 1fr; }
            .auth-brand-panel {
                padding: 40px 24px 36px;
                min-height: auto;
            }
            .auth-brand-panel::before,
            .auth-brand-panel::after { display: none; }
            .auth-brand-copy { position: static; margin-top: 20px; }
            .auth-form-panel { padding: 40px 24px; }
            .auth-form-inner { padding: 24px 18px; border-radius: 16px; }
        }
    </style>

    <!-- Vite assets LAST – this allows Vite to override inline styles where needed -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>

<body>
    <div class="auth-wrap">

        {{-- Brand panel --}}
        <div class="auth-brand-panel">
            <div class="auth-brand-grid"></div>
            <div class="auth-brand-logo">
                <img src="{{ asset('images/knights_logo.png') }}" alt="Knights Logo">
            </div>
            <div class="auth-brand-name">Knights Transport</div>
            <div class="auth-brand-tagline">Smart Accounting System</div>
            <div class="auth-brand-accent"></div>
            <p class="auth-brand-copy">&copy; {{ date('Y') }} Knights Transport Services Corporation</p>
        </div>

        {{-- Form panel --}}
        <div class="auth-form-panel">
            <div class="auth-form-inner">

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')

            </div>
        </div>

    </div>

    @yield('scripts')
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