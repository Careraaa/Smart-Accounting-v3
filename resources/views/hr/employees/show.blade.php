@extends('layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Employee Details</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('employees.edit', $employee) }}"
                                class="btn btn-outline-primary border-1 rounded">Edit</a>
                            <a href="{{ route('employees.index') }}"
                                class="btn btn-outline-secondary border-1 rounded">Back</a>
                        </div>
                    </div>
                    <div class="card-body">

                        <!-- Tabs -->
                        <ul class="nav nav-tabs mb-4" id="employeeViewTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="general-tab" data-bs-toggle="tab"
                                    data-bs-target="#general" type="button" role="tab" aria-controls="general"
                                    aria-selected="true">General Info</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="salary-tab" data-bs-toggle="tab" data-bs-target="#salary"
                                    type="button" role="tab" aria-controls="salary" aria-selected="false">Salary &
                                    Setup</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="allowances-tab" data-bs-toggle="tab"
                                    data-bs-target="#allowances" type="button" role="tab" aria-controls="allowances"
                                    aria-selected="false">Allowances & Benefits</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="deductions-tab" data-bs-toggle="tab"
                                    data-bs-target="#deductions" type="button" role="tab" aria-controls="deductions"
                                    aria-selected="false">Deductions & Contributions</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="employeeViewTabsContent">
                            <!-- General Info -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel"
                                aria-labelledby="general-tab">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>First Name:</strong>
                                        <p>{{ $employee->first_name }}</p>
                                        <strong>Last Name:</strong>
                                        <p>{{ $employee->last_name }}</p>
                                        <strong>Email:</strong>
                                        <p>{{ $employee->email }}</p>
                                        <strong>Phone:</strong>
                                        <p>{{ $employee->phone }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Position:</strong>
                                        <p>{{ $employee->position }}</p>
                                        <strong>Department:</strong>
                                        <p>{{ $employee->department }}</p>
                                        <strong>Date of Hire:</strong>
                                        <p>{{ $employee->date_of_hire?->format('M d, Y') ?? 'N/A' }}</p>
                                        <strong>Status:</strong>
                                        <p>
                                            <span
                                                class="badge px-3 {{ $employee->status === 'active' ? 'bg-soft-info text-info' : 'bg-soft-danger text-danger' }}">
                                                {{ ucfirst($employee->status) }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                @if ($employee->address)
                                    <div class="mb-3">
                                        <strong>Address:</strong>
                                        <p>{{ $employee->address }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Salary & Setup -->
                            <div class="tab-pane fade" id="salary" role="tabpanel" aria-labelledby="salary-tab">
                                <div class="mb-3">
                                    <strong>Salary Rate:</strong>
                                    <p>₱{{ number_format($employee->salary_rate, 2) }}</p>
                                </div>
                                <div class="mb-3">
                                    <strong>Basic Salary (15 days):</strong>
                                    <p>₱{{ number_format($employee->basic_salary, 2) }}</p>
                                </div>
                                <p class="text-muted">Other salary setup details can be added here.</p>
                            </div>

                            <!-- Allowances & Benefits -->
                            <div class="tab-pane fade" id="allowances" role="tabpanel" aria-labelledby="allowances-tab">
                                <div class="mb-3">
                                    <strong>Transportation Allowance:</strong>
                                    <p>₱{{ number_format($employee->transportation_allowance ?? 0, 2) }}</p>
                                </div>
                                <div class="mb-3">
                                    <strong>Meal Allowance:</strong>
                                    <p>₱{{ number_format($employee->meal_allowance ?? 0, 2) }}</p>
                                </div>
                                <div class="mb-3">
                                    <strong>Health / Medical Benefits:</strong>
                                    <p>₱{{ number_format($employee->medical_benefits ?? 0, 2) }}</p>
                                </div>
                                @if ($employee->other_allowances)
                                    <div class="mb-3">
                                        <strong>Other Allowances / Benefits:</strong>
                                        <p>{{ $employee->other_allowances }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Deductions & Contributions -->
                            <div class="tab-pane fade" id="deductions" role="tabpanel" aria-labelledby="deductions-tab">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" disabled
                                        {{ $employee->has_sss ? 'checked' : '' }}>
                                    <label class="form-check-label">Enrolled in SSS</label>
                                </div>
                                @if ($employee->sss_placeholder)
                                    <div class="mb-3">
                                        <strong>SSS Number / Details:</strong>
                                        <p>{{ $employee->sss_placeholder }}</p>
                                    </div>
                                @endif

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" disabled
                                        {{ $employee->has_pagibig ? 'checked' : '' }}>
                                    <label class="form-check-label">Enrolled in Pag-IBIG</label>
                                </div>
                                @if ($employee->pagibig_placeholder)
                                    <div class="mb-3">
                                        <strong>Pag-IBIG Number / Details:</strong>
                                        <p>{{ $employee->pagibig_placeholder }}</p>
                                    </div>
                                @endif

                                <p class="text-muted">Other deductions can be added later in payroll.</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <hr>
                        <div class="d-flex gap-2">
                            <a href="{{ route('employees.edit', $employee) }}"
                                class="btn btn-outline-primary border-1 rounded">Edit Employee</a>
                            <a href="{{ route('employees.index') }}"
                                class="btn btn-outline-secondary border-1 rounded">Back</a>
                            <form action="{{ route('employees.destroy', $employee) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger border-1 rounded"
                                    onclick="return confirm('Are you sure you want to delete this employee?')">
                                    Delete Employee
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
