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

                            <!-- Employee Selection -->
                            <div class="mb-3">
                                <label for="employee_id" class="form-label">Employee *</label>
                                <select name="employee_id" id="employee_id"
                                    class="form-control @error('employee_id') is-invalid @enderror" required>
                                    <option value="">-- Select Employee --</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" data-salary-rate="{{ $employee->salary_rate }}"
                                            {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
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
                                        <button type="button" class="btn btn-success w-100"
                                            onclick="addAllowance()">Add</button>
                                    </div>
                                </div>

                                <ul class="list-group mb-2" id="allowance_list"></ul>

                                <!-- Hidden total -->
                                <input type="hidden" name="total_allowances" id="total_allowances" value="0">
                                <div id="allowances_inputs"></div>

                                <small class="text-muted">
                                    Total Allowances: ₱<span id="allowance_total_display">0.00</span>
                                </small>
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
                                        <button type="button" class="btn btn-danger w-100"
                                            onclick="addDeduction()">Add</button>
                                    </div>
                                </div>

                                <ul class="list-group mb-2" id="deduction_list"></ul>

                                <!-- Hidden total -->
                                <input type="hidden" name="total_deductions" id="total_deductions" value="0">
                                <div id="deductions_inputs"></div>

                                <small class="text-muted">
                                    Total Deductions: ₱<span id="deduction_total_display">0.00</span>
                                </small>
                            </div>


                            <!-- Net Salary Display -->
                            <div class="mb-3">
                                <label class="form-label">Net Salary</label>
                                <p id="net_salary_display" class="form-control-plaintext">₱0.00</p>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Create Payroll</button>
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
            const employeeSelect = document.getElementById('employee_id');
            const basicSalaryDisplay = document.getElementById('basic_salary_display');
            const netSalaryDisplay = document.getElementById('net_salary_display');

            const allowanceList = document.getElementById('allowance_list');
            const deductionList = document.getElementById('deduction_list');

            const allowanceTotalInput = document.getElementById('total_allowances');
            const deductionTotalInput = document.getElementById('total_deductions');

            const allowanceTotalDisplay = document.getElementById('allowance_total_display');
            const deductionTotalDisplay = document.getElementById('deduction_total_display');

            let allowances = [];
            let deductions = [];

            // Update salary display
            function updateSalary() {
                const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
                const salaryRate = parseFloat(selectedOption.dataset.salaryRate || 0);
                const basicSalary = salaryRate * 15;

                basicSalaryDisplay.innerText = `₱${basicSalary.toFixed(2)}`;

                const totalAllowances = allowances.reduce((sum, a) => sum + a.amount, 0);
                const totalDeductions = deductions.reduce((sum, d) => sum + d.amount, 0);

                allowanceTotalInput.value = totalAllowances;
                deductionTotalInput.value = totalDeductions;

                allowanceTotalDisplay.innerText = totalAllowances.toFixed(2);
                deductionTotalDisplay.innerText = totalDeductions.toFixed(2);

                const netSalary = basicSalary + totalAllowances - totalDeductions;
                netSalaryDisplay.innerText = `₱${netSalary.toFixed(2)}`;
            }

            // Render list and hidden inputs for form
            function renderList(list, container, type) {
                container.innerHTML = '';

                const hiddenContainer = type === 'allowance' ?
                    document.getElementById('allowances_inputs') :
                    document.getElementById('deductions_inputs');

                hiddenContainer.innerHTML = '';

                list.forEach((item, index) => {
                    // Visible list
                    container.innerHTML += `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${item.name}</span>
                    <div>
                        ₱${item.amount.toFixed(2)}
                        <button type="button" class="btn btn-sm btn-outline-danger ms-2"
                            onclick="removeItem('${type}', ${index})">✕</button>
                    </div>
                </li>
            `;

                    // Hidden inputs for submission
                    hiddenContainer.innerHTML += `
                <input type="hidden" name="${type}s[${index}][name]" value="${item.name}">
                <input type="hidden" name="${type}s[${index}][amount]" value="${item.amount}">
            `;
                });
            }

            // Add allowance
            function addAllowance() {
                const name = document.getElementById('allowance_name').value.trim();
                const amount = parseFloat(document.getElementById('allowance_amount').value);

                if (!name || amount <= 0) return;

                allowances.push({
                    name,
                    amount
                });

                renderList(allowances, allowanceList, 'allowance');

                document.getElementById('allowance_name').value = '';
                document.getElementById('allowance_amount').value = '';

                updateSalary();
            }

            // Add deduction
            function addDeduction() {
                const name = document.getElementById('deduction_name').value.trim();
                const amount = parseFloat(document.getElementById('deduction_amount').value);

                if (!name || amount <= 0) return;

                deductions.push({
                    name,
                    amount
                });

                renderList(deductions, deductionList, 'deduction');

                document.getElementById('deduction_name').value = '';
                document.getElementById('deduction_amount').value = '';

                updateSalary();
            }

            // Remove item from list
            function removeItem(type, index) {
                if (type === 'allowance') {
                    allowances.splice(index, 1);
                    renderList(allowances, allowanceList, 'allowance');
                } else {
                    deductions.splice(index, 1);
                    renderList(deductions, deductionList, 'deduction');
                }
                updateSalary();
            }

            employeeSelect.addEventListener('change', updateSalary);

            // Initialize salary display
            updateSalary();
        </script>
    @endpush
@endsection
