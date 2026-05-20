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
        $baseQuery = Leave::query()->with('employee', 'approvedBy');

        // Search by employee name or ID
        if ($request->search) {
            $baseQuery->whereHas('employee', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%');
            });
        }

        // Status for table view
        $status = $request->status ?? 'pending';

        // Filter by leave type
        if ($request->leave_type && $request->leave_type !== 'all') {
            $baseQuery->where('leave_type', $request->leave_type);
        }

        // Filter by department
        if ($request->department) {
            $baseQuery->whereHas('employee', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        $query = clone $baseQuery;

        // Filter current table list by status
        if ($status) {
            $query->where('status', $status);
        }

        // Sort options
        $sortBy = $request->sort_by ?? 'start_date';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $leaves = $query->paginate(10)->appends($request->query());

        // Statistics (aligned to current filters: search/department/leave_type)
        $totalLeaves = (clone $baseQuery)->count();
        $pendingLeaves = (clone $baseQuery)->where('status', 'pending')->count();
        $approvedLeaves = (clone $baseQuery)->where('status', 'approved')->count();
        $rejectedLeaves = (clone $baseQuery)->where('status', 'rejected')->count();

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
            // Pending page:
            // - This Week: pending requests created this week
            // - This Month: total requests this month (all statuses), respecting active filters
            $thisWeekLeaves = (clone $baseQuery)
                ->where('status', 'pending')
                ->whereBetween('created_at', [$thisWeekStart, $thisWeekEnd])
                ->count();
            $thisMonthLeaves = (clone $baseQuery)
                ->whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])
                ->count();
        } elseif ($status === 'approved') {
            $thisWeekLeaves = (clone $baseQuery)->where('status', 'approved')
                ->whereBetween('updated_at', [$thisWeekStart, $thisWeekEnd])
                ->count();
            $thisMonthLeaves = (clone $baseQuery)->where('status', 'approved')
                ->whereBetween('updated_at', [$thisMonthStart, $thisMonthEnd])
                ->count();
        } elseif ($status === 'rejected') {
            $thisWeekLeaves = (clone $baseQuery)->where('status', 'rejected')
                ->whereBetween('updated_at', [$thisWeekStart, $thisWeekEnd])
                ->count();
            $thisMonthLeaves = (clone $baseQuery)->where('status', 'rejected')
                ->whereBetween('updated_at', [$thisMonthStart, $thisMonthEnd])
                ->count();
        } else {
            $thisWeekLeaves = (clone $baseQuery)->whereBetween('created_at', [$thisWeekStart, $thisWeekEnd])->count();
            $thisMonthLeaves = (clone $baseQuery)->whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])->count();
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
        $employees = Employee::where('status', 'active')->whereNotIn('role', ['superadmin', 'qr_admin'])->get();
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
        $employees = Employee::where('status', 'active')->whereNotIn('role', ['superadmin', 'qr_admin'])->get();
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

    public function getDetails(Leave $leave)
    {
        $leave->load('employee', 'leaveType');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $leave->id,
                'leave_type_name' => $leave->leave_type,
                'from_date' => $leave->start_date,
                'to_date' => $leave->end_date,
                'number_of_days' => $leave->getDaysAttribute(),
                'status' => $leave->status,
                'reason' => $leave->reason,
                'rejection_reason' => $leave->rejection_reason,
                'employee_name' => $leave->employee?->first_name . ' ' . $leave->employee?->last_name,
                'approved_by' => $leave->approvedBy?->first_name . ' ' . $leave->approvedBy?->last_name,
            ]
        ]);
    }
}
