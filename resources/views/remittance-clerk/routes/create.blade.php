@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Create New Route</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('routes.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="origin">Origin *</label>
                                    <input type="text" class="form-control @error('origin') is-invalid @enderror" name="origin" value="{{ old('origin') }}" placeholder="e.g., Marikina" required>
                                    @error('origin')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="destination">Destination *</label>
                                    <input type="text" class="form-control @error('destination') is-invalid @enderror" name="destination" value="{{ old('destination') }}" placeholder="e.g., Cubao" required>
                                    @error('destination')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Create Route</button>
                            <a href="{{ route('routes.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
