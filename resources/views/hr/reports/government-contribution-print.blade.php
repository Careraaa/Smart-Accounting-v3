<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Government Contribution Summary – {{ date('M d, Y') }}</title>
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
                <h1>CONTRIBUTION SUMMARY</h1>
                <div class="report-period">
                    {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} – {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                </div>
            </div>
        </div>

        {{-- Summary by Type Table --}}
        <table>
            <thead>
                <tr>
                    <th>Contribution Type</th>
                    <th class="text-right">Employee</th>
                    <th class="text-right">Employer</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>SSS</strong></td>
                    <td class="text-right mono">₱{{ number_format($summary['employee_sss'] ?? 0, 2) }}</td>
                    <td class="text-right mono accent-purple">₱{{ number_format($summary['employer_sss'] ?? 0, 2) }}</td>
                    <td class="text-right mono accent-green"><strong>₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employer_sss'] ?? 0), 2) }}</strong></td>
                </tr>
                <tr>
                    <td><strong>Pag-IBIG</strong></td>
                    <td class="text-right mono">₱{{ number_format($summary['employee_pagibig'] ?? 0, 2) }}</td>
                    <td class="text-right mono accent-purple">₱{{ number_format($summary['employer_pagibig'] ?? 0, 2) }}</td>
                    <td class="text-right mono accent-green"><strong>₱{{ number_format(($summary['employee_pagibig'] ?? 0) + ($summary['employer_pagibig'] ?? 0), 2) }}</strong></td>
                </tr>
                <tr>
                    <td><strong>PhilHealth</strong></td>
                    <td class="text-right mono">₱{{ number_format($summary['employee_philhealth'] ?? 0, 2) }}</td>
                    <td class="text-right mono accent-purple">₱{{ number_format($summary['employer_philhealth'] ?? 0, 2) }}</td>
                    <td class="text-right mono accent-green"><strong>₱{{ number_format(($summary['employee_philhealth'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</strong></td>
                </tr>
                <tr class="total-row">
                    <td><strong>TOTAL</strong></td>
                    <td class="text-right mono"><strong>₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employee_pagibig'] ?? 0) + ($summary['employee_philhealth'] ?? 0), 2) }}</strong></td>
                    <td class="text-right mono"><strong>₱{{ number_format(($summary['employer_sss'] ?? 0) + ($summary['employer_pagibig'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</strong></td>
                    <td class="text-right mono accent-green"><strong>₱{{ number_format(($summary['employee_sss'] ?? 0) + ($summary['employee_pagibig'] ?? 0) + ($summary['employee_philhealth'] ?? 0) + ($summary['employer_sss'] ?? 0) + ($summary['employer_pagibig'] ?? 0) + ($summary['employer_philhealth'] ?? 0), 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- {{-- Detailed Breakdown --}}
        <div class="section-title">Detailed Breakdown by Employee</div>
        <table>
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Payroll Period</th>
                    <th class="text-right">Employee SSS</th>
                    <th class="text-right">Employer SSS</th>
                    <th class="text-right">Employee Pag-IBIG</th>
                    <th class="text-right">Employer Pag-IBIG</th>
                    <th class="text-right">Employee PhilHealth</th>
                    <th class="text-right">Employer PhilHealth</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contributions as $contribution)
                    <tr>
                        <td><strong>{{ $contribution['employee_name'] }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($contribution['period_start'])->format('M d') }} – {{ \Carbon\Carbon::parse($contribution['period_end'])->format('M d, Y') }}</td>
                        <td class="text-right mono">₱{{ number_format($contribution['employee_sss'] ?? 0, 2) }}</td>
                        <td class="text-right mono accent-purple">₱{{ number_format($contribution['employer_sss'] ?? 0, 2) }}</td>
                        <td class="text-right mono">₱{{ number_format($contribution['employee_pagibig'] ?? 0, 2) }}</td>
                        <td class="text-right mono accent-purple">₱{{ number_format($contribution['employer_pagibig'] ?? 0, 2) }}</td>
                        <td class="text-right mono">₱{{ number_format($contribution['employee_philhealth'] ?? 0, 2) }}</td>
                        <td class="text-right mono accent-purple">₱{{ number_format($contribution['employer_philhealth'] ?? 0, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 20px; text-align: center;">No contribution records found for the selected period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table> -->
    </div>

    <script>
        function updateReport() {
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            const employeeId = document.getElementById('employeeId').value;

            let url = '{{ route("reports.government-contribution") }}?date_from=' + dateFrom + '&date_to=' + dateTo;
            if (employeeId) url += '&employee_id=' + employeeId;

            window.location.href = url;
        }

        function resetFilters() {
            document.getElementById('dateFrom').value = '{{ now()->startOfMonth()->format('Y-m-d') }}';
            document.getElementById('dateTo').value = '{{ now()->format('Y-m-d') }}';
            document.getElementById('employeeId').value = '';
            window.location.href = '{{ route("reports.government-contribution") }}';
        }
    </script>

</body>

</html>
