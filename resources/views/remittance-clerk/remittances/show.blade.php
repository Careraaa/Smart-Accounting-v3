@extends('layouts.layout')

@section('content')
<div class="col-md-10 offset-md-1">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0" style="color:#1c1c1e;">{{ $remittance->remittance_date?->format('F d, Y') ?? 'Remittance' }}</h5>
            <span class="emp-view-label">{{ $remittance->route->route_name ?? '' }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('remittances.edit', $remittance) }}" class="emp-action-btn emp-action-edit">
                <i class="feather-edit-2 me-1"></i> Edit
            </a>
            <form action="{{ route('remittances.destroy', $remittance) }}" method="POST"
                onsubmit="return confirm('Delete this remittance?')" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="emp-action-btn emp-action-danger">
                    <i class="feather-trash-2 me-1"></i> Delete
                </button>
            </form>
            <a href="{{ route('remittances.index') }}" class="emp-action-btn emp-action-back">
                <i class="feather-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    {{-- Remittance Details --}}
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Remittance Details</span></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Remittance Date</span>
                    <div class="emp-field-value">{{ $remittance->remittance_date?->format('F d, Y') ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Driver</span>
                    <div class="emp-field-value">{{ $remittance->driver->name ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">PAO</span>
                    <div class="emp-field-value">{{ $remittance->pao->name ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Vehicle</span>
                    <div class="emp-field-value">{{ $remittance->vehicle->plate_number ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Route</span>
                    <div class="emp-field-value">{{ $remittance->route->route_name ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Status</span>
                    <div class="mt-1">
                        @if ($remittance->status === 'approved')
                            <span class="emp-badge emp-badge-approved">Approved</span>
                        @elseif ($remittance->status === 'pending')
                            <span class="emp-badge emp-badge-pending">Pending</span>
                        @else
                            <span class="emp-badge emp-badge-inactive">Rejected</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Financials --}}
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Financial Summary</span></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Total Collection</span>
                    <div style="font-size: 1rem; font-weight: 700; color: #16a34a;">₱{{ number_format($remittance->total_collection, 2) }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Total Expenses</span>
                    <div style="font-size: 1rem; font-weight: 700; color: #e11d48;">₱{{ number_format($remittance->total_expenses, 2) }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Net Remittance</span>
                    <div style="font-size: 1rem; font-weight: 700; color: #1c1c1e;">₱{{ number_format($remittance->net_remittance, 2) }}</div>
                </div>
            </div>

            <div class="prl-net-box mt-2">
                <span class="prl-net-label">Net Remittance</span>
                <span class="prl-net-value">₱{{ number_format($remittance->net_remittance, 2) }}</span>
            </div>
        </div>
    </div>

</div>

<style>
.emp-action-btn {
    height: 30px;
    padding: 0 12px;
    font-size: 0.815rem;
    font-weight: 500;
    white-space: nowrap;
    width: auto;
}
</style>
@endsection