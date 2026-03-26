<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle & Route Report – {{ date('M d, Y') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --black: #0a0a0a;
            --gray-dark: #3a3a3a;
            --gray-mid: #707070;
            --gray-light: #b0b0b0;
            --gray-rule: #e0e0e0;
            --gray-bg: #f7f7f7;
            --white: #ffffff;
            --accent: #1a1a1a;
            --page-w: 900px;
            --font-body: 'Sora', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        body {
            font-family: var(--font-body);
            font-size: 12px;
            color: var(--black);
            background: #efefef;
            line-height: 1.55;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Controls */
        .controls {
            max-width: var(--page-w);
            margin: 28px auto 12px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 4px;
            font-family: var(--font-body);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: 1.5px solid transparent;
            transition: opacity .15s;
        }

        .btn:hover {
            opacity: .8;
        }

        .btn-primary {
            background: var(--black);
            color: #fff;
        }

        .btn-ghost {
            background: #fff;
            color: var(--gray-dark);
            border-color: var(--gray-rule);
        }

        /* Page */
        .page {
            background: var(--white);
            max-width: var(--page-w);
            margin: 0 auto 40px;
            box-shadow: 0 2px 24px rgba(0, 0, 0, .08);
            padding: 40px;
        }

        /* Header */
        .report-header {
            border-bottom: 2px solid var(--black);
            padding-bottom: 24px;
            margin-bottom: 28px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
        }

        .company-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .company-info img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .company-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--black);
        }

        .company-sub {
            font-size: 10px;
            font-weight: 400;
            color: var(--gray-mid);
            text-transform: uppercase;
            margin-top: 2px;
        }

        .report-title {
            text-align: right;
        }

        .report-title h1 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .report-period {
            font-size: 11px;
            color: var(--gray-mid);
        }

        /* Section */
        .section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1.5px solid var(--gray-rule);
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        thead {
            background: var(--gray-bg);
            border-bottom: 1.5px solid var(--gray-rule);
        }

        th {
            padding: 10px;
            text-align: left;
            font-weight: 600;
            color: var(--gray-dark);
        }

        td {
            padding: 10px;
            border-bottom: 1px solid var(--gray-rule);
        }

        tbody tr:hover {
            background: var(--gray-bg);
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .mono {
            font-family: var(--font-mono);
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
        }

        .badge-active {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Print */
        @media print {
            body {
                background: #fff;
            }

            .controls {
                display: none !important;
            }

            .page {
                margin: 0;
                box-shadow: none;
                max-width: 100%;
                padding: 12mm;
            }
        }

        @page {
            size: A4 landscape;
            margin: 12mm;
        }
    </style>
</head>

<body>

    {{-- Controls --}}
    <div class="controls">
        <a href="{{ route('vehicles.index') }}" class="btn btn-ghost">← Back</a>
        <button class="btn btn-primary" onclick="window.print()">Print Report</button>
    </div>

    <div class="page">

        {{-- Header --}}
        <div class="report-header">
            <div class="company-info">
                <img src="{{ asset('images/knights_logo_icon.png') }}" alt="Logo">
                <div>
                    <div class="company-name">Smart Accounting</div>
                    <div class="company-sub">Remittance Management</div>
                </div>
            </div>
            <div class="report-title">
                <h1>VEHICLES</h1>
                <div class="report-period">{{ date('M d, Y') }}</div>
            </div>
        </div>

        {{-- Vehicles Section --}}
        <div class="section">
            <h2 class="section-title">Active Vehicles</h2>
            <table>
                <thead>
                    <tr>
                        <th>Plate Number</th>
                        <th>Assigned Route</th>
                        <th>Operator</th>
                        <th>Boundary Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vehicleStats as $index => $vehicle)
                        @php
                            $route = $routeStats[$index] ?? null;
                        @endphp

                        <tr>
                            <td class="mono">{{ $vehicle['plate_number'] }}</td>
                            <td>{{ $vehicle['route'] }}</td>
                            <td>{{ $vehicle['operator'] }}</td>
                            <td>
                                ₱{{ number_format($route['boundary'] ?? 0, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center" style="padding: 20px;">
                                No active vehicles found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--gray-rule); text-align: center; font-size: 10px; color: var(--gray-mid);">
            <p>Report generated on {{ date('M d, Y \a\t h:i A') }}</p>
        </div>

    </div>

</body>

</html>
