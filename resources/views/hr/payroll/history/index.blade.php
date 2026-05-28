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

    {{-- Flash messages --}}
    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="fade-up flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium
            {{ $t === 'success' ? 'bg-emerald-50 border border-emerald-200 text-emerald-700' : '' }}
            {{ $t === 'error' ? 'bg-red-50 border border-red-200 text-red-700' : '' }}
            {{ $t === 'info' ? 'bg-blue-50 border border-blue-200 text-blue-700' : '' }}">
            @if($t === 'success')
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
            @endif
            <span class="flex-1">{{ session($t) }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-{{ $t === 'success' ? 'emerald' : ($t === 'error' ? 'red' : 'blue') }}-500 hover:text-{{ $t === 'success' ? 'emerald' : ($t === 'error' ? 'red' : 'blue') }}-700 cursor-pointer bg-transparent border-none p-0 leading-none">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endif
    @endforeach

    {{-- Header --}}
    <div class="fade-up flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Payroll History</h1>
            <p class="text-sm text-gray-400 mt-0.5">View all finalized and released payroll batches</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('hr.reports.print.payroll-history-report') }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-600 transition-all hover:border-gray-400 hover:text-gray-900 active:scale-[0.97 no-underline">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Print Report
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M8 21h8M12 17v4"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Total Batches</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5">{{ $totalBatches }}</p>
                    <p class="text-[0.55rem] text-gray-400 font-mono mt-0.5">generated periods</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Total Payroll</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5">₱{{ number_format($totalPayroll,2) }}</p>
                    <p class="text-[0.55rem] text-gray-400 font-mono mt-0.5">all time</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Released Payrolls</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5">{{ $totalReleased }}</p>
                    <p class="text-[0.55rem] text-gray-400 font-mono mt-0.5">across all batches</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium">Next Cutoff</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5" style="font-size:1rem;">{{ $nextCutoffDate ? $nextCutoffDate->format('M d, Y') : 'N/A' }}</p>
                    <p class="text-[0.55rem] text-gray-400 font-mono mt-0.5">{{ $nextCutoffDate ? $nextCutoffDate->format('l') : '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Section head --}}
    <div class="fade-up flex items-center gap-2">
        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
        <h2 class="text-sm font-bold text-gray-900">Payroll Batches</h2>
    </div>

    {{-- Filter bar --}}
    <div class="fade-up flex items-center gap-3 flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <input type="text" id="prlSearch" placeholder="Search by period…" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
        </div>
        <select id="filterMonth" onchange="applyFilter()" class="border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100 cursor-pointer">
            <option value="">All Months</option>
            @for($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ $filterMonth == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromDate(null, $i)->format('F') }}</option>
            @endfor
        </select>
        <select id="filterYear" onchange="applyFilter()" class="border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100 cursor-pointer">
            <option value="">All Years</option>
            @for($i = now()->year - 5; $i <= now()->year; $i++)
                <option value="{{ $i }}" {{ $filterYear == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
    </div>

    {{-- Batch cards --}}
    <div class="table-wrap bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-50" id="prlGrid"></div>
        <div id="prlNoResults" class="hidden">
            <div class="flex flex-col items-center py-12 text-center">
                <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><path stroke-linecap="round" d="M8 21h8M12 17v4"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-500" id="prlEmptyTitle">No payroll batches yet</p>
                <p class="text-xs text-gray-400 mt-0.5" id="prlEmptySub">Generated batches will appear here.</p>
            </div>
        </div>
        {{-- Pagination --}}
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
            <div class="text-xs text-gray-400" id="prlPaginationInfo">Showing <strong class="text-gray-700">0</strong> batches</div>
            <nav id="prlPaginationNav" class="flex items-center gap-1"></nav>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
window.allHistoryBatches = {!! json_encode($allBatches->map(fn($b) => [
    'id' => $b->id,
    'period_start' => $b->period_start->format('Y-m-d'),
    'period_end' => $b->period_end->format('Y-m-d'),
    'period_start_display' => $b->period_start->format('M d'),
    'period_end_display' => $b->period_end->format('M d, Y'),
    'month_year' => $b->period_start->format('F Y'),
    'month_num' => (int) $b->period_start->format('n'),
    'year_num' => (int) $b->period_start->format('Y'),
    'is_first' => (int) $b->period_start->format('d') <= 15,
    'status' => $b->status ?? 'submitted',
    'employee_count' => $b->payrolls->count(),
    'net_pay' => (float) ($b->total_net_pay ?? $b->payrolls->sum('net_pay') ?? 0),
    'url' => route('payroll.history.batch', ['start' => $b->period_start->format('Y-m-d'), 'end' => $b->period_end->format('Y-m-d')]),
])) !!};

(function () {
    const search  = document.getElementById('prlSearch');
    const monthF  = document.getElementById('filterMonth');
    const yearF   = document.getElementById('filterYear');
    const grid    = document.getElementById('prlGrid');
    const noRes   = document.getElementById('prlNoResults');
    const emptyTitle = document.getElementById('prlEmptyTitle');
    const emptySub   = document.getElementById('prlEmptySub');
    const PER     = 10;
    let page = 1, filtered = [];

    const S_COLORS = {
        finalized: { bg: 'bg-blue-50', text: 'text-blue-700', border: 'border-blue-200', dot: 'bg-blue-500', label: 'Finalized' },
        submitted: { bg: 'bg-violet-50', text: 'text-violet-700', border: 'border-violet-200', dot: 'bg-violet-500', label: 'Submitted' },
        approved:  { bg: 'bg-emerald-50', text: 'text-emerald-700', border: 'border-emerald-200', dot: 'bg-emerald-500', label: 'Approved' },
        released:  { bg: 'bg-emerald-50', text: 'text-emerald-700', border: 'border-emerald-200', dot: 'bg-emerald-500', label: 'Released' },
        paid:      { bg: 'bg-emerald-50', text: 'text-emerald-700', border: 'border-emerald-200', dot: 'bg-emerald-500', label: 'Paid' },
    };

    function applyFilters() {
        const q = search.value.toLowerCase().trim();
        const m = monthF.value;
        const y = yearF.value;
        filtered = window.allHistoryBatches.filter(b => {
            if (m && b.month_num !== parseInt(m)) return false;
            if (y && b.year_num !== parseInt(y)) return false;
            const haystack = (b.month_year + ' ' + b.status).toLowerCase();
            if (q && !haystack.includes(q)) return false;
            return true;
        });
        page = 1;
        render();
    }

    function render() {
        const start = (page - 1) * PER;
        const end = Math.min(start + PER, filtered.length);
        const pageData = filtered.slice(start, end);
        grid.innerHTML = '';

        if (pageData.length === 0) {
            noRes.classList.remove('hidden');
            emptyTitle.textContent = filtered.length === 0 ? 'No payroll batches yet' : 'No results found';
            emptySub.textContent = filtered.length === 0 ? 'Generated batches will appear here.' : 'Try a different search or filter.';
        } else {
            noRes.classList.add('hidden');
            pageData.forEach(b => {
                const si = S_COLORS[b.status] || S_COLORS.submitted;
                const iconSvg = b.is_first
                    ? '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'
                    : '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                const gradient = b.is_first ? 'bg-gradient-to-br from-red-600 to-red-800' : 'bg-gradient-to-br from-sky-600 to-blue-800';
                const halfLabel = b.is_first ? '1st' : '2nd';

                const card = document.createElement('div');
                card.innerHTML = `
                    <a href="${b.url}" class="block no-underline text-inherit">
                        <div class="flex items-center gap-4 px-5 py-4 transition-colors hover:bg-gray-50/60">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 ${gradient} text-white">${iconSvg}</div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-gray-900">${b.month_year} — ${halfLabel} half</p>
                                <p class="text-[0.55rem] font-mono text-gray-400 mt-0.5">${b.period_start_display} – ${b.period_end_display}</p>
                            </div>
                            <div class="flex items-center gap-5 shrink-0">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[0.55rem] font-semibold border ${si.bg} ${si.text} ${si.border}">
                                    <span class="w-1.5 h-1.5 rounded-full ${si.dot}"></span>
                                    ${si.label}
                                </span>
                                <div class="text-center">
                                    <p class="text-[0.5rem] font-semibold uppercase tracking-wide text-gray-400">Employees</p>
                                    <p class="text-sm font-bold text-gray-900 tabular-nums mt-0.5">${b.employee_count}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-[0.5rem] font-semibold uppercase tracking-wide text-gray-400">Net Pay</p>
                                    <p class="text-sm font-bold text-emerald-600 tabular-nums mt-0.5">₱${b.net_pay.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    </a>
                `;
                grid.appendChild(card);
            });
        }
        updatePagination();
    }

    function updatePagination() {
        const total = filtered.length;
        const pages = Math.ceil(total / PER);
        const info  = document.getElementById('prlPaginationInfo');
        const nav   = document.getElementById('prlPaginationNav');
        if (!info || !nav) return;
        if (total === 0) { info.innerHTML = 'No batches to display'; nav.innerHTML = ''; return; }
        const s = (page - 1) * PER + 1, e = Math.min(page * PER, total);
        info.innerHTML = `Showing <strong class="text-gray-700">${s}</strong>–<strong class="text-gray-700">${e}</strong> of <strong class="text-gray-700">${total}</strong>`;
        if (pages <= 1) { nav.innerHTML = ''; return; }

        const base = `flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150`;
        const act  = `${base} bg-gray-900 text-white border-gray-900`;
        const def  = `${base} bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300`;
        const dis  = `${base} bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none`;

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
    monthF.addEventListener('change', applyFilters);
    yearF.addEventListener('change', applyFilters);
    applyFilters();
})();
</script>
@endpush
