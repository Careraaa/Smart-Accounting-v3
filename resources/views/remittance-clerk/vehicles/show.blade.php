@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
    <div class="remui-page">

        <div class="prl-topbar">
            <div>
                <h1 class="prl-topbar-title">{{ $vehicle->plate_number }}</h1>
                <p class="prl-topbar-sub">
                    {{ $vehicle->route ? ($vehicle->route->origin . ' → ' . $vehicle->route->destination) : 'No route assigned' }}
                </p>
            </div>
            <div class="prl-topbar-actions">
                <a href="{{ route('vehicles.edit', $vehicle) }}" class="prl-btn-ghost">
                    <i class="feather-edit-2"></i> Edit
                </a>
                <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="d-inline"
                    data-sa-confirm="Delete this vehicle?">
                    @csrf @method('DELETE')
                    <button type="submit" class="prl-action-btn danger">
                        <i class="feather-trash-2"></i> Delete
                    </button>
                </form>
                <a href="{{ route('vehicles.index') }}" class="prl-btn-ghost">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="prl-detail-card">
            <div class="prl-detail-head">
                <h2 class="prl-detail-title">Vehicle Information</h2>
            </div>
            <div class="prl-detail-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Plate Number</span>
                        <div class="prl-field-value">{{ $vehicle->plate_number ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Operator</span>
                        <div class="prl-field-value">{{ $vehicle->operator ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Status</span>
                        <div class="mt-1">
                            @php $status = strtolower($vehicle->status ?? 'active'); @endphp
                            @if ($status === 'active')
                                <span class="prl-status s-active">Active</span>
                            @elseif ($status === 'under_maintenance')
                                <span class="prl-status s-pending">Under Maintenance</span>
                            @else
                                <span class="prl-status s-inactive">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Origin</span>
                        <div class="prl-field-value">{{ $vehicle->route->origin ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Destination</span>
                        <div class="prl-field-value">{{ $vehicle->route->destination ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Boundary Rate</span>
                        <div class="prl-field-value">
                            @if($vehicle->route && $vehicle->route->boundary)
                                ₱{{ number_format($vehicle->route->boundary, 2) }}
                            @else
                                —
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
