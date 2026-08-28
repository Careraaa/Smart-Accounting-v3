@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    @foreach(['success','error'] as $t)
        @if(session($t))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium animate-[fadeSlideUp_0.3s_ease] @switch($t) @case('success') bg-green-50 text-green-700 @break @case('error') bg-red-50 text-red-700 @break @endswitch">
            <span>{{ session($t) }}</span>
        </div>
        @endif
    @endforeach

    <div class="fade-up flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">General Ledger</h1>
            <p class="text-sm text-gray-400 mt-0.5">View account transactions and running balances</p>
        </div>
        <a href="{{ route('accounting.general-ledger.summary') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 text-gray-600 rounded-xl text-xs font-semibold transition-all hover:bg-gray-50 active:scale-[0.97] no-underline whitespace-nowrap">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Trial Balance
        </a>
    </div>

    {{-- Filters --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[220px]">
                <label class="block text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 mb-1.5">Account <span class="text-rose-500">*</span></label>
                <select name="account_id" required class="w-full text-xs border border-gray-300 rounded-lg px-3 py-2.5 outline-none transition-all focus:border-gray-900 focus:ring-1 focus:ring-gray-900/20 bg-white">
                    <option value="">Select account…</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->account_code }} – {{ $account->account_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[140px]">
                <label class="block text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 mb-1.5">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full text-xs border border-gray-300 rounded-lg px-3 py-2.5 outline-none transition-all focus:border-gray-900 focus:ring-1 focus:ring-gray-900/20 bg-white">
            </div>
            <div class="min-w-[140px]">
                <label class="block text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 mb-1.5">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full text-xs border border-gray-300 rounded-lg px-3 py-2.5 outline-none transition-all focus:border-gray-900 focus:ring-1 focus:ring-gray-900/20 bg-white">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-gray-900 text-white rounded-lg text-xs font-semibold transition-all hover:bg-gray-800 active:scale-[0.97] border-0 cursor-pointer whitespace-nowrap">
                View Ledger
            </button>
        </form>
    </div>

    @if($selectedAccount)
        {{-- Account Info --}}
        <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h2 class="text-sm font-bold text-gray-900">{{ $selectedAccount->account_code }} – {{ $selectedAccount->account_name }}</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $selectedAccount->account_type }} Account &middot; {{ $transactions->count() }} transaction(s)</p>
                </div>
                <div class="text-right">
                    <p class="text-[0.6rem] font-semibold uppercase tracking-wide text-gray-400 mb-0.5">Running Balance</p>
                    <p class="text-lg font-bold {{ $runningBalance >= 0 ? 'text-gray-900' : 'text-red-600' }} tabular-nums">₱{{ number_format($runningBalance, 2) }}</p>
                </div>
            </div>
        </div>

        {{-- Transactions --}}
        <div class="table-wrap bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Date</th>
                            <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Journal #</th>
                            <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Description</th>
                            <th class="text-right text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Debit</th>
                            <th class="text-right text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Credit</th>
                            <th class="text-right text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" id="glGrid"></tbody>
                </table>
            </div>
            <div id="glNoResults" class="hidden">
                <div class="flex flex-col items-center py-12 text-center">
                    <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-500">No transactions found</p>
                    <p class="text-xs text-gray-400 mt-0.5">Select an account and date range to view ledger entries</p>
                </div>
            </div>
            <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
                <div class="text-xs text-gray-400" id="glInfo"></div>
                <nav id="glNav" class="flex items-center gap-1"></nav>
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 py-16">
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-500">Select an account to view its ledger</p>
                <p class="text-xs text-gray-400 mt-0.5">Choose an account from the dropdown above and click "View Ledger"</p>
            </div>
        </div>
    @endif
</div>
@endsection

