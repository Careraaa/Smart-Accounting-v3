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

        $leaves = collect();
        $thisWeekLeaves = 0;
        $thisMonthLeaves = 0;

        if (in_array($tab, ['pending', 'approved', 'rejected'])) {
            $query = Leave::with('employee', 'approvedBy');

            if ($tab === 'pending') {
                $query->where('status', 'pending');
            } elseif ($tab === 'approved') {
                $query->whereIn('status', ['approved', 'paid']);
            } elseif ($tab === 'rejected') {
                $query->where('status', 'rejected');
            }

            $leaves = $query->orderBy('created_at', 'desc')->paginate(10)->appends(['tab' => $tab]);

            $timeColumn = $tab === 'pending' ? 'created_at' : 'updated_at';

            $baseCount = Leave::query();
            if ($tab === 'pending') {
                $baseCount->where('status', 'pending');
            } elseif ($tab === 'approved') {
                $baseCount->whereIn('status', ['approved', 'paid']);
            } elseif ($tab === 'rejected') {
                $baseCount->where('status', 'rejected');
            }

            $thisWeekLeaves = (clone $baseCount)->whereBetween($timeColumn, [$thisWeekStart, $thisWeekEnd])->count();
            $thisMonthLeaves = (clone $baseCount)->whereBetween($timeColumn, [$thisMonthStart, $thisMonthEnd])->count();
        }

        return view('hr.leave.index', compact(
            'tab',
            'leaveTypes',
            'totalTypes',
            'activeTypes',
            'inactiveTypes',
            'leaves',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'departments',
            'leaveTypeNames',
            'thisWeekLeaves',
            'thisMonthLeaves'
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
