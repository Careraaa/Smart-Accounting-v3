@extends('layouts.layout')
@section('title', 'Bonuses')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.92)} 100%{opacity:1;transform:scale(1)} }
.stat-card { animation:scaleIn 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.table-wrap { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
.filter-bar { animation:fadeSlideUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
<div class="space-y-6">

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="fade-up flex items-center gap-3 px-5 py-3.5 rounded-xl border border-green-200 bg-green-50 text-green-800 text-sm font-semibold">
        <svg class="w-4 h-4 shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="fade-up flex items-center gap-3 px-5 py-3.5 rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm font-semibold">
        <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Topbar --}}
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900 tracking-tight">Bonuses</h1>
            <p class="text-xs text-gray-400 font-semibold mt-0.5">Manage employee bonus types and computation rules</p>
        </div>
        <a href="{{ route('bonuses.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 text-white rounded-xl text-xs font-bold transition-all hover:bg-black active:scale-[0.97] no-underline">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Create Bonus
        </a>
    </div>

    {{-- Filter bar --}}
    <div class="filter-bar flex items-center gap-3 flex-wrap">
        <div class="flex-1 min-w-[200px] max-w-sm">
            <input type="text" id="bnSearch" placeholder="Search bonuses…" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm text-gray-900 bg-white outline-none transition-all focus:border-gray-400 focus:ring-2 focus:ring-gray-100">
        </div>
        <div class="flex items-center gap-1.5">
            <button data-status="all" class="bn-stat px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer bg-gray-900 text-white">All</button>
            <button data-status="active" class="bn-stat px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-gray-600 border border-gray-200 hover:bg-gray-50">Active</button>
            <button data-status="inactive" class="bn-stat px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer bg-white text-gray-600 border border-gray-200 hover:bg-gray-50">Inactive</button>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-wrap bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Bonus Name</th>
                        <th class="px-5 py-3 text-left text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Bonus Type</th>
                        <th class="px-5 py-3 text-left text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody id="bnTbody"></tbody>
            </table>
        </div>
        <div id="bnNoResults" class="hidden">
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <p class="text-sm font-semibold text-gray-700">No bonuses found</p>
                <p class="text-xs text-gray-400 mt-1">Create one to get started.</p>
            </div>
        </div>
        {{-- Pagination --}}
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50/50 flex-wrap gap-3">
            <div class="text-xs text-gray-400" id="bnPaginationInfo">Showing <strong class="text-gray-700">0</strong> bonuses</div>
            <nav id="bnPaginationNav" class="flex items-center gap-1"></nav>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.allBonuses = {!! json_encode($allBonuses->map(fn($b) => [
    'id' => $b->id,
    'name' => $b->name,
    'name_lower' => strtolower($b->name),
    'type_label' => $b->type_label,
    'status' => $b->status,
    'rowUrl' => $b->rowUrl(),
])) !!};

(function () {
    const search  = document.getElementById('bnSearch');
    const tbody   = document.getElementById('bnTbody');
    const noRes   = document.getElementById('bnNoResults');
    const PER     = 10;
    let page = 1, filtered = [];
    let curStatus = 'all';

    function switchStatus(status) {
        curStatus = status;
        document.querySelectorAll('.bn-stat').forEach(btn => {
            const s = btn.dataset.status;
            const isActive = s === status;
            btn.className = `px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer` +
                (isActive ? ` bg-gray-900 text-white` : ` bg-white text-gray-600 border border-gray-200 hover:bg-gray-50`);
        });
        page = 1;
        applyFilters();
    }

    function applyFilters() {
        const q = search.value.toLowerCase().trim();
        filtered = window.allBonuses.filter(b => {
            if (curStatus !== 'all' && b.status !== curStatus) return false;
            if (q && !b.name_lower.includes(q)) return false;
            return true;
        });
        page = 1;
        render();
    }

    function render() {
        const start = (page - 1) * PER;
        const end = Math.min(start + PER, filtered.length);
        const pageData = filtered.slice(start, end);
        tbody.innerHTML = '';

        if (pageData.length === 0) {
            noRes.classList.remove('hidden');
        } else {
            noRes.classList.add('hidden');
            pageData.forEach(b => {
                const statusHtml = b.status === 'active'
                    ? '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 border border-green-200 text-green-700 text-[0.6rem] font-bold uppercase tracking-wide">Active</span>'
                    : '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-gray-100 border border-gray-200 text-gray-500 text-[0.6rem] font-bold uppercase tracking-wide">Inactive</span>';
                const row = document.createElement('tr');
                row.className = 'cursor-pointer transition-colors hover:bg-gray-50';
                row.onclick = () => window.location.href = b.rowUrl;
                row.innerHTML = `
                    <td class="px-5 py-3.5"><span class="font-semibold text-gray-900 text-sm">${b.name}</span></td>
                    <td class="px-5 py-3.5 text-sm text-gray-600">${b.type_label}</td>
                    <td class="px-5 py-3.5">${statusHtml}</td>
                `;
                tbody.appendChild(row);
            });
        }
        updatePagination();
    }

    function updatePagination() {
        const total = filtered.length;
        const pages = Math.ceil(total / PER);
        const info  = document.getElementById('bnPaginationInfo');
        const nav   = document.getElementById('bnPaginationNav');
        if (!info || !nav) return;
        if (total === 0) { info.innerHTML = 'No bonuses to display'; nav.innerHTML = ''; return; }
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
    document.querySelectorAll('.bn-stat').forEach(btn => {
        btn.addEventListener('click', function () { switchStatus(this.dataset.status); });
    });
    applyFilters();
})();
</script>
@endpush
