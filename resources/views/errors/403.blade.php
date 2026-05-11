<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Access Forbidden">
    <title>403 - Access Forbidden | Smart Accounting</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}">

    <link rel="stylesheet" href="{{ asset('vendors/css/vendors.min.css') }}">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            font-family: 'Sora', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 24px;
        }

        .error-layout {
            width: 100%;
            max-width: 1100px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 34px;
            position: relative;
        }

        .error-layout::before {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(200, 41, 42, 0.14) 0%, rgba(200, 41, 42, 0) 70%);
            top: -120px;
            left: -140px;
            z-index: 0;
            pointer-events: none;
        }

        .error-layout::after {
            content: "";
            position: absolute;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(17, 24, 39, 0.07) 0%, rgba(17, 24, 39, 0) 70%);
            bottom: -130px;
            right: -120px;
            z-index: 0;
            pointer-events: none;
        }

        .error-content {
            text-align: left;
            max-width: 560px;
            position: relative;
            z-index: 1;
        }

        .error-icon-wrap {
            width: 460px;
            height: 460px;
            margin: 0 auto;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        .error-icon-wrap img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 20px 30px rgba(17, 24, 39, 0.16));
        }

        .error-code {
            font-size: clamp(5rem, 13vw, 9rem);
            font-weight: 900;
            letter-spacing: -2px;
            color: #c8292a;
            margin: 0 0 8px;
            line-height: 1;
            text-shadow: 0 8px 20px rgba(200, 41, 42, 0.18);
        }

        .error-title {
            margin: 8px 0 10px;
            font-size: 1.5rem;
            font-weight: 800;
            color: #111827;
        }

        .error-quote {
            margin: 0 0 14px;
            color: #1f2937;
            font-weight: 800;
            font-size: 1.15rem;
        }

        .error-description {
            margin: 0 0 24px;
            max-width: 560px;
            color: #6b7280;
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .error-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-start;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .error-actions .btn {
            border-radius: 10px;
            padding: 10px 16px;
            font-weight: 700;
        }

        .error-footnote {
            margin: 0;
            color: #9ca3af;
            font-size: 0.78rem;
        }

        @media (max-width: 768px) {
            .error-layout {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .error-layout::before,
            .error-layout::after {
                display: none;
            }

            .error-content {
                text-align: center;
                max-width: 100%;
            }

            .error-icon-wrap {
                width: 260px;
                height: 260px;
                order: -1;
                margin-bottom: 16px;
            }

            .error-description {
                margin: 0 auto 24px;
            }

            .error-actions {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="error-layout">
        <div class="error-content">
            <h1 class="error-code">403</h1>
            <p class="error-quote">You shall not pass.</p>

            <p class="error-description">
                You do not have permission to access this page. This area is restricted to authorized personnel only.
                If you believe this is a mistake, please contact your system administrator.
            </p>

            <div class="error-actions">
                <a href="{{ auth()->check() ? route('dashboard') : url('/') }}" class="btn btn-primary">
                    Go to Home
                </a>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    Go Back
                </a>
            </div>

            <p class="error-footnote">If this keeps happening, report the page URL to your administrator.</p>
        </div>

        <div class="error-icon-wrap">
            <img src="{{ url('images/403%20icon.png') }}" alt="Access Forbidden">
        </div>
    </div>
</body>
</html>
