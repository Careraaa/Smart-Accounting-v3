@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.stat-card:nth-child(3) { animation-delay:0.15s; }
.stat-card:nth-child(4) { animation-delay:0.2s; }
</style>
@endpush

@section('content')
@php
$tab = request()->query('tab', 'pending');
@endphp

{{-- Header --}}
<div class="flex items-start justify-between mb-6 flex-wrap gap-4 fade-up">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Payroll Approval</h1>
        <p class="text-sm text-gray-400 mt-0.5">Review and approve payroll batches submitted by HR</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold border border-amber-200">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
            {{ $pendingCount }} pending
        </span>
    </div>
</div>

{{-- Flash --}}
@foreach(['success','error'] as $t)
    @if(session($t))
    <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium @switch($t) @case('success') bg-green-50 text-green-700 @break @case('error') bg-red-50 text-red-700 @break @endswitch fade-up mb-5">
        <span>{{ session($t) }}</span>
    </div>
    @endif
@endforeach

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <p class="text-xs text-gray-400 font-medium">Pending Batches</p>
        <p class="text-xl font-bold text-amber-600 tabular-nums mt-1">{{ $pendingCount }}</p>
    </div>
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <p class="text-xs text-gray-400 font-medium">Employees</p>
        <p class="text-xl font-bold text-gray-900 tabular-nums mt-1">{{ $totalEmpInPending }}</p>
    </div>
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <p class="text-xs text-gray-400 font-medium">Total Gross</p>
        <p class="text-lg font-bold text-gray-900 tabular-nums mt-1">₱{{ number_format($totalGrossAll, 2) }}</p>
    </div>
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <p class="text-xs text-gray-400 font-medium">Total Net</p>
        <p class="text-lg font-bold text-emerald-600 tabular-nums mt-1">₱{{ number_format($totalNetAll, 2) }}</p>
    </div>
</div>

