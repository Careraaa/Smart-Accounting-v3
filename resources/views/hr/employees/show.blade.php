@extends('layouts.layout')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Sora:wght@400;600;700;800&display=swap');
.emp-page { font-family: 'Sora', sans-serif; }

/* ── Topbar ─────────────────────────────────────────────────── */
.emp-topbar { display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap; }
.emp-topbar-left h1 { font-size:1.35rem;font-weight:800;color:#111827;letter-spacing:-0.02em;margin:0 0 6px; }
.emp-topbar-actions { display:flex;gap:8px;flex-wrap:wrap;align-items:center; }

.emp-btn-sec {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#374151;
    border:1px solid #e5e7eb;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.emp-btn-sec:hover { border-color:#c8292a;color:#c8292a;background:#fff5f5; }

.emp-btn-danger {
    display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:#fff;color:#c8292a;
    border:1px solid #fecaca;border-radius:10px;font-family:'Sora',sans-serif;font-size:0.82rem;
    font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;white-space:nowrap;
}
.emp-btn-danger:hover { background:#fff0f0;border-color:#c8292a; }

/* ── Status badge ────────────────────────────────────────────── */
.emp-hero-status { display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em; }
.emp-hero-status::before { content:'';width:6px;height:6px;border-radius:50%; }
.emp-hero-status.s-active   { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.emp-hero-status.s-active::before { background:#16a34a; }
.emp-hero-status.s-inactive { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.emp-hero-status.s-inactive::before { background:#ef4444; }

.emp-hero-position { font-size:0.82rem;color:#6b7280;margin:0 12px 0 0; }

/* ── Info cards ──────────────────────────────────────────────── */
.emp-info-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px; }
.emp-info-card-header { padding:14px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between; }
.emp-info-card-title { font-size:0.82rem;font-weight:700;color:#111827;margin:0;display:flex;align-items:center;gap:8px; }
.emp-dot { width:7px;height:7px;border-radius:50%;background:#c8292a;display:inline-block; }
.emp-info-card-body { padding:20px; }

/* Field label / value */
.emp-field-label { font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#9ca3af;display:block;margin-bottom:4px; }
.emp-field-value { font-size:0.875rem;color:#111827;font-weight:500; }
.emp-field-mono  { font-family:'DM Mono',monospace;font-size:0.845rem;color:#111827; }

/* Section dividers within info cards */
.emp-sub-divider { font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#9ca3af;border-bottom:1px solid #f3f4f6;padding-bottom:8px;margin:20px 0 16px;grid-column:1/-1; }

/* Grid layout for field groups */
.emp-fields-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:16px 24px; }
@media (max-width:768px) { .emp-fields-grid { grid-template-columns:repeat(2,1fr); } }
@media (max-width:480px) { .emp-fields-grid { grid-template-columns:1fr; } }
.emp-fields-grid.cols-2 { grid-template-columns:repeat(2,1fr); }
.emp-fields-grid.cols-4 { grid-template-columns:repeat(4,1fr); }
@media (max-width:900px) { .emp-fields-grid.cols-4 { grid-template-columns:repeat(2,1fr); } }

/* ── Gov numbers inline ─────────────────────────────────────── */
.emp-gov-row { display:flex;align-items:center;gap:8px; }
.emp-enrolled-tag { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:20px;font-size:0.67rem;font-weight:700;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }

/* ── Sub-tables (work exp, skills, etc.) ────────────────────── */
.emp-sub-table-wrap { border-radius:10px;overflow:hidden;border:1px solid #f3f4f6; }
.emp-sub-table { width:100%;border-collapse:collapse;font-size:0.82rem; }
.emp-sub-table thead tr { background:#f8f9fb; }
.emp-sub-table thead th { padding:9px 14px;font-size:0.67rem;font-weight:700;text-transform:uppercase;letter-spacing:0.09em;color:#6b7280; }
.emp-sub-table tbody tr { border-top:1px solid #f3f4f6;transition:background 0.1s; }
.emp-sub-table tbody tr:hover { background:#fafafa; }
.emp-sub-table tbody td { padding:10px 14px;color:#374151;vertical-align:middle; }

/* ── Attachment grid ─────────────────────────────────────────── */
.emp-att-summary { display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px; }
.emp-att-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:10px; }
@media (max-width:900px) { .emp-att-grid { grid-template-columns:repeat(3,1fr); } }
@media (max-width:600px) { .emp-att-grid { grid-template-columns:repeat(2,1fr); } }

.emp-att-tile { border-radius:10px;padding:10px 8px;text-align:center;display:flex;flex-direction:column;align-items:center;gap:6px;min-height:90px;transition:box-shadow 0.13s; }
.emp-att-tile:hover { box-shadow:0 2px 12px rgba(0,0,0,0.07); }
.emp-att-tile.t-approved { background:#fff;border:2px solid #16a34a; }
.emp-att-tile.t-pending  { background:#fff;border:2px solid #d97706; }
.emp-att-tile.t-rejected { background:#fff;border:2px solid #c8292a; }
.emp-att-tile.t-missing  { background:#f9fafb;border:1px dashed #d1d5db; }
.emp-att-tile img { width:100%;height:48px;object-fit:cover;border-radius:6px; }
.emp-att-tile-label { font-size:0.68rem;font-weight:600;color:#374151;text-align:center;line-height:1.3;word-break:break-word; }
.emp-att-status-tag { display:inline-block;padding:2px 8px;border-radius:20px;font-size:0.63rem;font-weight:700;text-transform:uppercase; }
.emp-att-status-tag.t-approved { background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0; }
.emp-att-status-tag.t-pending  { background:#fffbeb;color:#d97706;border:1px solid #fde68a; }
.emp-att-status-tag.t-rejected { background:#fff0f0;color:#c8292a;border:1px solid #fecaca; }
.emp-att-status-tag.t-missing  { background:#f3f4f6;color:#9ca3af;border:1px solid #e5e7eb; }

/* ── Manage button ───────────────────────────────────────────── */
.emp-manage-btn { display:inline-flex;align-items:center;gap:6px;padding:6px 12px;background:#f3f4f6;color:#374151;border:1px solid #e5e7eb;border-radius:8px;font-family:'Sora',sans-serif;font-size:0.75rem;font-weight:600;text-decoration:none;transition:all 0.13s; }
.emp-manage-btn:hover { background:#fff5f5;border-color:#c8292a;color:#c8292a; }
</style>
@endpush

@section('content')
@php
    $addr = [];
    if ($employee->address && str_starts_with(trim($employee->address), '{')) {
        $addr = json_decode($employee->address, true) ?? [];
    }
    $addressFormatted = collect([
        $addr['street'] ?? null, $addr['barangay'] ?? null,
        $addr['city'] ?? null, $addr['province'] ?? null,
    ])->filter()->implode(', ');
    if (!$addressFormatted) $addressFormatted = $employee->address ?? '—';

    $sc = $employee->status === 'active' ? 's-active' : 's-inactive';
@endphp

<div class="emp-page">

    {{-- Topbar --}}
    <div class="emp-topbar">
        <div class="emp-topbar-left">
            <h1>{{ $employee->first_name }} {{ $employee->middle_name }} {{ $employee->last_name }}</h1>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <span class="emp-hero-position">{{ $employee->position ?? '' }}</span>
                <span class="emp-hero-status {{ $sc }}">{{ ucfirst($employee->status) }}</span>
            </div>
        </div>
        <div class="emp-topbar-actions">
            <a href="{{ route('employees.index') }}" class="emp-btn-sec">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back
            </a>
            <a href="{{ route('employees.edit', $employee) }}" class="emp-btn-sec">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
            <form action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('Delete this employee?')" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="emp-btn-danger">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Personal Information --}}
    <div class="emp-info-card">
        <div class="emp-info-card-header">
            <h2 class="emp-info-card-title"><span class="emp-dot"></span> Personal Information</h2>
        </div>
        <div class="emp-info-card-body">
            <div class="emp-fields-grid">
                <div><span class="emp-field-label">First Name</span><div class="emp-field-value">{{ $employee->first_name ?? '—' }}</div></div>
                <div><span class="emp-field-label">Middle Name</span><div class="emp-field-value">{{ $employee->middle_name ?? '—' }}</div></div>
                <div><span class="emp-field-label">Last Name</span><div class="emp-field-value">{{ $employee->last_name ?? '—' }}</div></div>
                <div><span class="emp-field-label">Email</span><div class="emp-field-value">{{ $employee->email ?? '—' }}</div></div>
                <div><span class="emp-field-label">Phone</span><div class="emp-field-value emp-field-mono">{{ $employee->phone ?? '—' }}</div></div>
                <div><span class="emp-field-label">Gender</span><div class="emp-field-value">{{ $employee->gender ? ucwords(str_replace('_', ' ', $employee->gender)) : '—' }}</div></div>
                <div><span class="emp-field-label">Civil Status</span><div class="emp-field-value">{{ ucfirst($employee->civil_status ?? '—') }}</div></div>
                @if($employee->civil_status === 'married')
                <div><span class="emp-field-label">Spouse Name</span><div class="emp-field-value">{{ $employee->spouse_name ?? '—' }}</div></div>
                @endif
                <div><span class="emp-field-label">Date of Birth</span><div class="emp-field-value">{{ $employee->date_of_birth?->format('F d, Y') ?? '—' }}</div></div>
                <div><span class="emp-field-label">Place of Birth</span><div class="emp-field-value">{{ $employee->place_of_birth ?? '—' }}</div></div>
                <div><span class="emp-field-label">Educational Attainment</span><div class="emp-field-value">{{ $employee->educational_attainment ?? '—' }}</div></div>
            </div>

            <div class="emp-sub-divider">Address</div>
            @if(!empty($addr))
            <div class="emp-fields-grid cols-2">
                <div><span class="emp-field-label">Street / House No.</span><div class="emp-field-value">{{ $addr['street'] ?: '—' }}</div></div>
                <div><span class="emp-field-label">Barangay</span><div class="emp-field-value">{{ $addr['barangay'] ?: '—' }}</div></div>
                <div><span class="emp-field-label">City / Municipality</span><div class="emp-field-value">{{ $addr['city'] ?: '—' }}</div></div>
                <div><span class="emp-field-label">Province</span><div class="emp-field-value">{{ $addr['province'] ?: '—' }}</div></div>
            </div>
            @else
            <div><span class="emp-field-label">Address</span><div class="emp-field-value">{{ $addressFormatted }}</div></div>
            @endif
        </div>
    </div>

    {{-- Account Information --}}
    <div class="emp-info-card">
        <div class="emp-info-card-header">
            <h2 class="emp-info-card-title"><span class="emp-dot"></span> Account Information</h2>
        </div>
        <div class="emp-info-card-body">
            <div class="emp-fields-grid">
                <div><span class="emp-field-label">Username</span><div class="emp-field-value emp-field-mono">{{ $employee->username ?? '—' }}</div></div>
                <div><span class="emp-field-label">Role</span><div class="emp-field-value">{{ ucfirst($employee->role ?? '—') }}</div></div>
                <div>
                    <span class="emp-field-label">Account Status</span>
                    <div style="margin-top:4px;">
                        <span class="emp-hero-status {{ $sc }}">{{ ucfirst($employee->status) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Employment Information --}}
    <div class="emp-info-card">
        <div class="emp-info-card-header">
            <h2 class="emp-info-card-title"><span class="emp-dot"></span> Employment Information</h2>
        </div>
        <div class="emp-info-card-body">
            <div class="emp-fields-grid">
                <div><span class="emp-field-label">Date of Hire</span><div class="emp-field-value">{{ $employee->date_of_hire?->format('F d, Y') ?? '—' }}</div></div>
                <div><span class="emp-field-label">Position</span><div class="emp-field-value">{{ $employee->position ?? '—' }}</div></div>
                <div><span class="emp-field-label">Department</span><div class="emp-field-value">{{ ucfirst($employee->department ?? '—') }}</div></div>
                <div><span class="emp-field-label">Daily Rate</span><div class="emp-field-value emp-field-mono">₱{{ number_format($employee->salary_rate, 2) }}</div></div>
                <div><span class="emp-field-label">Driver's License</span><div class="emp-field-value emp-field-mono">{{ $employee->driver_license_number ?? '—' }}</div></div>
                <div><span class="emp-field-label">License Validity</span><div class="emp-field-value">{{ $employee->driver_license_validity?->format('F d, Y') ?? '—' }}</div></div>
            </div>
        </div>
    </div>

    {{-- Government Numbers --}}
    <div class="emp-info-card">
        <div class="emp-info-card-header">
            <h2 class="emp-info-card-title"><span class="emp-dot"></span> Government Numbers</h2>
        </div>
        <div class="emp-info-card-body">
            <div class="emp-fields-grid cols-2">
                <div>
                    <span class="emp-field-label">SSS Number</span>
                    <div class="emp-gov-row">
                        <span class="emp-field-value emp-field-mono">{{ $employee->sss_number ?? '—' }}</span>
                        @if($employee->has_sss)<span class="emp-enrolled-tag"><svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Enrolled</span>@endif
                    </div>
                </div>
                <div>
                    <span class="emp-field-label">TIN Number</span>
                    <div class="emp-gov-row">
                        <span class="emp-field-value emp-field-mono">{{ $employee->tin_number ?? '—' }}</span>
                        @if($employee->has_tin)<span class="emp-enrolled-tag"><svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Has TIN</span>@endif
                    </div>
                </div>
                <div>
                    <span class="emp-field-label">Pag-IBIG Number</span>
                    <div class="emp-gov-row">
                        <span class="emp-field-value emp-field-mono">{{ $employee->pagibig_number ?? '—' }}</span>
                        @if($employee->has_pagibig)<span class="emp-enrolled-tag"><svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Enrolled</span>@endif
                    </div>
                </div>
                <div>
                    <span class="emp-field-label">PhilHealth Number</span>
                    <div class="emp-gov-row">
                        <span class="emp-field-value emp-field-mono">{{ $employee->philhealth_number ?? '—' }}</span>
                        @if($employee->has_philhealth)<span class="emp-enrolled-tag"><svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Enrolled</span>@endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Work Experience --}}
    <div class="emp-info-card">
        <div class="emp-info-card-header">
            <h2 class="emp-info-card-title"><span class="emp-dot"></span> Work Experience</h2>
        </div>
        <div class="emp-info-card-body" style="padding:0;">
            <div class="emp-sub-table-wrap" style="border-radius:0;border:none;">
                <table class="emp-sub-table">
                    <thead><tr><th>Company</th><th>Position</th><th>Duration</th><th>Responsibilities</th></tr></thead>
                    <tbody>
                        @forelse($employee->workExperiences as $we)
                            <tr>
                                <td style="font-weight:600;color:#111827;">{{ $we->company_name }}</td>
                                <td>{{ $we->position }}</td>
                                <td><span style="font-family:'DM Mono',monospace;font-size:0.78rem;color:#6b7280;">{{ $we->duration }}</span></td>
                                <td style="color:#6b7280;font-size:0.8rem;">{{ $we->responsibilities }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted" style="padding:18px;">
                                    No work experience records yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Special Skills --}}
    <div class="emp-info-card">
        <div class="emp-info-card-header">
            <h2 class="emp-info-card-title"><span class="emp-dot"></span> Special Skills</h2>
        </div>
        <div class="emp-info-card-body" style="padding:0;">
            <div class="emp-sub-table-wrap" style="border-radius:0;border:none;">
                <table class="emp-sub-table">
                    <thead><tr><th>Skill</th><th>Proficiency</th></tr></thead>
                    <tbody>
                        @forelse($employee->specialSkills as $skill)
                            <tr>
                                <td style="font-weight:600;color:#111827;">{{ $skill->skill_name }}</td>
                                <td>
                                    @php $profColors = ['beginner'=>['#f0f9ff','#0284c7','#bae6fd'],'intermediate'=>['#fffbeb','#d97706','#fde68a'],'advanced'=>['#f5f3ff','#7c3aed','#ddd6fe'],'expert'=>['#f0fdf4','#16a34a','#bbf7d0']]; $pc = $profColors[strtolower($skill->proficiency)] ?? ['#f3f4f6','#6b7280','#e5e7eb']; @endphp
                                    <span style="background:{{ $pc[0] }};color:{{ $pc[1] }};border:1px solid {{ $pc[2] }};padding:2px 10px;border-radius:20px;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;">{{ ucfirst($skill->proficiency) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted" style="padding:18px;">
                                    No special skills added yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Beneficiaries --}}
    <div class="emp-info-card">
        <div class="emp-info-card-header">
            <h2 class="emp-info-card-title"><span class="emp-dot"></span> Beneficiaries</h2>
        </div>
        <div class="emp-info-card-body" style="padding:0;">
            <div class="emp-sub-table-wrap" style="border-radius:0;border:none;">
                <table class="emp-sub-table">
                    <thead><tr><th>Name</th><th>Relationship</th><th>Date of Birth</th></tr></thead>
                    <tbody>
                        @forelse($employee->beneficiaries as $b)
                            <tr>
                                <td style="font-weight:600;color:#111827;">{{ $b->name }}</td>
                                <td>{{ ucfirst($b->relationship) }}</td>
                                <td><span style="font-family:'DM Mono',monospace;font-size:0.78rem;color:#6b7280;">{{ $b->date_of_birth ? \Carbon\Carbon::parse($b->date_of_birth)->format('F d, Y') : '—' }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted" style="padding:18px;">
                                    No beneficiaries recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Character References --}}
    <div class="emp-info-card">
        <div class="emp-info-card-header">
            <h2 class="emp-info-card-title"><span class="emp-dot"></span> Character References</h2>
        </div>
        <div class="emp-info-card-body" style="padding:0;">
            <div class="emp-sub-table-wrap" style="border-radius:0;border:none;">
                <table class="emp-sub-table">
                    <thead><tr><th>Name</th><th>Address</th><th>Contact Number</th></tr></thead>
                    <tbody>
                        @forelse($employee->charRefs as $ref)
                            <tr>
                                <td style="font-weight:600;color:#111827;">{{ $ref->name }}</td>
                                <td style="color:#6b7280;font-size:0.8rem;">{{ $ref->address }}</td>
                                <td><span style="font-family:'DM Mono',monospace;font-size:0.78rem;">{{ $ref->contact_number }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted" style="padding:18px;">
                                    No character references added yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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
    <div class="emp-info-card">
        <div class="emp-info-card-header">
            <h2 class="emp-info-card-title"><span class="emp-dot"></span> Attachments</h2>
            <a href="{{ route('employees.attachments.index', $employee) }}" class="emp-manage-btn">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                Manage
            </a>
        </div>
        <div class="emp-info-card-body">
            <div class="emp-att-summary">
                <span class="emp-att-status-tag t-approved">{{ $approvedCount }} Approved</span>
                @if($pendingCount)<span class="emp-att-status-tag t-pending">{{ $pendingCount }} Pending</span>@endif
                @if($rejectedCount)<span class="emp-att-status-tag t-rejected">{{ $rejectedCount }} Rejected</span>@endif
                @if($missingCount)<span class="emp-att-status-tag t-missing">{{ $missingCount }} Missing</span>@endif
            </div>
            <div class="emp-att-grid">
                @foreach($attachmentTypes as $key => $label)
                    @php
                        $att = $attachmentsByKey->get($key);
                        $tileCls = match($att?->status) {
                            'approved' => 't-approved',
                            'pending'  => 't-pending',
                            'rejected' => 't-rejected',
                            default    => 't-missing',
                        };
                        $tagCls = $tileCls;
                        $tagLabel = $att ? ucfirst($att->status) : 'Missing';
                    @endphp
                    <div class="emp-att-tile {{ $tileCls }}">
                        @if($att)
                            @if(in_array($att->mime_type, ['image/jpeg','image/png','image/gif','image/webp']))
                                <a href="{{ $att->url }}" target="_blank" style="width:100%;">
                                    <img src="{{ $att->url }}" alt="{{ $label }}">
                                </a>
                            @else
                                <a href="{{ $att->url }}" target="_blank" style="color:#9ca3af;">
                                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </a>
                            @endif
                        @else
                            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="color:#d1d5db;"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        @endif
                        <div class="emp-att-tile-label">{{ $label }}</div>
                        <span class="emp-att-status-tag {{ $tagCls }}">{{ $tagLabel }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
@endsection