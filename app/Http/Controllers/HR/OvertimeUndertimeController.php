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

        if ($request->search) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->type && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->start_date) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $sortBy = $request->sort_by ?? 'date';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $overtimeRecords = $query->paginate(10);

        // Statistics (kept as-is for now)
        $totalRecords = OvertimeUndertime::count();
        $pendingRecords = OvertimeUndertime::where('status', 'pending')->count();
        $approvedRecords = OvertimeUndertime::where('status', 'approved')->count();
        $rejectedRecords = OvertimeUndertime::where('status', 'rejected')->count();

        $overtimeCount = OvertimeUndertime::where('type', 'overtime')->count();
        $undertimeCount = OvertimeUndertime::where('type', 'undertime')->count();

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
        $employees = Employee::where('status', 'active')->whereNotIn('role', ['superadmin', 'qr_admin'])->get();
        $types = ['overtime', 'undertime'];

        return view('hr.overtime.create', compact('employees', 'types'));
    }

    // ==================== MAIN CHANGE ====================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'type' => 'required|in:overtime,undertime',
            'hours' => 'required|numeric|min:0.5|max:24',
            'reason' => 'required|string',
        ]);

        $employee = Employee::findOrFail($validated['user_id']);

        // Calculate hourly rate from daily salary_rate
        $hourlyRate = $employee->salary_rate / 8;

        // Compute amount
        $amount = $validated['type'] === 'overtime'
            ? $validated['hours'] * $hourlyRate
            : -($validated['hours'] * $hourlyRate);

        $overtime = OvertimeUndertime::create([
            'user_id'          => $validated['user_id'],
            'date'             => $validated['date'],
            'type'             => $validated['type'],
            'hours'            => $validated['hours'],
            'reason'           => $validated['reason'],
            'status'           => 'pending',           // default
            'amount'           => $amount,
            'hourly_rate_used' => $hourlyRate,
        ]);

        $overtime->load('employee');

        // Notifications
        OvertimeNotification::submitted($overtime);
        OvertimeNotification::notifyManagersForApproval($overtime);

        return redirect()->route('overtime.index')
            ->with('success', 'Overtime/Undertime record created successfully.');
    }

    public function show(OvertimeUndertime $overtime)
    {
        $overtime->load('employee');
        return view('hr.overtime.show', compact('overtime'));
    }

    public function edit(OvertimeUndertime $overtime)
    {
        $employees = Employee::where('status', 'active')->whereNotIn('role', ['superadmin', 'qr_admin'])->get();
        $types = ['overtime', 'undertime'];

        return view('hr.overtime.edit', compact('overtime', 'employees', 'types'));
    }

    // ==================== MAIN CHANGE ====================
    public function update(Request $request, OvertimeUndertime $overtime)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'type' => 'required|in:overtime,undertime',
            'hours' => 'required|numeric|min:0.5|max:24',
            'reason' => 'required|string',
        ]);

        $employee = Employee::findOrFail($validated['user_id']);

        $hourlyRate = $employee->salary_rate / 8;

        $amount = $validated['type'] === 'overtime'
            ? $validated['hours'] * $hourlyRate
            : -($validated['hours'] * $hourlyRate);

        $overtime->update([
            'user_id'          => $validated['user_id'],
            'date'             => $validated['date'],
            'type'             => $validated['type'],
            'hours'            => $validated['hours'],
            'reason'           => $validated['reason'],
            'amount'           => $amount,
            'hourly_rate_used' => $hourlyRate,
        ]);

        $overtime->load('employee');

        OvertimeNotification::updated($overtime);

        return redirect()->route('overtime.show', $overtime->id)
            ->with('success', 'Overtime/Undertime record updated successfully.');
    }

    // approve / reject / destroy methods remain the same
    public function approve(Request $request, OvertimeUndertime $overtime)
    {
        $overtime->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);
        $overtime->load('employee');
        OvertimeNotification::approved($overtime);

        return redirect()->back()->with('success', 'Record approved successfully.');
    }

    public function reject(Request $request, OvertimeUndertime $overtime)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $overtime->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => auth()->id(),
        ]);
        $overtime->load('employee');
        OvertimeNotification::rejected($overtime);

        return redirect()->back()->with('success', 'Record rejected successfully.');
    }

    public function destroy(OvertimeUndertime $overtime)
    {
        $overtime->load('employee');
        OvertimeNotification::deleted($overtime);
        $overtime->delete();

        return redirect()->route('overtime.index')->with('success', 'Overtime/Undertime record deleted successfully.');
    }
}