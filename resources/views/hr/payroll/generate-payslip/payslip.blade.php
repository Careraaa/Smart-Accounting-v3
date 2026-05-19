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
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
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
            --earn-color: #000000;
            --ded-color: #000000;
            --page-width: 215.9mm;
            --page-height: 139.7mm;
            --font-body: 'Sora', sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        body {
            font-family: var(--font-body);
            font-size: 15px;
            color: var(--black);
            background: #efefef;
            line-height: 1.2;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .controls {
            max-width: var(--page-width);
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
        }

        .btn:hover { opacity: 0.8; }

        .btn-primary {
            background: var(--black);
            color: #fff;
        }

        .btn-ghost {
            background: #fff;
            color: var(--gray-dark);
            border-color: var(--gray-rule);
        }

        .page {
            background: var(--white);
            width: var(--page-width);
            height: var(--page-height);           
            margin: 0 auto 40px;
            box-shadow: 0 2px 24px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            display: flex;
            flex-direction: row;
        }

        .main-content {
            flex: 4;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .slip-header,
        .emp-strip {
            flex-shrink: 0;
        }

        .slip-header {
            padding: 8px 12px 6px;
            border-bottom: 1px solid var(--black);
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 6px;
        }

        .company-logo-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .company-logo-wrap img {
            width: 20px;
            height: 20px;
            object-fit: contain;
        }

        .company-name {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .slip-title {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: -0.04em;
        }

        .emp-strip {
            padding: 6px 12px;
            background: var(--gray-bg);
            border-bottom: 1px solid var(--black);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px 12px;
            font-size: 13px;
        }

        .emp-label {
            font-weight: 600;
            color: var(--black);
        }

        /* Body - Takes all remaining space */
        .slip-body {
            flex: 1;
            padding: 6px 8px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            overflow: hidden;
        }

        /* Two-column section grows to fill space */
        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px;
            border: 0.5px solid var(--black);
            flex: 1;                   
            min-height: 0;              
        }

        .two-col > div {
            border-right: 0.5px solid var(--black);
            padding: 5px;
            display: flex;
            flex-direction: column;
        }

        .two-col > div:last-child {
            border-right: none;
        }

        /* Tables inside columns take available space */
        .pay-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            flex: unset;
        }

        .pay-table thead th {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-light);
            padding: 1px 2px;
            border-bottom: 0.5px solid var(--black);
            text-align: left;
        }

        .pay-table thead th.right { text-align: right; }

        .pay-table td {
            padding: 1px 2px;
            color: var(--black);
            font-size: 13px;
            vertical-align: top;
        }

        .pay-table td.mono,
        .pay-table td:last-child {
            font-family: var(--font-mono);
            font-weight: 600;
            text-align: right;
            white-space: nowrap;
        }

        .pay-table td .td-sub {
            font-size: 13px;
            color: var(--black);
            display: block;
        }

        .pay-table td.dim {
            color: var(--black);
            font-style: italic;
        }

        /* Subtotal stays at bottom of each column */
        .subtotal-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 3px 2px;
            border-top: 0.5px solid var(--black);
            margin-top: auto;           /* Pushes to bottom */
            font-size: 13px;
        }

        .subtotal-row .st-label {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
        }

        .subtotal-row .st-amt {
            font-family: var(--font-mono);
            font-weight: 600;
        }

        .subtotal-row.earn * { color: var(--earn-color); }
        .subtotal-row.ded * { color: var(--ded-color); }

        /* Net Pay - Fixed at bottom */
        .net-pay-block {
            border: 1px solid var(--black);
            padding: 6px 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .np-label {
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--black);
        }

        .np-period {
            font-size: 15px;
            color: var(--black);
        }

        .np-amount {
            font-family: var(--font-mono);
            font-size: 16px;
            font-weight: 600;
            letter-spacing: -0.03em;
        }

        .np-words {
            font-size: 15px;
            color: var(--gray-light);
            font-style: italic;
            text-align: right;
        }

        .acknowledgement {
            flex: 0.8;
            border-left: 1px solid var(--black);
            padding: 3px 10px;
            display: flex;
            flex-direction: column;
            font-size: 13px;
            height: 100%;
        }

        /* Print Styles */
        @media print {
            body { background: #fff; margin: 0; padding: 0; }
            .controls { display: none !important; }

            .page {
                margin: 0;
                box-shadow: none;
                width: 215.9mm;
                height: 93.1mm;
            }
        }

        @page {
            size: A6 landscape;
            margin: 0mm;
        }
    </style>
</head>

<body>

    <div class="controls">
        <button class="btn btn-primary" onclick="window.print()">Print Payslip</button>
    </div>

    <div class="page">

        <!-- LEFT COLUMN - Main Payslip -->
        <div class="main-content">

            <!-- Header -->
            <div class="slip-header">
                <div class="company-logo-wrap">
                    <img src="{{ asset('images/knights_logo_icon.png') }}" alt="Logo">
                    <div class="company-name">Knights Transport Services Corporation</div>
                </div>
                <div>
                    <div class="slip-title">PAY SLIP</div>
                </div>
            </div>

            <!-- Employee Strip -->
            <div class="emp-strip">
                <div><span class="emp-label">Employee :</span> {{ $payroll->user->name ?? 'N/A' }}</div>
                <div><span class="emp-label">Days of Work :</span> <span style="font-family: var(--font-mono);">{{ $payroll->days_worked ?? 0 }}</span></div>
                <div><span class="emp-label">Pay Period :</span> {{ $payroll->payroll_period_start->format('m/d/Y') }} – {{ $payroll->payroll_period_end->format('m/d/Y') }}</div>
                <div><span class="emp-label">Days Present :</span> <span style="font-family: var(--font-mono);">{{ $payroll->days_worked ?? 0 }}</span></div>
            </div>

            <!-- Body - Now fully occupies remaining space -->
            <div class="slip-body">

                <div class="two-col">

                    <!-- Earnings Column -->
                    <div>
                        <div class="section-label">Earnings</div>
                        <table class="pay-table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th class="right mono">Hours</th>
                                    <th class="right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Basic Pay</td>
                                    <td class="mono right">{{ $payroll->hours_worked ?? 0 }}</td>
                                    <td class="mono">₱{{ number_format($payroll->basic_salary, 2) }}</td>
                                </tr>

                                @forelse ($payroll->allowances as $allow)
                                    <tr>
                                        <td>{{ $allow->allowance_type }}</td>
                                        <td class="mono right">{{ $allow->hours ?? '—' }}</td>
                                        <td class="mono">₱{{ number_format($allow->amount, 2) }}</td>
                                    </tr>
                                @empty @endforelse
                            </tbody>
                        </table>

                        <div class="subtotal-row earn">
                            <span class="st-label">Gross Pay</span>
                            <span class="st-amt">₱{{ number_format($payroll->gross_pay, 2) }}</span>
                        </div>
                    </div>

                    <!-- Deductions Column -->
                    <div>
                        <div class="section-label">Deductions</div>
                        <table class="pay-table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th class="right mono">Hours</th>
                                    <th class="right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payroll->deductions as $ded)
                                    <tr>
                                        <td>
                                            {{ $ded->deduction_type }}
                                            @if ($ded->description && $ded->deduction_type !== 'Cash Advance')
                                                <span class="td-sub">{{ $ded->description }}</span>
                                            @endif
                                        </td>
                                        <td class="mono right">{{ $ded->hours ?? '—' }}</td>
                                        <td class="mono">₱{{ number_format($ded->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td class="dim" colspan="3">No deductions this period</td></tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="subtotal-row ded">
                            <span class="st-label">Total Deductions</span>
                            <span class="st-amt">₱{{ number_format($payroll->total_deductions, 2) }}</span>
                        </div>
                    </div>

                </div>

                <!-- Net Pay (stays at bottom) -->
                <div class="net-pay-block">
                    <div>
                        <div class="np-label">Net Pay</div>
                    </div>
                    <div style="text-align: right;">
                        <div class="np-amount">₱{{ number_format($payroll->net_pay, 2) }}</div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Acknowledgement
        <div class="acknowledgement">
            <div style="line-height: 1.3; margin-top: 80px; margin-bottom: 10px; font-weight: 600; text-align: justify;">
                I ACKNOWLEDGE TO HAVE RECEIVED THE AMOUNT BELOW AND HAVE NO FURTHER CLAIMS FOR SERVICES RENDERED.
            </div>

            <div style="margin-bottom: 3px; font-size: 6.5px;">
                <div style="margin-bottom: 1px; display: flex; justify-content: space-between;">
                    <div style="font-weight: 600;">Pay Period :</div>
                    <div style="color: var(--black); text-align: right;">{{ $payroll->payroll_period_start->format('m/d/Y') }} – {{ $payroll->payroll_period_end->format('m/d/Y') }}</div>
                </div>
                <div style="margin-bottom: 1px; display: flex; justify-content: space-between;">
                    <div style="font-weight: 600;">Name:</div>
                    <div style="color: var(--black); text-align: right;">{{ $payroll->user->name ?? 'N/A' }}</div>
                </div>
                <div style="margin-bottom: 1px; display: flex; justify-content: space-between;">
                    <div style="font-weight: 600;">Total Earnings:</div>
                    <div style="font-family: var(--font-mono);">₱{{ number_format($payroll->gross_pay, 2) }}</div>
                </div>
                <div style="margin-bottom: 1px; display: flex; justify-content: space-between;">
                    <div style="font-weight: 600;">Total Deductions:</div>
                    <div style="font-family: var(--font-mono);">₱{{ number_format($payroll->total_deductions, 2) }}</div>
                </div>
                <div style="padding-top: 1px; border-top: 0.5px solid var(--black); display: flex; justify-content: space-between;">
                    <div style="font-weight: 700;">Net Pay :</div>
                    <div style="font-weight: 700; font-family: var(--font-mono); font-size: 7px;">₱{{ number_format($payroll->net_pay, 2) }}</div>
                </div>
            </div>

            <div style="margin-top: 50px; text-align: center;">
                <div style="font-weight: 600;">_____________</div>
                <div style="font-weight: 600;">Signature</div>
            </div>
        </div> -->

    </div>

</body>
</html>
