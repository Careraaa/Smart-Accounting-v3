@extends('layouts.layout')

@push('styles')
<style>
.print-only { display: none; }
@media print {
    @page { size: A4 landscape; margin: 12mm; }
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
    .print-report .overflow-x-auto { overflow: visible !important; }
    .print-report table { width: 100% !important; border-collapse: collapse !important; font-size: 10px !important; }
    .print-report th, .print-report td { padding: 5px 7px !important; color: #222 !important; border-color: #d1d5db !important; }
    .print-report th { background: #f3f4f6 !important; }
    .print-report tr { break-inside: avoid; }
}
</style>
@endpush

@section('content')
<div class="space-y-5 w-full max-w-7xl mx-auto print-report-page">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Trial Balance</h1>
            <p class="text-sm text-gray-400 mt-0.5">Posted account activity as of {{ \Carbon\Carbon::parse($asOf)->format('F d, Y') }}</p>
        </div>
        <button type="button" onclick="window.print()" class="no-print px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-semibold text-gray-600">Print</button>
    </div>
    @if($errors->any())
        <div class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif
    <form method="GET" action="{{ route('accounting.trial-balance.index') }}" class="no-print bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4 flex flex-wrap items-end gap-4">
        <div>
            <label for="as_of" class="block text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 mb-1.5">As of</label>
            <input id="as_of" type="date" name="as_of" value="{{ $asOf }}" class="text-xs border border-gray-300 rounded-lg px-3 py-2.5">
        </div>
        <button class="px-5 py-2.5 bg-gray-900 text-white rounded-lg text-xs font-semibold">View Trial Balance</button>
    </form>
    <div class="print-report">
    <div class="print-only mb-6 pb-4 border-b-2 border-gray-900 flex items-end justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/knights-icon.png') }}" alt="" class="w-10 h-10 object-contain">
            <div><div class="text-sm font-bold text-gray-900">Knights Transport Services Corporation</div><div class="text-[10px] uppercase text-gray-500">Accounting</div></div>
        </div>
        <div class="text-right"><h1 class="text-xl font-bold text-gray-900">TRIAL BALANCE</h1><p class="text-xs text-gray-500">As of {{ \Carbon\Carbon::parse($asOf)->format('F d, Y') }}</p></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-4 py-3 text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400">Code</th>
                    <th class="px-4 py-3 text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400">Account name</th>
                    <th class="px-4 py-3 text-left text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400">Type</th>
                    <th class="px-4 py-3 text-right text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400">Total debits</th>
                    <th class="px-4 py-3 text-right text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400">Total credits</th>
                    <th class="px-4 py-3 text-right text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400">Ending debit</th>
                    <th class="px-4 py-3 text-right text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400">Ending credit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($accounts as $account)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 text-xs font-bold font-mono text-gray-900">{{ $account->account_code }}</td>
                        <td class="px-4 py-3 text-xs font-medium text-gray-800">{{ $account->account_name }} @if($account->is_contra)<span class="text-[10px] text-amber-700">(Contra)</span>@endif</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $account->account_type }}</td>
                        <td class="px-4 py-3 text-right text-xs tabular-nums">{{ number_format($account->total_debit, 2) }}</td>
                        <td class="px-4 py-3 text-right text-xs tabular-nums">{{ number_format($account->total_credit, 2) }}</td>
                        <td class="px-4 py-3 text-right text-xs tabular-nums">{{ $account->ending_debit > 0 ? number_format($account->ending_debit, 2) : '—' }}</td>
                        <td class="px-4 py-3 text-right text-xs tabular-nums">{{ $account->ending_credit > 0 ? number_format($account->ending_credit, 2) : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">No active accounts are available.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-gray-200 bg-gray-50 font-bold">
                    <td colspan="3" class="px-4 py-3 text-xs text-gray-800">Totals</td>
                    <td class="px-4 py-3 text-right text-xs tabular-nums">{{ number_format($totalDebit, 2) }}</td>
                    <td class="px-4 py-3 text-right text-xs tabular-nums">{{ number_format($totalCredit, 2) }}</td>
                    <td class="px-4 py-3 text-right text-xs tabular-nums">{{ number_format($endingDebit, 2) }}</td>
                    <td class="px-4 py-3 text-right text-xs tabular-nums">{{ number_format($endingCredit, 2) }}</td>
                </tr>
                <tr class="bg-gray-50/70">
                    <td colspan="5" class="px-4 py-3 text-xs font-semibold text-gray-700">Difference (ending debits less ending credits)</td>
                    <td colspan="2" class="px-4 py-3 text-right text-xs font-bold tabular-nums {{ abs($difference) < 0.01 ? 'text-emerald-700' : 'text-red-700' }}">
                        {{ number_format(abs($difference), 2) }} · {{ abs($difference) < 0.01 ? 'BALANCED' : 'UNBALANCED' }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <p class="text-xs text-gray-400">Balances and totals include posted journal entries dated on or before the selected date only. Draft and void entries are excluded.</p>
    </div>
</div>
@endsection
