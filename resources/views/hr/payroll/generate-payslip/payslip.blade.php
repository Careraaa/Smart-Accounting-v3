<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip – {{ $payroll->user->name }} –
        {{ $payroll->payroll_period_start->format('M d') }}–{{ $payroll->payroll_period_end->format('M d, Y') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">

    <style>
        /* ── Reset ─────────────────────────────────────── */
        *,
        *::before,
        *::after {
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
            --earn-color: #1a6b3a;
            --ded-color: #8b1a1a;
            --page-w: 720px;
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

        /* ── Controls ──────────────────────────────────── */
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

        /* ── Page ──────────────────────────────────────── */
        .page {
            background: var(--white);
            max-width: var(--page-w);
            margin: 0 auto 40px;
            box-shadow: 0 2px 24px rgba(0, 0, 0, .08);
        }

        /* ── Header ────────────────────────────────────── */
        .slip-header {
            padding: 32px 40px 26px;
            border-bottom: 2px solid var(--black);
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
        }

        .company-logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .company-logo-wrap img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        .company-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--black);
            letter-spacing: -.02em;
            line-height: 1.2;
        }

        .company-sub {
            font-size: 10px;
            font-weight: 400;
            color: var(--gray-mid);
            letter-spacing: .05em;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .slip-title-block {
            text-align: right;
        }

        .slip-title-block .slip-title {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -.04em;
            color: var(--black);
            line-height: 1;
        }

        .slip-title-block .slip-period {
            font-family: var(--font-mono);
            font-size: 10.5px;
            color: var(--gray-mid);
            margin-top: 5px;
        }

        /* ── Employee Strip ────────────────────────────── */
        .emp-strip {
            padding: 18px 40px;
            background: var(--gray-bg);
            border-bottom: 1px solid var(--gray-rule);
            display: flex;
            gap: 0;
            flex-wrap: wrap;
        }

        .emp-col {
            flex: 1;
            min-width: 140px;
            padding-right: 20px;
        }

        .emp-label {
            font-size: 9.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--gray-light);
            margin-bottom: 3px;
        }

        .emp-val {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--black);
        }

        .emp-val.mono {
            font-family: var(--font-mono);
            font-size: 11.5px;
        }

        /* ── Body ──────────────────────────────────────── */
        .slip-body {
            padding: 28px 40px 32px;
        }

        /* ── Section Label ─────────────────────────────── */
        .section-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .15em;
            color: var(--gray-light);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--gray-rule);
        }

        /* ── Pay Table ─────────────────────────────────── */
        .pay-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .pay-table thead tr th {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--gray-light);
            padding: 0 10px 8px;
            border-bottom: 1px solid var(--gray-rule);
            text-align: left;
        }

        .pay-table thead tr th.right {
            text-align: right;
        }

        .pay-table tbody tr td {
            padding: 8px 10px;
            border-bottom: 1px solid var(--gray-rule);
            color: var(--gray-dark);
            vertical-align: middle;
        }

        .pay-table tbody tr:last-child td {
            border-bottom: none;
        }

        .pay-table td.mono {
            font-family: var(--font-mono);
            font-size: 11.5px;
            font-weight: 500;
            text-align: right;
            white-space: nowrap;
        }

        .pay-table td.right {
            text-align: right;
        }

        .pay-table td .td-sub {
            display: block;
            font-size: 9.5px;
            color: var(--gray-light);
            margin-top: 1px;
        }

        .pay-table td.dim {
            color: var(--gray-light);
            font-style: italic;
        }

        /* ── Subtotal Row ──────────────────────────────── */
        .subtotal-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 10px;
            border-top: 1.5px solid var(--black);
            margin-top: 2px;
        }

        .subtotal-row .st-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--black);
        }

        .subtotal-row .st-amt {
            font-family: var(--font-mono);
            font-size: 13px;
            font-weight: 600;
            color: var(--black);
        }

        .subtotal-row.earn .st-label,
        .subtotal-row.earn .st-amt {
            color: var(--earn-color);
        }

        .subtotal-row.ded .st-label,
        .subtotal-row.ded .st-amt {
            color: var(--ded-color);
        }

        /* ── Two-col layout ────────────────────────────── */
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            margin-bottom: 28px;
        }

        /* ── Government Contributions ──────────────────── */
        .gov-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11.5px;
            margin-bottom: 4px;
        }

        .gov-table td {
            padding: 7px 10px;
            border-bottom: 1px solid var(--gray-rule);
            color: var(--gray-dark);
        }

        .gov-table tr:last-child td {
            border-bottom: none;
        }

        .gov-table td.gov-name {
            font-weight: 600;
            color: var(--black);
        }

        .gov-table td.gov-num {
            font-family: var(--font-mono);
            font-size: 10.5px;
            color: var(--gray-mid);
        }

        .gov-table td.gov-status {
            text-align: right;
        }

        .badge-enrolled {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 2px;
            background: #e8f5ee;
            color: var(--earn-color);
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .badge-na {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 2px;
            background: var(--gray-bg);
            color: var(--gray-light);
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        /* ── Net Pay ───────────────────────────────────── */
        .net-pay-block {
            margin-top: 24px;
            border: 2px solid var(--black);
            padding: 22px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .net-pay-block .np-left .np-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .15em;
            color: var(--gray-mid);
        }

        .net-pay-block .np-left .np-period {
            font-size: 11px;
            color: var(--gray-mid);
            margin-top: 3px;
        }

        .net-pay-block .np-right .np-amount {
            font-family: var(--font-mono);
            font-size: 32px;
            font-weight: 600;
            color: var(--black);
            letter-spacing: -.03em;
        }

        .net-pay-block .np-right .np-words {
            font-size: 9.5px;
            color: var(--gray-light);
            text-align: right;
            margin-top: 3px;
            font-style: italic;
        }

        /* ── Signatures ────────────────────────────────── */
        .sig-row {
            display: flex;
            gap: 20px;
            margin-top: 32px;
        }

        .sig-col {
            flex: 1;
        }

        .sig-space {
            height: 36px;
        }

        .sig-line {
            border-top: 1px solid var(--black);
            padding-top: 5px;
        }

        .sig-name {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--black);
        }

        .sig-role {
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--gray-light);
            margin-top: 1px;
        }

        /* ── Footer ────────────────────────────────────── */
        .slip-footer {
            margin-top: 0;
            padding: 12px 40px;
            border-top: 1px solid var(--gray-rule);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .footer-note {
            font-size: 9.5px;
            color: var(--gray-light);
            font-style: italic;
        }

        .footer-id {
            font-family: var(--font-mono);
            font-size: 9.5px;
            color: var(--gray-light);
        }

        /* ── Empty state ───────────────────────────────── */
        .empty-td {
            text-align: center;
            font-size: 10.5px;
            font-style: italic;
            color: var(--gray-light);
            padding: 10px 10px !important;
        }

        /* ── Print ─────────────────────────────────────── */
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
            }
        }

        @page {
            size: A4;
            margin: 14mm 12mm;
        }
    </style>
