<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\OvertimeUndertime;
use App\Models\Employee;
use App\Notifications\OvertimeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OvertimeUndertimeController extends Controller
{
    public function index(Request $request)
    {
        $query = OvertimeUndertime::with('employee');

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

        // Filter by type (overtime/undertime)
        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->start_date) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // Sort options
        $sortBy = $request->sort_by ?? 'date';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $overtimeRecords = $query->paginate(10);

        // Statistics
        $totalRecords = OvertimeUndertime::count();
        $pendingRecords = OvertimeUndertime::where('status', 'pending')->count();
        $approvedRecords = OvertimeUndertime::where('status', 'approved')->count();
        $rejectedRecords = OvertimeUndertime::where('status', 'rejected')->count();

        // Type statistics (Overtime vs Undertime)
        $overtimeCount = OvertimeUndertime::where('type', 'overtime')->count();
        $undertimeCount = OvertimeUndertime::where('type', 'undertime')->count();

        // Total hours by type
        $totalOvertimeHours = OvertimeUndertime::where('type', 'overtime')->sum('hours');
        $totalUndertimeHours = OvertimeUndertime::where('type', 'undertime')->sum('hours');

        return view('hr.overtime.index', compact(
            'overtimeRecords',
            'totalRecords',
            'pendingRecords',
            'approvedRecords',
            'rejectedRecords',
            'overtimeCount',
            'undertimeCount',
            'totalOvertimeHours',
            'totalUndertimeHours',
            'sortBy',
            'sortOrder'
        ));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->where('role', '!=', 'superadmin')->get();
        $types = ['overtime', 'undertime'];

        return view('hr.overtime.create', compact('employees', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'type' => 'required|in:overtime,undertime',
            'hours' => 'required|numeric|min:0.5|max:24',
            'reason' => 'required|string',
        ]);

        $overtime = OvertimeUndertime::create($validated);
        $overtime->load('employee');

        // Send notifications
        OvertimeNotification::submitted($overtime);
        OvertimeNotification::notifyManagersForApproval($overtime);

        return redirect()->route('overtime.index')->with('success', 'Overtime/Undertime record created successfully.');
    }

    public function show(OvertimeUndertime $overtime)
    {
        $overtime->load('employee');

        return view('hr.overtime.show', compact('overtime'));
    }

    public function edit(OvertimeUndertime $overtime)
    {
        $employees = Employee::where('status', 'active')->where('role', '!=', 'superadmin')->get();
        $types = ['overtime', 'undertime'];

        return view('hr.overtime.edit', compact('overtime', 'employees', 'types'));
    }

    public function update(Request $request, OvertimeUndertime $overtime)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'type' => 'required|in:overtime,undertime',
            'hours' => 'required|numeric|min:0.5|max:24',
            'reason' => 'required|string',
        ]);

        $overtime->update($validated);
        $overtime->load('employee');

        // Send notification
        OvertimeNotification::updated($overtime);

        return redirect()->route('overtime.show', $overtime->id)->with('success', 'Overtime/Undertime record updated successfully.');
    }

    public function destroy(OvertimeUndertime $overtime)
    {
        $overtime->load('employee');

        // Send notification
        OvertimeNotification::deleted($overtime);

        $overtime->delete();

        return redirect()->route('overtime.index')->with('success', 'Overtime/Undertime record deleted successfully.');
    }

    public function approve(Request $request, OvertimeUndertime $overtime)
    {
        $overtime->update([
            'status' => 'approved',
        ]);
        $overtime->load('employee');

        // Send notification
        OvertimeNotification::approved($overtime);

        return redirect()->back()->with('success', 'Record approved successfully.');
    }

    public function reject(Request $request, OvertimeUndertime $overtime)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string',
        ]);

        $overtime->update([
            'status' => 'rejected',
        ]);
        $overtime->load('employee');

        // Send notification
        OvertimeNotification::rejected($overtime);

        return redirect()->back()->with('success', 'Record rejected successfully.');
    }
}
