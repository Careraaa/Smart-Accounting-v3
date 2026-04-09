@extends('layouts.layout')

@push('styles')
    @include('employee._ui-styles')
@endpush

@section('content')
<div class="container-fluid empui-page empui-wrap">
    <div class="empui-backdrop"><div class="empui-grid"></div></div>
    <div class="empui-content">

        <div class="empui-hero">
            <div class="empui-hero-left">
                <h1 class="empui-title">Edit OT / UT Request</h1>
                <p class="empui-sub">Update your request details while it’s still pending.</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="empui-chip"><i class="feather-calendar"></i> {{ $overtimeUndertime->date->format('M d, Y') }}</span>
                    <span class="empui-chip"><i class="feather-clock"></i> {{ number_format($overtimeUndertime->hours, 2) }}h</span>
                </div>
            </div>
            <div class="empui-hero-right">
                <a class="empui-btn-sec" href="{{ route('employee.overtime-undertime.show', $overtimeUndertime->id) }}">
                    <i class="feather-arrow-left"></i>
                    Back to Details
                </a>
            </div>
        </div>

        <div class="empui-card" style="max-width: 980px; margin: 0 auto;">
            <div class="empui-card-head">
                <p class="empui-card-title"><span class="empui-dot"></span> Update Details</p>
                <span class="empui-pill pending">Pending</span>
            </div>
            <div class="empui-card-body">
                <form action="{{ route('employee.overtime-undertime.update', $overtimeUndertime->id) }}" method="POST" id="overtimeForm">
                    @csrf
                    @method('PATCH')

                    <div class="row g-3">
                        <div class="col-md-6">
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

                        <div class="col-md-6">
                            <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror"
                                   value="{{ old('date', $overtimeUndertime->date->format('Y-m-d')) }}" required max="{{ date('Y-m-d') }}">
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="hours" class="form-label">Hours <span class="text-danger">*</span></label>
                            <input type="number" name="hours" id="hours" class="form-control @error('hours') is-invalid @enderror"
                                   value="{{ old('hours', $overtimeUndertime->hours) }}" required min="0.5" max="24" step="0.5">
                            @error('hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="amount" class="form-label">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="text" id="amount" class="form-control" readonly>
                            </div>
                            <div class="empui-muted mt-1">Calculated automatically based on your hourly rate</div>
                        </div>

                        <div class="col-md-12">
                            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror"
                                      rows="4" required>{{ old('reason', $overtimeUndertime->reason) }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap mt-4">
                        <button type="submit" class="empui-btn">
                            <i class="feather-save"></i>
                            Update Request
                        </button>
                        <a href="{{ route('employee.overtime-undertime.show', $overtimeUndertime->id) }}" class="empui-btn-sec">
                            <i class="feather-x"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
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
