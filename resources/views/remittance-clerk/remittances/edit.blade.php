@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Edit Remittance</h5>
                </div>
                <div class="card-body">
                    @if(in_array($remittance->status, ['approved', 'rejected']))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="feather-alert-triangle"></i>
                            <strong>Notice:</strong> This remittance is currently {{ ucfirst($remittance->status) }}. Any changes will reset the status back to <strong>Pending</strong> for re-approval.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <form action="{{ route('remittances.update', $remittance) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="remittance_date">Remittance Date *</label>
                                    <input type="date" class="form-control @error('remittance_date') is-invalid @enderror" name="remittance_date" value="{{ old('remittance_date', $remittance->remittance_date ? $remittance->remittance_date->format('Y-m-d') : '') }}" required>
                                    @error('remittance_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="driver_id">Driver *</label>
                                    <select class="form-control @error('driver_id') is-invalid @enderror" name="driver_id" required>
                                        <option value="">Select Driver</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}" {{ old('driver_id', $remittance->driver_id) == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
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
                                            <option value="{{ $pao->id }}" {{ old('pao_id', $remittance->pao_id) == $pao->id ? 'selected' : '' }}>{{ $pao->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('pao_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="vehicle_id">Vehicle *</label>
                                    <select class="form-control @error('vehicle_id') is-invalid @enderror" name="vehicle_id" required>
                                        <option value="">Select Vehicle</option>
                                        @foreach($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $remittance->vehicle_id) == $vehicle->id ? 'selected' : '' }}>{{ $vehicle->plate_number }} ({{ $vehicle->route->origin ?? 'N/A' }} - {{ $vehicle->route->destination ?? 'N/A' }})</option>
                                        @endforeach
                                    </select>
                                    @error('vehicle_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="total_collection">Total Collection *</label>
                                    <input type="number" min = "0" step="0.1" class="form-control @error('total_collection') is-invalid @enderror" name="total_collection" value="{{ old('total_collection', $remittance->total_collection) }}" required>
                                    @error('total_collection')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="total_expenses">Total Expenses *</label>
                                    <input type="number" min = "0" step="0.1" class="form-control @error('total_expenses') is-invalid @enderror" name="total_expenses" value="{{ old('total_expenses', $remittance->total_expenses) }}" required>
                                    @error('total_expenses')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="net_remittance">Net Remittance *</label>
                                    <input type="number" min = "0" step="0.1" class="form-control @error('net_remittance') is-invalid @enderror" name="net_remittance" value="{{ old('net_remittance', $remittance->net_remittance) }}" required>
                                    @error('net_remittance')<span class="invalid-feedback">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">Update Remittance</button>
                            <a href="{{ route('remittances.index') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('js/global/global-datepicker.js') }}"></script>
    @endpush
</div>
@endsection
