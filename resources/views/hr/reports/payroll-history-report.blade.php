<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslips Issued – {{ date('M d, Y') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights-icon.png') }}">

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

        th.text-right,
        td.text-right {
            text-align: right;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid var(--gray-rule);
            vertical-align: middle;
        }

        tbody tr:hover {
            background: var(--gray-bg);
        }

        .batch-pill {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }

        .batch-1 {
            background: #f5e6f0;
            color: #8B3A62;
        }

        .batch-2 {
            background: #f9f3eb;
            color: #B8860B;
        }

        .mono {
            font-family: var(--font-mono);
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
                <img src="{{ asset('images/knights-icon.png') }}" alt="Logo" class="rounded-lg">
                <div>
                    <div class="company-name">Smart Accounting</div>
                    <div class="company-sub">Payroll Management</div>
                </div>
            </div>
            <div class="report-title">
                <h1>PAYSLIPS ISSUED</h1>
                <div class="report-period">{{ date('M d, Y') }}</div>
            </div>
        </div>

        @php
            $totalBasicSalary = $payrolls->sum('basic_salary');
            $totalAllowances  = $payrolls->sum('total_allowances');
        @endphp

        {{-- Summary Strip --}}
        <div class="summary-strip">
            <div class="summary-item">
                <span class="summary-label">Total Employees</span>
                <span class="summary-value">{{ $totalEmployees }}</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-item">
                <span class="summary-label">Gross Pay</span>
                <span class="summary-value">₱{{ number_format($totalGrossPay, 2) }}</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-item">
                <span class="summary-label">Deductions</span>
                <span class="summary-value" style="color: #dc2626;">₱{{ number_format($totalDeductions, 2) }}</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-item">
                <span class="summary-label">Total Net Pay</span>
                <span class="summary-value" style="color: #16a34a;">₱{{ number_format($totalNetPay, 2) }}</span>
            </div>
        </div>

        {{-- Table --}}
        <table>
            <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Payroll Period</th>
                    <th class="text-right">Basic Salary</th>
                    <th class="text-right">Allowances</th>
                    <th class="text-right">Gross Pay</th>
                    <th class="text-right">Deductions</th>
                    <th class="text-right">Net Pay</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payrolls as $payroll)
                    <tr>
                        <td>{{ $payroll->user->first_name ?? '' }} {{ $payroll->user->last_name ?? '' }}</td>
                        <td>{{ $payroll->payroll_period_start->format('M d') }} – {{ $payroll->payroll_period_end->format('M d, Y') }}</td>
                        <td class="text-right mono">₱{{ number_format($payroll->basic_salary, 2) }}</td>
                        <td class="text-right mono">₱{{ number_format($payroll->total_allowances, 2) }}</td>
                        <td class="text-right mono"><strong>₱{{ number_format($payroll->gross_pay, 2) }}</strong></td>
                        <td class="text-right mono" style="color: #dc2626;">₱{{ number_format($payroll->total_deductions, 2) }}</td>
                        <td class="text-right mono"><strong style="color: #16a34a;">₱{{ number_format($payroll->net_pay, 2) }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 20px; text-align: center;">No payslips found.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="border-top: 2px solid var(--gray-dark); background: var(--gray-bg); font-weight: 700; color: var(--black);">
                    <td style="padding: 10px; font-size: 13px;">TOTAL</td>
                    <td style="padding: 10px;"></td>
                    <td class="text-right mono" style="padding: 10px; font-size: 13px;">₱{{ number_format($totalBasicSalary, 2) }}</td>
                    <td class="text-right mono" style="padding: 10px; font-size: 13px;">₱{{ number_format($totalAllowances, 2) }}</td>
                    <td class="text-right mono" style="padding: 10px; font-size: 13px;">₱{{ number_format($totalGrossPay, 2) }}</td>
                    <td class="text-right mono" style="padding: 10px; font-size: 13px; color: #dc2626;">₱{{ number_format($totalDeductions, 2) }}</td>
                    <td class="text-right mono" style="padding: 10px; font-size: 13px; color: #16a34a;">₱{{ number_format($totalNetPay, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Footer --}}
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--gray-rule); text-align: center; font-size: 10px; color: var(--gray-mid);">
            <p>Report generated on {{ date('M d, Y \a\t h:i A') }}</p>
        </div>

    </div>

</body>

</html>