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
                                    <label for="plate_number">Plate Number *</label>
                                    <input type="text" class="form-control @error('plate_number') is-invalid @enderror" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" required>
                                    @error('plate_number')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="year">Year *</label>
                                    <input type="number" class="form-control @error('year') is-invalid @enderror" name="year" value="{{ old('year', $vehicle->year) }}" required>
                                    @error('year')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="make">Make *</label>
                                    <input type="text" class="form-control @error('make') is-invalid @enderror" name="make" value="{{ old('make', $vehicle->make) }}" required>
                                    @error('make')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="model">Model *</label>
                                    <input type="text" class="form-control @error('model') is-invalid @enderror" name="model" value="{{ old('model', $vehicle->model) }}" required>
                                    @error('model')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="engine_number">Engine Number</label>
                            <input type="text" class="form-control" name="engine_number" value="{{ old('engine_number', $vehicle->engine_number) }}">
                        </div>
                        <div class="form-group mb-3">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control" name="remarks">{{ old('remarks', $vehicle->remarks) }}</textarea>
                        </div>
                        <div class="form-group d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Vehicle</button>
                            <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
