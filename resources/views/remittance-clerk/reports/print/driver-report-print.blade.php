<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Report – {{ date('M d, Y') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Sora', sans-serif; font-size: 12px; color: #0a0a0a; background: #efefef; line-height: 1.55; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .print-page { margin: 0; box-shadow: none; max-width: 100%; padding: 12mm; }
        }
        @media screen and (max-width: 900px) {
            .no-print { margin: 12px 12px 8px; }
            .print-page { margin: 0 12px 16px; padding: 20px; }
            .report-header { align-items: flex-start; gap: 16px; }
            .report-table,
            .report-table tbody,
            .report-table tr { display: block; width: 100%; }
            .report-table thead { display: none; }
            .report-table tbody tr { padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
            .report-table td { display: grid; grid-template-columns: minmax(7rem, 38%) minmax(0, 1fr); gap: 10px; padding: 5px 0; overflow-wrap: anywhere; }
            .report-table td::before { content: attr(data-label); font-weight: 600; color: #4b5563; }
            .report-table td[colspan] { display: block; text-align: center; }
        }
        @page { size: A4 landscape; margin: 0; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/print-standard.css') }}">
</head>
<body>
    {{-- Controls --}}
    <div class="no-print print-controls max-w-[900px] mx-auto mt-7 mb-3 flex justify-end gap-2">
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:border-gray-300 hover:text-gray-900 hover:shadow-sm active:scale-[0.97] transition-all duration-200 cursor-pointer">Print Report</button>
    </div>

    <div class="print-page bg-white max-w-[900px] mx-auto mb-10 shadow-sm p-10" style="box-shadow:0 2px 24px rgba(0,0,0,.08);">
        {{-- Header --}}
        <div class="report-header flex items-end justify-between pb-6 mb-7" style="border-bottom:2px solid #0a0a0a;">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/knights-icon.png') }}" alt="Logo" class="w-10 h-10 object-contain rounded-lg">
                <div>
                    <div class="text-sm font-bold text-gray-900">Smart Accounting</div>
                    <div class="text-[10px] text-gray-500 uppercase mt-0.5">Remittance Management</div>
                </div>
            </div>
            <div class="text-right">
                <h1 class="text-xl font-bold text-gray-900 mb-1">DRIVERS</h1>
                <div class="text-[11px] text-gray-500">{{ date('M d, Y') }}</div>
            </div>
        </div>

        {{-- Table --}}
        <table class="report-table w-full text-[11px] border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200" style="background:#f7f7f7;">
                    <th class="text-left px-5 py-3 font-semibold text-gray-700">Name</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-700">License Number</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-700">Contact Number</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-700">Email</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-700">Sex</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-700">Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($drivers as $driver)
                    <tr class="border-b border-gray-200">
                        <td data-label="Name" class="px-5 py-3 font-semibold">{{ $driver->name }}</td>
                        <td data-label="License Number" class="px-5 py-3">{{ $driver->license_number ?? 'N/A' }}</td>
                        <td data-label="Contact Number" class="px-5 py-3">{{ $driver->contact_number ?? 'N/A' }}</td>
                        <td data-label="Email" class="px-5 py-3">{{ $driver->email ?? 'N/A' }}</td>
                        <td data-label="Sex" class="px-5 py-3">{{ ucfirst($driver->gender ?? 'N/A') }}</td>
                        <td data-label="Address" class="px-5 py-3">{{ $driver->address ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-gray-400">No active drivers found.</td>
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
