@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Vehicle Reports</span>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.print.vehicle-route-report') }}" class="btn btn-sm btn-primary" target="_blank">
                    <i class="feather-printer me-1"></i> Print
                </a>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                    <i class="feather-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            <th>Plate Number</th>
                            <th>Model</th>
                            <th>Route</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicleStats as $vehicle)
                            <tr>
                                <td><strong>{{ $vehicle['plate_number'] }}</strong></td>
                                <td>{{ $vehicle['model'] }}</td>
                                <td>{{ $vehicle['route'] }}</td>
                                <td class="text-center">
                                    @if ($vehicle['status'] === 'active')
                                        <span class="emp-badge emp-badge-approved">Active</span>
                                    @else
                                        <span class="emp-badge emp-badge-inactive">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
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

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Route Reports</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover w-100 mb-0">
                    <thead>
                        <tr>
                            <th>Route Name</th>
                            <th class="text-center">Vehicles Assigned</th>
                            <th class="text-center">Total Remittances</th>
                            <th class="text-end">Boundary Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($routeStats as $route)
                            <tr>
                                <td><strong>{{ $route['route_name'] }}</strong></td>
                                <td class="text-center">{{ $route['vehicles'] }}</td>
                                <td class="text-center">{{ $route['total_remittances'] }}</td>
                                <td class="text-end">₱{{ number_format($route['boundary'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
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
@endsection
