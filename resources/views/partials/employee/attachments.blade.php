{{-- attachments.blade.php --}}
<p class="emp-section-title-form">Attachments</p>
<p class="emp-section-sub">Accepted: JPG, PNG, PDF · Max 5MB each. Files uploaded here are auto-approved. Employees can upload missing docs from their portal, which require HR review.</p>

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

<div class="emp-row cols-2">
    @foreach($attachmentTypes as $key => $label)
        @php
            $existing = $existingByKey->get($key);
            $cardClass = $existing ? 'has-file' : '';
            $tagClass  = match($existing?->status) {
                'approved' => 'background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;',
                'pending'  => 'background:#fffbeb;color:#d97706;border:1px solid #fde68a;',
                'rejected' => 'background:#fff0f0;color:#c8292a;border:1px solid #fecaca;',
                default    => '',
            };
            $tagLabel = $existing ? ucfirst($existing->status) : null;
        @endphp
        <div>
            <div class="emp-att-form-card {{ $cardClass }}">
                <div class="emp-att-form-title">
                    @if($existing)
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="color:#16a34a;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#9ca3af;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    @endif
                    {{ $label }}
                    @if($tagLabel)
                        <span style="margin-left:auto;padding:2px 8px;border-radius:20px;font-size:0.63rem;font-weight:700;text-transform:uppercase;{{ $tagClass }}">{{ $tagLabel }}</span>
                    @endif
                </div>

                @if($existing)
                    @if($existing->is_image ?? in_array($existing->mime_type, ['image/jpeg','image/png','image/gif','image/webp']))
                        <img src="{{ $existing->url }}" alt="{{ $label }}" class="emp-att-preview">
                    @else
                        <div class="emp-att-file-row">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#9ca3af;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <a href="{{ $existing->url }}" target="_blank">{{ $existing->original_name }}</a>
                        </div>
                    @endif
                    <p class="emp-att-time">{{ $existing->created_at->diffForHumans() }} · {{ $existing->file_size_human }}</p>
                @endif

                <div class="emp-field">
                    <label class="emp-label" style="margin-bottom:4px;">
                        @if($existing) Replace File @else Upload File @endif
                    </label>
                    <input type="file"
                        name="attachments_files[{{ $key }}]"
                        class="emp-input" style="padding:7px 10px;font-size:0.78rem;cursor:pointer;"
                        accept=".jpg,.jpeg,.png,.pdf">
                    @if($existing)
                        <p class="emp-hint">Selecting a new file will replace the current one.</p>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>