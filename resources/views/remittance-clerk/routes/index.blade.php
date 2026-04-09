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
                <h5 class="remui-title">Manage Routes</h5>
                <p class="remui-subtitle mb-0">Define boundaries and track assigned vehicles per route.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('routes.create') }}" class="emp-action-btn emp-action-edit">
                    <i class="feather-plus"></i><span>Add Route</span>
                </a>
            </div>
        </div>

        <div class="card remui-card">
        <div class="card-header">
            <span class="card-title mb-0">Route List</span>
        </div>
        <div class="card-body">
            {{-- Statistics Cards --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Total Routes</div>
                            <h3 class="mb-1" style="color: #0369a1;">{{ $routes->count() }}</h3>
                            <small class="text-muted">All routes</small>
                        </div>
                        <div class="card-icon" style="color: #0ea5e9; opacity: 0.2;">
                            <i class="feather-map" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Total Vehicles</div>
                            <h3 class="mb-1" style="color: #16a34a;">{{ $totalVehicles }}</h3>
                            <small class="text-muted">Assigned vehicles</small>
                        </div>
                        <div class="card-icon" style="color: #22c55e; opacity: 0.2;">
                            <i class="feather-truck" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-statistic">
                        <div class="card-body">
                            <div class="stat-label">Active Vehicles</div>
                            <h3 class="mb-1" style="color: #dc2626;">{{ $activeVehicles }}</h3>
                            <small class="text-muted">Currently active</small>
                        </div>
                        <div class="card-icon" style="color: #ef4444; opacity: 0.2;">
                            <i class="feather-check-circle" style="font-size: 2.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0 remui-table">
                    <thead>
                        <tr>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Route Name</th>
                            <th>Boundary</th>
                            <th class="text-center">Total Vehicles</th>
                            <th class="text-center">Active Vehicles</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($routes as $route)
                            <tr>
                                <td class="align-middle"><strong>{{ $route->origin ?? 'N/A' }}</strong></td>
                                <td class="align-middle"><strong>{{ $route->destination ?? 'N/A' }}</strong></td>
                                <td class="align-middle">{{ $route->route_name ?? 'N/A' }}</td>
                                <td class="align-middle">
                                    @if($route->boundary)
                                        <span style="background-color: #fef3c7; color: #d97706; padding: 4px 8px; border-radius: 4px; font-weight: 500;">
                                            ₱{{ number_format($route->boundary, 2) }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-primary">{{ $route->vehicles->count() }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-success">{{ $route->vehicles->where('status', 'active')->count() }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('routes.show', $route) }}"
                                            class="emp-action-btn emp-action-view" title="View Route">
                                            <i class="feather-eye"></i>
                                        </a>
                                        <a href="{{ route('routes.edit', $route) }}"
                                            class="emp-action-btn emp-action-edit" title="Edit Route">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                        <form action="{{ route('routes.destroy', $route) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="emp-action-btn emp-action-danger" title="Delete"
                                                onclick="return confirm('Delete this route?')">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="feather-map d-block mb-2" style="font-size:28px; opacity:.3;"></i>
                                    No routes found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
