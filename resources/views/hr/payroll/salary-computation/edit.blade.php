@extends('layouts.layout')

@section('content')
<div class="col-md-8 offset-md-2">
    <div class="card">
        <div class="card-header">
            <span class="card-title mb-0">Edit Payroll</span>
        </div>
        <div class="card-body">
            <form action="{{ route('payroll.update', $payroll) }}" method="POST">
                @csrf @method('PUT')

                {{-- Employee --}}
                <div class="mb-4">
                    <label for="user_id" class="form-label">Employee <span class="text-danger">*</span></label>
                    <select name="user_id" id="user_id"
                        class="form-control @error('user_id') is-invalid @enderror" required>
                        <option value="">— Select Employee —</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}"
                                data-salary-rate="{{ $employee->salary_rate }}"
                                data-has-sss="{{ $employee->has_sss ? 1 : 0 }}"
                                data-has-pagibig="{{ $employee->has_pagibig ? 1 : 0 }}"
                                {{ $payroll->user_id == $employee->id ? 'selected' : '' }}>
                                {{ $employee->first_name }} {{ $employee->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Period --}}
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="payroll_period_start" class="form-label">Period Start <span class="text-danger">*</span></label>
                        <input type="date" name="payroll_period_start" id="payroll_period_start"
                            class="form-control @error('payroll_period_start') is-invalid @enderror"
                            value="{{ old('payroll_period_start', $payroll->payroll_period_start->format('Y-m-d')) }}" required>
                        @error('payroll_period_start')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="payroll_period_end" class="form-label">Period End <span class="text-danger">*</span></label>
                        <input type="date" name="payroll_period_end" id="payroll_period_end"
                            class="form-control @error('payroll_period_end') is-invalid @enderror"
                            value="{{ old('payroll_period_end', $payroll->payroll_period_end->format('Y-m-d')) }}" required>
                        @error('payroll_period_end')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                {{-- Basic Salary --}}
                <div class="mb-4">
                    <label class="form-label">Basic Salary <span class="prl-hint">(15 days)</span></label>
                    <p id="basic_salary_display" class="prl-computed">₱0.00</p>
                </div>

                {{-- Allowances --}}
                <div class="prl-section-divider">Allowances</div>
                <div class="mb-4">
                    <ul class="list-group prl-list mb-2" id="allowance_list"></ul>
                    <input type="hidden" name="total_allowances" id="total_allowances" value="0">
                    <div id="allowances_inputs"></div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <input type="text" id="allowance_name" class="form-control" placeholder="Allowance name">
                        </div>
                        <div class="col-md-4">
                            <input type="number" min="0" step="0.1" id="allowance_amount" class="form-control" placeholder="Amount">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-secondary w-100" onclick="addAllowance()">
                                <i class="feather-plus"></i>
                            </button>
                        </div>
                    </div>
                    <span class="prl-hint">Total: ₱<span id="allowance_total_display">0.00</span></span>
                </div>

                {{-- Deductions --}}
                <div class="prl-section-divider">Deductions</div>
                <div class="mb-4">
                    <ul class="list-group prl-list mb-2" id="deduction_list"></ul>
                    <input type="hidden" name="total_deductions" id="total_deductions" value="0">
                    <div id="deductions_inputs"></div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <input type="text" id="deduction_name" class="form-control" placeholder="Deduction name">
                        </div>
                        <div class="col-md-4">
                            <input type="number" min="0" step="0.1" id="deduction_amount" class="form-control" placeholder="Amount">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-secondary w-100" onclick="addDeduction()">
                                <i class="feather-plus"></i>
                            </button>
                        </div>
                    </div>
                    <span class="prl-hint">Total: ₱<span id="deduction_total_display">0.00</span></span>
                </div>

                {{-- Net Salary --}}
                <div class="prl-net-box mb-4">
                    <span class="prl-net-label">Net Salary</span>
                    <span class="prl-net-value" id="net_salary_display">₱0.00</span>
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary btn-sm">Update Payroll</button>
                    <a href="{{ route('payroll.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.prl-hint         { font-size: 0.8rem; color: #9898a8; }
.prl-computed     { font-size: 1rem; font-weight: 700; color: #1c1c1e; margin: 0; }
.prl-section-divider {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #9898a8;
    border-bottom: 1px solid #e8e8ef;
    padding-bottom: 8px;
    margin-bottom: 14px;
}
.prl-list { border-radius: 8px; overflow: hidden; }
.prl-list .list-group-item {
    border-color: #e8e8ef;
    font-size: 0.845rem;
    padding: 8px 14px;
    background: #fafafa;
}
.prl-net-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #1c1c1e;
    border-radius: 10px;
    padding: 16px 20px;
}
.prl-net-label { font-size: 0.8rem; font-weight: 600; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1px; }
.prl-net-value { font-size: 1.4rem; font-weight: 800; color: #fff; }
</style>

@push('scripts')
    <script>
        window.initialAllowances = @json($payroll->allowances->map(fn($a) => ['name' => $a->allowance_type ?? $a->name, 'amount' => floatval($a->amount)]));
        window.initialDeductions = @json($payroll->deductions->map(fn($d) => ['name' => $d->deduction_type, 'amount' => floatval($d->amount)]));
    </script>
    <script src="{{ asset('js/Payroll/edit-payroll.js') }}"></script>
    <script src="{{ asset('js/global/global-datepicker.js') }}"></script>
@endpush
@endsection