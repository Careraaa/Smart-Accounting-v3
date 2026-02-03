<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return view('hr.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('hr.employees.create');
    }

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

        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return view('hr.employees.edit', compact('employee'));
    }

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

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