{{-- Tabs + Filter --}}
<div class="flex flex-wrap items-end justify-between gap-4 mb-5 fade-up">
    <div class="border-b border-gray-200 flex-1">
        <nav class="flex gap-1 -mb-px" role="tablist">
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'pending']) }}" role="tab"
               class="relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 inline-flex items-center gap-2
               {{ $tab === 'pending' ? 'border-amber-500 text-amber-700 bg-amber-50/60' : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <svg class="w-4 h-4 {{ $tab === 'pending' ? 'text-amber-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>
                Pending
                @if($pendingCount > 0)<span class="ml-0.5 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold {{ $tab === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-gray-200 text-gray-600' }}">{{ $pendingCount }}</span>@endif
            </a>
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'approved']) }}" role="tab"
               class="relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 inline-flex items-center gap-2
               {{ $tab === 'approved' ? 'border-emerald-500 text-emerald-700 bg-emerald-50/60' : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <svg class="w-4 h-4 {{ $tab === 'approved' ? 'text-emerald-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Approved
                <span class="ml-0.5 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold {{ $tab === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600' }}">{{ collect($batches)->where('status','approved')->count() }}</span>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'rejected']) }}" role="tab"
               class="relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 inline-flex items-center gap-2
               {{ $tab === 'rejected' ? 'border-red-500 text-red-700 bg-red-50/60' : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">
                <svg class="w-4 h-4 {{ $tab === 'rejected' ? 'text-red-500' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
                Rejected
                <span class="ml-0.5 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold {{ $tab === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-200 text-gray-600' }}">{{ collect($batches)->where('status','rejected')->count() }}</span>
            </a>
        </nav>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <div class="relative">
            <svg class="w-3.5 h-3.5 text-gray-500 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" id="batchSearch" placeholder="Search period…" class="pl-8 pr-3 py-2 w-56 text-xs border border-gray-300 rounded-lg outline-none transition-all focus:border-gray-900 focus:ring-1 focus:ring-gray-900/20 bg-white placeholder:text-gray-400">
        </div>
    </div>
</div>

{{-- Table --}}
<div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-50">
                    <th class="text-left text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-5 py-3">Period</th>
                    <th class="text-center text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Employees</th>
                    <th class="text-right text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Gross</th>
                    <th class="text-right text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Net</th>
                    <th class="text-center text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400 px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" id="batchGrid"></tbody>
        </table>
    </div>
    <div id="batchNoResults" class="hidden">
        <div class="flex flex-col items-center py-12 text-center">
            <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-500">No batches found</p>
            <p class="text-xs text-gray-400 mt-0.5">Try a different search or tab</p>
        </div>
    </div>
    <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
        <div class="text-xs text-gray-400" id="batchInfo">Showing <strong class="text-gray-700">0</strong> batches</div>
        <nav id="batchNav" class="flex items-center gap-1"></nav>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.batchData = {!! json_encode($batches) !!};

(function () {
    const grid     = document.getElementById('batchGrid');
    const noRes    = document.getElementById('batchNoResults');
    const search   = document.getElementById('batchSearch');
    const PER = 10;
    let page = 1;
    let currentTab = '{{ $tab }}';

    function getFiltered() {
        const q = (search?.value || '').toLowerCase();
        return window.batchData.filter(b => {
            if (b.status === 'submitted' && currentTab !== 'pending') return false;
            if (b.status === 'approved'  && currentTab !== 'approved') return false;
            if (b.status === 'rejected'  && currentTab !== 'rejected') return false;
            if (!q) return true;
            return (b.month_year || '').toLowerCase().includes(q) ||
                   (b.half || '').toLowerCase().includes(q);
        });
    }

    function render() {
        const data = getFiltered();
        const total = data.length;
        const pages = Math.ceil(total / PER);
        const start = (page - 1) * PER;
        const end = Math.min(start + PER, total);
        const pageData = data.slice(start, end);
        grid.innerHTML = '';

        if (pageData.length === 0) {
            noRes.classList.remove('hidden');
        } else {
            noRes.classList.add('hidden');
            pageData.forEach(b => {
                const tr = document.createElement('tr');
                tr.className = 'transition-colors hover:bg-gray-50/40 cursor-pointer';
                tr.addEventListener('click', () => { window.location = b.url; });

                let stCls, stDot, stLbl;
                if (b.status === 'submitted') {
                    stCls = 'bg-amber-50 text-amber-700 border-amber-200';
                    stDot = 'bg-amber-500'; stLbl = 'Pending';
                } else if (b.status === 'approved') {
                    stCls = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                    stDot = 'bg-emerald-500'; stLbl = 'Approved';
                } else {
                    stCls = 'bg-red-50 text-red-700 border-red-200';
                    stDot = 'bg-red-500'; stLbl = 'Rejected';
                }

                tr.innerHTML = `
                    <td class="px-5 py-3.5">
                        <p class="text-xs font-semibold text-gray-900">${b.month_year} &mdash; ${b.half} Half</p>
                        <p class="text-[0.6rem] text-gray-400 mt-0.5">${b.period_dates}</p>
                    </td>
                    <td class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 tabular-nums">${b.count}</td>
                    <td class="px-4 py-3.5 text-right text-xs tabular-nums text-gray-600">₱${b.total_gross.toLocaleString('en-US')}</td>
                    <td class="px-4 py-3.5 text-right text-xs font-bold tabular-nums ${b.status === 'approved' ? 'text-emerald-600' : b.status === 'rejected' ? 'text-red-600' : 'text-gray-900'}">₱${b.total_net.toLocaleString('en-US')}</td>
                    <td class="px-4 py-3.5 text-center">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.5rem] font-semibold border ${stCls}">
                            <span class="w-1.5 h-1.5 rounded-full ${stDot}"></span>
                            ${stLbl}
                        </span>
                    </td>
                `;
                grid.appendChild(tr);
            });
        }
        updatePagination(total, pages);
    }

    function updatePagination(total, pages) {
        const info = document.getElementById('batchInfo');
        const nav  = document.getElementById('batchNav');
        if (!info || !nav) return;
        if (total === 0) { info.innerHTML = 'No batches to display'; nav.innerHTML = ''; return; }
        const s = (page - 1) * PER + 1, e = Math.min(page * PER, total);
        info.innerHTML = `Showing <strong class="text-gray-700">${s}</strong>–<strong class="text-gray-700">${e}</strong> of <strong class="text-gray-700">${total}</strong>`;

        if (pages <= 1) { nav.innerHTML = ''; return; }
        const base = 'flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150';
        const act  = base + ' bg-gray-900 text-white border-gray-900';
        const def  = base + ' bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300';
        const dis  = base + ' bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none';

        let html = '';
        html += `<button data-p="${page - 1}" class="${page === 1 ? dis : def}">‹</button>`;
        for (let i = 1; i <= pages; i++) {
            html += `<button data-p="${i}" class="${i === page ? act : def}">${i}</button>`;
        }
        html += `<button data-p="${page + 1}" class="${page === pages ? dis : def}">›</button>`;
        nav.innerHTML = html;
        nav.querySelectorAll('button[data-p]').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const p = parseInt(btn.dataset.p);
                if (p < 1 || p > pages) return;
                page = p;
                render();
            });
        });
    }

    if (search) {
        search.addEventListener('input', function () { page = 1; render(); });
    }

    render();
})();
</script>
@endpush