<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveType;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    use LogsUserActivity;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return redirect()->route('leave.index', ['tab' => 'types']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hr.leavetype.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:leave_types,name',
            'abbreviation' => 'nullable|string|max:5|unique:leave_types,abbreviation',
            'days_allowed' => 'required|integer|min:0',
            'carry_over' => 'nullable|boolean',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $leaveType = LeaveType::create($validated);

        $this->logActivity('created', "Leave type: {$leaveType->name}", request()->url(), 'leave_type', $leaveType->id);

        return redirect()->route('leave-type.index')->with('success', 'Leave type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveType $leaveType)
    {
        return redirect()->route('leave.index', ['tab' => 'types']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveType $leaveType)
    {
        return view('hr.leavetype.edit', compact('leaveType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeaveType $leaveType)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:leave_types,name,' . $leaveType->id,
            'abbreviation' => 'nullable|string|max:5|unique:leave_types,abbreviation,' . $leaveType->id,
            'days_allowed' => 'required|integer|min:0',
            'carry_over' => 'nullable|boolean',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $leaveType->update($validated);

        // Sync total_days on all existing employee balances for this leave type
        if ($leaveType->wasChanged('days_allowed')) {
            EmployeeLeaveBalance::where('leave_type_id', $leaveType->id)
                ->update(['total_days' => $leaveType->days_allowed]);
        }

        $this->logActivity('updated', "Leave type: {$leaveType->name}", request()->url(), 'leave_type', $leaveType->id);

        return redirect()->route('leave-type.index')->with('success', 'Leave type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveType $leaveType)
    {
        $this->logActivity('deleted', "Leave type: {$leaveType->name}", request()->url(), 'leave_type', $leaveType->id);
        $leaveType->delete();

        return redirect()->route('leave-type.index')->with('success', 'Leave type deleted successfully.');
    }
}
