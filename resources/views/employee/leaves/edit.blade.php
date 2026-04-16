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
                <h1 class="empui-title">Edit Leave Request</h1>
                <p class="empui-sub">Update your dates/reason while the request is still pending.</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="empui-chip"><i class="feather-calendar"></i> {{ $leave->start_date->format('M d, Y') }} – {{ $leave->end_date->format('M d, Y') }}</span>
                    <span class="empui-chip"><i class="feather-tag"></i> {{ $leave->leaveType?->name ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="empui-hero-right">
                <a class="empui-btn-sec" href="{{ route('employee.leaves.show', $leave) }}">
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
                <form action="{{ route('employee.leaves.update', $leave) }}" method="POST" id="leaveForm">
                    @csrf
                    @method('PATCH')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="leave_type_id" class="form-label">Leave Type <span class="text-danger">*</span></label>
                            <select name="leave_type_id" id="leave_type_id" class="form-control @error('leave_type_id') is-invalid @enderror" required>
                                <option value="">Select Leave Type</option>
                                @foreach($leaveTypes as $typeId => $typeName)
                                    <option value="{{ $typeId }}" {{ ($leave->leave_type_id ?? old('leave_type_id')) == $typeId ? 'selected' : '' }}>
                                        {{ $typeName }}
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
                                   value="{{ old('start_date', $leave->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror"
                                   value="{{ old('end_date', $leave->end_date->format('Y-m-d')) }}" required>
                            <div class="empui-muted mt-1">
                                Duration: <span id="durationDays" class="empui-mono fw-bold">0</span> day(s)
                            </div>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror"
                                      rows="3" required placeholder="Brief reason for your leave request...">{{ old('reason', $leave->reason) }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap mt-4">
                        <button type="submit" class="empui-btn">
                            <i class="feather-save"></i>
                            Update Leave Request
                        </button>
                        <a href="{{ route('employee.leaves.show', $leave) }}" class="empui-btn-sec">
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
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const durationSpan = document.getElementById('durationDays');

    function calculateDuration() {
        if (startDateInput.value && endDateInput.value) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            const timeDiff = endDate - startDate;
            const daysDiff = timeDiff / (1000 * 60 * 60 * 24) + 1;
            durationSpan.textContent = daysDiff > 0 ? daysDiff : 0;
        }
    }

    startDateInput.addEventListener('change', calculateDuration);
    endDateInput.addEventListener('change', calculateDuration);
    
    // Calculate on page load
    calculateDuration();
</script>
@endsection
