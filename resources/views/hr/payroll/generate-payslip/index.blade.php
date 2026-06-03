@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.filter-bar { animation:fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.table-wrap { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) 0.1s both; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    <div class="fade-up flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Pay Slips</h1>
            <p class="text-sm text-gray-400 mt-0.5">Browse payroll batches and view employee payslips</p>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="filter-bar flex items-center gap-3 flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <input type="text" id="psSearch" placeholder="Search batch…" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
        </div>
        <select id="psStatusFilter" class="border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100 cursor-pointer">
            <option value="">All Statuses</option>
            <option value="submitted">Submitted</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
        <span class="text-xs text-gray-400 ml-auto" id="psCount">0 batches</span>
    </div>

    {{-- Batch cards --}}
    <div class="table-wrap bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-50" id="psGrid"></div>
        <div id="psNoResults" class="hidden">
            <div class="flex flex-col items-center py-12 text-center">
                <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <p class="text-sm font-semibold text-gray-500">No payroll batches yet</p>
                <p class="text-xs text-gray-400 mt-0.5">Generate a payroll batch first to view payslips.</p>
            </div>
        </div>
        {{-- Pagination --}}
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
            <div class="text-xs text-gray-400" id="psPaginationInfo">Showing <strong class="text-gray-700">0</strong> batches</div>
            <nav id="psPaginationNav" class="flex items-center gap-1"></nav>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
window.allBatches = {!! json_encode($allBatches->map(fn($b) => [
    'id' => $b->id,
    'period_start' => $b->period_start->format('Y-m-d'),
    'period_end' => $b->period_end->format('Y-m-d'),
    'period_start_display' => $b->period_start->format('M d'),
    'period_end_display' => $b->period_end->format('M d, Y'),
    'month_year' => $b->period_start->format('F Y'),
    'is_first' => (int) $b->period_start->format('d') <= 15,
    'status' => $b->status,
    'payrolls_count' => (int) ($b->payrolls_count ?? $b->payrolls->count()),
    'total_net_pay' => (float) ($b->total_net_pay ?? $b->payrolls->sum('net_pay')),
    'url' => route('payroll.batch.payslips', $b),
])) !!};

(function () {
    const search  = document.getElementById('psSearch');
    const statusF = document.getElementById('psStatusFilter');
    const grid    = document.getElementById('psGrid');
    const noRes   = document.getElementById('psNoResults');
    const count   = document.getElementById('psCount');
    const PER     = 10;
    let page = 1, filtered = [];

    const STATUS_MAP = {
        draft:     { bg: 'bg-amber-50', text: 'text-amber-700', border: 'border-amber-200', dot: 'bg-amber-500', label: 'Draft' },
        submitted: { bg: 'bg-violet-50', text: 'text-violet-700', border: 'border-violet-200', dot: 'bg-violet-500', label: 'Submitted' },
        approved:  { bg: 'bg-emerald-50', text: 'text-emerald-700', border: 'border-emerald-200', dot: 'bg-emerald-500', label: 'Approved' },
        rejected:  { bg: 'bg-red-50', text: 'text-red-700', border: 'border-red-200', dot: 'bg-red-500', label: 'Rejected' },
    };

    function applyFilters() {
        const q = search.value.toLowerCase().trim();
        const st = statusF.value;
        filtered = window.allBatches.filter(b => {
            if (st && b.status !== st) return false;
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
        } else {
            noRes.classList.add('hidden');
            pageData.forEach(b => {
                const si = STATUS_MAP[b.status] || STATUS_MAP.submitted;
                const halfLabel = b.is_first ? '1st' : '2nd';

                const card = document.createElement('div');
                card.innerHTML = `
                    <a href="${b.url}" class="block no-underline text-inherit">
                        <div class="flex items-center gap-4 px-5 py-4 transition-colors hover:bg-gray-50/60">
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
                                    <p class="text-sm font-bold text-gray-900 tabular-nums mt-0.5">${b.payrolls_count}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-[0.5rem] font-semibold uppercase tracking-wide text-gray-400">Net Pay</p>
                                    <p class="text-sm font-bold text-emerald-600 tabular-nums mt-0.5">₱${b.total_net_pay.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</p>
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
        const info  = document.getElementById('psPaginationInfo');
        const nav   = document.getElementById('psPaginationNav');
        count.textContent = `${total} ${total === 1 ? 'batch' : 'batches'}`;
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
    statusF.addEventListener('change', applyFilters);
    applyFilters();
})();
</script>
@endpush
