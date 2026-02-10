<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // List all employees
    public function index()
    {
        $employees = Employee::all();
        return view('hr.employees.index', compact('employees'));
    }

    // Show create form
    public function create()
    {
        // Pass an empty Employee object for future-proof form
        $employee = new Employee();
        return view('hr.employees.form', compact('employee'));
    }

    // Store new employee
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:employees',
            'phone' => 'required',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'date_of_hire' => 'required|date',
            'position' => 'required|string',
            'department' => 'required|string',
            'salary_rate' => 'required|numeric',
        ]);

        $validated['has_sss'] = $request->has('has_sss') ? true : false;
        $validated['has_pagibig'] = $request->has('has_pagibig') ? true : false;

        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    // Show employee details
    public function show(Employee $employee)
    {
        // Prepare optional relationships for tabs (future-proof)
        $employee->loadMissing(['allowances', 'deductions']);
        return view('hr.employees.show', compact('employee'));
    }

    // Show edit form
    public function edit(Employee $employee)
    {
        // Prepare optional relationships for tabs (future-proof)
        $employee->loadMissing(['allowances', 'deductions']);
        return view('hr.employees.form', compact('employee'));
    }

    // Update employee
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'date_of_hire' => 'required|date',
            'position' => 'required|string',
            'department' => 'required|string',
            'salary_rate' => 'required|numeric',
        ]);

        $validated['has_sss'] = $request->has('has_sss') ? true : false;
        $validated['has_pagibig'] = $request->has('has_pagibig') ? true : false;

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    // Delete employee
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
