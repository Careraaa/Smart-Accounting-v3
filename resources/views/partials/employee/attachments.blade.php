<h5 class="mb-4">Attachments</h5>
<p class="text-muted mb-4">Upload required documents. Accepted formats: JPG, PNG, PDF. Max 5MB each.</p>

<div class="row">
    @php
        $attachmentTypes = [
            'drivers_license'     => "Driver's License",
            'valid_id_1'          => 'Valid ID 1',
            'valid_id_2'          => 'Valid ID 2',
            '2x2_picture'         => '2x2 Picture',
            '1x1_picture'         => '1x1 Picture',
            'police_clearance'    => 'Police Clearance',
            'barangay_clearance'  => 'Barangay Clearance',
            'house_sketch'        => 'House Sketch',
            'medical_cert'        => 'Medical Certificate',
            'drug_test'           => 'Drug Test Result',
            'x_ray'               => 'X-Ray Result',
        ];

        $existingAttachments = ($employee->exists && $employee->attachments)
            ? $employee->attachments
            : [];
    @endphp

    @foreach($attachmentTypes as $key => $label)
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="card-title mb-3">{{ $label }}</h6>
                    <input type="file"
                        name="attachments_files[{{ $key }}]"
                        class="form-control form-control-sm"
                        accept=".jpg,.jpeg,.png,.pdf">
                    @if(isset($existingAttachments[$key]))
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="bi bi-paperclip"></i> Current file:
                                <a href="{{ Storage::url($existingAttachments[$key]) }}" target="_blank">
                                    {{ basename($existingAttachments[$key]) }}
                                </a>
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>