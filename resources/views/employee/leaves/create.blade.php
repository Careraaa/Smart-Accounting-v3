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
                <h1 class="empui-title">Request New Leave</h1>
                <p class="empui-sub">Choose dates, provide a short reason, and submit for approval.</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="empui-chip"><i class="feather-calendar"></i> {{ now()->format('l, F d, Y') }}</span>
                    <span class="empui-chip"><i class="feather-info"></i> Tip: double-check end date</span>
                </div>
            </div>
            <div class="empui-hero-right">
                <a class="empui-btn-sec" href="{{ route('employee.leaves.index') }}">
                    <i class="feather-arrow-left"></i>
                    Back to Requests
                </a>
            </div>
        </div>

        <div class="empui-card" style="max-width: 980px; margin: 0 auto;">
            <div class="empui-card-head">
                <p class="empui-card-title"><span class="empui-dot"></span> Leave Details</p>
            </div>
            <div class="empui-card-body">
                <form action="{{ route('employee.leaves.store') }}" method="POST" id="leaveForm">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="leave_type_id" class="form-label">Leave Type <span class="text-danger">*</span></label>
                            <select name="leave_type_id" id="leave_type_id" class="form-control @error('leave_type_id') is-invalid @enderror" required>
                                <option value="">Select Leave Type</option>
                                @foreach($leaveTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}" data-available="{{ $type->balance->remaining_days }}">
                                        {{ $type->name }} ({{ $type->balance->remaining_days }}/{{ $type->balance->total_days }} days available)
                                    </option>
                                @endforeach
                            </select>
                            @error('leave_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                   value="{{ old('start_date') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror"
                                   value="{{ old('end_date') }}" required>
                            <div class="empui-muted mt-1">
                                Duration: <span id="durationDays" class="empui-mono fw-bold">0</span> day(s)
                                <span id="balanceWarning" class="text-danger ms-2" style="display: none;"></span>
                            </div>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror"
                                      rows="3" required placeholder="Brief reason for your leave request...">{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap mt-4">
                        <button type="submit" class="empui-btn">
                            <i class="feather-send"></i>
                            Submit Leave Request
                        </button>
                        <a href="{{ route('employee.leaves.index') }}" class="empui-btn-sec">
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
    const leaveTypeSelect = document.getElementById('leave_type_id');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const durationSpan = document.getElementById('durationDays');
    const balanceWarning = document.getElementById('balanceWarning');

    function calculateDuration() {
        if (startDateInput.value && endDateInput.value) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            const timeDiff = endDate - startDate;
            const daysDiff = timeDiff / (1000 * 60 * 60 * 24) + 1;
            durationSpan.textContent = daysDiff > 0 ? daysDiff : 0;
            
            // Check available balance
            if (daysDiff > 0 && leaveTypeSelect.value) {
                const availableDays = parseInt(leaveTypeSelect.options[leaveTypeSelect.selectedIndex].dataset.available);
                if (daysDiff > availableDays) {
                    balanceWarning.textContent = `⚠ Insufficient balance (need ${daysDiff}, have ${availableDays})`;
                    balanceWarning.style.display = 'inline';
                } else {
                    balanceWarning.style.display = 'none';
                }
            }
        }
    }

    startDateInput.addEventListener('change', calculateDuration);
    endDateInput.addEventListener('change', calculateDuration);
    leaveTypeSelect.addEventListener('change', calculateDuration);
    
    // Calculate on page load
    calculateDuration();
</script>
@endsection
