<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\EmployeeLeaveBalance;
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
        $status = $request->status ?? 'pending';
        if ($status) {
            $query->where('status', $status);
        }

        // Filter by leave type
        if ($request->leave_type && $request->leave_type !== 'all') {
            $query->where('leave_type', $request->leave_type);
        }

        // Filter by department
        if ($request->department) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        // Sort options
        $sortBy = $request->sort_by ?? 'start_date';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $leaves = $query->paginate(10);

        // Statistics - Overall
        $totalLeaves = Leave::count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $approvedLeaves = Leave::where('status', 'approved')->count();
        $rejectedLeaves = Leave::where('status', 'rejected')->count();

        // Leave types - fetch from leave_types table
        $leaveTypeStats = LeaveType::where('status', 'active')
            ->select('name as leave_type')
            ->get();

        // Get unique departments
        $departments = Employee::whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->filter()
            ->all();

        // Calculate statistics based on current status view
        $thisWeekStart = now()->startOfWeek();
        $thisWeekEnd = now()->endOfWeek();
        $thisMonthStart = now()->startOfMonth();
        $thisMonthEnd = now()->endOfMonth();

        if ($status === 'pending') {
            $thisWeekLeaves = Leave::where('status', 'pending')
                ->whereBetween('created_at', [$thisWeekStart, $thisWeekEnd])
                ->count();
            $thisMonthLeaves = Leave::where('status', 'pending')
                ->whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])
                ->count();
        } elseif ($status === 'approved') {
            $thisWeekLeaves = Leave::where('status', 'approved')
                ->whereBetween('updated_at', [$thisWeekStart, $thisWeekEnd])
                ->count();
            $thisMonthLeaves = Leave::where('status', 'approved')
                ->whereBetween('updated_at', [$thisMonthStart, $thisMonthEnd])
                ->count();
        } elseif ($status === 'rejected') {
            $thisWeekLeaves = Leave::where('status', 'rejected')
                ->whereBetween('updated_at', [$thisWeekStart, $thisWeekEnd])
                ->count();
            $thisMonthLeaves = Leave::where('status', 'rejected')
                ->whereBetween('updated_at', [$thisMonthStart, $thisMonthEnd])
                ->count();
        } else {
            $thisWeekLeaves = Leave::whereBetween('created_at', [$thisWeekStart, $thisWeekEnd])->count();
            $thisMonthLeaves = Leave::whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])->count();
        }

        // Determine which view to render
        $view = match($status) {
            'pending' => 'hr.leave.pending',
            'approved' => 'hr.leave.approved',
            'rejected' => 'hr.leave.rejected',
        };

        return view($view, compact(
            'leaves',
            'totalLeaves',
            'pendingLeaves',
            'approvedLeaves',
            'rejectedLeaves',
            'leaveTypeStats',
            'sortBy',
            'sortOrder',
            'status',
            'thisWeekLeaves',
            'thisMonthLeaves',
            'departments'
        ));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->where('role', '!=', 'superadmin')->get();
        $leaveTypes = LeaveType::where('status', 'active')->pluck('name');

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

        return redirect()->route('leave.pending')->with('success', 'Leave request created successfully.');
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
        $leave->load('employee', 'leaveType');

        // Send notification
        LeaveNotification::leaveDeleted($leave);

        $leave->delete();

        return redirect()->route('leave.pending')->with('success', 'Leave request deleted successfully.');
    }

    public function approve(Request $request, Leave $leave)
    {
        $leave->update([
            'status' => 'approved',
            'approved_by' => auth()->id() ?? null,
        ]);
        $leave->load('employee', 'leaveType');

        // Send notification
        LeaveNotification::leaveApproved($leave);

        return redirect()->route('leave.pending')->with('success', 'Leave request approved successfully.');
    }

    public function reject(Request $request, Leave $leave)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $leave->update([
            'status' => 'rejected',
            'approved_by' => auth()->id() ?? null,
            'rejection_reason' => $request->rejection_reason,
        ]);
        $leave->load('employee');

        // Send notification
        LeaveNotification::leaveRejected($leave);

        return redirect()->route('leave.pending')->with('success', 'Leave request rejected successfully.');
    }
}
