<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\EmployeeAttachmentController as HRAttachmentController;
use App\Models\EmployeeAttachment;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    // ── Employee: view their own document statuses ────────────────────
    public function index()
    {
        $employee        = auth()->user();
        $attachments     = EmployeeAttachment::where('user_id', $employee->id)
            ->orderBy('attachment_key')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('attachment_key');

        $attachmentTypes = EmployeeAttachment::attachmentTypes();

        return view('employee.attachments.index', compact('employee', 'attachments', 'attachmentTypes'));
    }

    // ── Employee: upload a missing or rejected file ───────────────────
    public function store(Request $request)
    {
        $request->validate([
            'attachment_key' => 'required|string|in:' . implode(',', array_keys(EmployeeAttachment::attachmentTypes())),
            'file'           => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $employee = auth()->user();
        $key      = $request->attachment_key;

        // Block re-upload while a review is already pending for this key
        $alreadyPending = EmployeeAttachment::where('user_id', $employee->id)
            ->where('attachment_key', $key)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return back()->with('error', 'You already have a pending upload for this document. Wait for HR to review it.');
        }

        HRAttachmentController::saveAttachment($request->file('file'), $employee, $key, 'employee');

        return back()->with('success', 'Document submitted. HR will review it shortly.');
    }
}