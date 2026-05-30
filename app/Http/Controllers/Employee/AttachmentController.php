<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\EmployeeAttachmentController as HRAttachmentController;
use App\Models\EmployeeAttachment;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    /**
     * Employee: view their own document statuses.
     */
    public function index()
    {
        $employee    = auth()->user();
        $attachments = EmployeeAttachment::where('user_id', $employee->id)
            ->orderBy('attachment_key')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('attachment_key');

        $attachmentTypes = EmployeeAttachment::attachmentTypes();

        return view('employee.attachments.index', compact('employee', 'attachments', 'attachmentTypes'));
    }

    /**
     * Employee: submit a missing or rejected document for HR review.
     * Cannot upload while a pending review exists, or if already approved.
     */
    public function store(Request $request)
    {
        $request->validate([
            'attachment_key' => 'required|string|in:' . implode(',', array_keys(EmployeeAttachment::attachmentTypes())),
            'file'           => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $employee = auth()->user();
        $key      = $request->attachment_key;

        $hasPending = EmployeeAttachment::where('user_id', $employee->id)
            ->where('attachment_key', $key)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->with('error', 'You already have a pending upload for this document. Please wait for HR to review it.');
        }

        $isApproved = EmployeeAttachment::where('user_id', $employee->id)
            ->where('attachment_key', $key)
            ->where('status', 'approved')
            ->exists();

        if ($isApproved) {
            return back()->with('error', 'This document is already approved. Contact HR if you need to replace it.');
        }

        $isHrRole = in_array($employee->role, ['hr', 'superadmin', 'accountant', 'qr_admin'], true);
        $uploadedByRole = $isHrRole ? 'hr' : 'employee';

        HRAttachmentController::saveAttachment($request->file('file'), $employee, $key, $uploadedByRole);

        $message = $isHrRole
            ? 'Document uploaded and approved.'
            : 'Document submitted for HR review.';

        return back()->with('success', $message);
    }
}