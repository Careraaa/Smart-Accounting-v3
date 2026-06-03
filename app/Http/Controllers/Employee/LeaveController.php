<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\LeaveType;
use App\Models\EmployeeLeaveBalance;
use App\Notifications\LeaveNotification;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    use LogsUserActivity;
    /**
     * Display a listing of the user's leave requests.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $query = Leave::where('user_id', $userId)->with('employee', 'approvedBy', 'leaveType');

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

        // Ensure current-year leave balances exist for all active leave types
        $activeLeaveTypes = LeaveType::where('status', 'active')->get();
        $balances = $activeLeaveTypes->map(function ($leaveType) use ($userId) {
            return EmployeeLeaveBalance::getBalance($userId, $leaveType->id);
        })->load('leaveType');

        $this->logActivity('viewed', 'Leave', request()->url());

        return view('employee.leaves.index', compact(
            'leaves',
            'totalLeaves',
            'pendingLeaves',
            'approvedLeaves',
            'rejectedLeaves',
            'status',
            'balances'
        ));
    }

    /**
     * Show the form for creating a new leave request.
     */
    public function create()
    {
        $userId = auth()->id();
        $currentYear = now()->year;

        // Get active leave types with their current balances
        $leaveTypes = LeaveType::where('status', 'active')->get()->map(function ($type) use ($userId, $currentYear) {
            $balance = EmployeeLeaveBalance::where('user_id', $userId)
                ->where('leave_type_id', $type->id)
                ->where('year', $currentYear)
                ->first();

            if (!$balance) {
                $balance = new EmployeeLeaveBalance([
                    'total_days' => $type->days_allowed,
                    'used_days' => 0,
                    'remaining_days' => $type->days_allowed,
                    'year' => $currentYear,
                ]);
            }

            $type->balance = $balance;
            return $type;
        });

        return view('employee.leaves.create', compact('leaveTypes'));
    }

    /**
     * Store a newly created leave request in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        $userId = auth()->id();
        $leaveTypeId = $validated['leave_type_id'];

        // Calculate the number of days
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $days = $endDate->diffInDays($startDate) + 1;

        // Check if employee has sufficient balance
        $balance = EmployeeLeaveBalance::getBalance($userId, $leaveTypeId);
        if ($balance->remaining_days < $days) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'leave_type_id' => "Insufficient balance. You have {$balance->remaining_days} days remaining for this leave type."
                ]);
        }

        // Create the leave request
        $validated['user_id'] = $userId;
        $validated['status'] = 'pending';
        $leave = Leave::create($validated);
        $leave->load('employee', 'leaveType');

        // Send notifications to managers/HR
        LeaveNotification::leaveSubmitted($leave);
        LeaveNotification::notifyManagersOfNewRequest($leave);

        $this->logActivity('submitted', 'Leave ' . $leave->id, request()->url(), 'leave', $leave->id);

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

        $leave->load('employee', 'approvedBy', 'leaveType');

        $this->logActivity('viewed', 'Leave ' . $leave->id, request()->url(), 'leave', $leave->id);

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

        $leaveTypes = LeaveType::where('status', 'active')->pluck('name', 'id');

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
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        // Calculate the new number of days
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $newDays = $endDate->diffInDays($startDate) + 1;

        // Calculate the old number of days for comparison
        $oldDays = $leave->end_date->diffInDays($leave->start_date) + 1;
        $daysDifference = $newDays - $oldDays;

        // If changing leave type or dates, validate balance
        if ($daysDifference > 0 || $leave->leave_type_id !== $validated['leave_type_id']) {
            $balance = EmployeeLeaveBalance::getBalance(auth()->id(), $validated['leave_type_id']);
            
            if ($daysDifference > 0 && $balance->remaining_days < $daysDifference) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'leave_type_id' => "Insufficient balance for update. You have {$balance->remaining_days} days remaining."
                    ]);
            }
        }

        $leave->update($validated);
        $leave->load('employee', 'leaveType');

        // Send notification about the update
        LeaveNotification::leaveUpdated($leave);

        $this->logActivity('updated', 'Leave ' . $leave->id, request()->url(), 'leave', $leave->id);

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

        $this->logActivity('deleted', 'Leave ' . $leave->id, request()->url(), 'leave', $leave->id);

        $leave->delete();

        return redirect()->route('employee.leaves.index')->with('success', 'Leave request cancelled successfully.');
    }
}
