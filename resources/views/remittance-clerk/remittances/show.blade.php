@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Remittance Details</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('remittances.edit', $remittance) }}" class="btn btn-warning btn-sm">Edit</a>
                        <a href="{{ route('remittances.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Remittance Date</label>
                                <p class="fs-5">{{ $remittance->remittance_date }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Driver</label>
                                <p class="fs-5">{{ $remittance->driver->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">PAO</label>
                                <p class="fs-5">{{ $remittance->pao->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Route</label>
                                <p class="fs-5">{{ $remittance->route->route_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Vehicle</label>
                                <p class="fs-5">{{ $remittance->vehicle->plate_number }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Status</label>
                                <p>@if($remittance->status === 'pending')
                                                <span class="badge bg-soft-warning text-warning">{{ ucfirst($remittance->status) }}</span>
                                            @elseif($remittance->status === 'approved')
                                                <span class="badge bg-soft-success text-success">{{ ucfirst($remittance->status) }}</span>
                                            @else
                                                <span class="badge bg-soft-danger text-danger">{{ ucfirst($remittance->status) }}</span>
                                            @endif</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted">Total Collection</label>
                                <p class="fs-5 text-success fw-bold">₱{{ number_format($remittance->total_collection, 2) }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted">Total Expenses</label>
                                <p class="fs-5 text-danger fw-bold">₱{{ number_format($remittance->total_expenses, 2) }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label text-muted">Net Remittance</label>
                                <p class="fs-5 text-primary fw-bold">₱{{ number_format($remittance->net_remittance, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
