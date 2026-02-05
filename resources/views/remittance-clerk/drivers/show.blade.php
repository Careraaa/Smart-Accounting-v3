@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Driver Details</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('drivers.edit', $driver) }}" class="btn btn-warning">Edit</a>
                        <a href="{{ route('drivers.index') }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Name</label>
                                <p class="fs-5">{{ $driver->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">License Number</label>
                                <p class="fs-5">{{ $driver->license_number }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Contact Number</label>
                                <p class="fs-5">{{ $driver->contact_number }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Email</label>
                                <p class="fs-5">{{ $driver->email }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Date of Hire</label>
                                <p class="fs-5">{{ $driver->date_of_hire ? $driver->date_of_hire->format('M d, Y') : 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Status</label>
                                <p><span class="badge bg-success">{{ $driver->status }}</span></p>
                            </div>
                        </div>
                    </div>

                    @if($driver->address)
                        <div class="mb-3">
                            <label class="form-label text-muted">Address</label>
                            <p class="fs-5">{{ $driver->address }}</p>
                        </div>
                    @endif

                    <hr>

                    <div class="d-flex gap-2">
                        <form action="{{ route('drivers.destroy', $driver) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this driver?')">Delete Driver</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
