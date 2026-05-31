<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\EmployeeLeaveBalance;
use App\Notifications\LeaveNotification;
use App\Services\LeaveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    protected $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }
    public function index(Request $request)
    {
        $tab = $request->get('tab', $request->route()->parameter('status', 'types'));

        $departments = Employee::whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->filter()
            ->all();

        $leaveTypeNames = LeaveType::where('status', 'active')->pluck('name');

        $totalTypes = LeaveType::count();
        $activeTypes = LeaveType::where('status', 'active')->count();
        $inactiveTypes = LeaveType::where('status', 'inactive')->count();

        $pendingCount = Leave::where('status', 'pending')->count();
        $approvedCount = Leave::whereIn('status', ['approved', 'paid'])->count();
        $rejectedCount = Leave::where('status', 'rejected')->count();

        $leaveTypes = LeaveType::orderBy('name')->get();

        $thisWeekStart = now()->startOfWeek();
        $thisWeekEnd = now()->endOfWeek();
        $thisMonthStart = now()->startOfMonth();
        $thisMonthEnd = now()->endOfMonth();

        // Load ALL leave data upfront so tabs can switch client-side
        $pendingLeaves = Leave::with('employee', 'approvedBy')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')->get();

        $approvedLeaves = Leave::with('employee', 'approvedBy')
            ->whereIn('status', ['approved', 'paid'])
            ->orderBy('created_at', 'desc')->get();

        $rejectedLeaves = Leave::with('employee', 'approvedBy')
            ->where('status', 'rejected')
            ->orderBy('created_at', 'desc')->get();

        // Per-tab week/month stats
        $pendingWeekLeaves = Leave::where('status', 'pending')
            ->whereBetween('created_at', [$thisWeekStart, $thisWeekEnd])->count();
        $pendingMonthLeaves = Leave::where('status', 'pending')
            ->whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])->count();

        $approvedWeekLeaves = Leave::whereIn('status', ['approved', 'paid'])
            ->whereBetween('updated_at', [$thisWeekStart, $thisWeekEnd])->count();
        $approvedMonthLeaves = Leave::whereIn('status', ['approved', 'paid'])
            ->whereBetween('updated_at', [$thisMonthStart, $thisMonthEnd])->count();

        $rejectedWeekLeaves = Leave::where('status', 'rejected')
            ->whereBetween('updated_at', [$thisWeekStart, $thisWeekEnd])->count();
        $rejectedMonthLeaves = Leave::where('status', 'rejected')
            ->whereBetween('updated_at', [$thisMonthStart, $thisMonthEnd])->count();

        return view('hr.leave.index', compact(
            'tab',
            'leaveTypes',
            'totalTypes',
            'activeTypes',
            'inactiveTypes',
            'pendingLeaves',
            'approvedLeaves',
            'rejectedLeaves',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'departments',
            'leaveTypeNames',
            'pendingWeekLeaves',
            'pendingMonthLeaves',
            'approvedWeekLeaves',
            'approvedMonthLeaves',
            'rejectedWeekLeaves',
            'rejectedMonthLeaves'
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

        $leaveType = LeaveType::where('name', $validated['leave_type'])->first();
        if ($leaveType) {
            $validated['leave_type_id'] = $leaveType->id;
        }

        $validated['status'] = 'pending';
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

        $leaveType = LeaveType::where('name', $validated['leave_type'])->first();
        if ($leaveType) {
            $validated['leave_type_id'] = $leaveType->id;
        }

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
        // Use LeaveService to approve leave with credit validation and attendance creation
        $result = $this->leaveService->approveLeave($leave, auth()->id() ?? null);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        $leave->load('employee', 'leaveType');

        // Send notification
        LeaveNotification::leaveApproved($leave);

        return redirect()->route('leave.approved')->with('success', $result['message']);
    }

    public function reject(Request $request, Leave $leave)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        // Use LeaveService to reject leave
        $result = $this->leaveService->rejectLeave($leave, auth()->id() ?? null, $request->rejection_reason);

        if (!$result['success']) {
            return redirect()->back()->with('error', $result['message']);
        }

        $leave->load('employee');

        // Send notification
        LeaveNotification::leaveRejected($leave);

        return redirect()->route('leave.pending')->with('success', $result['message']);
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
