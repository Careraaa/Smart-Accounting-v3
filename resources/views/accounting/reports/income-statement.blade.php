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
<div class="space-y-5 max-w-5xl print-report-page">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Income Statement</h1>
            <p class="text-sm text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($dateFrom)->format('F d, Y') }} – {{ \Carbon\Carbon::parse($dateTo)->format('F d, Y') }} · Posted entries only</p>
        </div>
        <button type="button" onclick="window.print()" class="no-print px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-semibold text-gray-600">Print</button>
    </div>
    @if($errors->any())
        <div class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif
    <form method="GET" action="{{ route('accounting.income-statement.index') }}" class="no-print bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4 flex flex-wrap items-end gap-4">
        <div>
            <label for="date_from" class="block text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 mb-1.5">From</label>
            <input id="date_from" type="date" name="date_from" value="{{ $dateFrom }}" class="text-xs border border-gray-300 rounded-lg px-3 py-2.5">
        </div>
        <div>
            <label for="date_to" class="block text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 mb-1.5">To</label>
            <input id="date_to" type="date" name="date_to" value="{{ $dateTo }}" class="text-xs border border-gray-300 rounded-lg px-3 py-2.5">
        </div>
        <button class="px-5 py-2.5 bg-gray-900 text-white rounded-lg text-xs font-semibold">View Statement</button>
    </form>
    <div class="print-report">
    <div class="print-only mb-6 pb-4 border-b-2 border-gray-900 flex items-end justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/knights-icon.png') }}" alt="" class="w-10 h-10 object-contain">
            <div><div class="text-sm font-bold text-gray-900">Knights Transport Services Corporation</div><div class="text-[10px] uppercase text-gray-500">Accounting</div></div>
        </div>
        <div class="text-right"><h1 class="text-xl font-bold text-gray-900">INCOME STATEMENT</h1><p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }} – {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}</p></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sm:p-8">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-center text-lg font-bold text-gray-900">Knights Transport Services Corporation</h2>
            <p class="text-center text-sm text-gray-500 mb-7">Income Statement</p>
            <table class="w-full text-sm">
                <tbody>
                    <tr class="border-b border-gray-100"><th colspan="2" class="py-3 text-left text-xs uppercase tracking-wide text-gray-500">Revenue</th></tr>
                    @forelse($revenues as $account)
                        <tr><td class="py-2 text-gray-700">{{ $account->account_code }} · {{ $account->account_name }}</td><td class="py-2 text-right tabular-nums">{{ number_format($account->statement_amount, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="py-2 text-sm text-gray-400">No revenue activity in this period.</td></tr>
                    @endforelse
                    <tr class="border-t border-gray-100"><td class="py-2 font-medium text-gray-700">Total revenue</td><td class="py-2 text-right font-semibold tabular-nums">{{ number_format($grossRevenue, 2) }}</td></tr>
                    @foreach($contraRevenues as $account)
                        <tr><td class="py-2 text-gray-600">{{ $account->account_code }} · {{ $account->account_name }} (contra-revenue)</td><td class="py-2 text-right tabular-nums">{{ number_format($account->statement_amount, 2) }}</td></tr>
                    @endforeach
                    <tr class="border-t border-gray-200"><td class="py-3 font-bold text-gray-900">Net revenue</td><td class="py-3 text-right font-bold tabular-nums">{{ number_format($netRevenue, 2) }}</td></tr>

                    <tr class="border-b border-gray-100"><th colspan="2" class="pt-6 pb-3 text-left text-xs uppercase tracking-wide text-gray-500">Expenses</th></tr>
                    @forelse($expenses as $account)
                        <tr><td class="py-2 text-gray-700">{{ $account->account_code }} · {{ $account->account_name }}</td><td class="py-2 text-right tabular-nums">{{ number_format($account->statement_amount, 2) }}</td></tr>
                    @empty
                        <tr><td colspan="2" class="py-2 text-sm text-gray-400">No expense activity in this period.</td></tr>
                    @endforelse
                    <tr class="border-t border-gray-100"><td class="py-2 font-medium text-gray-700">Total expenses</td><td class="py-2 text-right font-semibold tabular-nums">{{ number_format($grossExpenses, 2) }}</td></tr>
                    @foreach($contraExpenses as $account)
                        <tr><td class="py-2 text-gray-600">{{ $account->account_code }} · {{ $account->account_name }} (contra-expense)</td><td class="py-2 text-right tabular-nums">{{ number_format($account->statement_amount, 2) }}</td></tr>
                    @endforeach
                    <tr class="border-t border-gray-200"><td class="py-3 font-bold text-gray-900">Net expenses</td><td class="py-3 text-right font-bold tabular-nums">{{ number_format($netExpenses, 2) }}</td></tr>
                    <tr class="border-t-2 border-gray-900"><td class="py-4 font-bold text-gray-900">{{ $netIncome >= 0 ? 'Net income' : 'Net loss' }}</td><td class="py-4 text-right font-bold tabular-nums {{ $netIncome < 0 ? 'text-red-600' : 'text-gray-900' }}">{{ number_format(abs($netIncome), 2) }}</td></tr>
                </tbody>
            </table>
        </div>
        </div>
    </div>
</div>
@endsection
