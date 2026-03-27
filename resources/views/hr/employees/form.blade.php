@extends('layouts.layout')

@section('content')
@php $isEdit = isset($employee) && $employee->id; @endphp

<div class="col-md-10 offset-md-1">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">{{ $isEdit ? 'Edit Employee' : 'Add New Employee' }}</span>
            <a href="{{ route('employees.index') }}" class="btn btn-sm btn-secondary">
                <i class="feather-arrow-left me-1"></i> Back
            </a>
        </div>
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ $isEdit ? route('employees.update', $employee) : route('employees.store') }}"
                method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @if ($isEdit) @method('PUT') @endif

                {{-- Tab nav --}}
                <ul class="nav nav-tabs mb-4" id="employeeTabs">
                    @foreach (['Personal Info','Employment','Account','Government Numbers','Work Experience','Special Skills','Beneficiaries','Character References','Attachments'] as $tab)
                        <li class="nav-item">
                            <button class="nav-link" type="button">{{ $tab }}</button>
                        </li>
                    @endforeach
                </ul>

                {{-- Tab content --}}
                <div class="tab-content" id="employeeTabsContent">
                    <div class="tab-pane">@include('partials.employee.personal')</div>
                    <div class="tab-pane">@include('partials.employee.employment_hr')
@include('partials.employee.employment')</div>
                    <div class="tab-pane">@include('partials.employee.account')</div>
                    <div class="tab-pane">@include('partials.employee.government')</div>
                    <div class="tab-pane">@include('partials.employee.work_experience')</div>
                    <div class="tab-pane">@include('partials.employee.skills')</div>
                    <div class="tab-pane">@include('partials.employee.beneficiaries')</div>
                    <div class="tab-pane">@include('partials.employee.references')</div>
                    <div class="tab-pane">@include('partials.employee.attachments')</div>
                </div>

                {{-- Nav buttons --}}
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-secondary btn-sm" id="prevBtn" style="display:none;">
                        <i class="feather-arrow-left me-1"></i> Previous
                    </button>
                    <div class="ms-auto d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm" id="nextBtn">
                            Next <i class="feather-arrow-right ms-1"></i>
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm" id="submitBtn" style="display:none;">
                            <i class="feather-check me-1"></i>
                            {{ $isEdit ? 'Update Employee' : 'Add Employee' }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/Employee/employee-form.js') }}"></script>
    <script src="{{ asset('js/global/global-datepicker.js') }}"></script>
@endpush