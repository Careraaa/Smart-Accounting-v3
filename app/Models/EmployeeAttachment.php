<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EmployeeAttachment extends Model
{
    protected $fillable = [
        'user_id',
        'attachment_key',
        'label',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'uploaded_by_role',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'file_size'   => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getUrlAttribute(): string
    {
        $storageUrl = Storage::disk('public')->url($this->file_path);

        // Normalize to a path and rebuild with the current app host/base path.
        // This avoids hardcoded localhost links when accessed through tunnels.
        $path = parse_url($storageUrl, PHP_URL_PATH) ?: $storageUrl;

        return url(ltrim($path, '/'));
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes < 1024)    return "{$bytes} B";
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    public function getIsImageAttribute(): bool
    {
        return in_array($this->mime_type, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default    => '<span class="badge bg-warning text-dark">Pending</span>',
        };
    }

    public static function attachmentTypes(): array
    {
        return [
            'drivers_license'    => "Driver's License",
            'valid_id_1'         => 'Valid ID 1',
            'valid_id_2'         => 'Valid ID 2',
            '2x2_picture'        => '2x2 Picture',
            '1x1_picture'        => '1x1 Picture',
            'police_clearance'   => 'Police Clearance',
            'barangay_clearance' => 'Barangay Clearance',
            'house_sketch'       => 'House Sketch',
            'medical_cert'       => 'Medical Certificate',
            'drug_test'          => 'Drug Test Result',
            'x_ray'              => 'X-Ray Result',
        ];
    }
}