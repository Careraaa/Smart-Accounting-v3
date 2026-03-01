<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use App\Models\StatutoryDeduction;
use Carbon\Carbon;

class PayrollController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'payroll_period_start');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Whitelist allowed columns to prevent SQL injection
        $allowedColumns = ['payroll_period_start', 'gross_pay', 'net_pay', 'status'];
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'payroll_period_start';
        }
        
        // Validate sort order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }
        
        $payrolls = Payroll::with(['employee', 'allowances', 'deductions'])
            ->orderBy($sortBy, $sortOrder)->get();

        // Exclude superadmin from employee counts
        $totalEmployees = Employee::where('role', '!=', 'superadmin')->count();
        $activeEmployees = Employee::where('role', '!=', 'superadmin')->where('status', 'active')->count();
        $inactiveEmployees = $totalEmployees - $activeEmployees;

        $presentToday = Attendance::whereDate('date', now())->where('status', 'present')->count();
        $absentToday = Attendance::whereDate('date', now())->where('status', 'absent')->count();
        $lateToday = Attendance::whereDate('date', now())->where('status', 'late')->count();

        $onLeaveEmployees = Leave::where('status', 'approved')->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->count();

        $totalLeaves = Leave::count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $approvedLeaves = Leave::where('status', 'approved')->count();

        $attendanceRate = $totalEmployees > 0 ? ($presentToday / $totalEmployees) * 100 : 0;

        $attendanceTrend = Attendance::whereDate('date', '>=', now()->subDays(6))
            ->get()
            ->groupBy(fn($item) => $item->date->format('Y-m-d'))
            ->map(
                fn($group) => [
                    'date' => $group->first()->date->format('M d, Y'),
                    'present' => $group->where('status', 'present')->count(),
                    'absent' => $group->where('status', 'absent')->count(),
                    'late' => $group->where('status', 'late')->count(),
                ],
            )
            ->values();

        return view('hr.payroll.salary-computation.index', compact('payrolls', 'totalEmployees', 'activeEmployees', 'inactiveEmployees', 'presentToday', 'absentToday', 'lateToday', 'onLeaveEmployees', 'pendingLeaves', 'approvedLeaves', 'totalLeaves', 'attendanceRate', 'attendanceTrend', 'sortBy', 'sortOrder'));
    }

    public function create()
    {
        // Exclude superadmin from employee list
        $employees = Employee::where('role', '!=', 'superadmin')->get();
        return view('hr.payroll.salary-computation.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'payroll_period_start' => 'required|date',
            'payroll_period_end' => 'required|date',
            'total_allowances' => 'nullable|numeric|min:0',
            'total_deductions' => 'nullable|numeric|min:0',
            'allowances.*.name' => 'nullable|string',
            'allowances.*.amount' => 'nullable|numeric|min:0',
            'deductions.*.name' => 'nullable|string',
            'deductions.*.amount' => 'nullable|numeric|min:0',
        ]);

        $employee = Employee::find($validated['user_id']);
        $periodStart = Carbon::parse($validated['payroll_period_start']);
        $periodEnd = Carbon::parse($validated['payroll_period_end']);

        // Always calculate basic salary from attendance
        $daysWorked = $this->attendanceService->countWorkDaysInPeriod(
            $validated['user_id'],
            $periodStart,
            $periodEnd
        );
        $hoursWorked = $this->attendanceService->calculateTotalHoursWorked(
            $validated['user_id'],
            $periodStart,
            $periodEnd
        );
        $basicSalary = $this->attendanceService->calculateBasicSalary(
            $employee,
            $periodStart,
            $periodEnd
        );

        $totalAllowances = floatval($validated['total_allowances'] ?? 0);
        $totalDeductions = floatval($validated['total_deductions'] ?? 0);

        $payroll = Payroll::create([
            'user_id' => $validated['user_id'],
            'payroll_period_start' => $periodStart,
            'payroll_period_end' => $periodEnd,
            'total_allowances' => $totalAllowances,
            'total_deductions' => $totalDeductions,
            'basic_salary' => $basicSalary,
            'days_worked' => $daysWorked,
            'hours_worked' => $hoursWorked,
            'status' => 'pending',
        ]);

        foreach ($request->allowances ?? [] as $allowance) {
            if (!empty($allowance['name']) && $allowance['amount'] > 0) {
                $payroll->allowances()->create([
                    'allowance_type' => $allowance['name'],
                    'amount' => $allowance['amount'],
                ]);
            }
        }

        foreach ($request->deductions ?? [] as $deduction) {
            if (!empty($deduction['name']) && $deduction['amount'] > 0) {
                $payroll->deductions()->create([
                    'deduction_type' => $deduction['name'],
                    'amount' => $deduction['amount'],
                ]);
            }
        }

        return redirect()->route('payroll.index')->with('success', 'Payroll created successfully with attendance data.');
    }

    public function show(Payroll $payroll)
    {
        $payroll->load('employee', 'allowances', 'deductions');
        $attendanceSummary = $payroll->attendance_summary;
        $attendanceBreakdown = $payroll->getAttendanceBreakdown();
        
        return view('hr.payroll.salary-computation.show', compact('payroll', 'attendanceSummary', 'attendanceBreakdown'));
    }

    public function edit(Payroll $payroll)
    {
        // Include all employees except superadmin so payrolls linked to inactive employees still show correctly
        $employees = Employee::where('role', '!=', 'superadmin')->get();
        $payroll->load('allowances', 'deductions');
        $attendanceSummary = $payroll->attendance_summary;
        
        return view('hr.payroll.salary-computation.edit', compact('payroll', 'employees', 'attendanceSummary'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'payroll_period_start' => 'required|date',
            'payroll_period_end' => 'required|date',
            'recalculate_from_attendance' => 'nullable|boolean',
            'allowances.*.name' => 'nullable|string',
            'allowances.*.amount' => 'nullable|numeric|min:0',
            'deductions.*.name' => 'nullable|string',
            'deductions.*.amount' => 'nullable|numeric|min:0',
        ]);

        $totalAllowances = 0;
        foreach ($request->allowances ?? [] as $allowance) {
            $totalAllowances += floatval($allowance['amount'] ?? 0);
        }

        $totalDeductions = 0;
        foreach ($request->deductions ?? [] as $deduction) {
            $totalDeductions += floatval($deduction['amount'] ?? 0);
        }

        $periodStart = Carbon::parse($validated['payroll_period_start']);
        $periodEnd = Carbon::parse($validated['payroll_period_end']);

        $updateData = [
            'user_id' => $validated['user_id'],
            'payroll_period_start' => $periodStart,
            'payroll_period_end' => $periodEnd,
            'total_allowances' => $totalAllowances,
            'total_deductions' => $totalDeductions,
            'status' => 'pending',
        ];

        // Recalculate from attendance if requested
        if ($request->boolean('recalculate_from_attendance')) {
            $employee = Employee::find($validated['user_id']);
            $daysWorked = $this->attendanceService->countWorkDaysInPeriod(
                $validated['employee_id'],
                $periodStart,
                $periodEnd
            );
            $hoursWorked = $this->attendanceService->calculateTotalHoursWorked(
                $validated['employee_id'],
                $periodStart,
                $periodEnd
            );
            $basicSalary = $this->attendanceService->calculateBasicSalary(
                $employee,
                $periodStart,
                $periodEnd
            );

            $updateData['basic_salary'] = $basicSalary;
            $updateData['days_worked'] = $daysWorked;
            $updateData['hours_worked'] = $hoursWorked;
        }

        $payroll->update($updateData);

        $payroll->allowances()->delete();
        $payroll->deductions()->delete();

        foreach ($request->allowances ?? [] as $allowance) {
            if (!empty($allowance['name']) && $allowance['amount'] > 0) {
                $payroll->allowances()->create([
                    'allowance_type' => $allowance['name'],
                    'amount' => $allowance['amount'],
                ]);
            }
        }

        foreach ($request->deductions ?? [] as $deduction) {
            if (!empty($deduction['name']) && $deduction['amount'] > 0) {
                $payroll->deductions()->create([
                    'deduction_type' => $deduction['name'],
                    'amount' => $deduction['amount'],
                ]);
            }
        }

        return redirect()->route('payroll.index')->with('success', 'Payroll updated successfully.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->allowances()->delete();
        $payroll->deductions()->delete();
        $payroll->delete();

        return redirect()->route('payroll.index')->with('success', 'Payroll deleted successfully.');
    }

    public function generatePayslip(Payroll $payroll)
    {
        $payroll->load('employee', 'allowances', 'deductions');
        return view('hr.payroll.payslip', compact('payroll'));
    }

    /**
     * Generate payroll for multiple employees for a specific period
     */
    public function generatePayrollBatch(Request $request)
    {
        $validated = $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date',
            'employees' => 'nullable|array',
            'employees.*' => 'exists:employees,id',
        ]);

        $periodStart = Carbon::parse($validated['period_start']);
        $periodEnd = Carbon::parse($validated['period_end']);

        $query = Employee::query();
        if ($validated['employees'] ?? null) {
            $query->whereIn('id', $validated['employees']);
        }

        $employees = $query->get();
        $createdCount = 0;

        foreach ($employees as $employee) {
            // Check if payroll already exists for this period
            $existingPayroll = Payroll::where('user_id', $employee->id)
                ->whereBetween('payroll_period_start', [$periodStart, $periodEnd])
                ->first();

            if ($existingPayroll) {
                continue;
            }

            $daysWorked = $this->attendanceService->countWorkDaysInPeriod(
                $employee->id,
                $periodStart,
                $periodEnd
            );

            $hoursWorked = $this->attendanceService->calculateTotalHoursWorked(
                $employee->id,
                $periodStart,
                $periodEnd
            );

            $basicSalary = $this->attendanceService->calculateBasicSalary(
                $employee,
                $periodStart,
                $periodEnd
            );

            Payroll::create([
                'user_id' => $employee->id,
                'payroll_period_start' => $periodStart,
                'payroll_period_end' => $periodEnd,
                'basic_salary' => $basicSalary,
                'days_worked' => $daysWorked,
                'hours_worked' => $hoursWorked,
                'total_allowances' => 0,
                'total_deductions' => 0,
                'status' => 'pending',
            ]);

            $createdCount++;
        }

        return redirect()->route('payroll.index')
            ->with('success', "Payroll generated for $createdCount employee(s) based on attendance.");
    }

    public function recalculatePayroll(Payroll $payroll)
    {
        $payroll->recalculateFromAttendance();

        return redirect()->route('payroll.show', $payroll)
            ->with('success', 'Payroll recalculated from attendance records.');
    }

    public function computeStatutory(Request $request)
    {
        $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'has_sss' => 'required|boolean',
            'has_pagibig' => 'required|boolean',
        ]);

        $semiMonthlySalary = $request->basic_salary;
        $monthlySalary = $semiMonthlySalary * 2;
        $results = [];

        if ($request->has_sss) {
            $sss = StatutoryDeduction::where('name', 'SSS')->where('min_salary', '<=', $monthlySalary)->where('max_salary', '>=', $monthlySalary)->first();

            if ($sss) {
                $amount = $sss->employee_share ?? $monthlySalary * ($sss->percentage_employee / 100);
                $results[] = ['name' => 'SSS', 'amount' => round($amount / 2, 2)];
            }
        }

        if ($request->has_pagibig) {
            $pagibig = StatutoryDeduction::where('name', 'Pag-IBIG')->where('min_salary', '<=', $monthlySalary)->where('max_salary', '>=', $monthlySalary)->first();

            if ($pagibig) {
                $amount = $pagibig->employee_share ?? $monthlySalary * ($pagibig->percentage_employee / 100);
                $results[] = ['name' => 'Pag-IBIG', 'amount' => round($amount / 2, 2)];
            }
        }

        return response()->json($results);
    }

    /**
     * Get attendance summary for a specific period (API endpoint)
     */
    public function getAttendanceSummary(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date',
        ]);

        $periodStart = Carbon::parse($request->period_start);
        $periodEnd = Carbon::parse($request->period_end);

        $summary = $this->attendanceService->getAttendanceSummary(
            $request->user_id,
            $periodStart,
            $periodEnd
        );

        return response()->json($summary);
    }
}
