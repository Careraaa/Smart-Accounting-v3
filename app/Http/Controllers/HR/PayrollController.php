<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with('employee')->get();
        return view('hr.payroll.index', compact('payrolls'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();
        return view('hr.payroll.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payroll_period_start' => 'required|date',
            'payroll_period_end' => 'required|date',
            'basic_salary' => 'required|numeric',
            'total_allowances' => 'required|numeric',
            'total_deductions' => 'required|numeric',
        ]);

        $validated['gross_pay'] = $validated['basic_salary'] + $validated['total_allowances'];
        $validated['net_pay'] = $validated['gross_pay'] - $validated['total_deductions'];

        Payroll::create($validated);

        return redirect()->route('payroll.index')->with('success', 'Payroll created successfully.');
    }

    public function show(Payroll $payroll)
    {
        $payroll->load('employee', 'deductions');
        return view('hr.payroll.show', compact('payroll'));
    }

    public function edit(Payroll $payroll)
    {
        $employees = Employee::where('status', 'active')->get();
        return view('hr.payroll.edit', compact('payroll', 'employees'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'payroll_period_start' => 'required|date',
            'payroll_period_end' => 'required|date',
            'basic_salary' => 'required|numeric',
            'total_allowances' => 'required|numeric',
            'total_deductions' => 'required|numeric',
            'status' => 'required|in:draft,submitted,approved,paid',
        ]);

        $validated['gross_pay'] = $validated['basic_salary'] + $validated['total_allowances'];
        $validated['net_pay'] = $validated['gross_pay'] - $validated['total_deductions'];

        $payroll->update($validated);

        return redirect()->route('payroll.index')->with('success', 'Payroll updated successfully.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();
        return redirect()->route('payroll.index')->with('success', 'Payroll deleted successfully.');
    }

    public function generatePayslip(Payroll $payroll)
    {
        $payroll->load('employee', 'deductions');
        return view('hr.payroll.payslip', compact('payroll'));
    }
}
