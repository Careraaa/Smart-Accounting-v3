<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="System Maintenance">
    <title>System Under Maintenance - Smart Accounting</title>
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
            color: #111827;
            font-family: 'Sora', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 28px;
        }

        .maintenance-layout {
            width: 100%;
            max-width: 1160px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            align-items: center;
        }

        .maintenance-copy {
            max-width: 560px;
        }

        .maintenance-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.4px;
            color: #b91c1c;
            background: #fee2e2;
            margin-bottom: 14px;
            text-transform: uppercase;
        }

        .maintenance-code {
            margin: 0;
            line-height: 0.9;
            color: #c8292a;
            font-size: clamp(4.3rem, 11vw, 8.2rem);
            font-weight: 900;
            letter-spacing: -2px;
            text-shadow: 0 10px 24px rgba(200, 41, 42, 0.16);
        }

        .maintenance-title {
            margin: 10px 0 12px;
            font-size: clamp(1.4rem, 2.6vw, 2.1rem);
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -0.4px;
            color: #111827;
        }

        .maintenance-message {
            margin: 0 0 20px;
            color: #4b5563;
            font-size: 0.98rem;
            line-height: 1.7;
            max-width: 520px;
        }

        .maintenance-notes {
            margin: 0;
            padding-left: 18px;
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.75;
        }

        .maintenance-notes li {
            margin-bottom: 4px;
        }

        .maintenance-icon-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .maintenance-icon {
            width: min(100%, 520px);
            max-height: 72vh;
            object-fit: contain;
            filter: drop-shadow(0 22px 34px rgba(17, 24, 39, 0.18));
        }

        @media (max-width: 900px) {
            .maintenance-layout {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 18px;
            }

            .maintenance-copy {
                max-width: 100%;
                order: 2;
            }

            .maintenance-icon-wrap {
                order: 1;
            }

            .maintenance-chip {
                margin-left: auto;
                margin-right: auto;
            }

            .maintenance-message {
                margin-left: auto;
                margin-right: auto;
            }

            .maintenance-notes {
                display: inline-block;
                text-align: left;
            }

            .maintenance-icon {
                width: min(100%, 380px);
                max-height: 42vh;
            }
        }
    </style>
</head>
<body>
    @php
        $maintenanceIcon = file_exists(public_path('images/504 icon.png'))
            ? url('images/504%20icon.png')
            : (file_exists(public_path('images/503 icon.png')) ? url('images/503%20icon.png') : asset('images/knights_logo_icon.png'));
    @endphp

    <div class="maintenance-layout">
        <div class="maintenance-copy">
            <span class="maintenance-chip">System Notice</span>
            <h1 class="maintenance-code">503</h1>
            <h2 class="maintenance-title">The blacksmith is hard at work.</h2>
            <p class="maintenance-message">
                Smart Accounting is temporarily unavailable while we perform maintenance and reliability improvements.
                Please check back shortly.
            </p>
            <ul class="maintenance-notes">
                <li>Super admin access remains available for emergency work.</li>
                <li>If this lasts unusually long, please contact your system administrator.</li>
            </ul>
        </div>

        <div class="maintenance-icon-wrap">
            <img src="{{ $maintenanceIcon }}" alt="Maintenance Icon" class="maintenance-icon">
        </div>
    </div>
</body>
</html>
