@extends('layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <!-- Vehicles Section -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Manage Vehicles</h5>
                        <a href="{{ route('vehicles.create') }}" class="btn btn-outline-primary border-1 rounded">Add
                            Vehicle</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover w-100" style="table-layout: fixed;">
                                <thead>
                                    <tr>
                                        <th style="width: 20%; text-align: left;">Plate Number</th>
                                        <th style="width: 15%; text-align: left;">Origin</th>
                                        <th style="width: 15%; text-align: left;">Destination</th>
                                        <th style="width: 15%; text-align: left;">Operator</th>
                                        <th style="width: 15%; text-align: center;">Status</th>
                                        <th style="width: 20%; text-align: center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $statusStyles = [
                                            'active' => 'bg-soft-success text-success',
                                            'inactive' => 'bg-soft-danger text-danger',
                                            'pending' => 'bg-soft-warning text-warning',
                                        ];
                                    @endphp

                                    @forelse($vehicles as $vehicle)
                                        <tr>
                                            <td style="text-align: left;">{{ $vehicle->plate_number }}</td>
                                            <td style="text-align: left;">{{ $vehicle->route->origin ?? 'N/A' }}</td>
                                            <td style="text-align: left;">{{ $vehicle->route->destination ?? 'N/A' }}</td>
                                            <td style="text-align: left;">{{ $vehicle->operator }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge px-3 {{ $statusStyles[strtolower($vehicle->status ?? 'active')] ?? 'bg-soft-success text-success' }}">
                                                    {{ ucfirst($vehicle->status ?? 'Active') }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('vehicles.show', $vehicle) }}"
                                                        class="btn btn-outline-info btn-sm border-1 rounded" title="View">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    <a href="{{ route('vehicles.edit', $vehicle) }}"
                                                        class="btn btn-outline-warning btn-sm border-1 rounded"
                                                        title="Edit">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                    <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-outline-danger btn-sm border-1 rounded"
                                                            onclick="return confirm('Delete this vehicle?')" title="Delete">
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

                                    <!-- Invisible spacer row to fully show bottom button outlines -->
                                    <tr style="height: 8px;">
                                        <td colspan="6"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
