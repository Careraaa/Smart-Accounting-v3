{{-- resources/views/employee/attachments/index.blade.php --}}
@extends('layouts.layout')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h4 class="mb-0">My Documents</h4>
        <small class="text-muted">Upload missing documents or resubmit rejected ones. HR will review your submissions.</small>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $flat          = $attachments->flatten();
        $approvedCount = $flat->where('status','approved')->count();
        $pendingCount  = $flat->where('status','pending')->count();
        $rejectedCount = $flat->where('status','rejected')->count();
        $uploadedKeys  = $attachments->keys()->count();
        $totalTypes    = count($attachmentTypes);
        $missingCount  = $totalTypes - $uploadedKeys;
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 bg-light text-center py-3">
                <div class="fs-4 fw-bold">{{ $uploadedKeys }}/{{ $totalTypes }}</div>
                <div class="small text-muted">Uploaded</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 text-center py-3" style="background:#dcfce7;">
                <div class="fs-4 fw-bold text-success">{{ $approvedCount }}</div>
                <div class="small text-muted">Approved</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 text-center py-3" style="background:#fef9c3;">
                <div class="fs-4 fw-bold" style="color:#854d0e;">{{ $pendingCount }}</div>
                <div class="small text-muted">Under Review</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 text-center py-3" style="background:#fee2e2;">
                <div class="fs-4 fw-bold text-danger">{{ $rejectedCount + $missingCount }}</div>
                <div class="small text-muted">Action Needed</div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        @foreach($attachmentTypes as $key => $label)
            @php
                $records    = $attachments->get($key, collect());
                $latest     = $records->first();
                $isPending  = $latest?->status === 'pending';
                $isApproved = $latest?->status === 'approved';
                $isRejected = $latest?->status === 'rejected';
                $canUpload  = $latest === null || $isRejected;

                $borderClass = match(true) {
                    $isApproved => 'border-success',
                    $isPending  => 'border-warning',
                    $isRejected => 'border-danger',
                    default     => '',
                };
            @endphp

            <div class="col-md-6 col-xl-4">
                <div class="card h-100 {{ $borderClass }}"
                     style="{{ $latest ? 'border-width:2px!important;' : '' }}">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0">{{ $label }}</h6>
                            @if($isApproved)     <span class="badge bg-success">Approved</span>
                            @elseif($isPending)  <span class="badge bg-warning text-dark">Under Review</span>
                            @elseif($isRejected) <span class="badge bg-danger">Rejected</span>
                            @else                <span class="badge bg-secondary">Missing</span>
                            @endif
                        </div>

                        @if($latest)
                            @if($latest->is_image)
                                <img src="{{ $latest->url }}" alt="{{ $label }}"
                                     class="img-fluid rounded mb-2"
                                     style="max-height:100px;object-fit:cover;width:100%;">
                            @else
                                <div class="d-flex align-items-center gap-2 p-2 bg-light rounded mb-2">
                                    <i class="feather-file-text text-muted"></i>
                                    <span class="small text-truncate">{{ $latest->original_name }}</span>
                                </div>
                            @endif

                            <div class="small text-muted mb-1">
                                Submitted {{ $latest->created_at->diffForHumans() }}
                            </div>

                            @if($isRejected && $latest->rejection_reason)
                                <div class="alert alert-danger py-1 px-2 mb-2" style="font-size:.78rem;">
                                    <strong>Rejected:</strong> {{ $latest->rejection_reason }}
                                </div>
                            @endif

                            @if($isPending)
                                <p class="small text-warning mb-2">
                                    <i class="feather-clock me-1"></i>Waiting for HR review.
                                </p>
                            @endif

                            <a href="{{ $latest->url }}" target="_blank"
                               class="btn btn-sm btn-outline-primary mb-2">
                                <i class="feather-eye me-1"></i>View
                            </a>
                        @else
                            <p class="small text-muted mb-2">
                                <i class="feather-alert-circle me-1 text-warning"></i>Not uploaded yet.
                            </p>
                        @endif

                        @if($canUpload)
                            <form method="POST"
                                  action="{{ route('employee.attachments.store') }}"
                                  enctype="multipart/form-data"
                                  class="pt-2 border-top mt-1">
                                @csrf
                                <input type="hidden" name="attachment_key" value="{{ $key }}">
                                <label class="form-label small mb-1">
                                    {{ $isRejected ? 'Resubmit document' : 'Upload document' }}
                                </label>
                                <div class="d-flex gap-1">
                                    <input type="file" name="file"
                                           class="form-control form-control-sm"
                                           accept=".jpg,.jpeg,.png,.pdf" required>
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="feather-upload"></i>
                                    </button>
                                </div>
                                <div class="text-muted mt-1" style="font-size:.7rem;">
                                    JPG, PNG or PDF · Max 5MB
                                </div>
                            </form>
                        @elseif($isPending)
                            <p class="small text-muted mt-2 mb-0 pt-2 border-top">
                                Cannot re-upload while a review is pending.
                            </p>
                        @endif

                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection