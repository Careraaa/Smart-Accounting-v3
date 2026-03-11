@extends('layouts.layout')

@section('content')

@php
    $addr = [];
    if ($employee->address && str_starts_with(trim($employee->address), '{')) {
        $addr = json_decode($employee->address, true) ?? [];
    }
    $addressFormatted = collect([
        $addr['street']   ?? null,
        $addr['barangay'] ?? null,
        $addr['city']     ?? null,
        $addr['province'] ?? null,
    ])->filter()->implode(', ');
    if (!$addressFormatted) $addressFormatted = $employee->address ?? '—';
@endphp

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
                @if($employee->civil_status === 'married')
                <div class="col-md-4 mb-3"><span class="emp-field-label">Spouse Name</span><div class="emp-field-value">{{ $employee->spouse_name ?? '—' }}</div></div>
                @endif
                <div class="col-md-4 mb-3"><span class="emp-field-label">Date of Birth</span><div class="emp-field-value">{{ $employee->date_of_birth?->format('F d, Y') ?? '—' }}</div></div>
                <div class="col-md-4 mb-3"><span class="emp-field-label">Place of Birth</span><div class="emp-field-value">{{ $employee->place_of_birth ?? '—' }}</div></div>
                <div class="col-md-4 mb-3"><span class="emp-field-label">Educational Attainment</span><div class="emp-field-value">{{ $employee->educational_attainment ?? '—' }}</div></div>

                @if(!empty($addr))
                    <div class="col-md-6 mb-3"><span class="emp-field-label">Street / House No.</span><div class="emp-field-value">{{ $addr['street'] ?: '—' }}</div></div>
                    <div class="col-md-6 mb-3"><span class="emp-field-label">Barangay</span><div class="emp-field-value">{{ $addr['barangay'] ?: '—' }}</div></div>
                    <div class="col-md-6 mb-3"><span class="emp-field-label">City / Municipality</span><div class="emp-field-value">{{ $addr['city'] ?: '—' }}</div></div>
                    <div class="col-md-6 mb-3"><span class="emp-field-label">Province</span><div class="emp-field-value">{{ $addr['province'] ?: '—' }}</div></div>
                @else
                    <div class="col-md-12 mb-3"><span class="emp-field-label">Address</span><div class="emp-field-value">{{ $addressFormatted }}</div></div>
                @endif
            </div>
        </div>
    </div>

    {{-- Account Information --}}
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Account Information</span></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3"><span class="emp-field-label">Username</span><div class="emp-field-value">{{ $employee->username ?? '—' }}</div></div>
                <div class="col-md-4 mb-3"><span class="emp-field-label">Role</span><div class="emp-field-value">{{ ucfirst($employee->role ?? '—') }}</div></div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Account Status</span>
                    <div class="mt-1">
                        @if($employee->status === 'active')
                            <span class="emp-badge emp-badge-active">Active</span>
                        @else
                            <span class="emp-badge emp-badge-inactive">Inactive</span>
                        @endif
                    </div>
                </div>
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
                <div class="col-md-4 mb-3"><span class="emp-field-label">Department</span><div class="emp-field-value">{{ ucfirst($employee->department ?? '—') }}</div></div>
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
                        @if($employee->has_sss)<span class="emp-badge emp-badge-active">Enrolled</span>@endif
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">TIN Number</span>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="emp-field-value">{{ $employee->tin_number ?? '—' }}</span>
                        @if($employee->has_tin)<span class="emp-badge emp-badge-active">Has TIN</span>@endif
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <span class="emp-field-label">Pag-IBIG Number</span>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="emp-field-value">{{ $employee->pagibig_number ?? '—' }}</span>
                        @if($employee->has_pagibig)<span class="emp-badge emp-badge-active">Enrolled</span>@endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Work Experience --}}
    @if($employee->workExperiences->count())
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Work Experience</span></div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Company</th><th>Position</th><th>Duration</th><th>Responsibilities</th></tr></thead>
                <tbody>
                    @foreach($employee->workExperiences as $we)
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
    @if($employee->specialSkills->count())
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Special Skills</span></div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Skill</th><th>Proficiency</th></tr></thead>
                <tbody>
                    @foreach($employee->specialSkills as $skill)
                        <tr><td>{{ $skill->skill_name }}</td><td>{{ ucfirst($skill->proficiency) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Beneficiaries --}}
    @if($employee->beneficiaries->count())
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Beneficiaries</span></div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Name</th><th>Relationship</th><th>Date of Birth</th></tr></thead>
                <tbody>
                    @foreach($employee->beneficiaries as $b)
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
    @if($employee->charRefs->count())
    <div class="card mb-3">
        <div class="card-header"><span class="card-title mb-0">Character References</span></div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead><tr><th>Name</th><th>Address</th><th>Contact Number</th></tr></thead>
                <tbody>
                    @foreach($employee->charRefs as $ref)
                        <tr><td>{{ $ref->name }}</td><td>{{ $ref->address }}</td><td>{{ $ref->contact_number }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Attachments --}}
    @php
        use App\Models\EmployeeAttachment;
        $attachmentTypes  = EmployeeAttachment::attachmentTypes();
        $attachmentsByKey = $employee->employeeAttachments
            ->groupBy('attachment_key')
            ->map(fn($g) => $g->first());

        $approvedCount = $attachmentsByKey->where('status', 'approved')->count();
        $pendingCount  = $attachmentsByKey->where('status', 'pending')->count();
        $rejectedCount = $attachmentsByKey->where('status', 'rejected')->count();
        $missingCount  = count($attachmentTypes) - $attachmentsByKey->count();
    @endphp

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="card-title mb-0">Attachments</span>
            <a href="{{ route('employees.attachments.index', $employee) }}"
               class="emp-action-btn emp-action-edit"
               style="width:auto;padding:0 12px;font-size:.8rem;gap:4px;">
                <i class="feather-folder"></i> Manage
            </a>
        </div>
        <div class="card-body">

            {{-- Status summary --}}
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="emp-badge emp-badge-active">{{ $approvedCount }} Approved</span>
                @if($pendingCount)
                    <span class="emp-badge emp-badge-pending">{{ $pendingCount }} Pending</span>
                @endif
                @if($rejectedCount)
                    <span class="emp-badge emp-badge-inactive">{{ $rejectedCount }} Rejected</span>
                @endif
                @if($missingCount)
                    <span class="emp-badge" style="background:#f4f5f7;color:#9898a8;border:1px solid #e8e8ef;">
                        {{ $missingCount }} Missing
                    </span>
                @endif
            </div>

            {{-- Document grid --}}
            <div class="row g-2">
                @foreach($attachmentTypes as $key => $label)
                    @php
                        $att = $attachmentsByKey->get($key);
                        $cardBorder = match($att?->status) {
                            'approved' => '2px solid #16a34a',
                            'pending'  => '2px solid #d97706',
                            'rejected' => '2px solid #c8292a',
                            default    => '1px dashed #d4d4de',
                        };
                        $cardBg = $att ? '#fff' : '#f4f5f7';
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card text-center mb-0"
                             style="border:{{ $cardBorder }}!important;
                                    background:{{ $cardBg }}!important;
                                    box-shadow:none!important;
                                    min-height:90px;
                                    padding:8px 6px;">

                            @if($att)
                                @if(in_array($att->mime_type, ['image/jpeg','image/png','image/gif','image/webp']))
                                    <a href="{{ $att->url }}" target="_blank">
                                        <img src="{{ $att->url }}"
                                             style="width:100%;height:50px;object-fit:cover;border-radius:4px;"
                                             alt="{{ $label }}">
                                    </a>
                                @else
                                    <a href="{{ $att->url }}" target="_blank" class="text-decoration-none">
                                        <i class="feather-file-text mt-1"
                                           style="font-size:1.5rem;color:#9898a8;display:block;"></i>
                                    </a>
                                @endif
                            @else
                                <i class="feather-upload-cloud mt-1"
                                   style="font-size:1.5rem;color:#adb5bd;display:block;"></i>
                            @endif

                            <div class="text-truncate px-1 mt-1 fw-medium"
                                 title="{{ $label }}"
                                 style="font-size:.7rem;color:#4a4a58;">
                                {{ $label }}
                            </div>

                            @if($att)
                                @php
                                    $badgeClass = match($att->status) {
                                        'approved' => 'emp-badge-active',
                                        'pending'  => 'emp-badge-pending',
                                        'rejected' => 'emp-badge-inactive',
                                        default    => '',
                                    };
                                @endphp
                                <span class="emp-badge {{ $badgeClass }} mt-1"
                                      style="font-size:.6rem;padding:2px 7px;">
                                    {{ ucfirst($att->status) }}
                                </span>
                            @else
                                <span class="emp-badge mt-1"
                                      style="font-size:.6rem;padding:2px 7px;background:#f4f5f7;color:#9898a8;border:1px solid #e8e8ef;">
                                    Missing
                                </span>
                            @endif

                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

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