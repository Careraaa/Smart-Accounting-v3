@extends('layouts.layout')

@section('content')
<div class="col-md-10 offset-md-1">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0" style="color:#1c1c1e;">
                {{ $employee->first_name }} {{ $employee->middle_name }} {{ $employee->last_name }}
            </h5>
            <span class="emp-view-label">{{ $employee->position ?? '' }}</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('employees.edit', $employee) }}" class="emp-action-btn emp-action-edit" title="Edit">
                <i class="feather-edit-2 me-1"></i> Edit
            </a>
            <form action="{{ route('employees.destroy', $employee) }}" method="POST"
                onsubmit="return confirm('Delete this employee?')" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="emp-action-btn emp-action-danger" title="Delete">
                    <i class="feather-trash-2 me-1"></i> Delete
                </button>
            </form>
            <a href="{{ route('employees.index') }}" class="emp-action-btn emp-action-back">
                <i class="feather-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    {{-- Personal Information --}}
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Personal Information</span></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3"><span class="emp-field-label">First Name</span><div class="emp-field-value">{{ $employee->first_name ?? '—' }}</div></div>
                <div class="col-md-4 mb-3"><span class="emp-field-label">Middle Name</span><div class="emp-field-value">{{ $employee->middle_name ?? '—' }}</div></div>
                <div class="col-md-4 mb-3"><span class="emp-field-label">Last Name</span><div class="emp-field-value">{{ $employee->last_name ?? '—' }}</div></div>
                <div class="col-md-4 mb-3"><span class="emp-field-label">Email</span><div class="emp-field-value">{{ $employee->email ?? '—' }}</div></div>
                <div class="col-md-4 mb-3"><span class="emp-field-label">Phone</span><div class="emp-field-value">{{ $employee->phone ?? '—' }}</div></div>
                <div class="col-md-4 mb-3"><span class="emp-field-label">Civil Status</span><div class="emp-field-value">{{ ucfirst($employee->civil_status ?? '—') }}</div></div>
                <div class="col-md-4 mb-3"><span class="emp-field-label">Spouse Name</span><div class="emp-field-value">{{ $employee->spouse_name ?? '—' }}</div></div>
                <div class="col-md-4 mb-3"><span class="emp-field-label">Date of Birth</span><div class="emp-field-value">{{ $employee->date_of_birth?->format('F d, Y') ?? '—' }}</div></div>
                <div class="col-md-4 mb-3"><span class="emp-field-label">Place of Birth</span><div class="emp-field-value">{{ $employee->place_of_birth ?? '—' }}</div></div>
                <div class="col-md-6 mb-3"><span class="emp-field-label">Educational Attainment</span><div class="emp-field-value">{{ $employee->educational_attainment ?? '—' }}</div></div>
                <div class="col-md-6 mb-3"><span class="emp-field-label">Address</span><div class="emp-field-value">{{ $employee->address ?? '—' }}</div></div>
            </div>
        </div>
    </div>

    {{-- Employment Information --}}
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Employment Information</span></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3"><span class="emp-field-label">Date of Hire</span><div class="emp-field-value">{{ $employee->date_of_hire?->format('F d, Y') ?? '—' }}</div></div>
                <div class="col-md-4 mb-3"><span class="emp-field-label">Position</span><div class="emp-field-value">{{ $employee->position ?? '—' }}</div></div>
                <div class="col-md-4 mb-3"><span class="emp-field-label">Department</span><div class="emp-field-value">{{ $employee->department ?? '—' }}</div></div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Status</span>
                    <div class="mt-1">
                        @if ($employee->status === 'active')
                            <span class="emp-badge emp-badge-active">Active</span>
                        @else
                            <span class="emp-badge emp-badge-inactive">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4 mb-3"><span class="emp-field-label">Salary Rate</span><div class="emp-field-value">₱{{ number_format($employee->salary_rate, 2) }}</div></div>
                <div class="col-md-4 mb-3"><span class="emp-field-label">Driver's License</span><div class="emp-field-value">{{ $employee->driver_license_number ?? '—' }}</div></div>
                <div class="col-md-4 mb-3"><span class="emp-field-label">License Validity</span><div class="emp-field-value">{{ $employee->driver_license_validity?->format('F d, Y') ?? '—' }}</div></div>
            </div>
        </div>
    </div>

    {{-- Government Numbers --}}
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Government Numbers</span></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">SSS Number</span>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="emp-field-value">{{ $employee->sss_number ?? '—' }}</span>
                        @if ($employee->has_sss)<span class="emp-badge emp-badge-active">Enrolled</span>@endif
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">TIN Number</span>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="emp-field-value">{{ $employee->tin_number ?? '—' }}</span>
                        @if ($employee->has_tin)<span class="emp-badge emp-badge-active">Has TIN</span>@endif
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Pag-IBIG Number</span>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="emp-field-value">{{ $employee->pagibig_number ?? '—' }}</span>
                        @if ($employee->has_pagibig)<span class="emp-badge emp-badge-active">Enrolled</span>@endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Work Experience --}}
    @if ($employee->workExperiences->count())
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Work Experience</span></div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Company</th><th>Position</th><th>Duration</th><th>Responsibilities</th></tr></thead>
                <tbody>
                    @foreach ($employee->workExperiences as $we)
                        <tr>
                            <td>{{ $we->company_name }}</td>
                            <td>{{ $we->position }}</td>
                            <td>{{ $we->duration }}</td>
                            <td>{{ $we->responsibilities }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Special Skills --}}
    @if ($employee->specialSkills->count())
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Special Skills</span></div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Skill</th><th>Proficiency</th></tr></thead>
                <tbody>
                    @foreach ($employee->specialSkills as $skill)
                        <tr><td>{{ $skill->skill_name }}</td><td>{{ ucfirst($skill->proficiency) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Beneficiaries --}}
    @if ($employee->beneficiaries->count())
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Beneficiaries</span></div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Name</th><th>Relationship</th><th>Date of Birth</th></tr></thead>
                <tbody>
                    @foreach ($employee->beneficiaries as $b)
                        <tr>
                            <td>{{ $b->name }}</td>
                            <td>{{ ucfirst($b->relationship) }}</td>
                            <td>{{ $b->date_of_birth ? \Carbon\Carbon::parse($b->date_of_birth)->format('F d, Y') : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Character References --}}
    @if ($employee->characterReferences->count())
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Character References</span></div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Name</th><th>Address</th><th>Contact Number</th></tr></thead>
                <tbody>
                    @foreach ($employee->characterReferences as $ref)
                        <tr><td>{{ $ref->name }}</td><td>{{ $ref->address }}</td><td>{{ $ref->contact_number }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Attachments --}}
    @if ($employee->attachments && count($employee->attachments))
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Attachments</span></div>
        <div class="card-body">
            <div class="row">
                @php
                    $attachmentLabels = [
                        'drivers_license' => "Driver's License", 'valid_id_1' => 'Valid ID 1',
                        'valid_id_2' => 'Valid ID 2', '2x2_picture' => '2x2 Picture',
                        '1x1_picture' => '1x1 Picture', 'police_clearance' => 'Police Clearance',
                        'barangay_clearance' => 'Barangay Clearance', 'house_sketch' => 'House Sketch',
                        'medical_cert' => 'Medical Certificate', 'drug_test' => 'Drug Test Result',
                        'x_ray' => 'X-Ray Result',
                    ];
                @endphp
                @foreach ($employee->attachments as $key => $path)
                    @if ($path)
                        <div class="col-md-4 mb-3">
                            <span class="emp-field-label">{{ $attachmentLabels[$key] ?? ucwords(str_replace('_', ' ', $key)) }}</span>
                            <div class="mt-1">
                                <a href="{{ Storage::url($path) }}" target="_blank" class="emp-action-btn emp-action-view" style="width:auto; padding:5px 12px; border-radius:6px;">
                                    <i class="feather-file me-1"></i> View File
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
<style>
.emp-action-btn {
    height: 30px;
    padding: 0 12px;
    font-size: 0.815rem;
    font-weight: 500;
    white-space: nowrap;
    width: auto;
}
</style>
@endsection