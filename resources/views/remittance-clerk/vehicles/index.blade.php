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
            <h1 class="prl-topbar-title">Vehicles</h1>
            <p class="prl-topbar-sub">Assign routes, track status, and keep fleet records up to date</p>
        </div>
        <div class="prl-topbar-actions">
            <a href="{{ route('vehicles.create') }}" class="prl-btn-add">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Vehicle
            </a>
            <a href="{{ route('reports.print.vehicle-route-report') }}" class="prl-btn-ghost" target="_blank">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="prl-stats prl-stats-3">
        <div class="prl-stat s-blue">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg></div>
            <div>
                <div class="prl-stat-label">Total Vehicles</div>
                <div class="prl-stat-value">{{ $totalVehicles }}</div>
                <div class="prl-stat-sub">all vehicles</div>
            </div>
        </div>
        <div class="prl-stat s-green">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div>
                <div class="prl-stat-label">Active</div>
                <div class="prl-stat-value">{{ $activeVehicles }}</div>
                <div class="prl-stat-sub">currently active</div>
            </div>
        </div>
        <div class="prl-stat s-purple">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
            <div>
                <div class="prl-stat-label">Under Maintenance</div>
                <div class="prl-stat-value">{{ $underMaintenanceVehicles }}</div>
                <div class="prl-stat-sub">in maintenance</div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="prl-filter-bar">
        <div class="prl-search-wrap">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" class="prl-search-input" id="vehicleSearch" placeholder="Search plate or route…">
        </div>
        <select class="prl-filter-select" id="statusFilter">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="under_maintenance">Under Maintenance</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="prl-table-card">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ route('vehicles.index', ['sort_by' => 'plate_number', 'sort_order' => ($sortBy === 'plate_number' && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" class="sort-link">
                                Plate Number @if($sortBy === 'plate_number')<svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortOrder === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>@endif
                            </a>
                        </th>
                        <th>Route</th>
                        <th>Operator</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody id="vehicleTbody">
                @forelse($vehicles as $vehicle)
                    @php
                        $status = strtolower($vehicle->status ?? 'active');
                        $sc = match($status) { 'active' => 's-active', 'under_maintenance' => 's-maintenance', default => 's-inactive' };
                        $label = match($status) { 'under_maintenance' => 'Maintenance', default => ucfirst($status) };
                    @endphp
                    <tr class="clickable"
                        data-name="{{ strtolower($vehicle->plate_number . ' ' . ($vehicle->route->route_name ?? '')) }}"
                        data-status="{{ $status }}"
                        onclick="window.location='{{ route('vehicles.show', $vehicle) }}'">
                        <td><strong class="prl-mono">{{ $vehicle->plate_number }}</strong></td>
                        <td>{{ $vehicle->route->route_name ?? '—' }}</td>
                        <td>{{ $vehicle->operator }}</td>
                        <td class="text-center"><span class="prl-status {{ $sc }}">{{ $label }}</span></td>
                        <form id="delete-form-{{ $vehicle->id }}" action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" style="display:none;">
                            @csrf @method('DELETE')
                        </form>
                    </tr>
                @empty
                    <tr><td colspan="4">
                        <div class="prl-empty">
                            <div class="prl-empty-icon"><svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg></div>
                            <p class="prl-empty-title">No vehicles found</p>
                            <p class="prl-empty-sub">Add a vehicle to get started.</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div id="vehicleNoResults" style="display:none;">
            <div class="prl-empty" style="padding:28px;"><p class="prl-empty-title">No results</p><p class="prl-empty-sub">Try adjusting your filters.</p></div>
        </div>
    </div>

</div>{{-- remui-page --}}
</div>{{-- col-12 --}}

{{-- Delete Modal --}}
<div class="prl-modal-overlay" id="deleteModal">
    <div class="prl-modal">
        <div class="prl-modal-icon"><svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
        <h3 class="prl-modal-title">Delete Vehicle?</h3>
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
    const search = document.getElementById('vehicleSearch');
    const statusF = document.getElementById('statusFilter');
    const tbody = document.getElementById('vehicleTbody');
    const noRes = document.getElementById('vehicleNoResults');
    function run() {
        const q = search.value.toLowerCase().trim();
        const s = statusF.value;
        const rows = Array.from(tbody.querySelectorAll('tr[data-name]'));
        const vis = rows.filter(r => (!q || r.dataset.name.includes(q)) && (!s || r.dataset.status === s));
        rows.forEach(r => r.style.display = 'none');
        vis.forEach(r => r.style.display = '');
        noRes.style.display = vis.length === 0 && rows.length > 0 ? 'block' : 'none';
    }
    search.addEventListener('input', run);
    statusF.addEventListener('change', run);
})();

let _deleteId = null;
function openDeleteModal(id, plate) {
    _deleteId = id;
    document.getElementById('deleteModalBody').textContent = 'Delete vehicle "' + plate + '"? This cannot be undone.';
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
