@extends('layouts.layout')

@section('content')
<div class="col-md-10 offset-md-1">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0" style="color:#1c1c1e;">{{ $route->route_name }}</h5>
            <span class="emp-view-label">{{ $route->origin }} → {{ $route->destination }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('routes.edit', $route) }}" class="emp-action-btn emp-action-edit">
                <i class="feather-edit-2 me-1"></i> Edit
            </a>
            <form action="{{ route('routes.destroy', $route) }}" method="POST"
                onsubmit="return confirm('Delete this route?')" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="emp-action-btn emp-action-danger">
                    <i class="feather-trash-2 me-1"></i> Delete
                </button>
            </form>
            <a href="{{ route('routes.index') }}" class="emp-action-btn emp-action-back">
                <i class="feather-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    {{-- Route Information --}}
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Route Information</span></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Route Name</span>
                    <div class="emp-field-value">{{ $route->route_name ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Origin</span>
                    <div class="emp-field-value">{{ $route->origin ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Destination</span>
                    <div class="emp-field-value">{{ $route->destination ?? '—' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Boundary</span>
                    <div class="emp-field-value">
                        @if($route->boundary)
                            ₱{{ number_format($route->boundary, 2) }}
                        @else
                            —
                        @endif
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Total Vehicles</span>
                    <div class="emp-field-value">
                        <span class="badge bg-primary">{{ $route->vehicles->count() }}</span>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Active Vehicles</span>
                    <div class="emp-field-value">
                        <span class="badge bg-success">{{ $route->vehicles->where('status', 'active')->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Assigned Vehicles --}}
    @if($route->vehicles->count() > 0)
        <div class="card">
            <div class="card-header"><span class="card-title mb-0">Assigned Vehicles</span></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0">
                        <thead>
                            <tr>
                                <th>Plate Number</th>
                                <th>Operator</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($route->vehicles as $vehicle)
                                <tr>
                                    <td class="align-middle"><strong>{{ $vehicle->plate_number }}</strong></td>
                                    <td class="align-middle">{{ $vehicle->operator }}</td>
                                    <td class="text-center align-middle">
                                        @php $status = strtolower($vehicle->status ?? 'active'); @endphp
                                        @if ($status === 'active')
                                            <span class="emp-badge emp-badge-active">Active</span>
                                        @elseif ($status === 'under_maintenance')
                                            <span class="emp-badge emp-badge-inactive">Under Maintenance</span>
                                        @else
                                            <span class="emp-badge emp-badge-inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('vehicles.show', $vehicle) }}"
                                                class="emp-action-btn emp-action-view" title="View Vehicle">
                                                <i class="feather-eye"></i>
                                            </a>
                                            <a href="{{ route('vehicles.edit', $vehicle) }}"
                                                class="emp-action-btn emp-action-edit" title="Edit Vehicle">
                                                <i class="feather-edit-2"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

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
