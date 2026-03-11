<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeAttachmentController extends Controller
{
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

        static::saveAttachment($request->file('file'), $employee, $request->attachment_key, 'hr');

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

        return back()->with('success', "'{$attachment->label}' rejected.");
    }

    /**
     * HR: permanently delete an attachment file and its record.
     */
    public function destroy(EmployeeAttachment $attachment)
    {
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

        // Organised storage path: employees/{id}/attachments/{key}/filename
        $path = $file->store(
            "employees/{$employee->id}/attachments/{$key}",
            'public'
        );

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