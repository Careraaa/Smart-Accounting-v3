<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Report – {{ date('M d, Y') }}</title>
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

        .btn:hover { opacity: .8; }
        .btn-primary { background: var(--black); color: #fff; }
        .btn-ghost   { background: #fff; color: var(--gray-dark); border-color: var(--gray-rule); }

        /* Page */
        .page {
            background: var(--white);
            max-width: var(--page-w);
            margin: 0 auto 40px;
            box-shadow: 0 2px 24px rgba(0,0,0,.08);
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

        .company-info { display: flex; align-items: center; gap: 12px; }
        .company-info img { width: 40px; height: 40px; object-fit: contain; }
        .company-name { font-size: 15px; font-weight: 700; color: var(--black); }
        .company-sub  { font-size: 10px; font-weight: 400; color: var(--gray-mid); text-transform: uppercase; margin-top: 2px; }

        .report-title { text-align: right; }
        .report-title h1 { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
        .report-period { font-size: 11px; color: var(--gray-mid); }

        /* Table */
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        thead { background: var(--gray-bg); border-bottom: 1.5px solid var(--gray-rule); }
        th { padding: 10px; text-align: left; font-weight: 600; color: var(--gray-dark); }
        th.text-right, td.text-right { text-align: right; }
        td { padding: 10px; border-bottom: 1px solid var(--gray-rule); vertical-align: middle; }
        tbody tr:hover { background: var(--gray-bg); }

        .mono { font-family: var(--font-mono); }

        /* Print */
        @media print {
            body { background: #fff; }
            .controls { display: none !important; }
            .page { margin: 0; box-shadow: none; max-width: 100%; padding: 12mm; }
        }

        @page { size: A4 landscape; margin: 12mm; }
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
                <h1>PAYROLL REPORT</h1>
                <div class="report-period">
                    @if($period === 'weekly')
                        Week {{ $week }}, {{ $year }}
                    @elseif($period === 'monthly')
                        {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}
                    @else
                        {{ $year }}
                    @endif
                </div>
            </div>
        </div>

        @php
            $totalBatches    = count($batchData);
            $totalGross      = collect($batchData)->sum('total_gross');
            $totalDeductions = collect($batchData)->sum('total_deductions');
            $totalNet        = collect($batchData)->sum('total_net');
        @endphp

        {{-- Table --}}
        <table>
            <thead>
                <tr>
                    <th>Period</th>
                    <th class="text-right">Employees</th>
                    <th class="text-right">Gross</th>
                    <th class="text-right">Deductions</th>
                    <th class="text-right">Net Pay</th>
                </tr>
            </thead>
            <tbody>
                @forelse($batchData as $batch)
                    <tr>
                        <td>
                            {{ \Carbon\Carbon::parse($batch['period_start'])->format('M d') }} –
                            {{ \Carbon\Carbon::parse($batch['period_end'])->format('M d, Y') }}
                        </td>
                        <td class="text-right"><strong>{{ $batch['count'] }}</strong></td>
                        <td class="text-right mono">₱{{ number_format($batch['total_gross'], 2) }}</td>
                        <td class="text-right mono" style="color: #dc2626;">₱{{ number_format($batch['total_deductions'], 2) }}</td>
                        <td class="text-right mono"><strong style="color: #16a34a;">₱{{ number_format($batch['total_net'], 2) }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 20px; text-align: center;">No approved payroll batches found for this period.</td>
                    </tr>
                @endforelse
            </tbody>
            @if($totalBatches > 0)
            <tfoot>
                <tr style="border-top: 2px solid #e0e0e0; background: #f7f7f7; font-weight: 700;">
                    <td style="padding: 10px;">TOTAL</td>
                    <td class="text-right" style="padding: 10px;"></td>
                    <td class="text-right mono" style="padding: 10px;">₱{{ number_format($totalGross, 2) }}</td>
                    <td class="text-right mono" style="padding: 10px; color: #dc2626;">₱{{ number_format($totalDeductions, 2) }}</td>
                    <td class="text-right mono" style="padding: 10px; color: #16a34a;">₱{{ number_format($totalNet, 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

</body>
</html>