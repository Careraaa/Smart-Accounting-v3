@extends('layouts.layout')

@section('content')
<div class="col-md-9">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Resolve Short Remittance</span>
            <a href="{{ route('short-remittances.index') }}" class="btn btn-sm btn-secondary">
                <i class="feather-x me-1"></i> Close
            </a>
        </div>
        <div class="card-body">
            {{-- Remittance Summary --}}
            <div class="alert alert-warning mb-4">
                <h6 class="mb-2">
                    <i class="feather-alert-circle me-2"></i> Short Remittance on {{ $shortRemittance->remittance_date?->format('F d, Y') }}
                </h6>
                <p class="mb-1"><small>Vehicle: <strong>{{ $shortRemittance->vehicle->plate_number }}</strong></small></p>
                <p class="mb-1"><small>Short Amount: <strong style="color: #dc2626;">₱{{ number_format($shortRemittance->short_amount, 2) }}</strong></small></p>
                <p class="mb-0"><small>Driver & PAO each liable for: <strong>₱{{ number_format($shortRemittance->driver_share, 2) }}</strong></small></p>
            </div>

            <form action="{{ route('short-remittances.update', $shortRemittance) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Driver Resolution Section --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-3">
                            <i class="feather-user me-2" style="color: #0369a1;"></i> Driver Resolution
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="driver_name" class="form-label">Driver Name</label>
                            <input type="text" class="form-control" id="driver_name" value="{{ $shortRemittance->driver->name ?? 'N/A' }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="driver_liability" class="form-label">Driver Liability</label>
                            <input type="text" class="form-control" id="driver_liability" value="₱{{ number_format($shortRemittance->driver_share, 2) }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-4 mt-3">
                        <div class="form-group">
                            <label for="driver_amount_paid" class="form-label">Amount Paid <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    class="form-control @error('driver_amount_paid') is-invalid @enderror"
                                    id="driver_amount_paid"
                                    name="driver_amount_paid"
                                    value="{{ old('driver_amount_paid', $shortRemittance->driver_amount_paid ?? 0) }}"
                                    min="0"
                                    max="{{ $shortRemittance->driver_share }}"
                                >
                            </div>
                            @error('driver_amount_paid')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 mt-3">
                        <div class="form-group">
                            <label for="driver_remaining" class="form-label">Remaining Balance</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input 
                                    type="text" 
                                    class="form-control"
                                    id="driver_remaining"
                                    value="0.00"
                                    disabled
                                >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mt-3">
                        <div class="form-group">
                            <label for="driver_status" class="form-label">Status</label>
                            <select class="form-select @error('driver_status') is-invalid @enderror" id="driver_status" name="driver_status">
                                <option value="" selected>-- Select Status --</option>
                                <option value="pending" @selected(old('driver_status', $shortRemittance->driver_status) === 'pending')>Pending</option>
                                <option value="partial" @selected(old('driver_status', $shortRemittance->driver_status) === 'partial')>Partial Payment</option>
                                <option value="paid" @selected(old('driver_status', $shortRemittance->driver_status) === 'paid')>Fully Paid</option>
                            </select>
                            @error('driver_status')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- PAO Resolution Section --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h6 class="text-muted mb-3">
                            <i class="feather-users me-2" style="color: #16a34a;"></i> PAO Resolution
                        </h6>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pao_name" class="form-label">PAO Name</label>
                            <input type="text" class="form-control" id="pao_name" value="{{ $shortRemittance->pao->name ?? 'N/A' }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pao_liability" class="form-label">PAO Liability</label>
                            <input type="text" class="form-control" id="pao_liability" value="₱{{ number_format($shortRemittance->pao_share, 2) }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-4 mt-3">
                        <div class="form-group">
                            <label for="pao_amount_paid" class="form-label">Amount Paid <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    class="form-control @error('pao_amount_paid') is-invalid @enderror"
                                    id="pao_amount_paid"
                                    name="pao_amount_paid"
                                    value="{{ old('pao_amount_paid', $shortRemittance->pao_amount_paid ?? 0) }}"
                                    placeholder="0.00"
                                    min="0"
                                    max="{{ $shortRemittance->pao_share }}"
                                >
                            </div>
                            @error('pao_amount_paid')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4 mt-3">
                        <div class="form-group">
                            <label for="pao_remaining" class="form-label">Remaining Balance</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input 
                                    type="text" 
                                    class="form-control"
                                    id="pao_remaining"
                                    value="0.00"
                                    disabled
                                >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mt-3">
                        <div class="form-group">
                            <label for="pao_status" class="form-label">Status</label>
                            <select class="form-select @error('pao_status') is-invalid @enderror" id="pao_status" name="pao_status">
                                <option value="" selected>-- Select Status --</option>
                                <option value="pending" @selected(old('pao_status', $shortRemittance->pao_status) === 'pending')>Pending</option>
                                <option value="partial" @selected(old('pao_status', $shortRemittance->pao_status) === 'partial')>Partial Payment</option>
                                <option value="paid" @selected(old('pao_status', $shortRemittance->pao_status) === 'paid')>Fully Paid</option>
                            </select>
                            @error('pao_status')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Additional Notes --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="notes" class="form-label">Resolution Notes</label>
                            <textarea 
                                class="form-control @error('notes') is-invalid @enderror"
                                id="notes"
                                name="notes"
                                rows="4"
                                placeholder="Add notes about short remittance resolution, payment arrangements, or follow-up actions..."
                            >{{ old('notes', $shortRemittance->resolution_notes) }}</textarea>
                            @error('notes')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Form Buttons --}}
                <div class="d-flex gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="feather-save me-1"></i> Save Resolution
                    </button>
                    <a href="{{ route('short-remittances.index') }}" class="btn btn-secondary btn-sm">
                        <i class="feather-x me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Liability Summary Sidebar --}}
