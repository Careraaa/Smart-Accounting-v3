<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Smart Accounting System">
    <title>@yield('title', 'Smart Accounting')</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights.ico') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/theme.min.css') }}">

    <style>
        /* -- Layout ---------------------------------------------------- */
        body { margin: 0; background: #111; }

        .auth-wrap {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* Left — dark brand panel */
        .auth-brand-panel {
            background: #1c1c1e;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 48px;
            position: relative;
            overflow: hidden;
        }
        /* Subtle red glow in corner */
        .auth-brand-panel::before {
            content: '';
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 340px;
            height: 340px;
            background: radial-gradient(circle, rgba(200,41,42,0.18) 0%, transparent 70%);
            pointer-events: none;
        }
        .auth-brand-panel::after {
            content: '';
            position: absolute;
            top: -60px;
            left: -60px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(200,41,42,0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .auth-brand-logo {
    width: 110px;
    height: 110px;
    background: #fff;
    border-radius: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    margin-bottom: 28px;
    box-shadow: 0 8px 40px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.1);
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
            color: rgba(255,255,255,0.35);
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
            color: rgba(255,255,255,0.2);
            text-align: center;
            letter-spacing: 0.3px;
        }

        /* Right — form panel */
        .auth-form-panel {
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 48px;
            overflow-y: auto;
        }
        .auth-form-inner {
            width: 100%;
            max-width: 380px;
        }

        /* Page title area */
        .auth-page-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: #1c1c1e;
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }
        .auth-page-sub {
            font-size: 0.82rem;
            color: #9898a8;
            margin-bottom: 28px;
        }

        /* Form elements */
        .auth-form-inner .form-label {
            font-size: 0.815rem !important;
            font-weight: 600 !important;
            color: #1c1c1e !important;
            margin-bottom: 6px;
        }
        .auth-form-inner .form-control,
        .auth-form-inner .form-select {
            border-radius: 8px !important;
            padding: 10px 14px !important;
            border-color: #e8e8ef !important;
            font-size: 0.875rem !important;
            color: #1c1c1e !important;
            background: #fafafa !important;
            transition: border-color 0.15s, box-shadow 0.15s !important;
        }
        .auth-form-inner .form-control:focus,
        .auth-form-inner .form-select:focus {
            border-color: #c8292a !important;
            box-shadow: 0 0 0 3px rgba(200,41,42,0.1) !important;
            background: #fff !important;
            outline: none !important;
        }
        .auth-form-inner .form-control::placeholder { color: #c0c0cc !important; }

        /* Submit button */
        .btn-auth {
            background: #1c1c1e;
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 0.875rem;
            font-weight: 700;
            padding: 11px;
            width: 100%;
            cursor: pointer;
            transition: background 0.15s;
            letter-spacing: 0.2px;
        }
        .btn-auth:hover  { background: #c8292a; color: #fff; }
        .btn-auth:focus  { outline: none; box-shadow: 0 0 0 3px rgba(200,41,42,0.2); }

        /* Override template's btn-primary inside auth */
        .auth-form-inner .btn-primary,
        .auth-form-inner .btn-lg.btn-primary {
            background: #1c1c1e !important;
            border-color: #1c1c1e !important;
            border-radius: 8px !important;
            font-size: 0.875rem !important;
            font-weight: 700 !important;
            padding: 11px !important;
            transition: background 0.15s !important;
            box-shadow: none !important;
        }
        .auth-form-inner .btn-primary:hover {
            background: #c8292a !important;
            border-color: #c8292a !important;
        }

        /* Links */
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

        /* Divider */
        .auth-divider {
            width: 32px;
            height: 2px;
            background: #c8292a;
            border-radius: 2px;
            margin: 12px 0 0;
        }

        /* Checkbox */
        .auth-form-inner .form-check-input:checked {
            background-color: #1c1c1e !important;
            border-color: #1c1c1e !important;
        }

        /* Alerts */
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

        /* Bottom text */
        .auth-footer-text {
            margin-top: 24px;
            font-size: 0.82rem;
            color: #9898a8;
            text-align: center;
        }

        /* -- Mobile: stack vertically ---------------------------------- */
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
        }
    </style>
</head>

<body>
    <div class="auth-wrap">

        {{-- Brand panel --}}
        <div class="auth-brand-panel">
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

    <script src="{{ asset('vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('js/common-init.min.js') }}"></script>
    @yield('scripts')
</body>

</html>