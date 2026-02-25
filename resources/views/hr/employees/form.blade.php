@extends('layouts.layout')

@section('content')
    @php
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

                        @if ($errors->any())
                            <div class="alert alert-danger mb-3">
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ $isEdit ? route('employees.update', $employee) : route('employees.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if ($isEdit)
                                @method('PUT')
                            @endif

                            <ul class="nav nav-tabs mb-4" id="employeeTabs" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link" type="button">Personal Info</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" type="button">Employment</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" type="button">Government Numbers</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" type="button">Work Experience</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" type="button">Special Skills</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" type="button">Beneficiaries</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" type="button">Character References</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" type="button">Attachments</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="employeeTabsContent">

                                <div class="tab-pane">
                                    @include('partials.employee.personal')
                                </div>

                                <div class="tab-pane">
                                    @include('partials.employee.employment')
                                </div>

                                <div class="tab-pane">
                                    @include('partials.employee.government')
                                </div>

                                <div class="tab-pane">
                                    @include('partials.employee.work_experience')
                                </div>

                                <div class="tab-pane">
                                    @include('partials.employee.skills')
                                </div>

                                <div class="tab-pane">
                                    @include('partials.employee.beneficiaries')
                                </div>

                                <div class="tab-pane">
                                    @include('partials.employee.references')
                                </div>

                                <div class="tab-pane">
                                    @include('partials.employee.attachments')
                                </div>

                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div>
                                    <button type="button" class="btn btn-outline-secondary" id="prevBtn"
                                        style="display:none;">
                                        <i class="bi bi-arrow-left me-1"></i> Previous
                                    </button>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-outline-primary" id="nextBtn">
                                        Next <i class="bi bi-arrow-right ms-1"></i>
                                    </button>
                                    <button type="submit" class="btn btn-success" id="submitBtn"
                                        style="display:none;">
                                        <i class="bi bi-check-circle me-1"></i>
                                        {{ $isEdit ? 'Update Employee' : 'Add Employee' }}
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="{{ asset('js/Employee/employee-form.js') }}"></script>
    <script src="{{ asset('js/global/global-datepicker.js') }}"></script>
@endpush