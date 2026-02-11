@extends('layouts.layout')

@section('content')
    @php
        // Determine if this is an edit or create form
        $isEdit = isset($employee) && $employee->id;
    @endphp

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ $isEdit ? 'Edit Employee' : 'Add New Employee' }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ $isEdit ? route('employees.update', $employee) : route('employees.store') }}"
                            method="POST">
                            @csrf
                            @if ($isEdit)
                                @method('PUT')
                            @endif

                            <!-- Tabs -->
                            <ul class="nav nav-tabs mb-4" id="employeeTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active border-1 rounded" id="general-tab" data-bs-toggle="tab"
                                        data-bs-target="#general" type="button" role="tab" aria-controls="general"
                                        aria-selected="true">General Info</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link border-1 rounded" id="salary-tab" data-bs-toggle="tab"
                                        data-bs-target="#salary" type="button" role="tab" aria-controls="salary"
                                        aria-selected="false">Salary & Setup</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link border-1 rounded" id="allowances-tab" data-bs-toggle="tab"
                                        data-bs-target="#allowances" type="button" role="tab"
                                        aria-controls="allowances" aria-selected="false">Allowances & Benefits</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link border-1 rounded" id="deductions-tab" data-bs-toggle="tab"
                                        data-bs-target="#deductions" type="button" role="tab"
                                        aria-controls="deductions" aria-selected="false">Deductions & Contributions</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="employeeTabsContent">
                                <!-- General Info -->
                                <div class="tab-pane fade show active" id="general" role="tabpanel"
                                    aria-labelledby="general-tab">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="first_name" class="form-label">First Name *</label>
                                            <input type="text" name="first_name"
                                                class="form-control @error('first_name') is-invalid @enderror"
                                                value="{{ old('first_name', $employee->first_name ?? '') }}" required>
                                            @error('first_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="last_name" class="form-label">Last Name *</label>
                                            <input type="text" name="last_name"
                                                class="form-control @error('last_name') is-invalid @enderror"
                                                value="{{ old('last_name', $employee->last_name ?? '') }}" required>
                                            @error('last_name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email *</label>
                                            <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $employee->email ?? '') }}" required>
                                            @error('email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone *</label>
                                            <input type="text" name="phone"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                value="{{ old('phone', $employee->phone ?? '') }}" required>
                                            @error('phone')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="position" class="form-label">Position *</label>
                                            <input type="text" name="position"
                                                class="form-control @error('position') is-invalid @enderror"
                                                value="{{ old('position', $employee->position ?? '') }}" required>
                                            @error('position')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="department" class="form-label">Department *</label>
                                            <input type="text" name="department"
                                                class="form-control @error('department') is-invalid @enderror"
                                                value="{{ old('department', $employee->department ?? '') }}" required>
                                            @error('department')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="date_of_hire" class="form-label">Date of Hire *</label>
                                            <input type="date" name="date_of_hire" id="date_of_hire"
                                                class="form-control @error('date_of_hire') is-invalid @enderror"
                                                value="{{ old('date_of_hire', isset($employee) ? $employee->date_of_hire?->format('Y-m-d') : '') }}"
                                                required>
                                            @error('date_of_hire')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="status" class="form-label">Status *</label>
                                            <select name="status"
                                                class="form-control @error('status') is-invalid @enderror" required>
                                                <option value="active"
                                                    {{ old('status', $employee->status ?? '') == 'active' ? 'selected' : '' }}>
                                                    Active</option>
                                                <option value="inactive"
                                                    {{ old('status', $employee->status ?? '') == 'inactive' ? 'selected' : '' }}>
                                                    Inactive</option>
                                            </select>
                                            @error('status')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea name="address" class="form-control">{{ old('address', $employee->address ?? '') }}</textarea>
                                    </div>
                                </div>

                                <!-- Salary & Setup -->
                                <div class="tab-pane fade" id="salary" role="tabpanel" aria-labelledby="salary-tab">
                                    <div class="mb-3">
                                        <label for="salary_rate" class="form-label">Salary Rate *</label>
                                        <input type="number" step="0.01" name="salary_rate"
                                            class="form-control @error('salary_rate') is-invalid @enderror"
                                            value="{{ old('salary_rate', $employee->salary_rate ?? '') }}" required>
                                        @error('salary_rate')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <p class="text-muted">Other salary setup fields can go here (pay grade, payroll group,
                                        etc.)</p>
                                </div>

                                <!-- Allowances & Benefits -->
                                <!-- Allowances & Benefits -->
                                <div class="tab-pane fade" id="allowances" role="tabpanel"
                                    aria-labelledby="allowances-tab">
                                    <p class="text-muted">Manage employee allowances and benefits here. Fill in placeholder
                                        values for now:</p>

                                    <!-- Transportation Allowance -->
                                    <div class="mb-3">
                                        <label for="transportation_allowance" class="form-label">Transportation
                                            Allowance</label>
                                        <input type="number" step="0.01" name="transportation_allowance"
                                            id="transportation_allowance" class="form-control"
                                            value="{{ old('transportation_allowance', $employee->transportation_allowance ?? '') }}"
                                            placeholder="₱0.00">
                                    </div>

                                    <!-- Meal Allowance -->
                                    <div class="mb-3">
                                        <label for="meal_allowance" class="form-label">Meal Allowance</label>
                                        <input type="number" step="0.01" name="meal_allowance" id="meal_allowance"
                                            class="form-control"
                                            value="{{ old('meal_allowance', $employee->meal_allowance ?? '') }}"
                                            placeholder="₱0.00">
                                    </div>

                                    <!-- Health / Medical Benefits -->
                                    <div class="mb-3">
                                        <label for="medical_benefits" class="form-label">Health / Medical Benefits</label>
                                        <input type="number" step="0.01" name="medical_benefits"
                                            id="medical_benefits" class="form-control"
                                            value="{{ old('medical_benefits', $employee->medical_benefits ?? '') }}"
                                            placeholder="₱0.00">
                                    </div>

                                    <!-- Other Allowances Not Working Yet -->
                                    <div class="mb-3">
                                        <label for="other_allowances" class="form-label">Other Allowances /
                                            Benefits</label>
                                        <input type="text" name="other_allowances" id="other_allowances"
                                            class="form-control"
                                            value="{{ old('other_allowances', $employee->other_allowances ?? '') }}"
                                            placeholder="List any other allowances (optional)">
                                    </div>
                                </div>


                                <!-- Deductions & Contributions -->
                                <!-- Deductions & Contributions -->
                                <div class="tab-pane fade" id="deductions" role="tabpanel"
                                    aria-labelledby="deductions-tab">
                                    <p class="text-muted">Manage deductions and contributions here. Placeholder fields for
                                        now:</p>

                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="has_sss" id="has_sss"
                                            {{ old('has_sss', $employee->has_sss ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_sss">
                                            Enrolled in SSS
                                        </label>
                                    </div>
                                    <input type="text" name="sss_placeholder" class="form-control mb-3"
                                        value="{{ old('sss_placeholder', $employee->sss_placeholder ?? '') }}"
                                        placeholder="SSS Number / Details (placeholder)">

                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="has_pagibig"
                                            id="has_pagibig"
                                            {{ old('has_pagibig', $employee->has_pagibig ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_pagibig">
                                            Enrolled in Pag-IBIG
                                        </label>
                                    </div>
                                    <input type="text" name="pagibig_placeholder" class="form-control mb-3"
                                        value="{{ old('pagibig_placeholder', $employee->pagibig_placeholder ?? '') }}"
                                        placeholder="Pag-IBIG Number / Details (placeholder)">

                                    <hr>
                                    <p class="text-muted">Other deductions can be added later in payroll.</p>
                                </div>

                            </div>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary border-1 rounded" id="prevBtn"
                                    style="display: none;">
                                    Previous
                                </button>

                                <button type="button" class="btn btn-outline-primary border-1 rounded" id="nextBtn">
                                    Next
                                </button>

                                <button type="submit" class="btn btn-success border-1 rounded" id="submitBtn"
                                    style="display: none;">
                                    {{ $isEdit ? 'Update Employee' : 'Add Employee' }}
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        @push('scripts')
            <script src="{{ asset('js/Employee/employee-form.js') }}"></script>
        @endpush


    </div>
@endsection
