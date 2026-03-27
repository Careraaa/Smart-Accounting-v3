<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\PayrollCutoffSchedule;
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

        // Include all payroll-eligible roles: employee, hr, remittance_clerk, accountant
        // Exclude: superadmin, qr_admin
        $totalEmployees = User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])->count();
        $activeEmployees = User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])->where('status', 'active')->count();
        $inactiveEmployees = $totalEmployees - $activeEmployees;

        $presentToday = Attendance::whereDate('date', now())->where('status', 'present')->count();
        $absentToday = Attendance::whereDate('date', now())->where('status', 'absent')->count();
        $lateToday = Attendance::whereDate('date', now())->where('status', 'late')->count();

        $onLeaveEmployees = Leave::where('status', 'approved')->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->count();

        $totalLeaves = Leave::count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $approvedLeaves = Leave::where('status', 'approved')->count();

        $attendanceRate = $totalEmployees > 0 ? ($presentToday / $totalEmployees) * 100 : 0;

        // Payroll Management Data
        $cutoffSchedules = PayrollCutoffSchedule::where('is_active', true)
            ->orderBy('cutoff_day')
            ->get();

        $cutoffInfo = PayrollCutoffSchedule::getCurrentCutoffPeriod();
        $nextCutoffDate = PayrollCutoffSchedule::getNextCutoffDate();

        $pendingPayrolls = Payroll::with(['user'])
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'rejected')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPayroll = $pendingPayrolls->sum('net_pay');
        $payrollCount = $pendingPayrolls->count();
        $paidCount = Payroll::where('status', 'paid')->count();

        return view('hr.payroll.salary-computation.index', compact(
            'payrolls',
            'totalEmployees',
            'activeEmployees',
            'inactiveEmployees',
            'presentToday',
            'absentToday',
            'lateToday',
            'onLeaveEmployees',
            'pendingLeaves',
            'approvedLeaves',
            'totalLeaves',
            'attendanceRate',
            'sortBy',
            'sortOrder',
            'cutoffSchedules',
            'cutoffInfo',
            'nextCutoffDate',
            'pendingPayrolls',
            'totalPayroll',
            'payrollCount',
            'paidCount'
        ));
    }

    // ====================== CREATE ======================
    public function create()
    {
        $employees = User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])->where('status', 'active')->get();
        return view('hr.payroll.salary-computation.create', compact('employees'));
    }

    // ====================== PREVIEW (FIXED) ======================
    public function preview(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'allowances' => 'array',
            'allowances.*.name' => 'string',
            'allowances.*.amount' => 'numeric',
            'deductions' => 'array',
            'deductions.*.name' => 'string',
            'deductions.*.amount' => 'numeric',
        ]);

        $employee = User::findOrFail($request->user_id);
        $periodStart = Carbon::parse($request->period_start);
        $periodEnd = Carbon::parse($request->period_end);

        $values = app(\App\Services\PayrollService::class)->computePayroll($employee, $periodStart, $periodEnd, $request->allowances ?? [], $request->deductions ?? []);

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
            'allowances' => 'array',
            'allowances.*.name' => 'string',
            'allowances.*.amount' => 'numeric',
            'deductions' => 'array',
            'deductions.*.name' => 'string',
            'deductions.*.amount' => 'numeric',
        ]);

        $employee = User::findOrFail($validated['user_id']);
        $periodStart = Carbon::parse($validated['payroll_period_start']);
        $periodEnd = Carbon::parse($validated['payroll_period_end']);

        $payroll = app(\App\Services\PayrollService::class)->generatePayrollForEmployee($employee, $periodStart, $periodEnd, $validated['allowances'] ?? [], $validated['deductions'] ?? []);

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
            'allowances' => 'array',
            'allowances.*.name' => 'string',
            'allowances.*.amount' => 'numeric',
            'deductions' => 'array',
            'deductions.*.name' => 'string',
            'deductions.*.amount' => 'numeric',
        ]);

        $employee = User::findOrFail($validated['user_id']);
        $periodStart = Carbon::parse($validated['payroll_period_start']);
        $periodEnd = Carbon::parse($validated['payroll_period_end']);

        $payroll = app(\App\Services\PayrollService::class)->updatePayroll($payroll, $employee, $periodStart, $periodEnd, $validated['allowances'] ?? [], $validated['deductions'] ?? []);

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
        $employees = User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])->where('status', 'active')->get();
        $payroll->load(['user', 'allowances', 'deductions']);

        return view('hr.payroll.salary-computation.edit', compact('payroll', 'employees'));
    }

    // ====================== DELETE ======================
    public function destroy(Payroll $payroll)
    {
        $payroll->delete();

        return redirect()->route('payroll.salary-computation.index')->with('success', 'Payroll deleted successfully.');
    }

    // ====================== BATCH GENERATE ======================
    public function generateBatch(Request $request)
    {
        $validated = $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'employees' => 'array',
            'employees.*' => 'exists:users,id',
        ]);

        $periodStart = Carbon::parse($validated['period_start']);
        $periodEnd = Carbon::parse($validated['period_end']);

        // If none selected, get all active employees (including hr, remittance_clerk, accountant)
        // Exclude: superadmin, qr_admin
        $employees = empty($validated['employees']) ? User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])->where('status', 'active')->get() : User::whereIn('id', $validated['employees'])->get();

        $count = app(\App\Services\PayrollService::class)->generateBatch($employees, $periodStart, $periodEnd);

        return redirect()
            ->route('payroll.salary-computation.index')
            ->with('success', "Batch payroll generated for {$count} employees.");
    }

    // ====================== RELEASE PAYROLL ======================
    public function releasePayroll(Request $request)
    {
        $payrolls = Payroll::where('status', 'pending')
            ->orWhere('status', 'draft')
            ->get();

        $count = 0;
        foreach ($payrolls as $payroll) {
            $payroll->update(['status' => 'submitted']);
            $count++;
        }

        return redirect()
            ->route('payroll.salary-computation.index')
            ->with('success', "Released {$count} payroll(s) for processing.");
    }

    // ====================== EXPORT PDF ======================
    public function exportPdf(Request $request)
    {
        $payrolls = Payroll::with(['user', 'allowances', 'deductions'])
            ->where('status', '!=', 'rejected')
            ->get();

        // TODO: Implement PDF export with Barryvdh/DomPDF
        return redirect()
            ->route('payroll.salary-computation.index')
            ->with('info', 'PDF export functionality to be implemented');
    }

    // ====================== GENERATE PAYSLIP ======================
    public function generatePayslip(Payroll $payroll)
    {
        $payroll->load(['user', 'allowances', 'deductions']);

        return view('hr.payroll.generate-payslip.payslip', compact('payroll'));
    }
}
