<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Employee;
use App\Notifications\LeaveNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with('employee', 'approvedBy');

        // Search by employee name or ID
        if ($request->search) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by leave type
        if ($request->leave_type && $request->leave_type !== 'all') {
            $query->where('leave_type', $request->leave_type);
        }

        // Sort options
        $sortBy = $request->sort_by ?? 'start_date';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $leaves = $query->paginate(10);

        // Statistics
        $totalLeaves = Leave::count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $approvedLeaves = Leave::where('status', 'approved')->count();
        $rejectedLeaves = Leave::where('status', 'rejected')->count();

        // Leave types count
        $leaveTypeStats = DB::table('leaves')
            ->select('leave_type', DB::raw('count(*) as count'))
            ->groupBy('leave_type')
            ->get();

        return view('hr.leave.index', compact(
            'leaves',
            'totalLeaves',
            'pendingLeaves',
            'approvedLeaves',
            'rejectedLeaves',
            'leaveTypeStats',
            'sortBy',
            'sortOrder'
        ));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->where('role', '!=', 'superadmin')->get();
        $leaveTypes = ['Sick Leave', 'Vacation', 'Personal Leave', 'Maternity Leave', 'Paternity Leave', 'Other'];

        return view('hr.leave.create', compact('employees', 'leaveTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $leave = Leave::create($validated);
        $leave->load('employee');

        // Send notifications
        LeaveNotification::leaveSubmitted($leave);
        LeaveNotification::notifyManagersOfNewRequest($leave);

        return redirect()->route('leave.index')->with('success', 'Leave request created successfully.');
    }

    public function show(Leave $leave)
    {
        $leave->load('employee', 'approvedBy');

        return view('hr.leave.show', compact('leave'));
    }

    public function edit(Leave $leave)
    {
        $employees = Employee::where('status', 'active')->where('role', '!=', 'superadmin')->get();
        $leaveTypes = ['Sick Leave', 'Vacation', 'Personal Leave', 'Maternity Leave', 'Paternity Leave', 'Other'];

        return view('hr.leave.edit', compact('leave', 'employees', 'leaveTypes'));
    }

    public function update(Request $request, Leave $leave)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $leave->update($validated);
        $leave->load('employee');

        // Send notification
        LeaveNotification::leaveUpdated($leave);

        return redirect()->route('leave.show', $leave->id)->with('success', 'Leave request updated successfully.');
    }

    public function destroy(Leave $leave)
    {
        $leave->load('employee');

        // Send notification
        LeaveNotification::leaveDeleted($leave);

        $leave->delete();

        return redirect()->route('leave.index')->with('success', 'Leave request deleted successfully.');
    }

    public function approve(Request $request, Leave $leave)
    {
        $leave->update([
            'status' => 'approved',
            'approved_by' => auth()->user()->employee->id ?? null,
        ]);
        $leave->load('employee');

        // Send notification
        LeaveNotification::leaveApproved($leave);

        return redirect()->back()->with('success', 'Leave request approved successfully.');
    }

    public function reject(Request $request, Leave $leave)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string',
        ]);

        $leave->update([
            'status' => 'rejected',
            'approved_by' => auth()->user()->employee->id ?? null,
        ]);
        $leave->load('employee');

        // Send notification
        LeaveNotification::leaveRejected($leave);

        return redirect()->back()->with('success', 'Leave request rejected successfully.');
    }
}
