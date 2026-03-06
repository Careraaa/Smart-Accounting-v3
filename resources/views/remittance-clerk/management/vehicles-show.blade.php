@extends('layouts.layout')

@section('content')
<div class="col-md-10 offset-md-1">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0" style="color:#1c1c1e;">{{ $vehicle->plate_number }}</h5>
            <span class="emp-view-label">{{ $vehicle->route ? $vehicle->route->origin . ' → ' . $vehicle->route->destination : 'No route assigned' }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('vehicles.edit', $vehicle) }}" class="emp-action-btn emp-action-edit">
                <i class="feather-edit-2 me-1"></i> Edit
            </a>
            <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST"
                onsubmit="return confirm('Delete this vehicle?')" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="emp-action-btn emp-action-danger">
                    <i class="feather-trash-2 me-1"></i> Delete
                </button>
            </form>
            <a href="{{ route('vehicles.index') }}" class="emp-action-btn emp-action-back">
                <i class="feather-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    {{-- Vehicle Information --}}
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Vehicle Information</span></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Plate Number</span>
                    <div class="emp-field-value">{{ $vehicle->plate_number ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Operator</span>
                    <div class="emp-field-value">{{ $vehicle->operator ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Status</span>
                    <div class="mt-1">
                        @php $status = strtolower($vehicle->status ?? 'active'); @endphp
                        @if ($status === 'active')
                            <span class="emp-badge emp-badge-active">Active</span>
                        @elseif ($status === 'pending')
                            <span class="emp-badge emp-badge-pending">Pending</span>
                        @else
                            <span class="emp-badge emp-badge-inactive">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Origin</span>
                    <div class="emp-field-value">{{ $vehicle->route->origin ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Destination</span>
                    <div class="emp-field-value">{{ $vehicle->route->destination ?? '—' }}</div>
                </div>
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