</head>

<body>

    {{-- Controls --}}
    <div class="controls">
        <a href="{{ route('payroll.salary-computation.show', $payroll) }}" class="btn btn-ghost">← Back to Payroll</a>
        <button class="btn btn-primary" onclick="window.print()">Print Payslip</button>
    </div>

    <div class="page">

        {{-- ── HEADER ──────────────────────────────── --}}
        <div class="slip-header">
            <div class="company-logo-wrap">
                <img src="{{ asset('images/knights_logo_icon.png') }}" alt="Logo">
                <div>
                    <div class="company-name">Smart Accounting</div>
                    <div class="company-sub">Human Resources &amp; Payroll</div>
                </div>
            </div>
            <div class="slip-title-block">
                <div class="slip-title">PAYSLIP</div>
                <div class="slip-period">
                    {{ $payroll->payroll_period_start->format('M d, Y') }} –
                    {{ $payroll->payroll_period_end->format('M d, Y') }}
                </div>
            </div>
        </div>

        {{-- ── EMPLOYEE STRIP ──────────────────────── --}}
        <div class="emp-strip">
            <div class="emp-col">
                <div class="emp-label">Employee Name</div>
                <div class="emp-val">{{ $payroll->user->name ?? 'N/A' }}</div>
            </div>
            <div class="emp-col">
                <div class="emp-label">Employee ID</div>
                <div class="emp-val mono">#{{ str_pad($payroll->user_id, 5, '0', STR_PAD_LEFT) }}</div>
            </div>
            <div class="emp-col">
                <div class="emp-label">Position</div>
                <div class="emp-val">{{ $payroll->user->position ?? '—' }}</div>
            </div>
            <div class="emp-col">
                <div class="emp-label">Department</div>
                <div class="emp-val">{{ $payroll->user->department ?? '—' }}</div>
            </div>
            <div class="emp-col">
                <div class="emp-label">Status</div>
                <div class="emp-val" style="text-transform: capitalize;">{{ $payroll->status }}</div>
            </div>
        </div>

        {{-- ── BODY ────────────────────────────────── --}}
        <div class="slip-body">

            <div class="two-col">

                {{-- ── EARNINGS ─────────────────────── --}}
                <div>
                    <div class="section-label">Earnings</div>
                    <table class="pay-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="right">Units</th>
                                <th class="right">Rate</th>
                                <th class="right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Basic Pay row --}}
                            <tr>
                                <td>Basic Pay</td>
                                <td class="mono">{{ $payroll->days_worked ?? 0 }} days</td>
                                <td class="mono">₱{{ number_format($payroll->per_day_rate, 2) }}</td>
                                <td class="mono">₱{{ number_format($payroll->basic_salary, 2) }}</td>
                            </tr>

                            {{-- Allowances / Overtime / Adjustments --}}
                            @forelse ($payroll->allowances as $allow)
                                <tr>
                                    <td>{{ $allow->allowance_type }}</td>
                                    <td class="mono">—</td>
                                    <td class="mono">—</td>
                                    <td class="mono">₱{{ number_format($allow->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="dim">Overtime</td>
                                    <td class="mono dim">0.00 hrs</td>
                                    <td class="mono dim">—</td>
                                    <td class="mono dim">—</td>
                                </tr>
                                <tr>
                                    <td class="dim">Adjustment</td>
                                    <td class="mono dim">—</td>
                                    <td class="mono dim">—</td>
                                    <td class="mono dim">—</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="subtotal-row earn">
                        <span class="st-label">Gross Pay</span>
                        <span class="st-amt">₱{{ number_format($payroll->gross_pay, 2) }}</span>
                    </div>
                </div>

                {{-- ── DEDUCTIONS ────────────────────── --}}
                <div>
                    <div class="section-label">Deductions</div>
                    <table class="pay-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payroll->deductions as $ded)
                                <tr>
                                    <td>
                                        {{ $ded->deduction_type }}
                                        @if ($ded->description)
                                            <span class="td-sub">{{ $ded->description }}</span>
                                        @endif
                                    </td>
                                    <td class="mono">₱{{ number_format($ded->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="dim" colspan="2">No deductions this period</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="subtotal-row ded">
                        <span class="st-label">Total Deductions</span>
                        <span class="st-amt">₱{{ number_format($payroll->total_deductions, 2) }}</span>
                    </div>
                </div>

            </div>{{-- /two-col --}}

            {{-- ── GOVERNMENT CONTRIBUTIONS ──────────── --}}
            <div class="section-label">Government Contributions</div>
            <table class="gov-table" style="margin-bottom:28px;">
                <tbody>
                    <tr>
                        <td class="gov-name">SSS</td>
                        <td class="gov-num">{{ $payroll->user->sss_number ?? '—' }}</td>
                        <td class="gov-status">
                            @if ($payroll->user->has_sss)
                                <span class="badge-enrolled">Enrolled</span>
                            @else
                                <span class="badge-na">N/A</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="gov-name">Pag-IBIG</td>
                        <td class="gov-num">{{ $payroll->user->pagibig_number ?? '—' }}</td>
                        <td class="gov-status">
                            @if ($payroll->user->has_pagibig)
                                <span class="badge-enrolled">Enrolled</span>
                            @else
                                <span class="badge-na">N/A</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="gov-name">TIN</td>
                        <td class="gov-num">{{ $payroll->user->tin_number ?? '—' }}</td>
                        <td class="gov-status">
                            @if ($payroll->user->has_tin)
                                <span class="badge-enrolled">Enrolled</span>
                            @else
                                <span class="badge-na">N/A</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="gov-name">PhilHealth</td>
                        <td class="gov-num">{{ $payroll->user->philhealth_number ?? '—' }}</td>
                        <td class="gov-status">
                            @if ($payroll->user->has_philhealth ?? false)
                                <span class="badge-enrolled">Enrolled</span>
                            @else
                                <span class="badge-na">N/A</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- ── NET PAY ───────────────────────────── --}}
            <div class="net-pay-block">
                <div class="np-left">
                    <div class="np-label">Net Pay</div>
                    <div class="np-period">
                        {{ $payroll->payroll_period_start->format('M d') }} –
                        {{ $payroll->payroll_period_end->format('M d, Y') }}
                    </div>
                </div>
                <div class="np-right">
                    <div class="np-amount">₱{{ number_format($payroll->net_pay, 2) }}</div>
                    <div class="np-words">Philippine Peso</div>
                </div>
            </div>

            {{-- ── SIGNATURES ────────────────────────── --}}
            <div class="sig-row">
                <div class="sig-col">
                    <div class="sig-space"></div>
                    <div class="sig-line">
                        <div class="sig-name">{{ $payroll->user->name ?? '' }}</div>
                        <div class="sig-role">Employee — Received By</div>
                    </div>
                </div>
                <div class="sig-col">
                    <div class="sig-space"></div>
                    <div class="sig-line">
                        <div class="sig-name">{{ $payroll->approvedBy?->name ?? '&nbsp;' }}</div>
                        <div class="sig-role">Approved By</div>
                    </div>
                </div>
                <div class="sig-col">
                    <div class="sig-space"></div>
                    <div class="sig-line">
                        <div class="sig-name">&nbsp;</div>
                        <div class="sig-role">HR Officer</div>
                    </div>
                </div>
            </div>

        </div>{{-- /slip-body --}}

        {{-- ── FOOTER ──────────────────────────────── --}}
        <div class="slip-footer">
            <span class="footer-note">System-generated payslip. Please retain for your records.</span>
            <span class="footer-id">
                #{{ str_pad($payroll->id, 6, '0', STR_PAD_LEFT) }} &nbsp;·&nbsp; {{ now()->format('M d, Y h:i A') }}
            </span>
        </div>

    </div>{{-- /page --}}

</body>

</html>
