@extends('layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Edit Payroll</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('payroll.salary-computation.update', $payroll) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Employee Selection -->
                            <div class="mb-3">
                                <label for="employee_id" class="form-label">Employee *</label>
                                <select name="employee_id" id="employee_id"
                                    class="form-control @error('employee_id') is-invalid @enderror" required>
                                    <option value="">-- Select Employee --</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" data-salary-rate="{{ $employee->salary_rate }}"
                                            {{ $payroll->employee_id == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('employee_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Payroll Period -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="payroll_period_start" class="form-label">Period Start *</label>
                                    <input type="date" name="payroll_period_start" id="payroll_period_start"
                                        class="form-control @error('payroll_period_start') is-invalid @enderror"
                                        value="{{ old('payroll_period_start', $payroll->payroll_period_start->format('Y-m-d')) }}"
                                        required>
                                    @error('payroll_period_start')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="payroll_period_end" class="form-label">Period End *</label>
                                    <input type="date" name="payroll_period_end" id="payroll_period_end"
                                        class="form-control @error('payroll_period_end') is-invalid @enderror"
                                        value="{{ old('payroll_period_end', $payroll->payroll_period_end->format('Y-m-d')) }}"
                                        required>
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
                                <div id="allowances_container">
                                    @if ($payroll->allowances->count())
                                        @foreach ($payroll->allowances as $index => $allowance)
                                            <div class="input-group mb-2">
                                                <input type="text" name="allowances[name][]" class="form-control"
                                                    value="{{ $allowance->name }}" placeholder="Allowance Name">
                                                <input type="number" step="0.01" name="allowances[amount][]"
                                                    class="form-control" value="{{ $allowance->amount }}"
                                                    placeholder="Amount">
                                                <button type="button" class="btn btn-danger remove-row">-</button>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="input-group mb-2">
                                            <input type="text" name="allowances[name][]" class="form-control"
                                                placeholder="Allowance Name">
                                            <input type="number" step="0.01" name="allowances[amount][]"
                                                class="form-control" placeholder="Amount">
                                            <button type="button" class="btn btn-danger remove-row">-</button>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" id="add_allowance" class="btn btn-sm btn-primary">Add
                                    Allowance</button>
                            </div>

                            <!-- Deductions -->
                            <div class="mb-3">
                                <label class="form-label">Deductions</label>
                                <div id="deductions_container">
                                    @if ($payroll->deductions->count())
                                        @foreach ($payroll->deductions as $index => $deduction)
                                            <div class="input-group mb-2">
                                                <input type="text" name="deductions[name][]" class="form-control"
                                                    value="{{ $deduction->deduction_type }}" placeholder="Deduction Name">
                                                <input type="number" step="0.01" name="deductions[amount][]"
                                                    class="form-control" value="{{ $deduction->amount }}"
                                                    placeholder="Amount">
                                                <button type="button" class="btn btn-danger remove-row">-</button>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="input-group mb-2">
                                            <input type="text" name="deductions[name][]" class="form-control"
                                                placeholder="Deduction Name">
                                            <input type="number" step="0.01" name="deductions[amount][]"
                                                class="form-control" placeholder="Amount">
                                            <button type="button" class="btn btn-danger remove-row">-</button>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" id="add_deduction" class="btn btn-sm btn-primary">Add
                                    Deduction</button>
                            </div>

                            <!-- Net Salary Display -->
                            <div class="mb-3">
                                <label class="form-label">Net Salary</label>
                                <p id="net_salary_display" class="form-control-plaintext">₱0.00</p>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Update Payroll</button>
                                <a href="{{ route('payroll.salary-computation.index') }}"
                                    class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function addRow(container, nameField, amountField) {
                let row = document.createElement('div');
                row.classList.add('input-group', 'mb-2');
                row.innerHTML = `
            <input type="text" name="${nameField}[]" class="form-control" placeholder="Name">
            <input type="number" step="0.01" name="${amountField}[]" class="form-control" placeholder="Amount">
            <button type="button" class="btn btn-danger remove-row">-</button>
        `;
                container.appendChild(row);
            }

            document.getElementById('add_allowance').addEventListener('click', function() {
                addRow(document.getElementById('allowances_container'), 'allowances[name]', 'allowances[amount]');
            });

            document.getElementById('add_deduction').addEventListener('click', function() {
                addRow(document.getElementById('deductions_container'), 'deductions[name]', 'deductions[amount]');
            });

            document.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-row')) {
                    e.target.closest('.input-group').remove();
                }
            });
        </script>
    @endpush
@endsection
