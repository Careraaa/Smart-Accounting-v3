@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
<div class="remui-page">

    {{-- Flash --}}
    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="prl-flash {{ $t }}">
            @if($t==='success')<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>@endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    {{-- Topbar --}}
    <div class="prl-topbar">
        <div>
            <h1 class="prl-topbar-title">Routes</h1>
            <p class="prl-topbar-sub">Define boundaries and track assigned vehicles per route</p>
        </div>
        <div class="prl-topbar-actions">
            <a href="{{ route('routes.create') }}" class="prl-btn-add">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Route
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="prl-stats prl-stats-3">
        <div class="prl-stat s-blue">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg></div>
            <div>
                <div class="prl-stat-label">Total Routes</div>
                <div class="prl-stat-value">{{ $routes->count() }}</div>
                <div class="prl-stat-sub">all routes</div>
            </div>
        </div>
        <div class="prl-stat s-green">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg></div>
            <div>
                <div class="prl-stat-label">Total Vehicles</div>
                <div class="prl-stat-value">{{ $totalVehicles }}</div>
                <div class="prl-stat-sub">assigned vehicles</div>
            </div>
        </div>
        <div class="prl-stat s-amber">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div>
                <div class="prl-stat-label">Active Vehicles</div>
                <div class="prl-stat-value">{{ $activeVehicles }}</div>
                <div class="prl-stat-sub">currently active</div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="prl-filter-bar">
        <div class="prl-search-wrap">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" class="prl-search-input" id="routeSearch" placeholder="Search route, origin, destination…">
        </div>
    </div>

    {{-- Table --}}
    <div class="prl-table-card">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead>
                    <tr>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Route Name</th>
                        <th>Boundary</th>
                        <th class="text-center">Vehicles</th>
                        <th class="text-center">Active</th>
                    </tr>
                </thead>
                <tbody id="routeTbody">
                @forelse($routes as $route)
                    <tr class="clickable"
                        data-name="{{ strtolower(($route->origin ?? '') . ' ' . ($route->destination ?? '') . ' ' . ($route->route_name ?? '')) }}"
                        onclick="window.location='{{ route('routes.show', $route) }}'">
                        <td><strong>{{ $route->origin ?? '—' }}</strong></td>
                        <td><strong>{{ $route->destination ?? '—' }}</strong></td>
                        <td>{{ $route->route_name ?? '—' }}</td>
                        <td>
                            @if($route->boundary)
                                <span class="prl-status s-amber" style="font-family:'DM Mono',monospace;">₱{{ number_format($route->boundary, 2) }}</span>
                            @else
                                <span style="color:#9ca3af;">—</span>
                            @endif
                        </td>
                        <td class="text-center" style="font-family:'DM Mono',monospace;font-weight:700;">{{ $route->vehicles->count() }}</td>
                        <td class="text-center" style="font-family:'DM Mono',monospace;font-weight:700;color:#16a34a;">{{ $route->vehicles->where('status', 'active')->count() }}</td>
                        <form id="delete-form-{{ $route->id }}" action="{{ route('routes.destroy', $route) }}" method="POST" style="display:none;">
                            @csrf @method('DELETE')
                        </form>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <div class="prl-empty">
                            <div class="prl-empty-icon"><svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg></div>
                            <p class="prl-empty-title">No routes found</p>
                            <p class="prl-empty-sub">Add a route to get started.</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div id="routeNoResults" style="display:none;">
            <div class="prl-empty" style="padding:28px;"><p class="prl-empty-title">No results</p><p class="prl-empty-sub">Try adjusting your search.</p></div>
        </div>
    </div>

</div>{{-- remui-page --}}
</div>{{-- col-12 --}}

{{-- Delete Modal --}}
<div class="prl-modal-overlay" id="deleteModal">
    <div class="prl-modal">
        <div class="prl-modal-icon"><svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
        <h3 class="prl-modal-title">Delete Route?</h3>
        <p class="prl-modal-body" id="deleteModalBody">This action cannot be undone.</p>
        <div class="prl-modal-actions">
            <button type="button" class="prl-modal-cancel" onclick="closeModal()">Cancel</button>
            <button type="button" class="prl-modal-confirm" id="deleteConfirmBtn">Yes, Delete</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const search = document.getElementById('routeSearch');
    const tbody = document.getElementById('routeTbody');
    const noRes = document.getElementById('routeNoResults');
    function run() {
        const q = search.value.toLowerCase().trim();
        const rows = Array.from(tbody.querySelectorAll('tr[data-name]'));
        const vis = rows.filter(r => !q || r.dataset.name.includes(q));
        rows.forEach(r => r.style.display = 'none');
        vis.forEach(r => r.style.display = '');
        noRes.style.display = vis.length === 0 && rows.length > 0 ? 'block' : 'none';
    }
    search.addEventListener('input', run);
})();

let _deleteId = null;
function openDeleteModal(id, name) {
    _deleteId = id;
    document.getElementById('deleteModalBody').textContent = 'Delete route "' + name + '"? This cannot be undone.';
    document.getElementById('deleteModal').classList.add('open');
}
function closeModal() { document.getElementById('deleteModal').classList.remove('open'); }
document.getElementById('deleteConfirmBtn').addEventListener('click', function () {
    if (_deleteId) document.getElementById('delete-form-' + _deleteId).submit();
});
document.getElementById('deleteModal').addEventListener('click', function (e) { if (e.target === this) closeModal(); });
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });
</script>
@endpush
