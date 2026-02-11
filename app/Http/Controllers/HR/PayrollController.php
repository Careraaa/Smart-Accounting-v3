<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use Illuminate\Http\Request;
use App\Models\StatutoryDeduction;

class PayrollController extends Controller
{
    // Display payroll list
    public function index()
    {
        $payrolls = Payroll::with(['employee', 'allowances', 'deductions'])->get();

        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'active')->count();
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

        return view('hr.payroll.salary-computation.index', compact('payrolls', 'totalEmployees', 'activeEmployees', 'inactiveEmployees', 'presentToday', 'absentToday', 'lateToday', 'onLeaveEmployees', 'pendingLeaves', 'approvedLeaves', 'totalLeaves', 'attendanceRate', 'attendanceTrend'));
    }

    // Show create form
    public function create()
    {
        $employees = Employee::where('status', 'active')->get();
        return view('hr.payroll.salary-computation.create', compact('employees'));
    }

    // Store payroll
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payroll_period_start' => 'required|date',
            'payroll_period_end' => 'required|date',
            'total_allowances' => 'required|numeric|min:0',
            'total_deductions' => 'required|numeric|min:0',
            'allowances.*.name' => 'nullable|string',
            'allowances.*.amount' => 'nullable|numeric|min:0',
            'deductions.*.name' => 'nullable|string',
            'deductions.*.amount' => 'nullable|numeric|min:0',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        $basicSalary = $employee->salary_rate * 15; // semi-monthly

        $netSalary = $basicSalary + $validated['total_allowances'] - $validated['total_deductions'];

        $payroll = Payroll::create([
            'employee_id' => $employee->id,
            'payroll_period_start' => $validated['payroll_period_start'],
            'payroll_period_end' => $validated['payroll_period_end'],
            'basic_salary' => $basicSalary,
            'total_allowances' => $validated['total_allowances'],
            'total_deductions' => $validated['total_deductions'],
            'net_salary' => $netSalary,
            'status' => 'pending',
        ]);

        // Save allowances
        foreach ($request->allowances ?? [] as $allowance) {
            if (!empty($allowance['name']) && $allowance['amount'] > 0) {
                $payroll->allowances()->create([
                    'allowance_type' => $allowance['name'],
                    'amount' => $allowance['amount'],
                ]);
            }
        }

        // Save deductions
        foreach ($request->deductions ?? [] as $deduction) {
            if (!empty($deduction['name']) && $deduction['amount'] > 0) {
                $payroll->deductions()->create([
                    'deduction_type' => $deduction['name'],
                    'amount' => $deduction['amount'],
                ]);
            }
        }

        return redirect()->route('payroll.index')->with('success', 'Payroll created successfully.');
    }

    // Show single payroll
    public function show(Payroll $payroll)
    {
        $payroll->load('employee', 'allowances', 'deductions');
        return view('hr.payroll.salary-computation.show', compact('payroll'));
    }

    // Show edit form
    public function edit(Payroll $payroll)
    {
        $employees = Employee::where('status', 'active')->get();
        $payroll->load('allowances', 'deductions');
        return view('hr.payroll.salary-computation.edit', compact('payroll', 'employees'));
    }

    // Update payroll
    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payroll_period_start' => 'required|date',
            'payroll_period_end' => 'required|date',
            'allowances.name.*' => 'nullable|string',
            'allowances.amount.*' => 'nullable|numeric|min:0',
            'deductions.name.*' => 'nullable|string',
            'deductions.amount.*' => 'nullable|numeric|min:0',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        $basicSalary = $employee->salary_rate * 15;

        $totalAllowances = 0;
        foreach ($request->allowances ?? [] as $allowance) {
            $totalAllowances += floatval($allowance['amount'] ?? 0);
        }

        $totalDeductions = 0;
        foreach ($request->deductions ?? [] as $deduction) {
            $totalDeductions += floatval($deduction['amount'] ?? 0);
        }

        $netSalary = $basicSalary + $totalAllowances - $totalDeductions;

        $payroll->update([
            'employee_id' => $employee->id,
            'payroll_period_start' => $validated['payroll_period_start'],
            'payroll_period_end' => $validated['payroll_period_end'],
            'basic_salary' => $basicSalary,
            'total_allowances' => $totalAllowances,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'status' => 'pending',
        ]);

        // Reset old allowances/deductions
        $payroll->allowances()->delete();
        $payroll->deductions()->delete();

        // Re-save allowances
        foreach ($request->allowances ?? [] as $allowance) {
            $name = $allowance['name'] ?? null;
            $amount = $allowance['amount'] ?? 0;
            if ($name && $amount > 0) {
                $payroll->allowances()->create([
                    'allowance_type' => $name,
                    'amount' => $amount,
                ]);
            }
        }

        // Re-save deductions
        foreach ($request->deductions ?? [] as $deduction) {
            $name = $deduction['name'] ?? null;
            $amount = $deduction['amount'] ?? 0;
            if ($name && $amount > 0) {
                $payroll->deductions()->create([
                    'deduction_type' => $name,
                    'amount' => $amount,
                ]);
            }
        }

        return redirect()->route('payroll.salary-computation.index')->with('success', 'Payroll updated successfully.');
    }

    // Delete payroll
    public function destroy(Payroll $payroll)
    {
        $payroll->allowances()->delete();
        $payroll->deductions()->delete();
        $payroll->delete();

        return redirect()->route('payroll.salary-computation.index')->with('success', 'Payroll deleted successfully.');
    }

    // Generate payslip
    public function generatePayslip(Payroll $payroll)
    {
        $payroll->load('employee', 'allowances', 'deductions');
        return view('hr.payroll.payslip', compact('payroll'));
    }

    // =========================
    // Compute statutory deductions
    // =========================
    public function computeStatutory(Request $request)
    {
        $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'has_sss' => 'required|boolean',
            'has_pagibig' => 'required|boolean',
        ]);

        $semiMonthlySalary = $request->basic_salary;
        $monthlySalary = $semiMonthlySalary * 2; // Convert semi-monthly to monthly
        $results = [];

        // --------- SSS ----------
        if ($request->has_sss) {
            $sss = StatutoryDeduction::where('name', 'SSS')->where('min_salary', '<=', $monthlySalary)->where('max_salary', '>=', $monthlySalary)->first();

            if ($sss) {
                // Use fixed employee_share if available, else percentage
                $amount = $sss->employee_share ?? $monthlySalary * ($sss->percentage_employee / 100);

                // For semi-monthly payroll, divide by 2
                $results[] = [
                    'name' => 'SSS',
                    'amount' => round($amount / 2, 2),
                ];
            }
        }

        // --------- Pag-IBIG ----------
        if ($request->has_pagibig) {
            $pagibig = StatutoryDeduction::where('name', 'Pag-IBIG')->where('min_salary', '<=', $monthlySalary)->where('max_salary', '>=', $monthlySalary)->first();

            if ($pagibig) {
                $amount = $pagibig->employee_share ?? $monthlySalary * ($pagibig->percentage_employee / 100);
                $results[] = [
                    'name' => 'Pag-IBIG',
                    'amount' => round($amount / 2, 2), // Semi-monthly
                ];
            }
        }

        return response()->json($results);
    }
}
