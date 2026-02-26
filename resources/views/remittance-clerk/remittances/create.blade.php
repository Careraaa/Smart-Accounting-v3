@extends('layouts.layout')

@section('content')
<div class="col-md-8 offset-md-2">
    <div class="card">
        <div class="card-header">
            <span class="card-title mb-0">Create Remittance</span>
        </div>
        <div class="card-body">
            <form action="{{ route('remittances.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="remittance_date" class="form-label">Remittance Date <span class="text-danger">*</span></label>
                        <input type="date" name="remittance_date" id="remittance_date"
                            class="form-control @error('remittance_date') is-invalid @enderror"
                            value="{{ old('remittance_date') }}" required>
                        @error('remittance_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="driver_id" class="form-label">Driver <span class="text-danger">*</span></label>
                        <select name="driver_id" id="driver_id"
                            class="form-control @error('driver_id') is-invalid @enderror" required>
                            <option value="">— Select Driver —</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                    {{ $driver->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('driver_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="pao_id" class="form-label">PAO <span class="text-danger">*</span></label>
                        <select name="pao_id" id="pao_id"
                            class="form-control @error('pao_id') is-invalid @enderror" required>
                            <option value="">— Select PAO —</option>
                            @foreach ($paos as $pao)
                                <option value="{{ $pao->id }}" {{ old('pao_id') == $pao->id ? 'selected' : '' }}>
                                    {{ $pao->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('pao_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="vehicle_id" class="form-label">Vehicle <span class="text-danger">*</span></label>
                        <select name="vehicle_id" id="vehicle_id"
                            class="form-control @error('vehicle_id') is-invalid @enderror" required>
                            <option value="">— Select Vehicle —</option>
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->plate_number }} ({{ $vehicle->route->origin ?? 'N/A' }} - {{ $vehicle->route->destination ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="prl-section-divider">Financials</div>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <label for="total_collection" class="form-label">Total Collection <span class="text-danger">*</span></label>
                        <input type="number" name="total_collection" id="total_collection" min="0" step="0.1"
                            class="form-control @error('total_collection') is-invalid @enderror"
                            value="{{ old('total_collection') }}" required>
                        @error('total_collection')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4 mb-4">
                        <label for="total_expenses" class="form-label">Total Expenses <span class="text-danger">*</span></label>
                        <input type="number" name="total_expenses" id="total_expenses" min="0" step="1"
                            class="form-control @error('total_expenses') is-invalid @enderror"
                            value="{{ old('total_expenses') }}" required>
                        @error('total_expenses')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-4 mb-4">
                        <label for="net_remittance" class="form-label">Net Remittance <span class="text-danger">*</span></label>
                        <input type="number" name="net_remittance" id="net_remittance" min="0" step="1"
                            class="form-control @error('net_remittance') is-invalid @enderror"
                            value="{{ old('net_remittance') }}" required>
                        @error('net_remittance')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary btn-sm">Create Remittance</button>
                    <a href="{{ route('remittances.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.prl-section-divider {
    font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1.2px; color: #9898a8;
    border-bottom: 1px solid #e8e8ef;
    padding-bottom: 8px; margin-bottom: 14px;
}
</style>

@push('scripts')
    <script src="{{ asset('js/global/global-datepicker.js') }}"></script>
@endpush
@endsection