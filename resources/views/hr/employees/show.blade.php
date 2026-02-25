@extends('layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">

                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">
                        {{ $employee->first_name }} {{ $employee->middle_name }} {{ $employee->last_name }}
                    </h4>
                    <div>
                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                        <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Are you sure you want to delete this employee?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </form>
                        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                {{-- Personal Information --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Personal Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">First Name</small>
                                <span>{{ $employee->first_name ?? '—' }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">Middle Name</small>
                                <span>{{ $employee->middle_name ?? '—' }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">Last Name</small>
                                <span>{{ $employee->last_name ?? '—' }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">Email</small>
                                <span>{{ $employee->email ?? '—' }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">Phone</small>
                                <span>{{ $employee->phone ?? '—' }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">Civil Status</small>
                                <span>{{ ucfirst($employee->civil_status) ?? '—' }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">Spouse Name</small>
                                <span>{{ $employee->spouse_name ?? '—' }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">Date of Birth</small>
                                <span>{{ $employee->date_of_birth?->format('F d, Y') ?? '—' }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">Place of Birth</small>
                                <span>{{ $employee->place_of_birth ?? '—' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <small class="text-muted d-block">Educational Attainment</small>
                                <span>{{ $employee->educational_attainment ?? '—' }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <small class="text-muted d-block">Address</small>
                                <span>{{ $employee->address ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Employment Information --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Employment Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">Date of Hire</small>
                                <span>{{ $employee->date_of_hire?->format('F d, Y') ?? '—' }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">Position</small>
                                <span>{{ $employee->position ?? '—' }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">Department</small>
                                <span>{{ $employee->department ?? '—' }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">Status</small>
                                <span class="badge {{ $employee->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($employee->status) }}
                                </span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">Salary Rate</small>
                                <span>₱{{ number_format($employee->salary_rate, 2) }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">Driver's License</small>
                                <span>{{ $employee->driver_license_number ?? '—' }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">License Validity</small>
                                <span>{{ $employee->driver_license_validity?->format('F d, Y') ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Government Numbers --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Government Numbers</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">SSS Number</small>
                                <span>{{ $employee->sss_number ?? '—' }}</span>
                                @if ($employee->has_sss)
                                    <span class="badge bg-success ms-1">Enrolled</span>
                                @endif
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">TIN Number</small>
                                <span>{{ $employee->tin_number ?? '—' }}</span>
                                @if ($employee->has_tin)
                                    <span class="badge bg-success ms-1">Has TIN</span>
                                @endif
                            </div>
                            <div class="col-md-4 mb-3">
                                <small class="text-muted d-block">Pag-IBIG Number</small>
                                <span>{{ $employee->pagibig_number ?? '—' }}</span>
                                @if ($employee->has_pagibig)
                                    <span class="badge bg-success ms-1">Enrolled</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Work Experience --}}
                @if ($employee->workExperiences->count())
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Work Experience</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Company</th>
                                            <th>Position</th>
                                            <th>Duration</th>
                                            <th>Responsibilities</th>
                                        </tr>
                                    </thead>
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
                    </div>
                @endif

                {{-- Special Skills --}}
                @if ($employee->specialSkills->count())
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Special Skills</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Skill</th>
                                            <th>Proficiency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employee->specialSkills as $skill)
                                            <tr>
                                                <td>{{ $skill->skill_name }}</td>
                                                <td>{{ ucfirst($skill->proficiency) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Beneficiaries --}}
                @if ($employee->beneficiaries->count())
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Beneficiaries</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Relationship</th>
                                            <th>Date of Birth</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employee->beneficiaries as $b)
                                            <tr>
                                                <td>{{ $b->name }}</td>
                                                <td>{{ ucfirst($b->relationship) }}</td>
                                                <td>{{ $b->date_of_birth ? \Carbon\Carbon::parse($b->date_of_birth)->format('F d, Y') : '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Character References --}}
                @if ($employee->characterReferences->count())
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Character References</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Address</th>
                                            <th>Contact Number</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employee->characterReferences as $ref)
                                            <tr>
                                                <td>{{ $ref->name }}</td>
                                                <td>{{ $ref->address }}</td>
                                                <td>{{ $ref->contact_number }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Attachments --}}
                @if ($employee->attachments && count($employee->attachments))
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Attachments</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @php
                                    $attachmentLabels = [
                                        'drivers_license' => "Driver's License",
                                        'valid_id_1' => 'Valid ID 1',
                                        'valid_id_2' => 'Valid ID 2',
                                        '2x2_picture' => '2x2 Picture',
                                        '1x1_picture' => '1x1 Picture',
                                        'police_clearance' => 'Police Clearance',
                                        'barangay_clearance' => 'Barangay Clearance',
                                        'house_sketch' => 'House Sketch',
                                        'medical_cert' => 'Medical Certificate',
                                        'drug_test' => 'Drug Test Result',
                                        'x_ray' => 'X-Ray Result',
                                    ];
                                @endphp
                                @foreach ($employee->attachments as $key => $path)
                                    @if ($path)
                                        <div class="col-md-4 mb-3">
                                            <small
                                                class="text-muted d-block">{{ $attachmentLabels[$key] ?? ucwords(str_replace('_', ' ', $key)) }}</small>
                                            <a href="{{ Storage::url($path) }}" target="_blank"
                                                class="btn btn-outline-primary btn-sm mt-1">
                                                <i class="bi bi-file-earmark me-1"></i> View File
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
