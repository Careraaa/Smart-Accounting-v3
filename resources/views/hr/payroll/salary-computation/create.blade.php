@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Create Payroll</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('payroll.store') }}" method="POST">
                        @csrf

                        <!-- Select Employee -->
                        <div class="mb-3">
                            <label for="employee_id" class="form-label">Employee *</label>
                            <select name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror" required>
                                <option value="">-- Select Employee --</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->first_name }} {{ $employee->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <!-- Payroll Period -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="payroll_period_start" class="form-label">Period Start *</label>
                                <input type="date" name="payroll_period_start" id="payroll_period_start" class="form-control @error('payroll_period_start') is-invalid @enderror" value="{{ old('payroll_period_start') }}" required>
                                @error('payroll_period_start') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="payroll_period_end" class="form-label">Period End *</label>
                                <input type="date" name="payroll_period_end" id="payroll_period_end" class="form-control @error('payroll_period_end') is-invalid @enderror" value="{{ old('payroll_period_end') }}" required>
                                @error('payroll_period_end') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Basic Salary -->
                        <div class="mb-3">
                            <label for="basic_salary" class="form-label">Basic Salary *</label>
                            <input type="number" step="0.01" name="basic_salary" id="basic_salary" class="form-control @error('basic_salary') is-invalid @enderror" value="{{ old('basic_salary') }}" required>
                            @error('basic_salary') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <!-- Total Allowances -->
                        <div class="mb-3">
                            <label for="total_allowances" class="form-label">Total Allowances *</label>
                            <input type="number" step="0.01" name="total_allowances" id="total_allowances" class="form-control @error('total_allowances') is-invalid @enderror" value="{{ old('total_allowances', 0) }}" required>
                            @error('total_allowances') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <!-- Total Deductions -->
                        <div class="mb-3">
                            <label for="total_deductions" class="form-label">Total Deductions *</label>
                            <input type="number" step="0.01" name="total_deductions" id="total_deductions" class="form-control @error('total_deductions') is-invalid @enderror" value="{{ old('total_deductions', 0) }}" required>
                            @error('total_deductions') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="submitted" {{ old('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                            @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Create Payroll</button>
                            <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
