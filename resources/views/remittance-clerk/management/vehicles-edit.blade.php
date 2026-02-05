@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Edit Vehicle</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('vehicles.update', $vehicle) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="plate_number" class="form-label">Plate Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('plate_number') is-invalid @enderror" 
                                           name="plate_number" id="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" 
                                           placeholder="e.g., ABC-1234" required>
                                    @error('plate_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="operator" class="form-label">Operator <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('operator') is-invalid @enderror" 
                                           name="operator" id="operator" value="{{ old('operator', $vehicle->operator) }}" 
                                           placeholder="e.g., John Doe" required>
                                    @error('operator')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="origin" class="form-label">Origin <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('origin') is-invalid @enderror" 
                                           name="origin" id="origin" value="{{ old('origin', $vehicle->route->origin ?? '') }}" 
                                           placeholder="e.g., Marikina" required>
                                    @error('origin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="destination" class="form-label">Destination <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('destination') is-invalid @enderror" 
                                           name="destination" id="destination" value="{{ old('destination', $vehicle->route->destination ?? '') }}" 
                                           placeholder="e.g., Cubao" required>
                                    @error('destination')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-sm">Update Vehicle</button>
                            <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
