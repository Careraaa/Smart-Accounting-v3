@extends('layouts.layout')

@push('styles')
<style>
.print-only { display: none; }
@media print {
    @page { size: A4 portrait; margin: 12mm; }
    html, body { height: auto !important; overflow: visible !important; background: #fff !important; }
    body * { visibility: hidden !important; }
    .print-report, .print-report * { visibility: visible !important; }
    .print-report-page > :not(.print-report) { display: none !important; }
    .sidebar, header, #page-skeleton, .right-sidebar { display: none !important; }
    main, main > div, .main-content, .main-content > div {
        margin: 0 !important;
        padding: 0 !important;
        min-height: 0 !important;
        background: #fff !important;
    }
    .print-report-page { max-width: none !important; }
    .print-report {
        position: static !important;
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 12mm !important;
        overflow: visible !important;
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        background: #fff !important;
        color: #0a0a0a !important;
        font-family: Arial, sans-serif !important;
    }
    .print-report * { color: #222 !important; background-color: transparent !important; }
    .no-print { display: none !important; }
    .print-only { display: flex !important; }
    .print-report .bg-white { background-color: #fff !important; }
    .print-report .max-w-3xl { max-width: none !important; }
    .print-report table { width: 100% !important; border-collapse: collapse !important; font-size: 11px !important; }
    .print-report td, .print-report th { padding: 6px 8px !important; color: #222 !important; border-color: #d1d5db !important; }
    .print-report tr { break-inside: avoid; }
}
</style>
@endpush

@section('content')
<div class="space-y-5 w-full max-w-5xl mx-auto print-report-page">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Balance Sheet</h1>
            <p class="text-sm text-gray-400 mt-0.5">As of {{ \Carbon\Carbon::parse($asOf)->format('F d, Y') }} · Posted entries only</p>
        </div>
        <button type="button" onclick="window.print()" class="no-print px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-semibold text-gray-600">Print</button>
    </div>
    @if($errors->any())
        <div class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif
    <form method="GET" action="{{ route('accounting.balance-sheet.index') }}" class="no-print bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4 flex flex-wrap items-end gap-4">
        <div>
            <label for="as_of" class="block text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 mb-1.5">As of</label>
            <input id="as_of" type="date" name="as_of" value="{{ $asOf }}" class="text-xs border border-gray-300 rounded-lg px-3 py-2.5">
        </div>
        <button class="px-5 py-2.5 bg-gray-900 text-white rounded-lg text-xs font-semibold">View Balance Sheet</button>
    </form>
    <div class="print-report">
    <div class="print-only mb-6 pb-4 border-b-2 border-gray-900 flex items-end justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/knights-icon.png') }}" alt="" class="w-10 h-10 object-contain">
            <div><div class="text-sm font-bold text-gray-900">Knights Transport Services Corporation</div><div class="text-[10px] uppercase text-gray-500">Accounting</div></div>
        </div>
        <div class="text-right"><h1 class="text-xl font-bold text-gray-900">BALANCE SHEET</h1><p class="text-xs text-gray-500">As of {{ \Carbon\Carbon::parse($asOf)->format('F d, Y') }}</p></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sm:p-8">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-center text-lg font-bold text-gray-900">Knights Transport Services Corporation</h2>
            <p class="text-center text-sm text-gray-500 mb-7">Balance Sheet</p>
            <table class="w-full text-sm app-standard-table">
                <tbody>
                    <tr class="border-b border-gray-100"><th colspan="2" class="py-3 text-left text-xs uppercase tracking-wide text-gray-500">Assets</th></tr>
                    @forelse($assets as $account)
                        <tr><td class="py-2 text-gray-700">{{ $account->account_code }} · {{ $account->account_name }}</td><td class="py-2 text-right tabular-nums">{{ number_format($account->balance, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="py-2 text-sm text-gray-400">No active asset accounts.</td></tr>
                    @endforelse
                    <tr class="border-t border-gray-200"><td class="py-3 font-bold text-gray-900">Total assets</td><td class="py-3 text-right font-bold tabular-nums">{{ number_format($totalAssets, 2) }}</td></tr>

                    <tr class="border-b border-gray-100"><th colspan="2" class="pt-6 pb-3 text-left text-xs uppercase tracking-wide text-gray-500">Liabilities</th></tr>
                    @forelse($liabilities as $account)
                        <tr><td class="py-2 text-gray-700">{{ $account->account_code }} · {{ $account->account_name }}</td><td class="py-2 text-right tabular-nums">{{ number_format($account->balance, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="py-2 text-sm text-gray-400">No active liability accounts.</td></tr>
                    @endforelse
                    <tr class="border-t border-gray-200"><td class="py-3 font-bold text-gray-900">Total liabilities</td><td class="py-3 text-right font-bold tabular-nums">{{ number_format($totalLiabilities, 2) }}</td></tr>

                    <tr class="border-b border-gray-100"><th colspan="2" class="pt-6 pb-3 text-left text-xs uppercase tracking-wide text-gray-500">Equity</th></tr>
                    @forelse($equityAccounts as $account)
                        <tr><td class="py-2 text-gray-700">{{ $account->account_code }} · {{ $account->account_name }}</td><td class="py-2 text-right tabular-nums">{{ number_format($account->balance, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="py-2 text-sm text-gray-400">No active equity accounts.</td></tr>
                    @endforelse
                    <tr><td class="py-2 text-gray-700">Current-period {{ $currentEarnings >= 0 ? 'earnings' : 'loss' }} ({{ \Carbon\Carbon::parse($currentPeriodStart)->format('M d, Y') }} – {{ \Carbon\Carbon::parse($asOf)->format('M d, Y') }})</td><td class="py-2 text-right tabular-nums">{{ number_format($currentEarnings, 2) }}</td></tr>
                    <tr class="border-t border-gray-200"><td class="py-3 font-bold text-gray-900">Total equity</td><td class="py-3 text-right font-bold tabular-nums">{{ number_format($totalEquity, 2) }}</td></tr>
                    <tr class="border-t-2 border-gray-900"><td class="py-4 font-bold text-gray-900">Total liabilities and equity</td><td class="py-4 text-right font-bold tabular-nums">{{ number_format($totalLiabilities + $totalEquity, 2) }}</td></tr>
                </tbody>
            </table>
            <div class="mt-5 rounded-lg px-4 py-3 {{ abs($equationDifference) < 0.01 ? 'bg-emerald-50' : 'bg-red-50' }}">
                <div class="flex items-center justify-between gap-3 text-sm">
                    <span class="font-semibold text-gray-800">Accounting equation difference (Assets − Liabilities − Equity)</span>
                    <span class="font-bold tabular-nums {{ abs($equationDifference) < 0.01 ? 'text-emerald-700' : 'text-red-700' }}">{{ number_format($equationDifference, 2) }} · {{ abs($equationDifference) < 0.01 ? 'BALANCED' : 'UNBALANCED' }}</span>
                </div>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-400">Current-period earnings are calculated from posted revenue and expense activity from January 1 through the selected date. Any posted closing entries remain reflected in their account balances.</p>
        </div>
    </div>
</div>
@endsection
