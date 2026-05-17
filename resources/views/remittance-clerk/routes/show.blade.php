@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
    <div class="remui-page">

        <div class="prl-topbar">
            <div>
                <h1 class="prl-topbar-title">{{ $route->route_name }}</h1>
                <p class="prl-topbar-sub">{{ $route->origin }} → {{ $route->destination }}</p>
            </div>
            <div class="prl-topbar-actions">
                <a href="{{ route('routes.edit', $route) }}" class="prl-btn-ghost">
                    <i class="feather-edit-2"></i> Edit
                </a>
                <form action="{{ route('routes.destroy', $route) }}" method="POST" class="d-inline"
                    data-sa-confirm="Delete this route?">
                    @csrf @method('DELETE')
                    <button type="submit" class="prl-action-btn danger">
                        <i class="feather-trash-2"></i> Delete
                    </button>
                </form>
                <a href="{{ route('routes.index') }}" class="prl-btn-ghost">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>

        {{-- Route Information --}}
        <div class="prl-detail-card mb-3">
            <div class="prl-detail-head">
                <h2 class="prl-detail-title">Route Information</h2>
            </div>
            <div class="prl-detail-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Route Name</span>
                        <div class="prl-field-value">{{ $route->route_name ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Origin</span>
                        <div class="prl-field-value">{{ $route->origin ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Destination</span>
                        <div class="prl-field-value">{{ $route->destination ?? '—' }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Boundary</span>
                        <div class="prl-field-value">
                            @if($route->boundary)
                                ₱{{ number_format($route->boundary, 2) }}
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Total Vehicles</span>
                        <div class="prl-field-value">{{ $route->vehicles->count() }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <span class="prl-field-label">Active Vehicles</span>
                        <div class="prl-field-value">{{ $route->vehicles->where('status', 'active')->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Assigned Vehicles --}}
        @if($route->vehicles->count() > 0)
            <div class="prl-table-card">
                <div class="prl-detail-head">
                    <h2 class="prl-detail-title">Assigned Vehicles</h2>
                </div>
                <div class="table-responsive">
                    <table class="prl-table w-100 mb-0">
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
                                <tr style="cursor: pointer;" onclick="window.location='{{ route('vehicles.show', $vehicle) }}'">
                                    <td class="align-middle"><strong>{{ $vehicle->plate_number }}</strong></td>
                                    <td class="align-middle">{{ $vehicle->operator }}</td>
                                    <td class="text-center align-middle">
                                        @php $vstatus = strtolower($vehicle->status ?? 'active'); @endphp
                                        @if ($vstatus === 'active')
                                            <span class="prl-status s-active">Active</span>
                                        @elseif ($vstatus === 'under_maintenance')
                                            <span class="prl-status s-pending">Under Maintenance</span>
                                        @else
                                            <span class="prl-status s-inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle" onclick="event.stopPropagation()">
                                        <a href="{{ route('vehicles.edit', $vehicle) }}"
                                            class="prl-btn-ghost" title="Edit Vehicle">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
