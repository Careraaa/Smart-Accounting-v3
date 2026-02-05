@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Route Details</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('routes.edit', $route) }}" class="btn btn-warning">Edit</a>
                        <a href="{{ route('routes.index') }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Route Name</label>
                                <p class="fs-5">{{ $route->route_name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Origin</label>
                                <p class="fs-5">{{ $route->origin }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted">Destination</label>
                                <p class="fs-5">{{ $route->destination }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted">Status</label>
                        <p><span class="badge bg-success">{{ $route->status }}</span></p>
                    </div>
                    <hr>
                    <div class="d-flex gap-2">
                        <form action="{{ route('routes.destroy', $route) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this route?')">Delete Route</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
