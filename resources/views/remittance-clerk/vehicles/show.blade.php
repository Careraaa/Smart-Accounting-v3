@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
    <div class="remui-page">
        <div class="remui-backdrop"></div>

        <div class="remui-hero mb-3">
            <div>
                <h5 class="remui-title">{{ $vehicle->plate_number }}</h5>
                <p class="remui-subtitle mb-0">
                    {{ $vehicle->route ? ($vehicle->route->origin . ' → ' . $vehicle->route->destination) : 'No route assigned' }}
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <a href="{{ route('vehicles.edit', $vehicle) }}" class="emp-action-btn emp-action-edit">
                    <i class="feather-edit-2"></i><span>Edit</span>
                </a>
                <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST"
                    onsubmit="return confirm('Delete this vehicle?')" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="emp-action-btn emp-action-danger">
                        <i class="feather-trash-2"></i><span>Delete</span>
                    </button>
                </form>
                <a href="{{ route('vehicles.index') }}" class="emp-action-btn emp-action-back">
                    <i class="feather-arrow-left"></i><span>Back</span>
                </a>
            </div>
        </div>

    {{-- Vehicle Information --}}
    <div class="card remui-card mb-3">
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
</div>
@endsection