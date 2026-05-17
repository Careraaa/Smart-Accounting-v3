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
            <h1 class="prl-topbar-title">Remittances</h1>
            <p class="prl-topbar-sub">Pending and approved remittances for review and tracking</p>
        </div>
        <div class="prl-topbar-actions">
            <a href="{{ route('remittances.create') }}" class="prl-btn-add">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add Remittance
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="prl-stats">
        <div class="prl-stat s-blue">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
            <div>
                <div class="prl-stat-label">Total</div>
                <div class="prl-stat-value">{{ $totalRemittances }}</div>
                <div class="prl-stat-sub">all remittances</div>
            </div>
        </div>
        <div class="prl-stat s-green">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <div>
                <div class="prl-stat-label">Approved</div>
                <div class="prl-stat-value">{{ $approvedCount }}</div>
                <div class="prl-stat-sub">approved</div>
            </div>
        </div>
        <div class="prl-stat s-amber">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg></div>
            <div>
                <div class="prl-stat-label">Pending</div>
                <div class="prl-stat-value">{{ $pendingCount }}</div>
                <div class="prl-stat-sub">awaiting review</div>
            </div>
        </div>
        <div class="prl-stat s-red">
            <div class="prl-stat-icon"><svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></div>
            <div>
                <div class="prl-stat-label">Rejected</div>
                <div class="prl-stat-value">{{ $rejectedRemittances }}</div>
                <div class="prl-stat-sub">rejected</div>
            </div>
        </div>
    </div>

    {{-- Pending Section --}}
    <div style="font-size:0.72rem;font-weight:900;text-transform:uppercase;letter-spacing:0.09em;color:#9ca3af;margin-bottom:10px;display:flex;align-items:center;gap:8px;">
        <span style="width:8px;height:8px;border-radius:50%;background:#d97706;display:inline-block;"></span>
        Pending Remittances
    </div>
    <div class="prl-table-card" style="margin-bottom:24px;">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ route('remittances.index', ['sort_by' => 'remittance_date', 'sort_order' => ($sortBy === 'remittance_date' && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" class="sort-link">
                                Date @if($sortBy === 'remittance_date')<svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortOrder === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>@endif
                            </a>
                        </th>
                        <th>Route</th>
                        <th>Vehicle</th>
                        <th class="text-end">Net Remittance</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($pendingRemittances as $remittance)
                    <tr class="clickable" onclick="window.location='{{ route('remittances.show', $remittance) }}'">
                        <td class="prl-mono muted">{{ $remittance->remittance_date?->format('M d, Y') }}</td>
                        <td><strong>{{ $remittance->route->route_name }}</strong></td>
                        <td class="prl-mono">{{ $remittance->vehicle->plate_number }}</td>
                        <td class="text-end">
                            <span class="prl-mono {{ $remittance->is_short_remittance ? 'red' : 'green' }}">
                                ₱{{ number_format($remittance->net_remittance, 2) }}
                            </span>
                        </td>
                        <td class="text-center"><span class="prl-status s-pending">Pending</span></td>
                        <td onclick="event.stopPropagation()">
                            <div class="prl-actions">
                                <a href="{{ route('remittances.edit', $remittance) }}" class="prl-action-btn edit" title="Edit">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                                <button type="button" class="prl-action-btn danger" title="Delete"
                                    onclick="openDeleteModal({{ $remittance->id }}, '{{ $remittance->remittance_date?->format('M d, Y') }}')">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Delete
                                </button>
                                <form id="delete-form-{{ $remittance->id }}" action="{{ route('remittances.destroy', $remittance) }}" method="POST" style="display:none;">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <div class="prl-empty" style="padding:32px;">
                            <div class="prl-empty-icon"><svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                            <p class="prl-empty-title">No pending remittances</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Approved Section --}}
    <div style="font-size:0.72rem;font-weight:900;text-transform:uppercase;letter-spacing:0.09em;color:#9ca3af;margin-bottom:10px;display:flex;align-items:center;gap:8px;">
        <span style="width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block;"></span>
        Approved Remittances
    </div>
    <div class="prl-table-card">
        <div class="prl-table-scroll">
            <table class="prl-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Route</th>
                        <th>Vehicle</th>
                        <th class="text-end">Net Remittance</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($approvedRemittances as $remittance)
                    <tr class="clickable" onclick="window.location='{{ route('remittances.show', $remittance) }}'">
                        <td class="prl-mono muted">{{ $remittance->remittance_date?->format('M d, Y') }}</td>
                        <td><strong>{{ $remittance->route->route_name }}</strong></td>
                        <td class="prl-mono">{{ $remittance->vehicle->plate_number }}</td>
                        <td class="text-end">
                            <span class="prl-mono {{ $remittance->is_short_remittance ? 'red' : 'green' }}">
                                ₱{{ number_format($remittance->net_remittance, 2) }}
                            </span>
                        </td>
                        <td class="text-center"><span class="prl-status s-approved">Approved</span></td>
                        <td onclick="event.stopPropagation()">
                            <div class="prl-actions">
                                <a href="{{ route('remittances.edit', $remittance) }}" class="prl-action-btn edit" title="Edit">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <div class="prl-empty" style="padding:32px;">
                            <div class="prl-empty-icon"><svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                            <p class="prl-empty-title">No approved remittances</p>
                        </div>
                    </td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>{{-- col-12 --}}

{{-- Delete Modal --}}
<div class="prl-modal-overlay" id="deleteModal">
    <div class="prl-modal">
        <div class="prl-modal-icon"><svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
        <h3 class="prl-modal-title">Delete Remittance?</h3>
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
let _deleteId = null;
function openDeleteModal(id, label) {
    _deleteId = id;
    document.getElementById('deleteModalBody').textContent = 'Delete remittance from "' + label + '"? This cannot be undone.';
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
