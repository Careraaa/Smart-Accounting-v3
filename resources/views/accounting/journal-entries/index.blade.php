@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.stat-card:nth-child(3) { animation-delay:0.15s; }
.stat-card:nth-child(4) { animation-delay:0.2s; }
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
    <div class="fade-up flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Journal Entries</h1>
            <p class="text-sm text-gray-400 mt-0.5">Record and manage double-entry accounting transactions</p>
        </div>
        <a href="{{ route('accounting.journal-entries.create') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-900 text-white rounded-xl text-xs font-semibold transition-all hover:bg-gray-800 active:scale-[0.97] no-underline whitespace-nowrap">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            New Journal Entry
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-gray-900 shrink-0"></span>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Total Entries</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Draft</p>
                    <p class="text-lg font-bold text-amber-600 tabular-nums mt-0.5">{{ $stats['draft'] }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Posted</p>
                    <p class="text-lg font-bold text-emerald-600 tabular-nums mt-0.5">{{ $stats['posted'] }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Void</p>
                    <p class="text-lg font-bold text-red-500 tabular-nums mt-0.5">{{ $stats['void'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="filter-bar flex items-center gap-2.5 mb-4 flex-wrap">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="w-3.5 h-3.5 text-gray-500 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" id="jeSearch" placeholder="Search number or description…"
                class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-900 placeholder-gray-400 outline-none transition-all duration-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/50 hover:border-gray-300">
        </div>
        <select id="jeStatusFilter"
            class="px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none transition-all duration-200 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/50 hover:border-gray-300 cursor-pointer">
            <option value="">All Status</option>
            <option value="Draft">Draft</option>
            <option value="Posted">Posted</option>
            <option value="Void">Void</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="table-wrap bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Journal #</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Date</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Description</th>
                        <th class="text-right text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Debit</th>
                        <th class="text-right text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Credit</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Status</th>
                        <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Created By</th>
                        <th class="text-right text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="jeGrid"></tbody>
            </table>
        </div>
        <div id="jeNoResults" class="hidden">
            <div class="flex flex-col items-center py-12 text-center">
                <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-500">No journal entries found</p>
                <p class="text-xs text-gray-400 mt-0.5">Try a different search or filter</p>
            </div>
        </div>
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
            <div class="text-xs text-gray-400" id="jeInfo"></div>
            <nav id="jeNav" class="flex items-center gap-1"></nav>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const raw = {!! json_encode($journalEntries->map(fn($e) => [
        'id' => $e->id,
        'journal_number' => $e->journal_number,
        'transaction_date' => $e->transaction_date,
        'description' => $e->description,
        'total_debit' => $e->total_debit,
        'total_credit' => $e->total_credit,
        'status' => $e->status,
        'created_by_name' => $e->createdBy?->name ?? '—',
    ])) !!};
    const PER = 5;
    let page = 1, filtered = [], searchQ = '', statusF = '';

    const statusColors = {
        'Draft':  'bg-amber-50 text-amber-700 border-amber-200',
        'Posted': 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'Void':   'bg-red-50 text-red-700 border-red-200',
    };

    const search = document.getElementById('jeSearch');
    const statusFilter = document.getElementById('jeStatusFilter');

    function fmtDate(d){
        if(!d) return '—';
        const dt = new Date(d);
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return months[dt.getMonth()] + ' ' + String(dt.getDate()).padStart(2,'0') + ', ' + dt.getFullYear();
    }

    function fmtMoney(n){ return '₱' + parseFloat(n).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2}); }

    function applyFilters(){
        searchQ = search.value.trim().toLowerCase();
        statusF = statusFilter.value;
        filtered = raw.filter(e => {
            if(statusF && e.status !== statusF) return false;
            if(searchQ){
                const hay = (e.journal_number + ' ' + (e.description||'') + ' ' + (e.created_by_name||'')).toLowerCase();
                if(!hay.includes(searchQ)) return false;
            }
            return true;
        });
        page = 1;
        render();
    }

    function render(){
        const tbody = document.getElementById('jeGrid');
        const nr = document.getElementById('jeNoResults');
        if(!filtered.length){
            tbody.innerHTML = '';
            nr.classList.remove('hidden');
            updatePagination();
            return;
        }
        nr.classList.add('hidden');
        const start = (page-1)*PER;
        const slice = filtered.slice(start, start+PER);
        tbody.innerHTML = '';
        slice.forEach(e => {
            const tc = statusColors[e.status] || '';
            const tr = document.createElement('tr');
            tr.className = 'transition-colors hover:bg-gray-50/40';
            tr.innerHTML =
                '<td class="px-4 py-3"><a href="/accounting/journal-entries/'+e.id+'" class="text-xs font-bold text-gray-900 font-mono no-underline hover:underline">'+e.journal_number+'</a></td>' +
                '<td class="px-4 py-3 text-xs text-gray-500">'+fmtDate(e.transaction_date)+'</td>' +
                '<td class="px-4 py-3 text-xs text-gray-600 max-w-[200px] truncate">'+e.description+'</td>' +
                '<td class="px-4 py-3 text-right text-xs font-semibold text-gray-900 tabular-nums">'+fmtMoney(e.total_debit)+'</td>' +
                '<td class="px-4 py-3 text-right text-xs font-semibold text-gray-900 tabular-nums">'+fmtMoney(e.total_credit)+'</td>' +
                '<td class="px-4 py-3"><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.6rem] font-semibold uppercase tracking-wide border '+tc+'">'+e.status+'</span></td>' +
                '<td class="px-4 py-3 text-xs text-gray-500">'+e.created_by_name+'</td>' +
                '<td class="px-4 py-3 text-right"><a href="/accounting/journal-entries/'+e.id+'" class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors no-underline" title="View"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></a></td>';
            tbody.appendChild(tr);
        });
        updatePagination();
    }

    function updatePagination(){
        const total = filtered.length;
        const pages = Math.ceil(total/PER);
        const info  = document.getElementById('jeInfo');
        const nav   = document.getElementById('jeNav');
        if(!info||!nav) return;
        if(total === 0){ info.innerHTML='No entries to display'; nav.innerHTML=''; return; }
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

    search.addEventListener('input', applyFilters);
    statusFilter.addEventListener('change', applyFilters);
    applyFilters();
})();
</script>
@endpush
