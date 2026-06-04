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
@php
$tab = request()->query('tab', 'pending');
@endphp

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
    <div class="fade-up">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Payroll Approval</h1>
        <p class="text-sm text-gray-400 mt-0.5">Review and approve payroll batches submitted by HR</p>
    </div>

    {{-- Metrics --}}
    @php
        $accentMap = ['rose'=>['dot'=>'bg-rose-500'],'amber'=>['dot'=>'bg-amber-500'],'emerald'=>['dot'=>'bg-emerald-500'],'blue'=>['dot'=>'bg-blue-500']];
        $statLabels = [$tab === 'pending' ? 'Pending' : ucfirst($tab).' Batches', 'Total Deductions', 'Total Gross', 'Total Net'];
        $statValues = [
            $tab === 'pending' ? $pendingCount : ($tab === 'approved' ? $approvedCount : $rejectedCount),
            '₱'.number_format($tab === 'pending' ? $totalDeductionsAll : ($tab === 'approved' ? $totalDeductionsApproved : $totalDeductionsRejected),2),
            '₱'.number_format($tab === 'pending' ? $totalGrossAll : ($tab === 'approved' ? $totalGrossApproved : $totalGrossRejected),2),
            '₱'.number_format($tab === 'pending' ? $totalNetAll : ($tab === 'approved' ? $totalNetApproved : $totalNetRejected),2),
        ];
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3" id="statsGrid">
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium" id="statLabel0">{{ $statLabels[0] }}</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5" id="statValue0">{{ $statValues[0] }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium" id="statLabel2">Total Gross Pay</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5" id="statValue2">{{ $statValues[2] }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium" id="statLabel1">Total Deductions</p>
                    <p class="text-lg font-bold text-gray-900 tabular-nums mt-0.5" id="statValue1">{{ $statValues[1] }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                <div class="min-w-0">
                    <p class="text-xs text-gray-400 font-medium" id="statLabel3">Total Net Pay</p>
                    <p class="text-lg font-bold text-emerald-600 tabular-nums mt-0.5" id="statValue3">{{ $statValues[3] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs + filter --}}
    <div class="fade-up flex flex-wrap items-end justify-between gap-4 bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-3">
        <nav class="flex gap-1" role="tablist">
            <button type="button" data-tab="pending"
               class="tab-btn relative px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 inline-flex items-center gap-2
               {{ $tab === 'pending' ? 'bg-amber-50 text-amber-700' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50/50' }}">
                Pending
                @if($pendingCount > 0)<span class="ml-0.5 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold {{ $tab === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-gray-200 text-gray-600' }}">{{ $pendingCount }}</span>@endif
            </button>
            <button type="button" data-tab="approved"
               class="tab-btn relative px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 inline-flex items-center gap-2
               {{ $tab === 'approved' ? 'bg-emerald-50 text-emerald-700' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50/50' }}">
                Approved
                <span class="ml-0.5 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold {{ $tab === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600' }}">{{ collect($batches)->where('status','approved')->count() }}</span>
            </button>
            <button type="button" data-tab="rejected"
               class="tab-btn relative px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 inline-flex items-center gap-2
               {{ $tab === 'rejected' ? 'bg-red-50 text-red-700' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50/50' }}">
                Rejected
                <span class="ml-0.5 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold {{ $tab === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-200 text-gray-600' }}">{{ collect($batches)->where('status','rejected')->count() }}</span>
            </button>
        </nav>
        <div class="relative">
            <svg class="w-3.5 h-3.5 text-gray-500 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" id="batchSearch" placeholder="Search period…" class="pl-8 pr-3 py-2 w-56 text-xs border border-gray-300 rounded-lg outline-none transition-all focus:border-gray-900 focus:ring-1 focus:ring-gray-900/20 bg-white placeholder:text-gray-400">
        </div>
    </div>

    {{-- Batches --}}
    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-900">Batches</span>
            <span class="text-xs text-gray-400 tabular-nums" id="batchTotalCount">0 total</span>
        </div>
        <div class="divide-y divide-gray-50" id="batchGrid"></div>
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

</div>
@endsection

@push('scripts')
<script>
window.batchData = {!! json_encode($batches) !!};

(function () {
    const grid     = document.getElementById('batchGrid');
    const noRes    = document.getElementById('batchNoResults');
    const totalEl  = document.getElementById('batchTotalCount');
    const search   = document.getElementById('batchSearch');
    const PER = 5;
    let page = 1;
    let currentTab = '{{ $tab }}';

    const STATUS_MAP = {
        submitted: { label: 'Pending',  dot: 'bg-amber-500', text: 'text-amber-700', bg: 'bg-amber-50' },
        approved:  { label: 'Approved', dot: 'bg-emerald-500', text: 'text-emerald-700', bg: 'bg-emerald-50' },
        rejected:  { label: 'Rejected', dot: 'bg-red-500', text: 'text-red-700', bg: 'bg-red-50' },
    };

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

        if (totalEl) totalEl.textContent = total + ' total';

        if (pageData.length === 0) {
            noRes.classList.remove('hidden');
        } else {
            noRes.classList.add('hidden');
            pageData.forEach(b => {
                const si = STATUS_MAP[b.status] || STATUS_MAP.submitted;

                const div = document.createElement('div');
                div.innerHTML = `
                    <a href="${b.url}" class="flex items-center gap-4 px-5 py-3.5 transition-colors hover:bg-gray-50/60 group">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-900">${b.month_year} &mdash; ${b.half} Half</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.6rem] font-semibold uppercase tracking-wide ${si.bg} ${si.text}">
                                    <span class="w-1.5 h-1.5 rounded-full ${si.dot}"></span>
                                    ${si.label}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5 font-mono">${b.period_dates}</p>
                        </div>
                        <div class="hidden sm:flex items-center gap-6 shrink-0">
                            <div class="text-left min-w-[44px]">
                                <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Employees</p>
                                <p class="text-sm font-semibold text-gray-900 tabular-nums">${b.count}</p>
                            </div>
                            <div class="text-left min-w-[88px]">
                                <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Gross Pay</p>
                                <p class="text-sm font-semibold text-gray-900 tabular-nums">₱${b.total_gross.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</p>
                            </div>
                            <div class="text-left min-w-[88px]">
                                <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Deductions</p>
                                <p class="text-sm font-semibold text-red-500 tabular-nums">₱${b.total_deductions.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</p>
                            </div>
                            <div class="text-left min-w-[88px]">
                                <p class="text-[0.55rem] font-semibold uppercase tracking-wide text-gray-400">Net Pay</p>
                                <p class="text-sm font-bold text-emerald-600 tabular-nums">₱${b.total_net.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2})}</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 group-hover:text-gray-400 transition-colors shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                `;
                grid.appendChild(div);
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
        const half = 2;
        let winStart = Math.max(1, page - half);
        let winEnd = Math.min(pages, winStart + 4);
        if (winEnd - winStart + 1 < 5) {
            winStart = Math.max(winEnd - 4, 1);
        }
        for (let i = winStart; i <= winEnd; i++) {
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

    function computeStats(tab) {
        const status = tab === 'pending' ? 'submitted' : tab;
        const filtered = window.batchData.filter(b => b.status === status);
        const count = filtered.length;
        const gross = filtered.reduce((s, b) => s + (b.total_gross || 0), 0);
        const deductions = filtered.reduce((s, b) => s + (b.total_deductions || 0), 0);
        const net = filtered.reduce((s, b) => s + (b.total_net || 0), 0);
        return { count, gross, deductions, net };
    }

    function updateStats(tab) {
        const s = computeStats(tab);
        const fmt = n => '₱' + n.toLocaleString('en-US', {minimumFractionDigits:2,maximumFractionDigits:2});
        const labels = ['Pending Batches', 'Approved Batches', 'Rejected Batches'];
        const idx = tab === 'pending' ? 0 : tab === 'approved' ? 1 : 2;
        document.getElementById('statLabel0').textContent = labels[idx];
        document.getElementById('statValue0').textContent = s.count;
        document.getElementById('statValue1').textContent = fmt(s.deductions);
        document.getElementById('statValue2').textContent = fmt(s.gross);
        document.getElementById('statValue3').textContent = fmt(s.net);
    }

    function switchTab(tab) {
        if (tab === currentTab) return;
        currentTab = tab;
        page = 1;
        document.querySelectorAll('.tab-btn').forEach(btn => {
            const t = btn.dataset.tab;
            const isActive = t === tab;
            btn.className = `tab-btn relative px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 inline-flex items-center gap-2 ${
                isActive
                    ? t === 'pending' ? 'bg-amber-50 text-amber-700' : t === 'approved' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'
                    : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50/50'
            }`;
        });
        const url = new URL(window.location);
        url.searchParams.set('tab', tab);
        history.pushState({ tab }, '', url);
        updateStats(tab);
        render();
    }

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            switchTab(this.dataset.tab);
        });
    });

    window.addEventListener('popstate', function (e) {
        if (e.state && e.state.tab) {
            currentTab = e.state.tab;
            page = 1;
            updateStats(currentTab);
            render();
            document.querySelectorAll('.tab-btn').forEach(btn => {
                const t = btn.dataset.tab;
                const isActive = t === currentTab;
                btn.className = `tab-btn relative px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 inline-flex items-center gap-2 ${
                    isActive
                        ? t === 'pending' ? 'bg-amber-50 text-amber-700' : t === 'approved' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'
                        : 'text-gray-400 hover:text-gray-600 hover:bg-gray-50/50'
                }`;
            });
        }
    });

    if (search) {
        search.addEventListener('input', function () { page = 1; render(); });
    }

    render();
})();
</script>
@endpush