@extends('layouts.layout')
@section('title', '13th Month Pay')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.stat-card:nth-child(1) { animation-delay:0.05s; }
.stat-card:nth-child(2) { animation-delay:0.1s; }
.stat-card:nth-child(3) { animation-delay:0.15s; }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.table-wrap { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; animation-delay:0.2s; }
.filter-bar { animation:fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) both; animation-delay:0.15s; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    @if(session('success'))
    <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium bg-green-50 text-green-700 animate-[fadeSlideUp_0.3s_ease]">
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <div class="fade-up flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">13th Month Pay</h1>
            <p class="text-sm text-gray-400 mt-0.5">Total basic salary earned &divide; 12 &middot; Calendar year {{ $calendarYear }}</p>
        </div>
        <a href="{{ route('bonuses.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-600 border border-gray-200 rounded-xl text-xs font-semibold no-underline hover:border-gray-600 hover:text-gray-600 hover:bg-gray-50 transition-colors whitespace-nowrap cursor-pointer">
            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Bonuses
        </a>
    </div>

    <div class="grid grid-cols-3 gap-3 max-md:grid-cols-1">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Employees</p>
            <p class="text-xl font-bold text-gray-900 tabular-nums mt-1">{{ $stats['total_employees'] }}</p>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Payable</p>
            <p class="text-xl font-bold text-gray-900 tabular-nums mt-1">₱{{ number_format($stats['total_payable'], 2) }}</p>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Paid</p>
            <p class="text-xl font-bold text-gray-900 tabular-nums mt-1">₱{{ number_format($stats['total_paid'], 2) }}</p>
        </div>
    </div>

    <div class="filter-bar bg-white rounded-xl shadow-sm border border-gray-100 px-4 py-3 flex items-center gap-3 flex-wrap">
        <form method="GET" action="{{ route('payroll.thirteenth-month-pay.index') }}" class="flex items-center gap-2 flex-wrap">
            <input type="number" name="year" value="{{ $calendarYear }}"
                   class="w-20 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 bg-gray-50 outline-none transition-all focus:border-gray-400 focus:bg-white"
                   min="2000" max="2100" aria-label="Year">
            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white text-gray-600 border border-gray-200 rounded-lg text-xs font-semibold transition-all hover:border-gray-300 hover:text-gray-800 active:scale-[0.97]">Filter</button>
        </form>
        <input type="search" id="tmpSearch" placeholder="Search employee&hellip;"
               class="min-w-[200px] flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 bg-gray-50 outline-none transition-all focus:border-gray-400 focus:bg-white">
        <form method="POST" action="{{ route('payroll.thirteenth-month-pay.compute') }}" class="ml-auto">
            @csrf
            <input type="hidden" name="year" value="{{ $calendarYear }}">
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-semibold transition-all hover:bg-gray-800 active:scale-[0.97]">Compute All Employees</button>
        </form>
    </div>

    <div class="table-wrap bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[760px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Employee</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Basic Earned</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Months</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">13th Month Pay</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Paid</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Remaining</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody id="tmpBody"></tbody>
            </table>
        </div>
        <div id="tmpNoResults" class="hidden">
            <div class="flex flex-col items-center py-12 text-center">
                <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-500" id="tmpEmptyTitle">No records yet</p>
                <p class="text-xs text-gray-400 mt-0.5" id="tmpEmptySub">Click Compute All Employees to generate.</p>
            </div>
        </div>
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
            <div class="text-xs text-gray-400" id="tmpPaginationInfo">Showing <strong class="text-gray-700">0</strong> records</div>
            <nav id="tmpPaginationNav" class="flex items-center gap-1"></nav>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.tmpRecords = {!! json_encode($allRecords->map(fn($r) => [
    'id' => $r->id,
    'name' => $r->user?->name ?? 'N/A',
    'basic_earned' => (float) $r->total_basic_salary_earned,
    'months_worked' => (float) $r->months_worked,
    'thirteenth' => (float) $r->thirteenth_month_pay,
    'paid' => (float) $r->amount_paid,
    'remaining' => (float) $r->amount_remaining,
    'status' => $r->status ?? 'pending',
    'url' => route('payroll.thirteenth-month-pay.show', $r),
])) !!};

(function () {
    const search = document.getElementById('tmpSearch');
    const body   = document.getElementById('tmpBody');
    const noRes  = document.getElementById('tmpNoResults');
    const emptyT = document.getElementById('tmpEmptyTitle');
    const emptyS = document.getElementById('tmpEmptySub');
    const PER = 20;
    let page = 1, filtered = [];

    function applyFilters() {
        const q = search.value.toLowerCase().trim();
        filtered = window.tmpRecords.filter(r => {
            if (q && !r.name.toLowerCase().includes(q)) return false;
            return true;
        });
        page = 1;
        render();
    }

    function render() {
        const start = (page - 1) * PER;
        const end = Math.min(start + PER, filtered.length);
        const pageData = filtered.slice(start, end);
        body.innerHTML = '';

        if (pageData.length === 0) {
            noRes.classList.remove('hidden');
            emptyT.textContent = filtered.length === 0 ? 'No records yet' : 'No results found';
            emptyS.textContent = filtered.length === 0 ? 'Click Compute All Employees to generate.' : 'Try a different search.';
        } else {
            noRes.classList.add('hidden');
            pageData.forEach(r => {
                const statusColors = { pending: 'bg-amber-50 text-amber-700 border-amber-200', partial: 'bg-blue-50 text-blue-700 border-blue-200', paid: 'bg-emerald-50 text-emerald-700 border-emerald-200' };
                const sc = statusColors[r.status] || 'bg-gray-50 text-gray-600 border-gray-200';
                const tr = document.createElement('tr');
                tr.className = 'border-b border-gray-50 hover:bg-gray-50/60 cursor-pointer transition-colors';
                tr.tabIndex = 0;
                tr.setAttribute('role', 'link');
                tr.setAttribute('aria-label', 'View ' + r.name);
                tr.addEventListener('click', () => window.location.href = r.url);
                tr.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); window.location.href = r.url; } });
                tr.innerHTML = `
                    <td class="px-4 py-3"><span class="font-semibold text-gray-900 text-sm">${r.name}</span></td>
                    <td class="px-4 py-3 text-right font-mono tabular-nums text-gray-700">₱${r.basic_earned.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</td>
                    <td class="px-4 py-3 text-right text-gray-700">${r.months_worked.toFixed(2)}</td>
                    <td class="px-4 py-3 text-right font-mono tabular-nums text-gray-900 font-semibold">₱${r.thirteenth.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</td>
                    <td class="px-4 py-3 text-right font-mono tabular-nums text-gray-700">₱${r.paid.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</td>
                    <td class="px-4 py-3 text-right font-mono tabular-nums text-gray-700">₱${r.remaining.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</td>
                    <td class="px-4 py-3"><span class="inline-flex items-center px-2.5 py-1 rounded-full text-[0.65rem] font-semibold border ${sc}">${r.status.charAt(0).toUpperCase() + r.status.slice(1)}</span></td>
                `;
                body.appendChild(tr);
            });
        }
        updatePagination();
    }

    function updatePagination() {
        const total = filtered.length;
        const pages = Math.ceil(total / PER);
        const info  = document.getElementById('tmpPaginationInfo');
        const nav   = document.getElementById('tmpPaginationNav');
        if (!info || !nav) return;
        if (total === 0) { info.innerHTML = 'No records to display'; nav.innerHTML = ''; return; }
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

    search.addEventListener('input', applyFilters);
    applyFilters();
})();
</script>
@endpush