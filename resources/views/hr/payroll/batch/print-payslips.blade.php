<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $batch->display_name }} — Employee Payslips</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            color: #172033;
            background: #eef1f5;
            font: 10px/1.35 Arial, sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .toolbar {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 16px;
        }
        .toolbar button {
            border: 0;
            border-radius: 5px;
            padding: 10px 16px;
            color: #fff;
            background: #172033;
            font-weight: 700;
            cursor: pointer;
        }
        .slip {
            width: 277mm;
            min-height: 190mm;
            margin: 0 auto 16px;
            padding: 10mm;
            background: #fff;
            box-shadow: 0 2px 12px #00000018;
            break-after: page;
            page-break-after: always;
        }
        .slip:last-child { break-after: auto; page-break-after: auto; }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 8px;
            border-bottom: 2px solid #172033;
        }
        .company { display: flex; align-items: center; gap: 8px; }
        .company img { width: 28px; height: 28px; object-fit: contain; }
        .company-name { font-size: 14px; font-weight: 700; }
        .slip-title { font-size: 18px; font-weight: 700; letter-spacing: .06em; }
        .employee {
            display: grid;
            grid-template-columns: 1.3fr 1fr 1fr 1fr;
            gap: 8px 16px;
            padding: 10px 0;
            border-bottom: 1px solid #cbd2dc;
        }
        .field-label {
            display: block;
            margin-bottom: 2px;
            color: #667085;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        .field-value { font-size: 11px; font-weight: 600; }
        .columns { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 10px; }
        .section { border: 1px solid #cbd2dc; }
        .section-title {
            padding: 6px 8px;
            background: #f3f5f8;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 4px 8px; text-align: left; border-top: 1px solid #e5e8ed; }
        th { color: #667085; font-size: 8px; text-transform: uppercase; }
        td.amount, th.amount { text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums; }
        .summary {
            display: flex;
            justify-content: space-between;
            padding: 6px 8px;
            border-top: 1px solid #cbd2dc;
            font-weight: 700;
        }
        .net-pay {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            padding: 10px 12px;
            border: 2px solid #172033;
        }
        .net-pay strong { font-size: 12px; text-transform: uppercase; letter-spacing: .08em; }
        .net-pay .amount { font-size: 18px; font-weight: 700; font-variant-numeric: tabular-nums; }
        .footer { margin-top: 7px; color: #667085; text-align: right; font-size: 8px; }
        @page { size: letter landscape; margin: 0; }
        @media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .slip {
                width: 100%;
                min-height: 0;
                height: 190mm;
                margin: 0;
                padding: 10mm;
                box-shadow: none;
                overflow: hidden;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Print All Payslips</button>
        <button type="button" onclick="window.close()">Close</button>
    </div>

    @foreach ($batch->payrolls as $payroll)
        <article class="slip">
            <header class="header">
                <div class="company">
                    <img src="{{ asset('images/knights-icon.png') }}" alt="">
                    <div class="company-name">Knights Transport Services Corporation</div>
                </div>
                <div class="slip-title">PAY SLIP</div>
            </header>

            <section class="employee">
                <div><span class="field-label">Employee</span><span class="field-value">{{ $payroll->user->name ?? 'N/A' }}</span></div>
                <div><span class="field-label">Position</span><span class="field-value">{{ $payroll->user->position ?? '—' }}</span></div>
                <div><span class="field-label">Pay Period</span><span class="field-value">{{ $payroll->payroll_period_start->format('m/d/Y') }} – {{ $payroll->payroll_period_end->format('m/d/Y') }}</span></div>
                <div><span class="field-label">Days Present / Scheduled</span><span class="field-value">{{ $payroll->days_worked ?? 0 }} / {{ $payroll->total_weekdays ?? 0 }}</span></div>
            </section>

            <div class="columns">
                <section class="section">
                    <div class="section-title">Earnings</div>
                    <table>
                        <thead><tr><th>Description</th><th class="amount">Amount</th></tr></thead>
                        <tbody>
                            <tr><td>Basic Pay</td><td class="amount">₱{{ number_format($payroll->basic_salary ?? 0, 2) }}</td></tr>
                            @foreach (($payroll->holiday_breakdown ?? []) as $holiday)
                                <tr><td>{{ $holiday['label'] ?? 'Holiday Pay' }}</td><td class="amount">₱{{ number_format($holiday['amount'] ?? 0, 2) }}</td></tr>
                            @endforeach
                            @if (($payroll->holiday_ot_pay ?? 0) > 0)
                                <tr><td>Holiday Overtime Pay</td><td class="amount">₱{{ number_format($payroll->holiday_ot_pay, 2) }}</td></tr>
                            @endif
                            @foreach ($payroll->allowances as $allowance)
                                <tr><td>{{ $allowance->allowance_type }}@if ($allowance->hours) ({{ $allowance->hours }} hrs)@endif</td><td class="amount">₱{{ number_format($allowance->amount, 2) }}</td></tr>
                            @endforeach
                            @foreach ($payroll->bonuses as $bonus)
                                <tr><td>{{ ucfirst($bonus->bonus_type) }}{{ $bonus->description ? ' — ' . $bonus->description : '' }}</td><td class="amount">₱{{ number_format($bonus->amount, 2) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="summary"><span>Gross Pay</span><span>₱{{ number_format($payroll->gross_pay, 2) }}</span></div>
                </section>

                <section class="section">
                    <div class="section-title">Deductions</div>
                    <table>
                        <thead><tr><th>Description</th><th class="amount">Amount</th></tr></thead>
                        <tbody>
                            @php $cashAdvanceDeduction = (float) ($payroll->cash_advance_deduction ?? 0); @endphp
                            @if ($cashAdvanceDeduction > 0)
                                <tr><td>Cash Advance</td><td class="amount">₱{{ number_format($cashAdvanceDeduction, 2) }}</td></tr>
                            @endif
                            @forelse ($payroll->deductions as $deduction)
                                <tr>
                                    <td>{{ $deduction->deduction_type === 'Late Deduction' ? 'Tardiness' : $deduction->deduction_type }}@if ($deduction->description && !in_array($deduction->deduction_type, ['Cash Advance', 'Late Deduction'], true))<br><span>{{ $deduction->description }}</span>@endif</td>
                                    <td class="amount">₱{{ number_format($deduction->amount, 2) }}</td>
                                </tr>
                            @empty
                                @if ($cashAdvanceDeduction <= 0)
                                    <tr><td colspan="2">No deductions this period</td></tr>
                                @endif
                            @endforelse
                        </tbody>
                    </table>
                    <div class="summary"><span>Total Deductions</span><span>₱{{ number_format($payroll->total_deductions, 2) }}</span></div>
                </section>
            </div>

            <div class="net-pay"><strong>Net Pay</strong><span class="amount">₱{{ number_format($payroll->net_pay, 2) }}</span></div>
            <div class="footer">{{ $batch->display_name }} · Employee {{ $loop->iteration }} of {{ $loop->count }}</div>
        </article>
    @endforeach
    <script>
        window.addEventListener('load', () => window.setTimeout(() => window.print(), 300));
    </script>
</body>
</html>
