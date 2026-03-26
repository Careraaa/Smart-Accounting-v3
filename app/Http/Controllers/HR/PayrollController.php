<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\User;
use App\Models\CashAdvance;
use App\Models\SalaryLoan;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\OvertimeUndertime;
use App\Services\AttendanceService;
use App\Notifications\PayrollNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    // ====================== INDEX ======================
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'payroll_period_start');
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedColumns = ['payroll_period_start', 'gross_pay', 'net_pay', 'status'];
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'payroll_period_start';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $payrolls = Payroll::with(['user', 'allowances', 'deductions'])
            ->orderBy($sortBy, $sortOrder)
            ->get();

        $totalEmployees = User::where('role', 'employee')->count();
        $activeEmployees = User::where('role', 'employee')->where('status', 'active')->count();
        $inactiveEmployees = $totalEmployees - $activeEmployees;

        $presentToday = Attendance::whereDate('date', now())->where('status', 'present')->count();
        $absentToday = Attendance::whereDate('date', now())->where('status', 'absent')->count();
        $lateToday = Attendance::whereDate('date', now())->where('status', 'late')->count();

        $onLeaveEmployees = Leave::where('status', 'approved')->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->count();

        $totalLeaves = Leave::count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $approvedLeaves = Leave::where('status', 'approved')->count();

        $attendanceRate = $totalEmployees > 0 ? ($presentToday / $totalEmployees) * 100 : 0;

        return view('hr.payroll.salary-computation.index', compact('payrolls', 'totalEmployees', 'activeEmployees', 'inactiveEmployees', 'presentToday', 'absentToday', 'lateToday', 'onLeaveEmployees', 'pendingLeaves', 'approvedLeaves', 'totalLeaves', 'attendanceRate', 'sortBy', 'sortOrder'));
    }

    // ====================== CREATE ======================
    public function create()
    {
        $employees = User::where('role', 'employee')->where('status', 'active')->get();
        return view('hr.payroll.salary-computation.create', compact('employees'));
    }

    // ====================== PREVIEW (FIXED) ======================
    public function preview(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        $employee = User::findOrFail($request->user_id);
        $periodStart = Carbon::parse($request->period_start);
        $periodEnd = Carbon::parse($request->period_end);

        // ✅ SINGLE SOURCE OF TRUTH
        $values = app(\App\Services\PayrollService::class)->computePayroll($employee, $periodStart, $periodEnd);

        return response()->json([
            'days_worked' => $values['daysWorked'],
            'hours_worked' => round($values['hoursWorked'], 2),
            'basic_salary' => round($values['basicSalary'], 2),
            'daily_rate' => round($values['dailyRate'], 2),

            'overtime_pay' => round($values['otPay'], 2),
            'undertime_deduction' => round($values['utDeduction'], 2),

            'sss' => round($values['sss'], 2),
            'pagibig' => round($values['pagibig'], 2),

            'gross_pay' => round($values['grossPay'], 2),
            'total_deductions' => round($values['totalDeductions'], 2),
            'net_pay' => round($values['netPay'], 2),
        ]);
    }

    // ====================== STORE ======================
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'payroll_period_start' => 'required|date',
            'payroll_period_end' => 'required|date|after_or_equal:payroll_period_start',
        ]);

        $employee = User::findOrFail($validated['user_id']);
        $periodStart = Carbon::parse($validated['payroll_period_start']);
        $periodEnd = Carbon::parse($validated['payroll_period_end']);

        if (in_array($employee->role, ['superadmin', 'qr_admin'])) {
            return back()->withErrors(['user_id' => 'Invalid employee']);
        }

        $payroll = app(\App\Services\PayrollService::class)->generatePayrollForEmployee($employee, $periodStart, $periodEnd);

        \App\Services\PayrollDeductionService::applyLoanDeductions($payroll);

        PayrollNotification::payrollCreated($payroll);

        return redirect()->route('payroll.salary-computation.index')->with('success', 'Payroll created successfully.');
    }

    // ====================== UPDATE ======================
    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'payroll_period_start' => 'required|date',
            'payroll_period_end' => 'required|date|after_or_equal:payroll_period_start',
        ]);

        $employee = User::findOrFail($validated['user_id']);
        $periodStart = Carbon::parse($validated['payroll_period_start']);
        $periodEnd = Carbon::parse($validated['payroll_period_end']);

        $payroll = app(\App\Services\PayrollService::class)->updatePayroll($payroll, $employee, $periodStart, $periodEnd);

        \App\Services\PayrollDeductionService::applyLoanDeductions($payroll);

        PayrollNotification::payrollUpdated($payroll);

        return redirect()->route('payroll.salary-computation.index')->with('success', 'Payroll updated successfully.');
    }

    // ====================== SHOW ======================
    public function show(Payroll $payroll)
    {
        $payroll->load(['user', 'allowances', 'deductions']);

        $overtimeUndertimeBreakdown = OvertimeUndertime::where('user_id', $payroll->user_id)
            ->whereBetween('date', [$payroll->payroll_period_start, $payroll->payroll_period_end])
            ->where('status', 'approved')
            ->orderBy('date')
            ->get();

        return view('hr.payroll.salary-computation.show', compact('payroll', 'overtimeUndertimeBreakdown'));
    }

    // ====================== EDIT ======================
    public function edit(Payroll $payroll)
    {
        $employees = User::where('role', 'employee')->where('status', 'active')->get();
        $payroll->load(['user', 'allowances', 'deductions']);

        return view('hr.payroll.salary-computation.edit', compact('payroll', 'employees'));
    }

    // ====================== DELETE ======================
    public function destroy(Payroll $payroll)
    {
        $payroll->delete();

        return redirect()->route('payroll.salary-computation.index')->with('success', 'Payroll deleted successfully.');
    }
}
