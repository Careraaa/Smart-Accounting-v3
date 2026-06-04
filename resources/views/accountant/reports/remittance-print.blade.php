<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remittance Report – {{ $period }} – {{ date('M d, Y') }}</title>
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

        /* Signatures */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid var(--gray-rule);
        }

        .signature-block {
            text-align: center;
            min-width: 200px;
        }

        .signature-label { font-size: 9px; font-weight: 600; color: var(--gray-mid); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px; }
        .signature-name { font-size: 14px; font-weight: 700; color: var(--black); }
        .signature-title { font-size: 9px; color: var(--gray-mid); margin-top: 4px; }

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
        <a href="{{ route('reports.remittance') }}" class="btn btn-ghost">← Back</a>
        <button class="btn btn-primary" onclick="window.print()">Print Report</button>
    </div>

    <div class="page">

        {{-- Header --}}
        <div class="report-header">
            <div class="company-info">
                <img src="{{ asset('images/knights-icon.png') }}" alt="Logo" class="rounded-lg">
                <div>
                    <div class="company-name">Smart Accounting</div>
                    <div class="company-sub">Remittance Management</div>
                </div>
            </div>
            <div class="report-title">
                <h1>REMITTANCE REPORT</h1>
                <div class="report-period">
                    @if ($period === 'weekly')
                        {{ 'Week ' . $week . ' - ' . date('Y') }}
                    @elseif ($period === 'monthly')
                        {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}
                    @elseif ($period === 'daily')
                        {{ date('F j, Y', mktime(0, 0, 0, $month, $day, $year)) }}
                    @else
                        {{ 'Year ' . $year }}
                    @endif
                </div>
            </div>
        </div>

        {{-- Table --}}
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-right">Collection</th>
                    <th class="text-right">Expenses</th>
                    <th class="text-right">Net Remittance</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($groupedRemittances as $remittance)
                    <tr>
                        <td>{{ $remittance['remittance_date']->format('M d, Y') }}</td>
                        <td class="text-right mono" style="color: #16a34a;">₱{{ number_format($remittance['total_collection'], 2) }}</td>
                        <td class="text-right mono">₱{{ number_format($remittance['total_expenses'], 2) }}</td>
                        <td class="text-right mono" style="font-weight: 700; color: {{ $remittance['is_short_remittance'] ? '#dc2626' : '#16a34a' }};">₱{{ number_format($remittance['net_remittance'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 20px; text-align: center;">No remittances found for the selected period.</td>
                    </tr>
                @endforelse
            </tbody>
            @if (count($groupedRemittances) > 0)
            <tfoot>
                <tr style="border-top: 2px solid var(--gray-rule); background: var(--gray-bg); font-weight: 700;">
                    <td style="padding: 10px;">TOTAL</td>
                    <td class="text-right mono" style="padding: 10px; color: #16a34a;">₱{{ number_format($totalCollection, 2) }}</td>
                    <td class="text-right mono" style="padding: 10px;">₱{{ number_format($totalExpenses, 2) }}</td>
                    <td class="text-right mono" style="padding: 10px;">₱{{ number_format($totalNetRemittance, 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>

        {{-- Signatures --}}
        <div class="signatures">
            <div class="signature-block">
                <p class="signature-label">Prepared by:</p>
                <p class="signature-name">{{ $preparedBy?->name ?? '________________________' }}</p>
                <p class="signature-title">{{ $preparedBy?->position ?? '________________________' }}</p>
            </div>
            <div class="signature-block">
                <p class="signature-label">Checked by:</p>
                <p class="signature-name">{{ $checkedBy?->name ?? '________________________' }}</p>
                <p class="signature-title">{{ $checkedBy?->position ?? '________________________' }}</p>
            </div>
        </div>
    </div>

</body>
</html>