<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttachment;
use App\Models\User;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmployeeAttachmentController extends Controller
{
    use LogsUserActivity;
    /**
     * HR: list all attachments for a specific employee, grouped by key.
     */
    public function index(User $employee)
    {
        $attachments     = EmployeeAttachment::where('user_id', $employee->id)
            ->orderBy('attachment_key')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('attachment_key');

        $attachmentTypes = EmployeeAttachment::attachmentTypes();

        $this->logActivity('viewed', "Employee attachments: {$employee->first_name} {$employee->last_name}", request()->url(), 'employee', $employee->id);

        return view('hr.employees.attachments.index', compact('employee', 'attachments', 'attachmentTypes'));
    }

    /**
     * HR: upload a file on behalf of the employee (auto-approved).
     */
    public function store(Request $request, User $employee)
    {
        $request->validate([
            'attachment_key' => 'required|string|in:' . implode(',', array_keys(EmployeeAttachment::attachmentTypes())),
            'file'           => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $attachment = static::saveAttachment($request->file('file'), $employee, $request->attachment_key, 'hr');

        $this->logActivity('created', "Attachment: {$attachment->label} for {$employee->first_name} {$employee->last_name}", request()->url(), 'employee', $employee->id);

        return back()->with('success', 'Attachment uploaded successfully.');
    }

    /**
     * HR: approve a pending attachment.
     */
    public function approve(EmployeeAttachment $attachment)
    {
        $attachment->update([
            'status'           => 'approved',
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
            'rejection_reason' => null,
        ]);

        $this->logActivity('approved', "Attachment: {$attachment->label}", request()->url(), 'employee', $attachment->user_id);

        return back()->with('success', "'{$attachment->label}' approved.");
    }

    /**
     * HR: reject an attachment with a reason.
     */
    public function reject(Request $request, EmployeeAttachment $attachment)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $attachment->update([
            'status'           => 'rejected',
            'reviewed_by'      => auth()->id(),
            'reviewed_at'      => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        $this->logActivity('rejected', "Attachment: {$attachment->label}", request()->url(), 'employee', $attachment->user_id);

        return back()->with('success', "'{$attachment->label}' rejected.");
    }

    /**
     * HR: permanently delete an attachment file and its record.
     */
    public function destroy(EmployeeAttachment $attachment)
    {
        $this->logActivity('deleted', "Attachment: {$attachment->label}", request()->url(), 'employee', $attachment->user_id);
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return back()->with('success', 'Attachment deleted.');
    }

    /**
     * Shared static helper — used by EmployeeController (form uploads)
     * and Employee\AttachmentController (self-service uploads).
     *
     * HR uploads  → status = 'approved'
     * Employee uploads → status = 'pending'
     */
    public static function saveAttachment(
        \Illuminate\Http\UploadedFile $file,
        User $employee,
        string $key,
        string $uploadedByRole
    ): EmployeeAttachment {
        $types = EmployeeAttachment::attachmentTypes();
        $label = $types[$key] ?? $key;

        // Organized storage path:
        // employees/{employee_id}/{attachment_key}/{uploaded_by_role}/filename
        $directory = "employees/{$employee->id}/{$key}/{$uploadedByRole}";
        $filename = now()->format('Ymd_His') . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, 'public');

        return EmployeeAttachment::create([
            'user_id'          => $employee->id,
            'attachment_key'   => $key,
            'label'            => $label,
            'file_path'        => $path,
            'original_name'    => $file->getClientOriginalName(),
            'mime_type'        => $file->getMimeType(),
            'file_size'        => $file->getSize(),
            'status'           => $uploadedByRole === 'hr' ? 'approved' : 'pending',
            'uploaded_by_role' => $uploadedByRole,
        ]);
    }
}