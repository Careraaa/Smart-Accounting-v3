@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Vehicle Details</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Plate Number</label>
                                <p class="fs-5">{{ $vehicle->plate_number }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Operator</label>
                                <p class="fs-5">{{ $vehicle->operator }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Route</label>
                                <p class="fs-5">
                                    @if($vehicle->route)
                                        {{ $vehicle->route->origin }} → {{ $vehicle->route->destination }}
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Status</label>
                                <p><span class="badge bg-soft-success text-success">{{ $vehicle->status ?? 'Active' }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
