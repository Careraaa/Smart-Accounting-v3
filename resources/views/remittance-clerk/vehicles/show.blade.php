@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Vehicle Details</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-warning">Edit</a>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Plate Number</label>
                                <p class="fs-5">{{ $vehicle->plate_number }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Make</label>
                                <p class="fs-5">{{ $vehicle->make }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Model</label>
                                <p class="fs-5">{{ $vehicle->model }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Year</label>
                                <p class="fs-5">{{ $vehicle->year }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Engine Number</label>
                                <p class="fs-5">{{ $vehicle->engine_number ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Status</label>
                                <p><span class="badge bg-success">{{ $vehicle->status ?? 'Active' }}</span></p>
                            </div>
                        </div>
                    </div>

                    @if($vehicle->remarks)
                        <div class="mb-3">
                            <label class="form-label text-muted">Remarks</label>
                            <p class="fs-5">{{ $vehicle->remarks }}</p>
                        </div>
                    @endif

                    <hr>

                    <div class="d-flex gap-2">
                        <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this vehicle?')">Delete Vehicle</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
