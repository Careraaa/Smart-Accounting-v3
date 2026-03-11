@extends('layouts.layout')

@section('content')
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <span class="card-title mb-0">Create Payroll</span>
            </div>
            <div class="card-body">
                <form action="{{ route('payroll.salary-computation.store') }}" method="POST">
                    @csrf

                    {{-- Employee --}}
                    <div class="mb-4">
                        <label for="user_id" class="form-label">Employee <span class="text-danger">*</span></label>
                        <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror"
                            required>
                            <option value="">— Select Employee —</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" data-salary-rate="{{ $employee->salary_rate }}"
                                    data-has-sss="{{ $employee->has_sss ? 1 : 0 }}"
                                    data-has-pagibig="{{ $employee->has_pagibig ? 1 : 0 }}"
                                    {{ old('user_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <div id="statutory_display" class="prl-hint mt-1">
                            Statutory deductions (SSS / Pag-IBIG) will appear here.
                        </div>
                    </div>

                    {{-- Period --}}
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="payroll_period_start" class="form-label">Period Start <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="payroll_period_start" id="payroll_period_start"
                                class="form-control @error('payroll_period_start') is-invalid @enderror"
                                value="{{ old('payroll_period_start') }}" required>
                            @error('payroll_period_start')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="payroll_period_end" class="form-label">Period End <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="payroll_period_end" id="payroll_period_end"
                                class="form-control @error('payroll_period_end') is-invalid @enderror"
                                value="{{ old('payroll_period_end') }}" required>
                            @error('payroll_period_end')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Attendance Fields --}}
                    <div id="attendance_fields" class="mb-4 p-3 bg-light rounded">
                        <h6 class="mb-3">Attendance Summary</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Days Worked</label>
                                <p id="days_worked_display" class="prl-computed">0</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Hours Worked</label>
                                <p id="hours_worked_display" class="prl-computed">0.00</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Present Days</label>
                                <p id="present_days_display" class="prl-computed">0</p>
                            </div>
                        </div>
                    </div>

                    {{-- Overtime / Undertime Summary --}}
                    <div id="ot_ut_fields" class="mb-4 p-3 rounded" style="background:#f8f9ff; border:1px solid #e8e8ef;">
                        <h6 class="mb-3">Overtime & Undertime <span class="prl-hint">(approved records only)</span></h6>
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label class="form-label prl-hint mb-1">OT Hours</label>
                                <p id="ot_hours_display" class="prl-computed text-success">0.00</p>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label prl-hint mb-1">OT Pay</label>
                                <p id="ot_pay_display" class="prl-computed text-success">₱0.00</p>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label prl-hint mb-1">UT Hours</label>
                                <p id="ut_hours_display" class="prl-computed text-danger">0.00</p>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label prl-hint mb-1">UT Deduction</label>
                                <p id="ut_deduction_display" class="prl-computed text-danger">₱0.00</p>
                            </div>
                        </div>
                        <p class="prl-hint mb-0">
                            <i class="feather-info" style="font-size:0.75rem;"></i>
                            Overtime will be added as an allowance line item. Undertime will be added as a deduction and
                            attendance will be flagged.
                        </p>
                    </div>

                    {{-- Basic Salary --}}
                    <div class="mb-4">
                        <label class="form-label">Basic Salary <span class="prl-hint" id="basic_salary_hint">(calculated
                                from attendance)</span></label>
                        <p id="basic_salary_display" class="prl-computed">₱0.00</p>
                        <input type="hidden" name="basic_salary" id="basic_salary_input" value="0">
                    </div>

                    {{-- Divider --}}
                    <div class="prl-section-divider">Allowances</div>
                    <div class="mb-4">
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <input type="text" id="allowance_name" class="form-control"
                                    placeholder="Allowance name">
                            </div>
                            <div class="col-md-4">
                                <input type="number" min="0" step="0.1" id="allowance_amount"
                                    class="form-control" placeholder="Amount">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-secondary w-100" onclick="addAllowance()">
                                    <i class="feather-plus"></i>
                                </button>
                            </div>
                        </div>
                        <ul class="list-group prl-list mb-2" id="allowance_list"></ul>
                        <input type="hidden" name="total_allowances" id="total_allowances" value="0">
                        <div id="allowances_inputs"></div>
                        <span class="prl-hint">Total: ₱<span id="allowance_total_display">0.00</span></span>
                    </div>

                    <div class="prl-section-divider">Deductions</div>
                    <div class="mb-4">
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <input type="text" id="deduction_name" class="form-control"
                                    placeholder="Deduction name">
                            </div>
                            <div class="col-md-4">
                                <input type="number" min="0" step="0.1" id="deduction_amount"
                                    class="form-control" placeholder="Amount">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-secondary w-100" onclick="addDeduction()">
                                    <i class="feather-plus"></i>
                                </button>
                            </div>
                        </div>
                        <ul class="list-group prl-list mb-2" id="deduction_list"></ul>
                        <input type="hidden" name="total_deductions" id="total_deductions" value="0">
                        <div id="deductions_inputs"></div>
                        <span class="prl-hint">Total: ₱<span id="deduction_total_display">0.00</span></span>
                    </div>

                    {{-- Net Salary --}}
                    <div class="prl-net-box mb-4">
                        <span class="prl-net-label">Net Salary</span>
                        <span class="prl-net-value" id="net_salary_display">₱0.00</span>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex gap-2 pt-3 border-top">
                        <button type="submit" class="btn btn-primary btn-sm">Create Payroll</button>
                        <a href="{{ route('payroll.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                    </div>
                </form>
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
        <script src="{{ asset('js/global/global-datepicker.js') }}"></script>
    @endpush
@endsection