<div class="col-md-3">
    <div class="card card-statistic">
        <div class="card-body">
            <h6 class="card-title mb-3">Liability Summary</h6>
            
            <div class="mb-3 pb-3 border-bottom">
                <small class="text-muted d-block mb-2">Short Amount</small>
                <p class="mb-0">
                    <strong style="color: #dc2626; font-size: 1.15rem;">₱{{ number_format($shortRemittance->short_amount, 2) }}</strong>
                </p>
            </div>

            <div class="mb-3 pb-3 border-bottom">
                <h6 class="text-muted mb-2">
                    <i class="feather-user me-1" style="color: #0369a1;"></i> Driver
                </h6>
                <p class="mb-2">
                    <strong>Liability:</strong> ₱{{ number_format($shortRemittance->driver_share, 2) }}
                </p>
                <p class="mb-2">
                    <strong>Amount Paid:</strong> <span id="driver_paid_display">₱0.00</span>
                </p>
                <p class="mb-0">
                    <strong>Remaining:</strong> <span id="driver_remaining_display" style="color: #dc2626;">₱{{ number_format($shortRemittance->driver_share, 2) }}</span>
                </p>
            </div>

            <div class="mb-3">
                <h6 class="text-muted mb-2">
                    <i class="feather-users me-1" style="color: #16a34a;"></i> PAO
                </h6>
                <p class="mb-2">
                    <strong>Liability:</strong> ₱{{ number_format($shortRemittance->pao_share, 2) }}
                </p>
                <p class="mb-2">
                    <strong>Amount Paid:</strong> <span id="pao_paid_display">₱0.00</span>
                </p>
                <p class="mb-0">
                    <strong>Remaining:</strong> <span id="pao_remaining_display" style="color: #dc2626;">₱{{ number_format($shortRemittance->pao_share, 2) }}</span>
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const driverLiability = {{ $shortRemittance->driver_share }};
    const paoLiability = {{ $shortRemittance->pao_share }};

    function updateDriverBalance() {
        const amountPaid = parseFloat(document.getElementById('driver_amount_paid').value) || 0;
        const remaining = Math.max(0, driverLiability - amountPaid);
        
        document.getElementById('driver_remaining').value = remaining.toFixed(2);
        document.getElementById('driver_paid_display').textContent = '₱' + amountPaid.toFixed(2);
        document.getElementById('driver_remaining_display').textContent = '₱' + remaining.toFixed(2);
        
        // Auto-select status based on payment
        if (amountPaid === 0) {
            document.getElementById('driver_status').value = 'pending';
        } else if (amountPaid >= driverLiability) {
            document.getElementById('driver_status').value = 'paid';
        } else {
            document.getElementById('driver_status').value = 'partial';
        }
    }

    function updatePaoBalance() {
        const amountPaid = parseFloat(document.getElementById('pao_amount_paid').value) || 0;
        const remaining = Math.max(0, paoLiability - amountPaid);
        
        document.getElementById('pao_remaining').value = remaining.toFixed(2);
        document.getElementById('pao_paid_display').textContent = '₱' + amountPaid.toFixed(2);
        document.getElementById('pao_remaining_display').textContent = '₱' + remaining.toFixed(2);
        
        // Auto-select status based on payment
        if (amountPaid === 0) {
            document.getElementById('pao_status').value = 'pending';
        } else if (amountPaid >= paoLiability) {
            document.getElementById('pao_status').value = 'paid';
        } else {
            document.getElementById('pao_status').value = 'partial';
        }
    }

    // Event listeners
    document.getElementById('driver_amount_paid').addEventListener('input', updateDriverBalance);
    document.getElementById('pao_amount_paid').addEventListener('input', updatePaoBalance);

    // Initialize on page load
    window.addEventListener('DOMContentLoaded', function() {
        updateDriverBalance();
        updatePaoBalance();
    });
</script>
@endpush
@endsection