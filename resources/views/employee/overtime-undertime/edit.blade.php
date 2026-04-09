@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <span class="card-title mb-0">Edit Overtime / Undertime Request</span>
        </div>
        <div class="card-body">
            <form action="{{ route('employee.overtime-undertime.update', $overtimeUndertime->id) }}" method="POST" id="overtimeForm">
                @csrf
                @method('PATCH')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label">Request Type <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                <option value="">Select Type</option>
                                @foreach($types as $value => $label)
                                    <option value="{{ $value }}" {{ old('type', $overtimeUndertime->type) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" 
                                value="{{ old('date', $overtimeUndertime->date->format('Y-m-d')) }}" required max="{{ date('Y-m-d') }}">
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="hours" class="form-label">Hours <span class="text-danger">*</span></label>
                            <input type="number" name="hours" id="hours" class="form-control @error('hours') is-invalid @enderror" 
                                value="{{ old('hours', $overtimeUndertime->hours) }}" required min="0.5" max="24" step="0.5">
                            @error('hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="text" id="amount" class="form-control" readonly>
                            </div>
                            <small class="text-muted d-block mt-1">Calculated automatically based on your hourly rate</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror" 
                                rows="4" required>{{ old('reason', $overtimeUndertime->reason) }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Request</button>
                    <a href="{{ route('employee.overtime-undertime.show', $overtimeUndertime->id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const typeSelect = document.getElementById('type');
    const hoursInput = document.getElementById('hours');
    const amountInput = document.getElementById('amount');

    function calculateAmount() {
        const hours = parseFloat(hoursInput.value) || 0;
        const type = typeSelect.value;
        const hourlyRate = {{ $overtimeUndertime->hourly_rate_used ?? 0 }};

        if (hours > 0 && hourlyRate > 0) {
            const amount = hours * hourlyRate;
            
            if (type === 'overtime') {
                amountInput.value = new Intl.NumberFormat('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(amount);
            } else if (type === 'undertime') {
                amountInput.value = '-' + new Intl.NumberFormat('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(amount);
            } else {
                amountInput.value = '';
            }
        } else {
            amountInput.value = '';
        }
    }

    typeSelect.addEventListener('change', calculateAmount);
    hoursInput.addEventListener('input', calculateAmount);
    
    // Calculate on page load
    calculateAmount();
</script>
@endsection
