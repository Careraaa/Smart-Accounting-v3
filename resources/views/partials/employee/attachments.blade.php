<h5 class="mb-1">Attachments</h5>
<p class="text-muted mb-4" style="font-size:.85rem;">
    Accepted: JPG, PNG, PDF · Max 5MB each.
    Files uploaded here are <strong>auto-approved</strong>.
    Employees can upload missing docs from their portal, which require HR review.
</p>

@php
    use App\Models\EmployeeAttachment;
    $attachmentTypes = EmployeeAttachment::attachmentTypes();
    $existingByKey   = collect();

    if ($employee->exists) {
        $existingByKey = EmployeeAttachment::where('user_id', $employee->id)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('attachment_key')
            ->map(fn($g) => $g->first());
    }
@endphp

<div class="row g-3">
    @foreach($attachmentTypes as $key => $label)
        @php
            $existing = $existingByKey->get($key);
            $statusBadge = match($existing?->status) {
                'approved' => '<span class="badge bg-success ms-1">Approved</span>',
                'pending'  => '<span class="badge bg-warning text-dark ms-1">Pending</span>',
                'rejected' => '<span class="badge bg-danger ms-1">Rejected</span>',
                default    => '',
            };
        @endphp
        <div class="col-md-6">
            <div class="card shadow-sm h-100 {{ $existing ? 'border-success' : '' }}"
                 style="{{ $existing ? 'border-width:2px!important;' : '' }}">
                <div class="card-body pb-2">

                    <div class="d-flex align-items-center mb-2">
                        <h6 class="card-title mb-0 me-1">{{ $label }}</h6>
                        {!! $statusBadge !!}
                    </div>

                    @if($existing)
                        <div class="mb-2">
                            @if($existing->is_image)
                                <img src="{{ $existing->url }}" alt="{{ $label }}"
                                     class="img-fluid rounded"
                                     style="max-height:80px;object-fit:cover;">
                            @else
                                <div class="d-flex align-items-center gap-2 p-2 bg-light rounded">
                                    <i class="feather-file text-muted"></i>
                                    <a href="{{ $existing->url }}" target="_blank"
                                       class="small text-truncate">{{ $existing->original_name }}</a>
                                </div>
                            @endif
                            <div class="text-muted mt-1" style="font-size:.7rem;">
                                {{ $existing->created_at->diffForHumans() }} · {{ $existing->file_size_human }}
                            </div>
                        </div>
                    @endif

                    <input type="file"
                           name="attachments_files[{{ $key }}]"
                           class="form-control form-control-sm"
                           accept=".jpg,.jpeg,.png,.pdf">

                    @if($existing)
                        <div class="text-muted mt-1" style="font-size:.7rem;">
                            <i class="feather-info" style="font-size:.7rem;"></i>
                            Selecting a file will replace the current one.
                        </div>
                    @endif

                </div>
            </div>
        </div>
    @endforeach
</div>