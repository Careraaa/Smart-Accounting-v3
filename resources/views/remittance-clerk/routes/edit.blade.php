@extends('layouts.layout')

@section('content')
<div class="col-md-8 offset-md-2">
    <div class="card">
        <div class="card-header">
            <span class="card-title mb-0">Edit Route</span>
        </div>
        <div class="card-body">
            <form action="{{ route('routes.update', $route) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="origin" class="form-label">Origin <span class="text-danger">*</span></label>
                        <input type="text" name="origin" id="origin"
                            class="form-control @error('origin') is-invalid @enderror"
                            value="{{ old('origin', $route->origin) }}" placeholder="e.g., Marikina" required>
                        @error('origin')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="destination" class="form-label">Destination <span class="text-danger">*</span></label>
                        <input type="text" name="destination" id="destination"
                            class="form-control @error('destination') is-invalid @enderror"
                            value="{{ old('destination', $route->destination) }}" placeholder="e.g., Cubao" required>
                        @error('destination')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="boundary" class="form-label">Boundary <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="boundary" id="boundary" step="0.01" min="0"
                                class="form-control @error('boundary') is-invalid @enderror"
                                value="{{ old('boundary', $route->boundary) }}" placeholder="0.00" required>
                            @error('boundary')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary btn-sm">Update Route</button>
                    <a href="{{ route('routes.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
@endpush
@endsection
