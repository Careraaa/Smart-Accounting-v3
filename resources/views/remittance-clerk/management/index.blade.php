@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Manage Vehicles</span>
            <a href="{{ route('vehicles.create') }}" class="btn btn-primary btn-sm">
                <i class="feather-plus me-1"></i> Add Vehicle
            </a>
        </div>
        <div class="card-body">
            {{-- Statistics Cards --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Total Vehicles</div>
                            <h3 class="mb-1" style="color: #0369a1;">{{ $totalVehicles }}</h3>
                            <small class="text-muted">All vehicles</small>
                        </div>
                        <div class="card-icon" style="color: #0ea5e9; opacity: 0.2;">
                            <i class="feather-truck" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Active Vehicles</div>
                            <h3 class="mb-1" style="color: #16a34a;">{{ $activeVehicles }}</h3>
                            <small class="text-muted">Currently active</small>
                        </div>
                        <div class="card-icon" style="color: #22c55e; opacity: 0.2;">
                            <i class="feather-check-circle" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Under Maintenance</div>
                            <h3 class="mb-1" style="color: #dc2626;">{{ $underMaintenanceVehicles }}</h3>
                            <small class="text-muted">Currently in maintenance</small>
                        </div>
                        <div class="card-icon" style="color: #ef4444; opacity: 0.2;">
                            <i class="feather-alert-circle" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            @php
                                $headers = [
                                    'plate_number' => 'Plate Number'
                                ];
                            @endphp
                            
                            @foreach($headers as $column => $label)
                                <th class="sortable-header @if($column === 'status') text-center @endif" data-column="{{ $column }}">
                                    <a href="{{ route('vehicles.index', ['sort_by' => $column, 'sort_order' => ($sortBy === $column && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" 
                                       class="sort-link">
                                        {{ $label }}
                                        @if($sortBy === $column)
                                            <i class="feather-arrow-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ms-1" style="font-size: 0.875rem;"></i>
                                        @else
                                            <i class="feather-arrow-up-down ms-1" style="font-size: 0.875rem; opacity: 0.3;"></i>
                                        @endif
                                    </a>
                                </th>
                            @endforeach
                            
                            <th class="sortable-header"><div class="sort-link">Origin</div></th>
                            <th class="sortable-header"><div class="sort-link">Destination</div></th>
                            <th class="sortable-header"><div class="sort-link">Boundary</div></th>
                            <th class="sortable-header"><div class="sort-link">Operator</div></th>
                            <th class="sortable-header text-center"><div class="sort-link justify-content-center">Status</div></th>
                            <th class="sortable-header text-center"><div class="sort-link justify-content-center">Actions</div></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $vehicle)
                            <tr>
                                <td class="align-middle"><strong>{{ $vehicle->plate_number }}</strong></td>
                                <td class="text-muted align-middle">{{ $vehicle->route->origin ?? 'N/A' }}</td>
                                <td class="text-muted align-middle">{{ $vehicle->route->destination ?? 'N/A' }}</td>
                                <td class="align-middle">
                                    @if($vehicle->route && $vehicle->route->boundary)
                                        <span style="background-color: #fef3c7; color: #d97706; padding: 4px 8px; border-radius: 4px; font-weight: 500;">
                                            ₱{{ number_format($vehicle->route->boundary, 2) }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
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
                                        <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="emp-action-btn emp-action-danger" title="Delete"
                                                onclick="return confirm('Delete this vehicle?')">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="feather-truck d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No vehicles found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection