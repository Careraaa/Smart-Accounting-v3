@extends('layouts.layout')

@push('styles')
    @include('remittance-clerk._ui-styles')
@endpush

@section('content')
<div class="col-12">
    <div class="remui-page">
        <div class="remui-backdrop"></div>

        <div class="remui-hero mb-3">
            <div>
                <h5 class="remui-title">Create Route</h5>
                <p class="remui-subtitle mb-0">Set origin, destination, and boundary rate for daily remittance.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('routes.index') }}" class="emp-action-btn emp-action-back">
                    <i class="feather-arrow-left"></i><span>Back</span>
                </a>
            </div>
        </div>

        <div class="card remui-card">
            <div class="card-header">
                <span class="card-title mb-0">Route Details</span>
            </div>
            <div class="card-body">
            <form action="{{ route('routes.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="origin" class="form-label">Origin <span class="text-danger">*</span></label>
                        <input type="text" name="origin" id="origin"
                            class="form-control @error('origin') is-invalid @enderror"
                            value="{{ old('origin') }}" placeholder="e.g., Marikina" required>
                        @error('origin')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="destination" class="form-label">Destination <span class="text-danger">*</span></label>
                        <input type="text" name="destination" id="destination"
                            class="form-control @error('destination') is-invalid @enderror"
                            value="{{ old('destination') }}" placeholder="e.g., Cubao" required>
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
                                value="{{ old('boundary') }}" placeholder="0.00" required>
                            @error('boundary')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary btn-sm">Create Route</button>
                    <a href="{{ route('routes.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@endpush
@endsection
