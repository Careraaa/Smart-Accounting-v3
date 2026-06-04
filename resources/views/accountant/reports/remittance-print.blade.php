<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remittance Report – {{ $period }} – {{ date('M d, Y') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Sora', sans-serif; font-size: 12px; color: #0a0a0a; background: #efefef; line-height: 1.55; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .print-page { margin: 0; box-shadow: none; max-width: 100%; padding: 12mm; }
        }
        @page { size: A4 landscape; margin: 12mm; }
    </style>
</head>
<body>
    <div class="no-print max-w-[900px] mx-auto mt-7 mb-3 flex justify-end gap-2">
        <a href="{{ route('reports.remittance') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded text-xs font-semibold bg-white text-gray-700 border border-gray-200 no-underline cursor-pointer hover:opacity-80 transition-opacity">← Back</a>
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded text-xs font-semibold text-white cursor-pointer border-none hover:opacity-80 transition-opacity" style="background:#0a0a0a;">Print Report</button>
    </div>

    <div class="print-page bg-white max-w-[900px] mx-auto mb-10 shadow-sm p-10" style="box-shadow:0 2px 24px rgba(0,0,0,.08);">
        <div class="flex items-end justify-between pb-6 mb-7" style="border-bottom:2px solid #0a0a0a;">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/knights-icon.png') }}" alt="Logo" class="w-10 h-10 object-contain rounded-lg">
                <div>
                    <div class="text-sm font-bold text-gray-900">Smart Accounting</div>
                    <div class="text-[10px] text-gray-500 uppercase mt-0.5">Remittance Management</div>
                </div>
            </div>
            <div class="text-right">
                <h1 class="text-xl font-bold text-gray-900 mb-1">REMITTANCE REPORT</h1>
                <div class="text-[11px] text-gray-500">
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

        <div class="grid grid-cols-4 gap-4 mb-8">
            <div class="p-4 rounded text-center bg-blue-100">
                <div class="text-[10px] text-gray-500 uppercase font-semibold mb-1.5">Total collection</div>
                <div class="text-lg font-bold text-gray-900 font-mono">₱{{ number_format($totalCollection, 2) }}</div>
            </div>
            <div class="p-4 rounded text-center bg-emerald-100">
                <div class="text-[10px] text-gray-500 uppercase font-semibold mb-1.5">Total expenses</div>
                <div class="text-lg font-bold text-gray-900 font-mono">₱{{ number_format($totalExpenses, 2) }}</div>
            </div>
            <div class="p-4 rounded text-center bg-orange-100">
                <div class="text-[10px] text-gray-500 uppercase font-semibold mb-1.5">Net remittance</div>
                <div class="text-lg font-bold text-gray-900 font-mono">₱{{ number_format($totalNetRemittance, 2) }}</div>
            </div>
            <div class="p-4 rounded text-center bg-red-100">
                <div class="text-[10px] text-gray-500 uppercase font-semibold mb-1.5">Short remittances</div>
                <div class="text-lg font-bold text-gray-900 font-mono">{{ $shortRemittances }}</div>
            </div>
        </div>

        <div>
            <table class="w-full text-[11px] border-collapse">
                <thead>
                    <tr class="border-b border-gray-300" style="background:#f7f7f7;">
                        <th class="text-left px-2.5 py-2.5 font-semibold text-gray-700">Date</th>
                        <th class="text-right px-2.5 py-2.5 font-semibold text-gray-700">Collection</th>
                        <th class="text-right px-2.5 py-2.5 font-semibold text-gray-700">Expenses</th>
                        <th class="text-right px-2.5 py-2.5 font-semibold text-gray-700">Net Remittance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($groupedRemittances as $remittance)
                        <tr class="border-b border-gray-200">
                            <td class="px-2.5 py-2.5">{{ $remittance['remittance_date']->format('M d, Y') }}</td>
                            <td class="px-2.5 py-2.5 text-right text-emerald-700 font-semibold">₱{{ number_format($remittance['total_collection'], 2) }}</td>
                            <td class="px-2.5 py-2.5 text-right text-gray-500">₱{{ number_format($remittance['total_expenses'], 2) }}</td>
                            <td class="px-2.5 py-2.5 text-right font-semibold {{ $remittance['is_short_remittance'] ? 'text-red-600' : 'text-emerald-600' }}">₱{{ number_format($remittance['net_remittance'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-gray-400">No remittances found for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-10 pt-5 text-center text-[10px] text-gray-500" style="border-top:1px solid #e0e0e0;">
            <p>Report generated on {{ date('M d, Y \a\t h:i A') }}</p>
        </div>
    </div>
</body>
</html>