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
$pendingRemits  = $remittances->where('status', 'pending');
$approvedRemits = $remittances->where('status', 'approved');
$rejectedRemits = $remittances->where('status', 'rejected');
$totalPending   = $pendingRemits->count();
$tab            = request()->query('tab', 'pending');
@endphp

{{-- Header --}}
<div class="flex items-start justify-between mb-6 flex-wrap gap-4 fade-up">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Remittance Approval</h1>
        <p class="text-sm text-gray-500 mt-0.5">Approve or reject remittances submitted by the remittance clerk</p>
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

{{-- Status tabs --}}
<div class="border-b border-gray-200 mb-6 fade-up">
    <nav class="flex gap-1 -mb-px overflow-x-auto" role="tablist">
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'pending']) }}" role="tab" class="relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 inline-flex items-center gap-2 {{ $tab === 'pending' ? 'border-violet-500 text-violet-700 bg-violet-50/60' : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">Pending @if($totalPending > 0)<span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold {{ $tab === 'pending' ? 'bg-violet-100 text-violet-800' : 'bg-gray-200 text-gray-600' }}">{{ $totalPending }}</span>@endif</a>
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'approved']) }}" role="tab" class="relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 inline-flex items-center gap-2 {{ $tab === 'approved' ? 'border-emerald-500 text-emerald-700 bg-emerald-50/60' : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">Approved <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold {{ $tab === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600' }}">{{ $approvedRemits->count() }}</span></a>
        <a href="{{ request()->fullUrlWithQuery(['tab' => 'rejected']) }}" role="tab" class="relative px-5 py-3 text-sm font-medium border-b-2 transition-all duration-200 inline-flex items-center gap-2 {{ $tab === 'rejected' ? 'border-red-500 text-red-700 bg-red-50/60' : 'border-transparent text-gray-400 hover:text-gray-600 hover:border-gray-300 hover:bg-gray-50/50' }}">Rejected <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-bold {{ $tab === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-200 text-gray-600' }}">{{ $rejectedRemits->count() }}</span></a>
    </nav>
</div>

{{-- Summary cards --}}
@php
    $selectedRemits = $tab === 'approved' ? $approvedRemits : ($tab === 'rejected' ? $rejectedRemits : $pendingRemits);
@endphp
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4"><div class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ ucfirst($tab) }} Remittances</div><div class="text-2xl font-bold text-gray-900 tabular-nums mt-1">{{ $selectedRemits->count() }}</div></div>
    <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4"><div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Collection</div><div class="text-lg font-bold text-gray-900 tabular-nums mt-1">₱{{ number_format($selectedRemits->sum('total_collection'), 2) }}</div></div>
    <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4"><div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Expenses</div><div class="text-lg font-bold text-gray-900 tabular-nums mt-1">₱{{ number_format($selectedRemits->sum('total_expenses'), 2) }}</div></div>
    <div class="stat-card bg-white border border-gray-200 rounded-xl px-5 py-4"><div class="text-xs font-semibold uppercase tracking-wider text-gray-400">Net Remittance</div><div class="text-lg font-bold text-emerald-600 tabular-nums mt-1">₱{{ number_format($selectedRemits->sum('net_remittance'), 2) }}</div></div>
</div>
{{-- Table --}}
<div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between gap-3 flex-wrap">
        <span class="text-sm font-semibold text-gray-900">Remittance records</span>
        <div class="relative"><svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35"/></svg><input type="text" id="remittanceSearch" placeholder="Search driver, route, vehicle…" class="pl-8 pr-3 py-2 w-64 text-xs border border-gray-200 rounded-xl outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200/50"></div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Route</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Vehicle</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Collection</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Expenses</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Net</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" id="remittanceGrid"></tbody>
        </table>
    </div>
    <div id="remittanceNoResults" class="hidden">
        <div class="flex flex-col items-center py-12 text-center">
            <div class="w-10 h-10 bg-gray-50 rounded-xl flex items-center justify-center text-gray-300 mb-3">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-500">No remittances found</p>
            <p class="text-xs text-gray-400 mt-0.5">Try a different search or tab</p>
        </div>
    </div>
    <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
        <div class="text-xs text-gray-400" id="remittanceInfo">Showing <strong class="text-gray-700">0</strong> remittances</div>
        <nav id="remittanceNav" class="flex items-center gap-1"></nav>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.remittanceData = {!! json_encode(
    $remittances->map(fn($r) => [
        'id'          => $r->id,
        'date'        => $r->remittance_date?->format('M d, Y'),
        'dateSort'    => $r->remittance_date?->format('Y-m-d'),
        'driver'      => $r->driver?->name ?? '',
        'route'       => $r->route->route_name ?? '',
        'vehicle'     => $r->vehicle->plate_number ?? '',
        'collection'  => (float) $r->total_collection,
        'expenses'    => (float) $r->total_expenses,
        'net'         => (float) $r->net_remittance,
        'status'      => $r->status,
        'url'         => route('remittance-approval.show', $r),
    ])->values()->all()
) !!};

(function () {
    const grid     = document.getElementById('remittanceGrid');
    const noRes    = document.getElementById('remittanceNoResults');
    const search   = document.getElementById('remittanceSearch');
    const PER = 10;
    let page = 1;
    let currentTab = '{{ $tab }}';

    function getFiltered() {
        const q = (search?.value || '').toLowerCase();
        return window.remittanceData.filter(r => {
            if (r.status !== currentTab) return false;
            if (!q) return true;
            return r.driver.toLowerCase().includes(q) ||
                   r.route.toLowerCase().includes(q) ||
                   r.vehicle.toLowerCase().includes(q);
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
            pageData.forEach(r => {
                const tr = document.createElement('tr');
                tr.className = 'transition-colors hover:bg-gray-50/40 cursor-pointer';
                tr.addEventListener('click', () => { window.location = r.url; });
                const stCls = r.status === 'pending' ? 'bg-amber-50 text-amber-700 border-amber-200'
                          : r.status === 'approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                          : 'bg-red-50 text-red-700 border-red-200';
                const stDot = r.status === 'pending' ? 'bg-amber-500' : r.status === 'approved' ? 'bg-emerald-500' : 'bg-red-500';
                tr.innerHTML = `
                    <td class="px-5 py-3">
                        <p class="text-xs font-semibold text-gray-900">${r.date}</p>
                        ${r.driver ? '<p class="text-[0.55rem] text-gray-400 mt-0.5">'+r.driver+'</p>' : ''}
                    </td>
                    <td class="px-5 py-3">
                        <p class="text-xs text-gray-600">${r.route}</p>
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-500">${r.vehicle}</td>
                    <td class="px-5 py-3 text-right text-xs tabular-nums text-gray-600">₱${r.collection.toLocaleString('en-US')}</td>
                    <td class="px-5 py-3 text-right text-xs tabular-nums text-amber-600">₱${r.expenses.toLocaleString('en-US')}</td>
                    <td class="px-5 py-3 text-right text-xs font-bold tabular-nums text-gray-900">₱${r.net.toLocaleString('en-US')}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[0.5rem] font-semibold border ${stCls}">
                            <span class="w-1.5 h-1.5 rounded-full ${stDot}"></span>
                            ${r.status.charAt(0).toUpperCase()+r.status.slice(1)}
                        </span>
                    </td>
                `;
                grid.appendChild(tr);
            });
        }
        updatePagination(total, pages);
    }

    function updatePagination(total, pages) {
        const info = document.getElementById('remittanceInfo');
        const nav  = document.getElementById('remittanceNav');
        if (!info || !nav) return;
        if (total === 0) { info.innerHTML = 'No remittances to display'; nav.innerHTML = ''; return; }
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

    if (search) {
        search.addEventListener('input', function () { page = 1; render(); });
    }

    render();
})();
</script>
@endpush
