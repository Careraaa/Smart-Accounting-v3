@extends('layouts.layout')

@section('content')
<div class="col-md-8 offset-md-2">
    <div class="card">
        <div class="card-header">
            <span class="card-title mb-0">Generate Batch Payroll</span>
        </div>
        <div class="card-body">
            <form action="{{ route('payroll.generate-batch') }}" method="POST">
                @csrf

                {{-- Period Start --}}
                <div class="mb-4">
                    <label for="period_start" class="form-label">Period Start <span class="text-danger">*</span></label>
                    <input type="date" name="period_start" id="period_start"
                        class="form-control @error('period_start') is-invalid @enderror"
                        value="{{ old('period_start') }}" required>
                    @error('period_start')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Period End --}}
                <div class="mb-4">
                    <label for="period_end" class="form-label">Period End <span class="text-danger">*</span></label>
                    <input type="date" name="period_end" id="period_end"
                        class="form-control @error('period_end') is-invalid @enderror"
                        value="{{ old('period_end') }}" required>
                    @error('period_end')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Select Employees --}}
                <div class="mb-4">
                    <label for="employees" class="form-label">Select Employees (Optional)</label>
                    <small class="text-muted d-block mb-2">Leave unchecked to generate for all active employees</small>
                    
                    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                        @php
                            $activeEmployees = \App\Models\Employee::where('status', 'active')->orderBy('first_name')->get();
                        @endphp
                        @forelse($activeEmployees as $employee)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="employees[]" 
                                    value="{{ $employee->id }}" id="employee_{{ $employee->id }}"
                                    {{ in_array($employee->id, old('employees', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="employee_{{ $employee->id }}">
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </label>
                            </div>
                        @empty
                            <p class="text-muted">No active employees found</p>
                        @endforelse
                    </div>
                    @error('employees')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                </div>

                {{-- Info Alert --}}
                <div class="alert alert-info mb-4" role="alert">
                    <i class="feather-info me-2"></i>
                    <strong>Note:</strong> The system will automatically calculate attendance-based salary for each employee and skip those who already have payroll records for the selected period.
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="feather-zap me-2"></i> Generate Payroll
                    </button>
                    <a href="{{ route('payroll.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.form-check {
    display: flex;
    align-items: center;
}
.form-check-label {
    margin-bottom: 0;
    cursor: pointer;
    margin-left: 0.5rem;
}
</style>
@endsection
