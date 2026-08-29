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

    {{-- Flash --}}
    @foreach(['success','error'] as $t)
        @if(session($t))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium animate-[fadeSlideUp_0.3s_ease] @switch($t) @case('success') bg-green-50 text-green-700 @break @case('error') bg-red-50 text-red-700 @break @endswitch">
            <span>{{ session($t) }}</span>
        </div>
        @endif
    @endforeach

    {{-- Header --}}
    <div class="fade-up flex items-start justify-between gap-4">
        <div class="min-w-0">
            <a href="{{ route('accounting.chart-of-accounts.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer mb-2">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">{{ $chartOfAccount->account_code }} – {{ $chartOfAccount->account_name }}</h1>
            <p class="text-xs text-gray-400 mt-0.5">{{ $chartOfAccount->account_type }} Account</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            @if($chartOfAccount->is_active)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.65rem] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Active
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[0.65rem] font-semibold bg-gray-50 text-gray-500 border border-gray-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                    Inactive
                </span>
            @endif
            <a href="{{ route('accounting.chart-of-accounts.edit', $chartOfAccount) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all hover:bg-gray-50 active:scale-[0.97] no-underline">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
        </div>
    </div>

    {{-- Account Info --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-sm font-bold text-gray-900 mb-4">Account Details</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 mb-1">Account Code</p>
                <p class="text-sm font-bold text-gray-900 font-mono">{{ $chartOfAccount->account_code }}</p>
            </div>
            <div>
                <p class="text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 mb-1">Account Name</p>
                <p class="text-sm font-bold text-gray-900">{{ $chartOfAccount->account_name }}</p>
            </div>
            <div>
                <p class="text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 mb-1">Account Type</p>
                @php
                    $typeColors = [
                        'Asset'     => 'bg-blue-50 text-blue-700 border-blue-200',
                        'Liability' => 'bg-rose-50 text-rose-700 border-rose-200',
                        'Equity'    => 'bg-purple-50 text-purple-700 border-purple-200',
                        'Revenue'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'Expense'   => 'bg-amber-50 text-amber-700 border-amber-200',
                    ];
                @endphp
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.6rem] font-semibold uppercase tracking-wide border {{ $typeColors[$chartOfAccount->account_type] ?? '' }}">
                    {{ $chartOfAccount->account_type }}
                </span>
            </div>
            <div>
                <p class="text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 mb-1">Status</p>
                <p class="text-sm font-semibold {{ $chartOfAccount->is_active ? 'text-emerald-600' : 'text-gray-500' }}">{{ $chartOfAccount->is_active ? 'Active' : 'Inactive' }}</p>
            </div>
        </div>
        @if($chartOfAccount->description)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-[0.65rem] font-semibold uppercase tracking-wide text-gray-400 mb-1">Description</p>
                <p class="text-sm text-gray-600">{{ $chartOfAccount->description }}</p>
            </div>
        @endif
    </div>

    {{-- Recent Journal Entries --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50">
            <span class="text-sm font-semibold text-gray-900">Posted Journal Entries</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50">
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Date</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Journal #</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Description</th>
                        <th class="text-right text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Debit</th>
                        <th class="text-right text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Credit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="jlGrid"></tbody>
            </table>
        </div>
        <div id="jlNoResults" class="hidden">
            <div class="flex flex-col items-center py-8 text-center text-xs text-gray-400">No journal entries yet for this account.</div>
        </div>
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
            <div class="text-xs text-gray-400" id="jlInfo"></div>
            <nav id="jlNav" class="flex items-center gap-1"></nav>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const raw = {!! json_encode($journalLines->map(fn($l) => [
        'id' => $l->id,
        'journal_entry_id' => $l->journal_entry_id,
        'transaction_date' => optional($l->journalEntry)->transaction_date?->toDateString(),
        'journal_number' => $l->journalEntry->journal_number,
        'description' => $l->description ?? $l->journalEntry->description,
        'debit' => $l->debit,
        'credit' => $l->credit,
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
        const tbody = document.getElementById('jlGrid');
        const nr = document.getElementById('jlNoResults');
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
        slice.forEach(l => {
            const tr = document.createElement('tr');
            tr.className = 'transition-colors hover:bg-gray-50/40';
            tr.innerHTML =
                '<td class="px-4 py-3 text-xs text-gray-500">'+fmtDate(l.transaction_date)+'</td>' +
                '<td class="px-4 py-3"><a href="/accounting/journal-entries/'+l.journal_entry_id+'" class="text-xs font-bold text-gray-900 font-mono no-underline hover:underline">'+l.journal_number+'</a></td>' +
                '<td class="px-4 py-3 text-xs text-gray-500">'+l.description+'</td>' +
                '<td class="px-4 py-3 text-right text-xs font-semibold text-gray-900 tabular-nums">'+(l.debit>0?fmtMoney(l.debit):'—')+'</td>' +
                '<td class="px-4 py-3 text-right text-xs font-semibold text-gray-900 tabular-nums">'+(l.credit>0?fmtMoney(l.credit):'—')+'</td>';
            tbody.appendChild(tr);
        });
        updatePagination();
    }

    function updatePagination(){
        const total = raw.length;
        const pages = Math.ceil(total/PER);
        const info  = document.getElementById('jlInfo');
        const nav   = document.getElementById('jlNav');
        if(!info||!nav) return;
        if(total === 0){ info.innerHTML='No entries'; nav.innerHTML=''; return; }
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
