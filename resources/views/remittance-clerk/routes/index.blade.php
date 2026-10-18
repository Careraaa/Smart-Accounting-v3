@extends('layouts.layout')
@push('styles')
<style>
@keyframes fadeUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.93)} 100%{opacity:1;transform:scale(1)} }
.fade-up { animation:fadeUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.scale-in { animation:scaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush
@section('content')
<div class="flex items-start justify-between flex-wrap gap-4 mb-5 fade-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Routes</h1>
            <p class="text-sm text-gray-500 mt-0.5">Define boundaries and track assigned vehicles per route</p>
        </div>
        <a href="{{ route('routes.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold text-white bg-gray-900 hover:bg-gray-800 shadow-sm transition-all no-underline">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Add Route
        </a>
    </div>

    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold mb-4 fade-up {{ $t === 'success' ? 'bg-emerald-500/15 border border-emerald-500/25 text-emerald-700' : 'bg-red-500/15 border border-red-500/25 text-red-700' }}">
            @if($t==='success')<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>@endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4 fade-up">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Routes</p>
                <p class="text-lg font-bold text-gray-900">{{ $routes->count() }}</p>
                <p class="text-[10px] text-gray-400">all routes</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Vehicles</p>
                <p class="text-lg font-bold text-gray-900">{{ $totalVehicles }}</p>
                <p class="text-[10px] text-gray-400">assigned vehicles</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Active Vehicles</p>
                <p class="text-lg font-bold text-gray-900">{{ $activeVehicles }}</p>
                <p class="text-[10px] text-gray-400">currently active</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4 flex items-center gap-3 flex-wrap fade-up">
        <div class="relative flex-1 min-w-[180px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" id="routeSearch" placeholder="Search route, origin, destination…" class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-xs text-gray-700 placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all">
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden fade-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Origin</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Destination</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Route Name</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Weekday Boundary</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Weekend Boundary</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Vehicles</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Active</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="routeTbody">
                @forelse($routes as $route)
                    <tr class="transition-colors hover:bg-gray-50/40 cursor-pointer"
                        data-name="{{ strtolower(($route->origin ?? '') . ' ' . ($route->destination ?? '') . ' ' . ($route->route_name ?? '')) }}"
                        onclick="window.location='{{ route('routes.show', $route) }}'">
                        <td class="px-5 py-3 text-sm font-semibold text-gray-700">{{ $route->origin ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm font-semibold text-gray-700">{{ $route->destination ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-600">{{ $route->route_name ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm font-bold text-gray-900">₱{{ number_format($route->weekday_boundary, 2) }}</td>
                        <td class="px-5 py-3 text-sm font-bold text-gray-900">₱{{ number_format($route->weekend_boundary, 2) }}</td>
                        <td class="px-5 py-3 text-sm text-center font-bold text-gray-700">{{ $route->vehicles->count() }}</td>
                        <td class="px-5 py-3 text-sm text-center font-bold text-emerald-600">{{ $route->vehicles->where('status', 'active')->count() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">
                        <div class="text-center py-12">
                            <div class="w-12 h-12 rounded-full bg-gray-50 text-gray-300 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            </div>
                            <p class="text-xs font-semibold text-gray-400">No routes found</p>
                            <p class="text-[10px] text-gray-300 mt-0.5">Add a route to get started.</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div id="routeNoResults" style="display:none;">
            <div class="text-center py-8"><p class="text-xs font-semibold text-gray-400">No results</p><p class="text-[10px] text-gray-300 mt-0.5">Try adjusting your search.</p></div>
        </div>
        <div class="flex items-center justify-between px-4 py-3 border-t border-gray-100 bg-gray-50/50">
            <p class="text-xs text-gray-400" id="routePaginationInfo">No routes to display</p>
            <nav class="flex items-center gap-1" id="routePaginationNav"></nav>
        </div>
    </div>

@endsection

@push('scripts')
<script>
(function () {
    const search = document.getElementById('routeSearch');
    const tbody = document.getElementById('routeTbody');
    const noRes = document.getElementById('routeNoResults');
    const info = document.getElementById('routePaginationInfo');
    const nav = document.getElementById('routePaginationNav');
    const perPage = 10;
    let page = 1;

    function run() {
        const q = search.value.toLowerCase().trim();
        const rows = Array.from(tbody.querySelectorAll('tr[data-name]'));
        const filtered = rows.filter(r => !q || r.dataset.name.includes(q));
        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        page = Math.min(page, totalPages);
        const start = (page - 1) * perPage;
        const end = Math.min(start + perPage, filtered.length);

        rows.forEach(r => r.style.display = 'none');
        filtered.slice(start, end).forEach(r => r.style.display = '');
        noRes.style.display = filtered.length === 0 && rows.length > 0 ? 'block' : 'none';
        info.innerHTML = filtered.length
            ? `Showing <strong class="text-gray-700">${start + 1}</strong>–<strong class="text-gray-700">${end}</strong> of <strong class="text-gray-700">${filtered.length}</strong>`
            : 'No routes to display';

        let html = '';
        const buttonClass = 'flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150';
        const activeClass = buttonClass + ' bg-gray-900 text-white border-gray-900';
        const defaultClass = buttonClass + ' bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300';
        const disabledClass = buttonClass + ' bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none';
        if (totalPages > 1) {
            html += `<button type="button" data-page="${page - 1}" aria-label="Previous page" class="${page === 1 ? disabledClass : defaultClass}">‹</button>`;
            const windowStart = Math.max(1, page - 2);
            const windowEnd = Math.min(totalPages, windowStart + 4);
            const adjustedStart = Math.max(windowEnd - 4, 1);
            for (let p = adjustedStart; p <= windowEnd; p++) {
                html += `<button type="button" data-page="${p}" aria-current="${p === page ? 'page' : 'false'}" class="${p === page ? activeClass : defaultClass}">${p}</button>`;
            }
            html += `<button type="button" data-page="${page + 1}" aria-label="Next page" class="${page === totalPages ? disabledClass : defaultClass}">›</button>`;
        }
        nav.innerHTML = html;
    }
    nav.addEventListener('click', event => {
        const button = event.target.closest('button[data-page]');
        if (!button) return;
        page = Number(button.dataset.page);
        run();
    });
    search.addEventListener('input', () => { page = 1; run(); });
    run();
})();
</script>
@endpush
