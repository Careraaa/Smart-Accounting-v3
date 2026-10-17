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
    .print-break { break-inside: avoid; }
    .ledger-page-hidden { display: table-row !important; }
    .print-report .overflow-x-auto { overflow: visible !important; }
    .print-report table { width: 100% !important; border-collapse: collapse !important; font-size: 10px !important; }
    .print-report th, .print-report td { padding: 5px 7px !important; color: #222 !important; border-color: #d1d5db !important; }
    .print-report th { background: #f3f4f6 !important; }
    .print-report tr { break-inside: avoid; }
}
.ledger-page-hidden { display: none; }
</style>
@endpush

@section('content')
<div class="space-y-5 print-report-page">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">General Ledger</h1>
            <p class="text-sm text-gray-400 mt-0.5">Posted account activity with opening, running, and closing balances</p>
        </div>
        <a href="{{ route('accounting.trial-balance.index') }}" class="no-print inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-xl text-xs font-semibold no-underline hover:bg-gray-50">Trial Balance</a>
    </div>

    @if($errors->any())
        <div class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
    @endif
    @foreach(['success', 'error'] as $type)
        @if(session($type))
            <div class="rounded-xl px-4 py-3 text-sm {{ $type === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">{{ session($type) }}</div>
        @endif
    @endforeach

    <div class="no-print bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4">
        <form method="GET" action="{{ route('accounting.general-ledger.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[220px]">
                <label for="account_id" class="block text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 mb-1.5">Account</label>
                <select id="account_id" name="account_id" required class="w-full text-xs border border-gray-300 rounded-lg px-3 py-2.5 bg-white">
                    <option value="">Select account…</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" @selected(($filters['account_id'] ?? '') == $account->id)>{{ $account->account_code }} – {{ $account->account_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 mb-1.5">From</label>
                <input id="date_from" type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="text-xs border border-gray-300 rounded-lg px-3 py-2.5">
            </div>
            <div>
                <label for="date_to" class="block text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 mb-1.5">To</label>
                <input id="date_to" type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="text-xs border border-gray-300 rounded-lg px-3 py-2.5">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-gray-900 text-white rounded-lg text-xs font-semibold">View Ledger</button>
        </form>
    </div>

    @if($selectedAccount && $ledger)
        @php
            $normalSideLabel = $selectedAccount->normal_balance_side === 'debit' ? 'DR' : 'CR';
            $oppositeSideLabel = $selectedAccount->normal_balance_side === 'debit' ? 'CR' : 'DR';
        @endphp
        <div class="flex justify-end gap-2 no-print">
            <a href="{{ route('accounting.general-ledger.csv', request()->query()) }}" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 no-underline hover:bg-gray-50">Export CSV</a>
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600">Print</button>
        </div>

        <div class="print-report">
        <div class="print-only mb-6 pb-4 border-b-2 border-gray-900 flex items-end justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/knights-icon.png') }}" alt="" class="w-10 h-10 object-contain">
                <div><div class="text-sm font-bold text-gray-900">Knights Transport Services Corporation</div><div class="text-[10px] uppercase text-gray-500">Accounting</div></div>
            </div>
            <div class="text-right">
                <h1 class="text-xl font-bold text-gray-900">GENERAL LEDGER</h1>
                <p class="text-xs text-gray-500">{{ $selectedAccount->account_code }} – {{ $selectedAccount->account_name }}
                    @if($ledger['dateFrom'] || $ledger['dateTo'])
                        · {{ $ledger['dateFrom'] ?? 'Beginning' }} to {{ $ledger['dateTo'] ?? 'Latest' }}
                    @else
                        · All posted activity
                    @endif
                </p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 print-break">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h2 class="text-sm font-bold text-gray-900">{{ $selectedAccount->account_code }} – {{ $selectedAccount->account_name }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $selectedAccount->account_type }} account
                        @if($ledger['dateFrom'] || $ledger['dateTo'])
                            · {{ $ledger['dateFrom'] ?? 'Beginning' }} to {{ $ledger['dateTo'] ?? 'Latest' }}
                        @else
                            · All posted activity
                        @endif
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400">Closing Balance</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums">₱{{ number_format(abs($ledger['closingBalance']), 2) }}
                        <span class="text-xs font-semibold text-gray-500">{{ $ledger['closingBalance'] < 0 ? $oppositeSideLabel : $normalSideLabel }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Date</th>
                        <th class="text-left text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Reference</th>
                        <th class="text-left text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Journal #</th>
                        <th class="text-left text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Description</th>
                        <th class="text-right text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Debit</th>
                        <th class="text-right text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Credit</th>
                        <th class="text-right text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Running Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <tr class="bg-blue-50/50 font-semibold">
                        <td colspan="4" class="px-4 py-3 text-xs text-gray-700">Opening balance</td>
                        <td colspan="2"></td>
                        <td class="px-4 py-3 text-right text-xs tabular-nums">₱{{ number_format(abs($ledger['openingBalance']), 2) }}
                            {{ $ledger['openingBalance'] < 0 ? $oppositeSideLabel : $normalSideLabel }}
                        </td>
                    </tr>
                    @forelse($ledger['transactions'] as $line)
                        <tr class="hover:bg-gray-50/50" data-ledger-row>
                            <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">{{ $line->journalEntry->transaction_date->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $line->ledger_reference }}</td>
                            <td class="px-4 py-3 text-xs font-bold font-mono">
                                <a href="{{ route('accounting.journal-entries.show', $line->journalEntry) }}" class="text-gray-900 no-underline hover:underline">{{ $line->journalEntry->journal_number }}</a>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $line->description ?: $line->journalEntry->description }}</td>
                            <td class="px-4 py-3 text-right text-xs tabular-nums">{{ (float) $line->debit > 0 ? '₱' . number_format((float) $line->debit, 2) : '—' }}</td>
                            <td class="px-4 py-3 text-right text-xs tabular-nums">{{ (float) $line->credit > 0 ? '₱' . number_format((float) $line->credit, 2) : '—' }}</td>
                            <td class="px-4 py-3 text-right text-xs font-semibold tabular-nums">₱{{ number_format(abs($line->running_balance), 2) }}
                                {{ $line->running_balance < 0 ? $oppositeSideLabel : $normalSideLabel }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-12 text-center text-sm text-gray-400">No posted transactions in this period.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-200 bg-gray-50 font-bold">
                        <td colspan="4" class="px-4 py-3 text-xs text-gray-800">Period totals</td>
                        <td class="px-4 py-3 text-right text-xs tabular-nums">₱{{ number_format($ledger['periodDebit'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-xs tabular-nums">₱{{ number_format($ledger['periodCredit'], 2) }}</td>
                        <td class="px-4 py-3 text-right text-xs tabular-nums">₱{{ number_format(abs($ledger['closingBalance']), 2) }}
                            {{ $ledger['closingBalance'] < 0 ? $oppositeSideLabel : $normalSideLabel }}
                        </td>
                    </tr>
                    <tr class="bg-blue-50/50 font-semibold">
                        <td colspan="4" class="px-4 py-3 text-xs text-gray-700">Closing balance</td>
                        <td colspan="2"></td>
                        <td class="px-4 py-3 text-right text-xs tabular-nums">₱{{ number_format(abs($ledger['closingBalance']), 2) }}
                            {{ $ledger['closingBalance'] < 0 ? $oppositeSideLabel : $normalSideLabel }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            <div class="no-print flex items-center justify-between gap-3 px-4 py-3 border-t border-gray-100 bg-gray-50/50">
                <span id="ledgerPageInfo" class="text-xs text-gray-500"></span>
                <nav id="ledgerPagination" class="flex items-center gap-1" aria-label="Ledger pages"></nav>
            </div>
        </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 py-16 text-center">
            <p class="text-sm font-semibold text-gray-500">Select an account to view its ledger</p>
            <p class="text-xs text-gray-400 mt-1">Choose an account and optional date range above.</p>
        </div>
    @endif
</div>
@endsection

@if($selectedAccount && $ledger)
@push('scripts')
<script>
(function () {
    const rows = Array.from(document.querySelectorAll('[data-ledger-row]'));
    const info = document.getElementById('ledgerPageInfo');
    const nav = document.getElementById('ledgerPagination');
    const perPage = 10;
    const pages = Math.ceil(rows.length / perPage);
    let page = 1;

    function addButton(label, target, disabled, active) {
        const button = document.createElement('button');
        button.type = 'button';
        button.textContent = label;
        button.disabled = disabled;
        button.className = 'w-8 h-8 rounded-lg border text-xs font-semibold ' +
            (active ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-200') +
            (disabled ? ' opacity-40 cursor-not-allowed' : ' hover:bg-gray-100');
        button.addEventListener('click', function () {
            page = target;
            render();
        });
        nav.appendChild(button);
    }

    function render() {
        rows.forEach(function (row, index) {
            row.classList.toggle('ledger-page-hidden', index < (page - 1) * perPage || index >= page * perPage);
        });
        info.textContent = rows.length
            ? 'Showing ' + ((page - 1) * perPage + 1) + '–' + Math.min(page * perPage, rows.length) + ' of ' + rows.length
            : 'No transactions';
        nav.replaceChildren();
        if (pages < 2) return;
        addButton('‹', page - 1, page === 1, false);
        let start = Math.max(1, page - 2);
        let end = Math.min(pages, start + 4);
        start = Math.max(1, end - 4);
        for (let number = start; number <= end; number++) {
            addButton(String(number), number, number === page, number === page);
        }
        addButton('›', page + 1, page === pages, false);
    }

    render();
})();
</script>
@endpush
@endif
