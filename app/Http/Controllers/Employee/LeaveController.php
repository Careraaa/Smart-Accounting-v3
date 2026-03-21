<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Notifications\LeaveNotification;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    /**
     * Display a listing of the user's leave requests.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $query = Leave::where('user_id', $userId)->with('employee', 'approvedBy');

        // Filter by status
        $status = $request->status ?? 'all';
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Sort by start_date descending (recent first)
        $query->orderBy('start_date', 'desc');

        $leaves = $query->paginate(10);

        // Statistics for the user's leaves
        $totalLeaves = Leave::where('user_id', $userId)->count();
        $pendingLeaves = Leave::where('user_id', $userId)->where('status', 'pending')->count();
        $approvedLeaves = Leave::where('user_id', $userId)->where('status', 'approved')->count();
        $rejectedLeaves = Leave::where('user_id', $userId)->where('status', 'rejected')->count();

        return view('employee.leaves.index', compact(
            'leaves',
            'totalLeaves',
            'pendingLeaves',
            'approvedLeaves',
            'rejectedLeaves',
            'status'
        ));
    }

    /**
     * Show the form for creating a new leave request.
     */
    public function create()
    {
        $leaveTypes = LeaveType::where('status', 'active')->pluck('name');

        return view('employee.leaves.create', compact('leaveTypes'));
    }

    /**
     * Store a newly created leave request in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        // Add the authenticated user's ID
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        $leave = Leave::create($validated);
        $leave->load('employee');

        // Send notifications to managers/HR
        LeaveNotification::leaveSubmitted($leave);
        LeaveNotification::notifyManagersOfNewRequest($leave);

        return redirect()->route('employee.leaves.index')->with('success', 'Leave request submitted successfully. Awaiting approval.');
    }

    /**
     * Display the specified leave request.
     */
    public function show(Leave $leave)
    {
        // Check if the leave belongs to the authenticated user
        if ($leave->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this leave request.');
        }

        $leave->load('employee', 'approvedBy');

        return view('employee.leaves.show', compact('leave'));
    }

    /**
     * Show the form for editing the specified leave request.
     */
    public function edit(Leave $leave)
    {
        // Check if the leave belongs to the authenticated user and can be edited
        if ($leave->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this leave request.');
        }

        if ($leave->status !== 'pending') {
            abort(403, 'Only pending leave requests can be edited.');
        }

        $leaveTypes = LeaveType::where('status', 'active')->pluck('name');

        return view('employee.leaves.edit', compact('leave', 'leaveTypes'));
    }

    /**
     * Update the specified leave request in storage.
     */
    public function update(Request $request, Leave $leave)
    {
        // Check if the leave belongs to the authenticated user and can be edited
        if ($leave->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this leave request.');
        }

        if ($leave->status !== 'pending') {
            abort(403, 'Only pending leave requests can be edited.');
        }

        $validated = $request->validate([
            'leave_type' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        $leave->update($validated);
        $leave->load('employee');

        // Send notification about the update
        LeaveNotification::leaveUpdated($leave);

        return redirect()->route('employee.leaves.show', $leave->id)->with('success', 'Leave request updated successfully.');
    }

    /**
     * Remove the specified leave request from storage.
     */
    public function destroy(Leave $leave)
    {
        // Check if the leave belongs to the authenticated user and can be deleted
        if ($leave->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this leave request.');
        }

        if ($leave->status !== 'pending') {
            abort(403, 'Only pending leave requests can be cancelled.');
        }

        $leave->load('employee');

        // Send notification about the cancellation
        LeaveNotification::leaveDeleted($leave);

        $leave->delete();

        return redirect()->route('employee.leaves.index')->with('success', 'Leave request cancelled successfully.');
    }
}
