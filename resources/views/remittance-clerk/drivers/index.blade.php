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
{{-- Header --}}
    <div class="flex items-start justify-between flex-wrap gap-4 mb-5 fade-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Drivers</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage driver profiles and status for remittance encoding</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.print.driver-report') }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition-all no-underline">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </a>
            <a href="{{ route('drivers.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold text-white bg-gray-900 hover:bg-gray-800 shadow-sm transition-all no-underline">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Driver
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold mb-4 fade-up {{ $t === 'success' ? 'bg-emerald-500/15 border border-emerald-500/25 text-emerald-700' : 'bg-red-500/15 border border-red-500/25 text-red-700' }}">
            @if($t==='success')<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>@endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 fade-up">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Drivers</p>
                <p class="text-lg font-bold text-gray-900">{{ $totalDrivers }}</p>
                <p class="text-[10px] text-gray-400">all drivers</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Active</p>
                <p class="text-lg font-bold text-gray-900">{{ $activeDrivers }}</p>
                <p class="text-[10px] text-gray-400">currently active</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Inactive</p>
                <p class="text-lg font-bold text-gray-900">{{ $inactiveDrivers }}</p>
                <p class="text-[10px] text-gray-400">currently inactive</p>
            </div>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4 flex items-center gap-3 flex-wrap fade-up">
        <div class="relative flex-1 min-w-[180px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" id="driverSearch" placeholder="Search driver…" value="{{ request('search') }}" class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-xs text-gray-700 placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all">
        </div>
        <select id="statusFilter" class="border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden fade-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm app-standard-table">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-400">
                            <a href="{{ route('drivers.index', ['sort_by' => 'name', 'sort_order' => ($sortBy === 'name' && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" class="text-gray-400 no-underline hover:text-gray-600 inline-flex items-center gap-1">
                                Name
                                @if($sortBy === 'name')<svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortOrder === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>@endif
                            </a>
                        </th>
                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-400">
                            <a href="{{ route('drivers.index', ['sort_by' => 'contact_number', 'sort_order' => ($sortBy === 'contact_number' && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" class="text-gray-400 no-underline hover:text-gray-600 inline-flex items-center gap-1">
                                Contact
                                @if($sortBy === 'contact_number')<svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortOrder === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>@endif
                            </a>
                        </th>
                        <th class="text-center px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody id="driverTbody" class="divide-y divide-gray-50">
                    @forelse($drivers as $driver)
                    @php $sc = match($driver->status) { 'active' => 'bg-emerald-500/10 text-emerald-600 border-emerald-200/30', 'pending' => 'bg-amber-500/10 text-amber-600 border-amber-200/30', default => 'bg-red-500/10 text-red-600 border-red-200/30' }; @endphp
                    <tr class="transition-colors hover:bg-gray-50/40 cursor-pointer"
                        data-name="{{ strtolower($driver->formatted_name) }}"
                        data-status="{{ $driver->status }}"
                        onclick="window.location='{{ route('drivers.show', $driver) }}'">
                        <td class="px-5 py-3">
                            <span class="text-sm font-semibold text-gray-900">{{ $driver->formatted_name }}</span>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-500 font-mono">{{ $driver->contact_number }}</td>
                        <td class="px-5 py-3 text-center">
                            <span class="app-status-badge {{ $driver->status === 'active' ? 'active' : ($driver->status === 'pending' ? 'pending' : 'inactive') }}">{{ ucfirst($driver->status) }}</span>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-12">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <p class="text-xs text-gray-400">No drivers found.</p>
                                <p class="text-[10px] text-gray-300">Add a driver to get started.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="driverNoResults" style="display:none;">
            <div class="py-12 text-center">
                <p class="text-xs text-gray-400">No results</p>
                <p class="text-[10px] text-gray-300">Try adjusting your filters.</p>
            </div>
        </div>
        <div class="flex items-center justify-between px-5 py-3 border-t border-gray-50 bg-gray-50/30">
            <p class="text-xs text-gray-400" id="driverPaginationInfo">No drivers to display</p>
            <nav class="flex items-center gap-1" id="driverPaginationBtns"></nav>
        </div>
    </div>

@endsection

@push('scripts')
<script>
(function () {
    const search  = document.getElementById('driverSearch');
    const statusF = document.getElementById('statusFilter');
    const tbody   = document.getElementById('driverTbody');
    const noRes   = document.getElementById('driverNoResults');
    const info    = document.getElementById('driverPaginationInfo');
    const btns    = document.getElementById('driverPaginationBtns');
    const perPage = 10;
    let page = 1;

    function run() {
        const q = search.value.toLowerCase().trim();
        const s = statusF.value;
        const rows = Array.from(tbody.querySelectorAll('tr[data-name]'));
        const filtered = rows.filter(r => (!q || r.dataset.name.includes(q)) && (!s || r.dataset.status === s));
        const totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
        page = Math.min(page, totalPages);
        const start = (page - 1) * perPage;
        const end = Math.min(start + perPage, filtered.length);

        rows.forEach(r => r.style.display = 'none');
        filtered.slice(start, end).forEach(r => r.style.display = '');
        noRes.style.display = filtered.length === 0 && rows.length > 0 ? 'block' : 'none';
        if (filtered.length === 0) {
            info.textContent = 'No drivers to display';
        } else {
            info.innerHTML = `Showing <strong class="text-gray-700">${start + 1}</strong>–<strong class="text-gray-700">${end}</strong> of <strong class="text-gray-700">${filtered.length}</strong>`;
        }

        let pagination = '';
        const buttonClass = 'flex items-center justify-center w-7 h-7 rounded-lg text-xs font-semibold border transition-all duration-150';
        const activeClass = buttonClass + ' bg-gray-900 text-white border-gray-900';
        const defaultClass = buttonClass + ' bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300';
        const disabledClass = buttonClass + ' bg-gray-50 text-gray-300 border-gray-100 cursor-not-allowed pointer-events-none';

        if (totalPages > 1) {
            pagination += `<button type="button" data-page="${page - 1}" aria-label="Previous page" class="${page === 1 ? disabledClass : defaultClass}">‹</button>`;
            const windowStart = Math.max(1, page - 2);
            const windowEnd = Math.min(totalPages, windowStart + 4);
            const adjustedStart = Math.max(windowEnd - 4, 1);
            for (let p = adjustedStart; p <= windowEnd; p++) {
                pagination += `<button type="button" data-page="${p}" aria-current="${p === page ? 'page' : 'false'}" class="${p === page ? activeClass : defaultClass}">${p}</button>`;
            }
            pagination += `<button type="button" data-page="${page + 1}" aria-label="Next page" class="${page === totalPages ? disabledClass : defaultClass}">›</button>`;
        }
        btns.innerHTML = pagination;
    }

    btns.addEventListener('click', event => {
        const button = event.target.closest('button[data-page]');
        if (!button) return;
        page = Number(button.dataset.page);
        run();
    });

    search.addEventListener('input', () => { page = 1; run(); });
    statusF.addEventListener('change', () => { page = 1; run(); });
    run();
})();
</script>
@endpush