@if($selectedAccount)
@push('scripts')
<script>
(function(){
    const raw = {!! json_encode($transactions->map(fn($t) => [
        'id' => $t->id,
        'journal_entry_id' => $t->journal_entry_id,
        'transaction_date' => $t->journalEntry->transaction_date,
        'journal_number' => $t->journalEntry->journal_number,
        'description' => $t->description ?? $t->journalEntry->description,
        'debit' => $t->debit,
        'credit' => $t->credit,
        'running_balance' => $t->running_balance,
    ])) !!};
    const PER = 10;
    let page = 1;

    function fmtDate(d){
        if(!d) return '—';
        const dt = new Date(d);
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return months[dt.getMonth()] + ' ' + String(dt.getDate()).padStart(2,'0') + ', ' + dt.getFullYear();
    }

    function fmtMoney(n){ return '₱' + parseFloat(n).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2}); }

    function render(){
        const tbody = document.getElementById('glGrid');
        const nr = document.getElementById('glNoResults');
        if(!raw.length){
            tbody.innerHTML = '';
            nr.classList.remove('hidden');
            updatePagination();
            return;
        }
        nr.classList.add('hidden');
        const start = (page-1)*PER;
        const slice = raw.slice(start, start+PER);
        tbody.innerHTML = '';
        slice.forEach(t => {
            const tr = document.createElement('tr');
            tr.className = 'transition-colors hover:bg-gray-50/40';
            const balClass = t.running_balance >= 0 ? 'text-gray-900' : 'text-red-600';
            tr.innerHTML =
                '<td class="px-4 py-3 text-xs text-gray-500">'+fmtDate(t.transaction_date)+'</td>' +
                '<td class="px-4 py-3"><a href="/accounting/journal-entries/'+t.journal_entry_id+'" class="text-xs font-bold text-gray-900 font-mono no-underline hover:underline">'+t.journal_number+'</a></td>' +
                '<td class="px-4 py-3 text-xs text-gray-600 max-w-[200px] truncate">'+t.description+'</td>' +
                '<td class="px-4 py-3 text-right text-xs font-semibold '+(t.debit>0?'text-gray-900':'text-gray-300')+' tabular-nums">'+(t.debit>0?fmtMoney(t.debit):'—')+'</td>' +
                '<td class="px-4 py-3 text-right text-xs font-semibold '+(t.credit>0?'text-gray-900':'text-gray-300')+' tabular-nums">'+(t.credit>0?fmtMoney(t.credit):'—')+'</td>' +
                '<td class="px-4 py-3 text-right text-xs font-bold tabular-nums '+balClass+'">'+fmtMoney(t.running_balance)+'</td>';
            tbody.appendChild(tr);
        });
        updatePagination();
    }

    function updatePagination(){
        const total = raw.length;
        const pages = Math.ceil(total/PER);
        const info  = document.getElementById('glInfo');
        const nav   = document.getElementById('glNav');
        if(!info||!nav) return;
        if(total === 0){ info.innerHTML='No transactions'; nav.innerHTML=''; return; }
        const s = (page-1)*PER+1, e = Math.min(page*PER, total);
        info.innerHTML = 'Showing <strong class="text-gray-700">' + s + '</strong>&ndash;<strong class="text-gray-700">' + e + '</strong> of <strong class="text-gray-700">' + total + '</strong>';
        if(pages<=1){ nav.innerHTML=''; return; }

        const btnClass = 'flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150';
        const activeClass = btnClass + ' bg-gray-900 text-white border-gray-900';
        const defClass    = btnClass + ' bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300';
        const disClass    = btnClass + ' bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none';

        let html = '';
        html += '<button data-p="' + (page-1) + '" class="' + (page===1?disClass:defClass) + '">&#8249;</button>';
        const half = 2;
        let winStart = Math.max(1, page - half);
        let winEnd = Math.min(pages, winStart + 4);
        if (winEnd - winStart + 1 < 5) {
            winStart = Math.max(winEnd - 4, 1);
        }
        for(let i=winStart;i<=winEnd;i++){
            html += '<button data-p="' + i + '" class="' + (i===page?activeClass:defClass) + '">' + i + '</button>';
        }
        html += '<button data-p="' + (page+1) + '" class="' + (page===pages?disClass:defClass) + '">&#8250;</button>';
        nav.innerHTML = html;
        nav.querySelectorAll('button[data-p]').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const p = parseInt(btn.dataset.p);
                if(p<1||p>pages) return;
                page = p; render();
            });
        });
    }

    render();
})();
</script>
@endpush
@endif
