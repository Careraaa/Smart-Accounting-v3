@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Vehicles Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Manage Vehicles</h5>
                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary btn-sm">Add Vehicle</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Plate Number</th>
                                    <th>Origin</th>
                                    <th>Destination</th>
                                    <th>Operator</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicles as $vehicle)
                                    <tr>
                                        <td>{{ $vehicle->plate_number }}</td>
                                        <td>{{ $vehicle->route->origin ?? 'N/A' }}</td>
                                        <td>{{ $vehicle->route->destination ?? 'N/A' }}</td>
                                        <td>{{ $vehicle->operator }}</td>
                                        <td><span class="badge bg-soft-success text-success">{{ $vehicle->status ?? 'Active' }}</span></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('vehicles.show', $vehicle) }}" class="avatar-text avatar-md text-info" title="View">
                                                    <i class="feather-eye"></i>
                                                </a>
                                                <a href="{{ route('vehicles.edit', $vehicle) }}" class="avatar-text avatar-md text-warning" title="Edit">
                                                    <i class="feather-edit"></i>
                                                </a>
                                                <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="avatar-text avatar-md text-danger" onclick="return confirm('Delete this vehicle?')" title="Delete">
                                                        <i class="feather-trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">No vehicles found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
