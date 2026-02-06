<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Attendance; 
use App\Models\Leave; 
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with('employee')->get();

        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'active')->count();
        $inactiveEmployees = $totalEmployees - $activeEmployees;

        $presentToday = Attendance::whereDate('date', now())->where('status', 'present')->count();
        $absentToday = Attendance::whereDate('date', now())->where('status', 'absent')->count();
        $lateToday = Attendance::whereDate('date', now())->where('status', 'late')->count();

        $onLeaveEmployees = Leave::where('status', 'approved')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->count();

        $totalLeaves = Leave::count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $approvedLeaves = Leave::where('status', 'approved')->count();

        $attendanceRate = $totalEmployees > 0 
            ? ($presentToday / $totalEmployees) * 100 
            : 0;

        $attendanceTrend = Attendance::whereDate('date', '>=', now()->subDays(6))
            ->get()
            ->groupBy(function($item) {
                return $item->date->format('Y-m-d');
            })
            ->map(function($group) {
                return [
                    'date' => $group->first()->date->format('M d, Y'),
                    'present' => $group->where('status', 'present')->count(),
                    'absent' => $group->where('status', 'absent')->count(),
                    'late' => $group->where('status', 'late')->count(),
                ];
            })->values();

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
            'attendanceTrend'
        ));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();
        return view('hr.payroll.salary-computation.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payroll_period_start' => 'required|date',
            'payroll_period_end' => 'required|date',
            'total_allowances' => 'required|numeric',
            'total_deductions' => 'required|numeric',
        ]);

        // Compute basic salary dynamically
        $employee = Employee::findOrFail($validated['employee_id']);
        $basicSalary = $employee->salary_rate * 15;

        $payroll = new Payroll();
        $payroll->employee_id = $employee->id;
        $payroll->payroll_period_start = $validated['payroll_period_start'];
        $payroll->payroll_period_end = $validated['payroll_period_end'];
        $payroll->total_allowances = $validated['total_allowances'];
        $payroll->total_deductions = $validated['total_deductions'];
        $payroll->gross_pay = $basicSalary + $validated['total_allowances'];
        $payroll->net_pay = $payroll->gross_pay - $validated['total_deductions'];
        $payroll->status = 'pending';
        $payroll->save();

        return redirect()->route('payroll.salary-computation.index')
            ->with('success', 'Payroll created successfully.');
    }

    public function show(Payroll $payroll)
    {
        $payroll->load('employee', 'deductions');
        return view('hr.payroll.salary-computation.show', compact('payroll'));
    }

    public function edit(Payroll $payroll)
    {
        $employees = Employee::where('status', 'active')->get();
        return view('hr.payroll.salary-computation.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payroll_period_start' => 'required|date',
            'payroll_period_end' => 'required|date',
            'total_allowances' => 'required|numeric',
            'total_deductions' => 'required|numeric',
        ]);

        // Compute basic salary dynamically
        $employee = Employee::findOrFail($validated['employee_id']);
        $basicSalary = $employee->salary_rate * 15;

        $payroll->employee_id = $employee->id;
        $payroll->payroll_period_start = $validated['payroll_period_start'];
        $payroll->payroll_period_end = $validated['payroll_period_end'];
        $payroll->total_allowances = $validated['total_allowances'];
        $payroll->total_deductions = $validated['total_deductions'];
        $payroll->gross_pay = $basicSalary + $validated['total_allowances'];
        $payroll->net_pay = $payroll->gross_pay - $validated['total_deductions'];
        // keep current status; HR cannot change it here
        $payroll->save();

        return redirect()->route('payroll.salary-computation.index')
            ->with('success', 'Payroll updated successfully.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payroll.salary-computation.index')
            ->with('success', 'Payroll deleted successfully.');
    }

    public function generatePayslip(Payroll $payroll)
    {
        $payroll->load('employee', 'deductions');
        return view('hr.payroll.payslip', compact('payroll'));
    }
}
