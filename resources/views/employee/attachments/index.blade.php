{{-- resources/views/employee/attachments/index.blade.php --}}
@extends('layouts.layout')

@push('styles')
    @include('employee._ui-styles')
@endpush

@section('content')
<div class="container-fluid empui-page empui-wrap">
    <div class="empui-backdrop"><div class="empui-grid"></div></div>
    <div class="empui-content">

        <div class="empui-hero">
            <div class="empui-hero-left">
                <h1 class="empui-title">My Documents</h1>
                <p class="empui-sub">Upload missing documents or resubmit rejected ones. HR will review your submissions.</p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="empui-chip"><i class="feather-calendar"></i> {{ now()->format('l, F d, Y') }}</span>
                    <span class="empui-chip"><i class="feather-upload"></i> JPG, PNG, PDF</span>
                </div>
            </div>
            <div class="empui-hero-right">
                <a class="empui-btn-sec" href="{{ route('employee.dashboard') }}">
                    <i class="feather-home"></i>
                    Dashboard
                </a>
            </div>
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

        <div class="empui-stats">
            <div class="empui-stat s-blue">
                <div class="empui-ico"><i class="feather-upload-cloud"></i></div>
                <div>
                    <div class="empui-lbl">Uploaded</div>
                    <div class="empui-val empui-mono">{{ $uploadedKeys }}/{{ $totalTypes }}</div>
                    <div class="empui-muted" style="margin-top:4px;">document types</div>
                </div>
            </div>
            <div class="empui-stat s-green">
                <div class="empui-ico"><i class="feather-check-circle"></i></div>
                <div>
                    <div class="empui-lbl">Approved</div>
                    <div class="empui-val empui-mono">{{ $approvedCount }}</div>
                    <div class="empui-muted" style="margin-top:4px;">ready</div>
                </div>
            </div>
            <div class="empui-stat s-amber">
                <div class="empui-ico"><i class="feather-clock"></i></div>
                <div>
                    <div class="empui-lbl">Under Review</div>
                    <div class="empui-val empui-mono">{{ $pendingCount }}</div>
                    <div class="empui-muted" style="margin-top:4px;">pending</div>
                </div>
            </div>
            <div class="empui-stat s-red">
                <div class="empui-ico"><i class="feather-alert-triangle"></i></div>
                <div>
                    <div class="empui-lbl">Action Needed</div>
                    <div class="empui-val empui-mono">{{ $rejectedCount + $missingCount }}</div>
                    <div class="empui-muted" style="margin-top:4px;">missing/rejected</div>
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
                    <div class="empui-card h-100 {{ $borderClass }}" style="{{ $latest ? 'border-width:2px!important;' : '' }}">
                        <div class="empui-card-head">
                            <p class="empui-card-title"><span class="empui-dot"></span> {{ $label }}</p>
                            @if($isApproved)     <span class="empui-pill approved">Approved</span>
                            @elseif($isPending)  <span class="empui-pill pending">Under Review</span>
                            @elseif($isRejected) <span class="empui-pill rejected">Rejected</span>
                            @else                <span class="empui-pill neutral">Missing</span>
                            @endif
                        </div>

                        <div class="empui-card-body">
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

                                <a href="{{ $latest->url }}" target="_blank" class="empui-btn-sec mb-2" style="padding:7px 12px;border-radius:9px;">
                                    <i class="feather-eye"></i> View
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
                                        <button type="submit" class="empui-btn" style="padding:8px 10px;border-radius:10px;box-shadow:none;">
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
</div>
@endsection