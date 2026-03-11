{{-- resources/views/hr/employees/attachments/index.blade.php --}}
@extends('layouts.layout')

@section('title', 'Employee Attachments')

@section('content')

<div class="kt-page-header mb-3">
    <div class="kt-page-header-left">
        <div>
            <h4 class="kt-page-title">
                {{ $employee->first_name }} {{ $employee->last_name }} — Attachments
            </h4>
            <div class="kt-breadcrumb">
                <span class="kt-breadcrumb-item">Employees</span>
                <span class="kt-breadcrumb-sep">›</span>
                <a href="{{ route('employees.show', $employee) }}" class="kt-breadcrumb-item"
                   style="color:inherit;text-decoration:none;">
                    {{ $employee->first_name }} {{ $employee->last_name }}
                </a>
                <span class="kt-breadcrumb-sep">›</span>
                <span class="kt-breadcrumb-item active">Attachments</span>
            </div>
        </div>
    </div>
    <div class="ms-auto">
        <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-secondary">
            <i class="feather-arrow-left me-1"></i> Back to Employee
        </a>
    </div>
</div>

<div class="main-content">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="feather-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="feather-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $flat          = $attachments->flatten();
        $approvedCount = $flat->where('status','approved')->count();
        $pendingCount  = $flat->where('status','pending')->count();
        $rejectedCount = $flat->where('status','rejected')->count();
        $uploadedKeys  = $attachments->keys()->count();
        $totalTypes    = count($attachmentTypes);
    @endphp

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card card-statistic">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="dash-icon">
                        <i class="feather-folder"></i>
                    </div>
                    <div>
                        <div class="dash-label">Uploaded</div>
                        <div class="dash-value" style="font-size:1.35rem;">
                            {{ $uploadedKeys }}<span style="font-size:.85rem;color:#9898a8;font-weight:600;">/{{ $totalTypes }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-statistic">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="dash-icon di-green">
                        <i class="feather-check-circle"></i>
                    </div>
                    <div>
                        <div class="dash-label">Approved</div>
                        <div class="dash-value" style="font-size:1.35rem;">{{ $approvedCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-statistic">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="dash-icon di-amber">
                        <i class="feather-clock"></i>
                    </div>
                    <div>
                        <div class="dash-label">Pending</div>
                        <div class="dash-value" style="font-size:1.35rem;">{{ $pendingCount }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card card-statistic">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="dash-icon di-red">
                        <i class="feather-alert-circle"></i>
                    </div>
                    <div>
                        <div class="dash-label">Rejected</div>
                        <div class="dash-value" style="font-size:1.35rem;">{{ $rejectedCount }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Document cards --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Documents</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($attachmentTypes as $key => $label)
                    @php
                        $records    = $attachments->get($key, collect());
                        $latest     = $records->first();
                        $isPending  = $latest?->status === 'pending';
                        $isApproved = $latest?->status === 'approved';
                        $isRejected = $latest?->status === 'rejected';

                        $cardBorder = match(true) {
                            $isApproved => '2px solid #16a34a',
                            $isPending  => '2px solid #d97706',
                            $isRejected => '2px solid #c8292a',
                            default     => '1px dashed #d4d4de',
                        };
                    @endphp

                    <div class="col-md-6 col-xl-4">
                        <div class="card mb-0 h-100"
                             style="border:{{ $cardBorder }}!important;box-shadow:none!important;">
                            <div class="card-body" style="padding:14px 16px!important;">

                                {{-- Title + badge --}}
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span style="font-size:.845rem;font-weight:700;color:#1c1c1e;">
                                        {{ $label }}
                                    </span>
                                    @if($isApproved)
                                        <span class="emp-badge emp-badge-active">Approved</span>
                                    @elseif($isPending)
                                        <span class="emp-badge emp-badge-pending">Pending</span>
                                    @elseif($isRejected)
                                        <span class="emp-badge emp-badge-inactive">Rejected</span>
                                    @else
                                        <span class="emp-badge"
                                              style="background:#f4f5f7;color:#9898a8;border:1px solid #e8e8ef;">
                                            Missing
                                        </span>
                                    @endif
                                </div>

                                {{-- File preview --}}
                                @if($latest)
                                    @if($latest->is_image)
                                        <a href="{{ $latest->url }}" target="_blank">
                                            <img src="{{ $latest->url }}" alt="{{ $label }}"
                                                 class="rounded mb-2"
                                                 style="width:100%;height:88px;object-fit:cover;">
                                        </a>
                                    @else
                                        <div class="d-flex align-items-center gap-2 p-2 rounded mb-2"
                                             style="background:#f4f5f7;">
                                            <i class="feather-file-text"
                                               style="color:#9898a8;font-size:17px;flex-shrink:0;"></i>
                                            <div class="overflow-hidden">
                                                <div class="text-truncate"
                                                     style="font-size:.8rem;font-weight:600;color:#1c1c1e;">
                                                    {{ $latest->original_name }}
                                                </div>
                                                <div style="font-size:.7rem;color:#9898a8;">
                                                    {{ $latest->file_size_human }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div style="font-size:.72rem;color:#9898a8;" class="mb-2">
                                        Uploaded {{ $latest->created_at->diffForHumans() }}
                                        · by {{ ucfirst($latest->uploaded_by_role) }}
                                    </div>

                                    @if($isRejected && $latest->rejection_reason)
                                        <div class="alert alert-danger py-1 px-2 mb-2"
                                             style="font-size:.78rem;">
                                            <strong>Reason:</strong> {{ $latest->rejection_reason }}
                                        </div>
                                    @endif

                                    {{-- Action buttons --}}
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        <a href="{{ $latest->url }}" target="_blank"
                                           class="emp-action-btn emp-action-view"
                                           style="width:auto;padding:0 10px;font-size:.78rem;gap:4px;">
                                            <i class="feather-eye"></i> View
                                        </a>

                                        @if($isPending)
                                            <form method="POST"
                                                  action="{{ route('employees.attachments.approve', [$employee, $latest]) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                        class="emp-action-btn emp-action-approve"
                                                        style="width:auto;padding:0 10px;font-size:.78rem;gap:4px;">
                                                    <i class="feather-check"></i> Approve
                                                </button>
                                            </form>

                                            <button type="button"
                                                    class="emp-action-btn emp-action-danger"
                                                    style="width:auto;padding:0 10px;font-size:.78rem;gap:4px;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal{{ $latest->id }}">
                                                <i class="feather-x"></i> Reject
                                            </button>
                                        @endif

                                        <form method="POST"
                                              action="{{ route('employees.attachments.destroy', [$employee, $latest]) }}"
                                              onsubmit="return confirm('Delete this file permanently?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="emp-action-btn emp-action-danger"
                                                    title="Delete">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <p style="font-size:.8rem;color:#9898a8;" class="mb-2">
                                        No file uploaded yet.
                                    </p>
                                @endif

                                {{-- HR upload form --}}
                                <form method="POST"
                                      action="{{ route('employees.attachments.store', $employee) }}"
                                      enctype="multipart/form-data"
                                      class="pt-2 border-top">
                                    @csrf
                                    <input type="hidden" name="attachment_key" value="{{ $key }}">
                                    <div class="d-flex gap-1 mt-1">
                                        <input type="file" name="file"
                                               class="form-control form-control-sm"
                                               accept=".jpg,.jpeg,.png,.pdf" required>
                                        <button type="submit"
                                                class="btn btn-sm btn-secondary text-nowrap">
                                            <i class="feather-upload me-1"></i>
                                            {{ $latest ? 'Replace' : 'Upload' }}
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

                    {{-- Reject modal --}}
                    @if($isPending && $latest)
                        <div class="modal fade" id="rejectModal{{ $latest->id }}"
                             tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST"
                                          action="{{ route('employees.attachments.reject', [$employee, $latest]) }}">
                                        @csrf @method('PATCH')
                                        <div class="modal-header">
                                            <h6 class="modal-title fw-bold">Reject — {{ $label }}</h6>
                                            <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label">
                                                Reason <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="rejection_reason"
                                                      class="form-control form-control-sm"
                                                      rows="3" required
                                                      placeholder="e.g. Image is blurry, wrong document..."></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-secondary"
                                                    data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit"
                                                    class="btn btn-sm btn-danger">Reject</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                @endforeach
            </div>
        </div>
    </div>

    {{-- Upload history --}}
    @php $hasHistory = $attachments->filter(fn($r) => $r->count() > 1)->isNotEmpty(); @endphp
    @if($hasHistory)
        <div class="card mt-4">
            <div class="card-header">
                <span class="card-title">Upload History</span>
            </div>
            <div class="card-body p-0">
                <div class="accordion accordion-flush" id="historyAccordion">
                    @foreach($attachments->filter(fn($r) => $r->count() > 1) as $key => $records)
                        <div class="accordion-item"
                             style="border:none;border-bottom:1px solid #e8e8ef;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed py-3" type="button"
                                        style="font-size:.845rem;font-weight:600;color:#1c1c1e;background:#fff;box-shadow:none;"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#hist{{ $loop->index }}">
                                    {{ $attachmentTypes[$key] ?? $key }}
                                    <span class="emp-badge ms-2"
                                          style="background:#f4f5f7;color:#9898a8;border:1px solid #e8e8ef;">
                                        {{ $records->count() }} versions
                                    </span>
                                </button>
                            </h2>
                            <div id="hist{{ $loop->index }}" class="accordion-collapse collapse">
                                <div class="accordion-body p-0">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th class="ps-3">File</th>
                                                <th>Uploaded</th>
                                                <th>By</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($records as $record)
                                                <tr>
                                                    <td class="ps-3">
                                                        <a href="{{ $record->url }}" target="_blank"
                                                           style="font-size:.82rem;">
                                                            {{ $record->original_name }}
                                                        </a>
                                                    </td>
                                                    <td class="text-muted" style="font-size:.82rem;">
                                                        {{ $record->created_at->format('M d, Y') }}
                                                    </td>
                                                    <td style="font-size:.82rem;">
                                                        {{ ucfirst($record->uploaded_by_role) }}
                                                    </td>
                                                    <td>{!! $record->status_badge !!}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

</div>
@endsection