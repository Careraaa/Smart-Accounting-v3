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
                <h5 class="remui-title">{{ $route->route_name }}</h5>
                <p class="remui-subtitle mb-0">{{ $route->origin }} → {{ $route->destination }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <a href="{{ route('routes.edit', $route) }}" class="emp-action-btn emp-action-edit">
                    <i class="feather-edit-2"></i><span>Edit</span>
                </a>
                <form action="{{ route('routes.destroy', $route) }}" method="POST"
                    onsubmit="return confirm('Delete this route?')" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="emp-action-btn emp-action-danger">
                        <i class="feather-trash-2"></i><span>Delete</span>
                    </button>
                </form>
                <a href="{{ route('routes.index') }}" class="emp-action-btn emp-action-back">
                    <i class="feather-arrow-left"></i><span>Back</span>
                </a>
            </div>
        </div>

    {{-- Route Information --}}
    <div class="card remui-card mb-3">
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
        <div class="card remui-card">
            <div class="card-header"><span class="card-title mb-0">Assigned Vehicles</span></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover w-100 mb-0 remui-table">
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
</div>
@endsection
