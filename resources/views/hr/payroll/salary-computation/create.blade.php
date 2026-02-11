@extends('layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Create Payroll</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('payroll.store') }}" method="POST">
                            @csrf

                            <!-- Employee Selection -->
                            <div class="mb-3">
                                <label for="employee_id" class="form-label">Employee *</label>
                                <select name="employee_id" id="employee_id"
                                    class="form-control @error('employee_id') is-invalid @enderror" required>
                                    <option value="">-- Select Employee --</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" data-salary-rate="{{ $employee->salary_rate }}"
                                            data-has-sss="{{ $employee->has_sss ? 1 : 0 }}"
                                            data-has-pagibig="{{ $employee->has_pagibig ? 1 : 0 }}"
                                            {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror

                                <div id="statutory_display" class="text-muted mt-1">
                                    Statutory deductions (SSS / Pag-IBIG) will appear here.
                                </div>
                            </div>

                            <!-- Payroll Period -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="payroll_period_start" class="form-label">Period Start *</label>
                                    <input type="date" name="payroll_period_start" id="payroll_period_start"
                                        class="form-control @error('payroll_period_start') is-invalid @enderror"
                                        value="{{ old('payroll_period_start') }}" required>
                                    @error('payroll_period_start')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="payroll_period_end" class="form-label">Period End *</label>
                                    <input type="date" name="payroll_period_end" id="payroll_period_end"
                                        class="form-control @error('payroll_period_end') is-invalid @enderror"
                                        value="{{ old('payroll_period_end') }}" required>
                                    @error('payroll_period_end')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Basic Salary -->
                            <div class="mb-3">
                                <label class="form-label">Basic Salary (15 days)</label>
                                <p id="basic_salary_display" class="form-control-plaintext">₱0.00</p>
                            </div>

                            <!-- Allowances -->
                            <div class="mb-3">
                                <label class="form-label">Allowances</label>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-6">
                                        <input type="text" id="allowance_name" class="form-control"
                                            placeholder="Allowance name">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" step="0.01" id="allowance_amount" class="form-control"
                                            placeholder="Amount">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-success w-100 border-1 rounded"
                                            onclick="addAllowance()"><i class="bi bi-plus-lg"></i>Add</button>
                                    </div>
                                </div>
                                <ul class="list-group mb-2" id="allowance_list"></ul>
                                <input type="hidden" name="total_allowances" id="total_allowances" value="0">
                                <div id="allowances_inputs"></div>
                                <small class="text-muted">Total Allowances: ₱<span
                                        id="allowance_total_display">0.00</span></small>
                            </div>

                            <!-- Deductions -->
                            <div class="mb-3">
                                <label class="form-label">Deductions</label>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-6">
                                        <input type="text" id="deduction_name" class="form-control"
                                            placeholder="Deduction name">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" step="0.01" id="deduction_amount" class="form-control"
                                            placeholder="Amount">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger w-100 border-1 rounded"
                                            onclick="addDeduction()"><i class="bi bi-plus-lg"></i>Add</button>
                                    </div>
                                </div>
                                <ul class="list-group mb-2" id="deduction_list"></ul>
                                <input type="hidden" name="total_deductions" id="total_deductions" value="0">
                                <div id="deductions_inputs"></div>
                                <small class="text-muted">Total Deductions: ₱<span
                                        id="deduction_total_display">0.00</span></small>
                            </div>

                            <!-- Net Salary -->
                            <div class="mb-3">
                                <label class="form-label">Net Salary</label>
                                <p id="net_salary_display" class="form-control-plaintext">₱0.00</p>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-outline-primary border-1 rounded">Create
                                    Payroll</button>
                                <a href="{{ route('payroll.salary-computation.index') }}"
                                    class="btn btn-outline-secondary border-1 rounded">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                window.statutoryDeductions = @json(\App\Models\StatutoryDeduction::all());
                window.initialAllowances = @json(old('allowances', []));
                window.initialDeductions = @json(old('deductions', []));
            </script>
            <script src="{{ asset('js/Payroll/create-payroll.js') }}"></script>
        @endpush
    </div>
@endsection
