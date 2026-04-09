@extends('layouts.layout')

@section('content')
<div class="col-md-8 offset-md-2">
    <div class="card">
        <div class="card-header">
            <span class="card-title mb-0">Edit Remittance</span>
        </div>
        <div class="card-body">

            @if(in_array($remittance->status, ['approved', 'rejected']))
                <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
                    <i class="feather-alert-triangle me-1"></i>
                    <strong>Notice:</strong> This remittance is currently {{ ucfirst($remittance->status) }}. Any changes will reset the status back to <strong>Pending</strong> for re-approval.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('remittances.update', $remittance) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="remittance_date" class="form-label">Remittance Date <span class="text-danger">*</span></label>
                        <input type="date" name="remittance_date" id="remittance_date"
                            class="form-control @error('remittance_date') is-invalid @enderror"
                            value="{{ old('remittance_date', $remittance->remittance_date?->format('Y-m-d')) }}" required>
                        @error('remittance_date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="driver_id" class="form-label">Driver <span class="text-danger">*</span></label>
                        <select name="driver_id" id="driver_id"
                            class="form-control @error('driver_id') is-invalid @enderror" required>
                            <option value="">— Select Driver —</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ old('driver_id', $remittance->driver_id) == $driver->id ? 'selected' : '' }}>
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
                                <option value="{{ $pao->id }}" {{ old('pao_id', $remittance->pao_id) == $pao->id ? 'selected' : '' }}>
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
                                <option value="{{ $vehicle->id }}" data-boundary="{{ $vehicle->route->boundary ?? 0 }}" {{ old('vehicle_id', $remittance->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->plate_number }} ({{ $vehicle->route->origin ?? 'N/A' }} - {{ $vehicle->route->destination ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="prl-section-divider">Financials</div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="total_collection" class="form-label">Total Collection <span class="text-danger">*</span></label>
                        <input type="number" name="total_collection" id="total_collection" min="0" step="0.1"
                            class="form-control @error('total_collection') is-invalid @enderror"
                            value="{{ old('total_collection', $remittance->total_collection) }}" required>
                        @error('total_collection')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="total_expenses" class="form-label">Total Expenses <span class="text-danger">*</span></label>
                        <input type="number" name="total_expenses" id="total_expenses" min="0" step="0.1"
                            class="form-control @error('total_expenses') is-invalid @enderror"
                            value="{{ old('total_expenses', $remittance->total_expenses) }}" required>
                        @error('total_expenses')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="boundary_display" class="form-label">Boundary Rate</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="text" id="boundary_display"
                                class="form-control" value="{{ number_format($remittance->boundary ?? $remittance->vehicle->route->boundary ?? 0, 2) }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="net_remittance" class="form-label">Net Remittance <span class="text-danger">*</span></label>
                        <input type="number" name="net_remittance" id="net_remittance" min="0" step="0.1"
                            class="form-control @error('net_remittance') is-invalid @enderror"
                            value="{{ old('net_remittance', $remittance->net_remittance) }}" required>
                        @error('net_remittance')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary btn-sm">Update Remittance</button>
                    <a href="{{ route('remittances.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const vehicleSelect = document.getElementById('vehicle_id');
            const boundaryDisplay = document.getElementById('boundary_display');
            const netRemittanceInput = document.getElementById('net_remittance');
            const shortRemittanceSection = document.getElementById('short-remittance-section');

            function updateShortRemittance() {
                const netRemittance = parseFloat(netRemittanceInput.value) || 0;

                // Check if it's a short remittance (negative net remittance)
                if (netRemittance < 0) {
                    shortRemittanceSection.style.display = 'flex';
                    shortRemittanceSection.classList.add('row');
                    
                    const shortAmount = Math.abs(netRemittance);
                    const share = shortAmount / 2;

                    // Update display fields for calculation
                    const shortAmountDisplay = document.getElementById('short_amount_display_calc');
                    const driverShareDisplay = document.getElementById('driver_share_display_calc');
                    const paoShareDisplay = document.getElementById('pao_share_display_calc');
                    
                    if (shortAmountDisplay) shortAmountDisplay.value = shortAmount.toFixed(2);
                    if (driverShareDisplay) driverShareDisplay.value = share.toFixed(2);
                    if (paoShareDisplay) paoShareDisplay.value = share.toFixed(2);

                    // Store values in hidden inputs for form submission
                    if (!document.getElementById('is_short_hidden')) {
                        const form = netRemittanceInput.closest('form');
                        const shortInput = document.createElement('input');
                        shortInput.type = 'hidden';
                        shortInput.id = 'is_short_hidden';
                        shortInput.name = 'is_short_remittance';
                        shortInput.value = '1';
                        form.appendChild(shortInput);

                        const shortAmountInput = document.createElement('input');
                        shortAmountInput.type = 'hidden';
                        shortAmountInput.id = 'short_amount_hidden';
                        shortAmountInput.name = 'short_amount';
                        shortAmountInput.value = shortAmount.toFixed(2);
                        form.appendChild(shortAmountInput);

                        const driverShareInput = document.createElement('input');
                        driverShareInput.type = 'hidden';
                        driverShareInput.id = 'driver_share_hidden';
                        driverShareInput.name = 'driver_share';
                        driverShareInput.value = share.toFixed(2);
                        form.appendChild(driverShareInput);

                        const paoShareInput = document.createElement('input');
                        paoShareInput.type = 'hidden';
                        paoShareInput.id = 'pao_share_hidden';
                        paoShareInput.name = 'pao_share';
                        paoShareInput.value = share.toFixed(2);
                        form.appendChild(paoShareInput);
                    } else {
                        document.getElementById('is_short_hidden').value = '1';
                        document.getElementById('short_amount_hidden').value = shortAmount.toFixed(2);
                        document.getElementById('driver_share_hidden').value = share.toFixed(2);
                        document.getElementById('pao_share_hidden').value = share.toFixed(2);
                    }
                } else {
                    shortRemittanceSection.style.display = 'none';
                    
                    // Remove hidden inputs
                    const isShortInput = document.getElementById('is_short_hidden');
                    const shortAmountInput = document.getElementById('short_amount_hidden');
                    const driverShareInput = document.getElementById('driver_share_hidden');
                    const paoShareInput = document.getElementById('pao_share_hidden');
                    
                    if (isShortInput) isShortInput.remove();
                    if (shortAmountInput) shortAmountInput.remove();
                    if (driverShareInput) driverShareInput.remove();
                    if (paoShareInput) paoShareInput.remove();
                }
            }

            // Update boundary when vehicle is selected
            vehicleSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const boundary = selectedOption.getAttribute('data-boundary');
                
                if (boundary && boundary !== '' && boundary !== 'null' && boundary !== '0') {
                    boundaryDisplay.value = parseFloat(boundary).toFixed(2);
                } else {
                    boundaryDisplay.value = '0.00';
                }
            });

            // Update short remittance display when net remittance changes
            netRemittanceInput.addEventListener('input', updateShortRemittance);

            // Trigger change event on page load if a vehicle is already selected
            if (vehicleSelect.value) {
                vehicleSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endpush
@endsection