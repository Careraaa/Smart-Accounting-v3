@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Create New Remittance</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('remittances.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="remittance_date">Remittance Date *</label>
                                    <input type="date" class="form-control @error('remittance_date') is-invalid @enderror" name="remittance_date" value="{{ old('remittance_date') }}" required>
                                    @error('remittance_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="driver_id">Driver *</label>
                                    <select class="form-control @error('driver_id') is-invalid @enderror" name="driver_id" required>
                                        <option value="">Select Driver</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('driver_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="pao_id">PAO *</label>
                                    <select class="form-control @error('pao_id') is-invalid @enderror" name="pao_id" required>
                                        <option value="">Select PAO</option>
                                        @foreach($paos as $pao)
                                            <option value="{{ $pao->id }}" {{ old('pao_id') == $pao->id ? 'selected' : '' }}>{{ $pao->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('pao_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="route_id">Route *</label>
                                    <select class="form-control @error('route_id') is-invalid @enderror" name="route_id" required>
                                        <option value="">Select Route</option>
                                        @foreach($routes as $route)
                                            <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>{{ $route->route_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('route_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vehicle_id">Vehicle *</label>
                                    <select class="form-control @error('vehicle_id') is-invalid @enderror" name="vehicle_id" required>
                                        <option value="">Select Vehicle</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->plate_number }}</option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status">Status *</label>
                                    <select class="form-control @error('status') is-invalid @enderror" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                    @error('status')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="total_collection">Total Collection *</label>
                                    <input type="number" step="0.01" class="form-control @error('total_collection') is-invalid @enderror" name="total_collection" value="{{ old('total_collection') }}" required>
                                    @error('total_collection')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="total_expenses">Total Expenses *</label>
                                    <input type="number" step="0.01" class="form-control @error('total_expenses') is-invalid @enderror" name="total_expenses" value="{{ old('total_expenses') }}" required>
                                    @error('total_expenses')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="net_remittance">Net Remittance *</label>
                                    <input type="number" step="0.01" class="form-control @error('net_remittance') is-invalid @enderror" name="net_remittance" value="{{ old('net_remittance') }}" required>
                                    @error('net_remittance')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Create Remittance</button>
                            <a href="{{ route('remittances.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
