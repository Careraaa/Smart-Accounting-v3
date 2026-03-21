<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = LeaveType::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $leaveTypes = $query->orderBy('name', 'asc')->paginate(10);

        // Get statistics
        $totalTypes = LeaveType::count();
        $activeTypes = LeaveType::where('status', 'active')->count();
        $inactiveTypes = LeaveType::where('status', 'inactive')->count();

        return view('hr.leavetype.index', compact(
            'leaveTypes',
            'totalTypes',
            'activeTypes',
            'inactiveTypes',
            'status'
        ));
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

        LeaveType::create($validated);

        return redirect()->route('leave-type.index')->with('success', 'Leave type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveType $leaveType)
    {
        return view('hr.leavetype.show', compact('leaveType'));
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

        return redirect()->route('leave-type.index')->with('success', 'Leave type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();

        return redirect()->route('leave-type.index')->with('success', 'Leave type deleted successfully.');
    }
}
