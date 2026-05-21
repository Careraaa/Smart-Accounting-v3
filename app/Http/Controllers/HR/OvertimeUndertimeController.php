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
    /**
     * List all pending OT/UT requests submitted by employees, with approve/reject actions.
     */
    public function pendingRequests(Request $request)
    {
        $status = $request->query('status', 'pending');
        $type   = $request->query('type', 'all');

        $query = OvertimeUndertime::with('employee')
            ->whereHas('employee', fn($q) => $q->whereNotIn('role', ['superadmin', 'qr_admin']));

        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $pendingCount  = OvertimeUndertime::where('status', 'pending')->count();
        $approvedCount = OvertimeUndertime::where('status', 'approved')->count();
        $rejectedCount = OvertimeUndertime::where('status', 'rejected')->count();

        return view('hr.overtime.pending', compact(
            'requests', 'status', 'type',
            'pendingCount', 'approvedCount', 'rejectedCount'
        ));
    }

    public function index(Request $request)
    {
        // All employees (excluding system roles), ordered by department then name
        $allEmployees = Employee::whereNotIn('role', ['superadmin', 'qr_admin'])
            ->orderBy('department')
            ->orderBy('last_name')
            ->get();

        // All unique departments
        $departments = $allEmployees->pluck('department')->filter()->unique()->sort()->values();

        // Per-employee OT/UT summary for the current month (for all employees)
        $monthStart = now()->startOfMonth();
        $monthEnd   = now()->endOfMonth();

        $monthlySummary = OvertimeUndertime::whereBetween('date', [$monthStart, $monthEnd])
            ->where('status', 'approved')
            ->whereIn('user_id', $allEmployees->pluck('id'))
            ->selectRaw('user_id,
                SUM(CASE WHEN type = "overtime"  THEN hours ELSE 0 END) as ot_hours,
                SUM(CASE WHEN type = "undertime" THEN hours ELSE 0 END) as ut_hours,
                COUNT(*) as total_records')
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        // Global stats (approved only)
        $totalOvertimeHours  = OvertimeUndertime::where('status', 'approved')->where('type', 'overtime')->sum('hours');
        $totalUndertimeHours = OvertimeUndertime::where('status', 'approved')->where('type', 'undertime')->sum('hours');
        $overtimeCount       = OvertimeUndertime::where('status', 'approved')->where('type', 'overtime')->count();
        $undertimeCount      = OvertimeUndertime::where('status', 'approved')->where('type', 'undertime')->count();

        return view('hr.overtime.index', compact(
            'allEmployees',
            'departments',
            'monthlySummary',
            'totalOvertimeHours',
            'totalUndertimeHours',
            'overtimeCount',
            'undertimeCount'
        ));
    }

    /**
     * Per-employee OT/UT calendar for a given month.
     */
    public function employeeCalendar(Request $request, $employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        $monthParam = $request->query('month');
        $month = $monthParam
            ? \Carbon\Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth()
            : now()->startOfMonth();

        $records = OvertimeUndertime::where('user_id', $employeeId)
            ->where('status', 'approved')
            ->whereBetween('date', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
            ->orderBy('date')
            ->get()
            ->groupBy(fn($r) => $r->date->format('Y-m-d'));

        $prevMonth = $month->copy()->subMonth()->format('Y-m');
        $nextMonth = $month->copy()->addMonth()->format('Y-m');

        // Month totals
        $monthOtHours = $records->flatten()->where('type', 'overtime')->sum('hours');
        $monthUtHours = $records->flatten()->where('type', 'undertime')->sum('hours');

        return view('hr.overtime.calendar', compact(
            'employee', 'month', 'records', 'prevMonth', 'nextMonth',
            'monthOtHours', 'monthUtHours'
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
            ? $validated['hours'] * $hourlyRate * 1.25
            : -($validated['hours'] * $hourlyRate);

        $overtime = OvertimeUndertime::create([
            'user_id'          => $validated['user_id'],
            'date'             => $validated['date'],
            'type'             => $validated['type'],
            'hours'            => $validated['hours'],
            'reason'           => $validated['reason'],
            'status'           => 'approved',
            'approved_by'      => auth()->id(),
            'amount'           => $amount,
            'hourly_rate_used' => $hourlyRate,
        ]);

        $overtime->load('employee');

        // Notify the employee their record was logged and approved
        OvertimeNotification::submitted($overtime);

        return redirect()->route('overtime.index')
            ->with('success', 'Overtime/Undertime record created and automatically approved.');
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
            ? $validated['hours'] * $hourlyRate * 1.25
            : -($validated['hours'] * $hourlyRate);

        $overtime->update([
            'user_id'          => $validated['user_id'],
            'date'             => $validated['date'],
            'type'             => $validated['type'],
            'hours'            => $validated['hours'],
            'reason'           => $validated['reason'],
            'status'           => 'approved',
            'approved_by'      => auth()->id(),
            'amount'           => $amount,
            'hourly_rate_used' => $hourlyRate,
        ]);

        $overtime->load('employee');

        OvertimeNotification::updated($overtime);

        return redirect()->route('overtime.show', $overtime->id)
            ->with('success', 'Overtime/Undertime record updated and approved.');
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