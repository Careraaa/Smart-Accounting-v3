@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Vehicles List</h5>
                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary">Add Vehicle</a>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Plate Number</th>
                                <th>Make & Model</th>
                                <th>Year</th>
                                <th>Engine Number</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicles as $vehicle)
                                <tr>
                                    <td>{{ $vehicle->plate_number }}</td>
                                    <td>{{ $vehicle->make }} {{ $vehicle->model }}</td>
                                    <td>{{ $vehicle->year }}</td>
                                    <td>{{ $vehicle->engine_number ?? 'N/A' }}</td>
                                    <td><span class="badge bg-success">{{ $vehicle->status ?? 'Active' }}</span></td>
                                    <td class="d-flex gap-2">
                                        <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-info btn-sm">View</a>
                                        <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
