<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="System Maintenance">
    <title>System Under Maintenance - Smart Accounting</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('vendors/css/vendors.min.css') }}">

    <!-- Vite compiled assets -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <style>
        body {
            margin: 0;
            background: #111;
            font-family: 'Sora', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .maintenance-wrap {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .maintenance-brand-panel {
            background: #1c1c1e;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 48px;
            position: relative;
            overflow: hidden;
        }

        .maintenance-brand-panel::before {
            content: '';
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 340px;
            height: 340px;
            background: radial-gradient(circle, rgba(200, 41, 42, 0.18) 0%, transparent 70%);
            pointer-events: none;
        }

        .maintenance-brand-panel::after {
            content: '';
            position: absolute;
            top: -60px;
            left: -60px;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(200, 41, 42, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .maintenance-logo {
            width: 110px;
            height: 110px;
            background: #fff;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            margin-bottom: 28px;
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.1);
        }

        .maintenance-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .maintenance-brand-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.3px;
            margin-bottom: 6px;
            text-align: center;
        }

        .maintenance-brand-tagline {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.35);
            text-transform: uppercase;
            letter-spacing: 2px;
            text-align: center;
        }

        .maintenance-brand-accent {
            width: 32px;
            height: 2px;
            background: #c8292a;
            border-radius: 2px;
            margin: 20px auto 0;
        }

        .maintenance-content-panel {
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 48px;
            overflow-y: auto;
        }

        .maintenance-content-inner {
            width: 100%;
            max-width: 380px;
            text-align: center;
        }

        .maintenance-icon-box {
            width: 80px;
            height: 80px;
            background: #fef3c7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 28px;
        }

        .maintenance-icon-box svg {
            width: 40px;
            height: 40px;
            color: #d97706;
        }

        .maintenance-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: #1c1c1e;
            letter-spacing: -0.3px;
            margin-bottom: 12px;
        }

        .maintenance-message {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 28px;
            line-height: 1.6;
        }

        .maintenance-details {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 28px;
            text-align: left;
        }

        .maintenance-detail-item {
            font-size: 0.82rem;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .maintenance-detail-item:last-child {
            margin-bottom: 0;
        }

        .maintenance-detail-item strong {
            color: #1c1c1e;
            font-weight: 600;
        }

        .maintenance-footer {
            font-size: 0.75rem;
            color: #9ca3af;
            letter-spacing: 0.3px;
        }

        @media (max-width: 768px) {
            .maintenance-wrap {
                grid-template-columns: 1fr;
            }

            .maintenance-brand-panel {
                padding: 40px 24px;
                min-height: auto;
                display: none;
            }

            .maintenance-content-panel {
                padding: 40px 24px;
                min-height: 100vh;
            }

            .maintenance-logo {
                width: 80px;
                height: 80px;
            }

            .maintenance-brand-name {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>
    <div class="maintenance-wrap">
        {{-- Brand Panel --}}
        <div class="maintenance-brand-panel">
            <div class="maintenance-logo">
                <img src="{{ asset('images/knights_logo_icon.png') }}" alt="Knights Transport">
            </div>
            <div class="maintenance-brand-name">Knights Transport</div>
            <div class="maintenance-brand-tagline">Smart Accounting System</div>
            <div class="maintenance-brand-accent"></div>
        </div>

        {{-- Content Panel --}}
        <div class="maintenance-content-panel">
            <div class="maintenance-content-inner">
                <div class="maintenance-icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20z"></path>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>

                <h1 class="maintenance-title">System Under Maintenance</h1>

                <p class="maintenance-message">
                    We're currently performing scheduled maintenance to improve system performance and security. We'll be back online shortly.
                </p>

                <div class="maintenance-details">
                    <div class="maintenance-detail-item">
                        <strong>Expected Duration:</strong><br>
                        Less than 1 hour
                    </div>
                    <div class="maintenance-detail-item">
                        <strong>What's Happening:</strong><br>
                        Our team is working to enhance system capabilities and ensure optimal performance.
                    </div>
                </div>

                <p class="maintenance-footer">
                    Thank you for your patience while we improve your experience
                </p>
            </div>
        </div>
    </div>
</body>
</html>
