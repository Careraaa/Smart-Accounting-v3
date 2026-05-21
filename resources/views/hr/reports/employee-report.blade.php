<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Report – {{ date('M d, Y') }}</title>
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
            --page-w: 279.4mm;
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
            width: 279.4mm; /* Letter landscape width */
            min-height: 215.9mm; /* Letter landscape height */
            margin: 0 auto 40px;
            box-shadow: 0 2px 24px rgba(0, 0, 0, .08);
            padding: 12mm;
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

        /* Summary strip */
        .summary-strip {
            display: flex;
            gap: 24px;
            background: var(--gray-bg);
            border-radius: 6px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }

        .summary-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .summary-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--gray-mid);
            letter-spacing: 0.5px;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 700;
            color: var(--black);
        }

        .summary-divider {
            width: 1px;
            background: var(--gray-rule);
            margin: 0 4px;
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

        .badge-active {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            background: #dcfce7;
            color: #16a34a;
        }

        .badge-inactive {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            background: #fee2e2;
            color: #dc2626;
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
                width: 279.4mm;
                min-height: 215.9mm;
                margin: 0;
                box-shadow: none;
                padding: 12mm;
            }
        }

        @page {
            size: Letter landscape;
            margin: 12mm;
        }
    </style>
</head>

<body>

    {{-- Controls --}}
    <div class="controls">
        <button class="btn btn-primary" onclick="window.print()">Print Report</button>
    </div>

    <div class="page">

        {{-- Header --}}
        <div class="report-header">
            <div class="company-info">
                <img src="{{ asset('images/knights_logo_icon.png') }}" alt="Logo">
                <div>
                    <div class="company-name">Knights Transport Services Corporation</div>
                    <div class="company-sub">Human Resources</div>
                </div>
            </div>
            <div class="report-title">
                <h1>EMPLOYEES</h1>
            </div>
        </div>

        {{-- Table --}}
        <table>
            <thead>
                <tr>
                    <th><strong>Name</strong></th>
                    <th><strong>Gender</strong></th>
                    <th><strong>Position</strong></th>
                    <th><strong>Email</strong></th>
                    <th><strong>Contact Number</strong></th>
                    <th><strong>Department</strong></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                    <tr>
                        <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                        <td>{{ ucfirst($employee->gender ?? 'N/A') }}</td>
                        <td>{{ $employee->position ?? 'N/A' }}</td>
                        <td>{{ $employee->email ?? 'N/A' }}</td>
                        <td>{{ $employee->phone ?? 'N/A' }}</td>
                        <td>{{ $employee->department ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 20px; text-align: center;">No employees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>

<script>
    function goBackOrClose() {
        if (window.opener) {
            window.close();
            return;
        }

        if (window.history.length > 1) {
            window.history.back();
            return;
        }

        window.location.href = "{{ route('employees.index') }}";
    }
</script>

</html>