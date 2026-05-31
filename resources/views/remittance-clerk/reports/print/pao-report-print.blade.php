<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAO Report – {{ date('M d, Y') }}</title>
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
    {{-- Controls --}}
    <div class="no-print max-w-[900px] mx-auto mt-7 mb-3 flex justify-end gap-2">
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded text-xs font-semibold text-white cursor-pointer border-none hover:opacity-80 transition-opacity" style="background:#0a0a0a;">Print Report</button>
    </div>

    <div class="print-page bg-white max-w-[900px] mx-auto mb-10 shadow-sm p-10" style="box-shadow:0 2px 24px rgba(0,0,0,.08);">
        {{-- Header --}}
        <div class="flex items-end justify-between pb-6 mb-7" style="border-bottom:2px solid #0a0a0a;">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/knights-icon.png') }}" alt="Logo" class="w-10 h-10 object-contain rounded-lg">
                <div>
                    <div class="text-sm font-bold text-gray-900">Smart Accounting</div>
                    <div class="text-[10px] text-gray-500 uppercase mt-0.5">Remittance Management</div>
                </div>
            </div>
            <div class="text-right">
                <h1 class="text-xl font-bold text-gray-900 mb-1">Passenger Assistant Officers</h1>
                <div class="text-[11px] text-gray-500">{{ date('M d, Y') }}</div>
            </div>
        </div>

        {{-- Table --}}
        <table class="w-full text-[11px] border-collapse">
            <thead>
                <tr class="border-b border-gray-300" style="background:#f7f7f7;">
                    <th class="text-left px-2.5 py-2.5 font-semibold text-gray-700">Name</th>
                    <th class="text-left px-2.5 py-2.5 font-semibold text-gray-700">Contact Number</th>
                    <th class="text-left px-2.5 py-2.5 font-semibold text-gray-700">Email</th>
                    <th class="text-left px-2.5 py-2.5 font-semibold text-gray-700">Gender</th>
                    <th class="text-left px-2.5 py-2.5 font-semibold text-gray-700">Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($paos as $pao)
                    <tr class="border-b border-gray-200">
                        <td class="px-2.5 py-2.5 font-semibold">{{ $pao->name }}</td>
                        <td class="px-2.5 py-2.5">{{ $pao->contact_number ?? 'N/A' }}</td>
                        <td class="px-2.5 py-2.5">{{ $pao->email ?? 'N/A' }}</td>
                        <td class="px-2.5 py-2.5">{{ ucfirst($pao->gender ?? 'N/A') }}</td>
                        <td class="px-2.5 py-2.5">{{ $pao->address ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-gray-400">No active PAOs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Footer --}}
        <div class="mt-10 pt-5 text-center text-[10px] text-gray-500" style="border-top:1px solid #e0e0e0;">
            <p>Report generated on {{ date('M d, Y \a\t h:i A') }}</p>
        </div>
    </div>
</body>
</html>
