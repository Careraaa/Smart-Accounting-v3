<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('employee')->get();
        return view('hr.attendance.index', compact('attendances'));
    }

    public function create()
    {
        $employees = Employee::where('status', 'active')->get();
        return view('hr.attendance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'time_in' => 'nullable|date_format:Y-m-d H:i',
            'time_out' => 'nullable|date_format:Y-m-d H:i',
            'date' => 'required|date',
            'qr_code' => 'nullable|string',
            'status' => 'required|in:present,absent,late,half_day',
        ]);

        Attendance::create($validated);

        return redirect()->route('attendance.index')->with('success', 'Attendance recorded successfully.');
    }

    public function show(Attendance $attendance)
    {
        $attendance->load('employee');
        return view('hr.attendance.show', compact('attendance'));
    }

    public function edit(Attendance $attendance)
    {
        $employees = Employee::where('status', 'active')->get();
        return view('hr.attendance.edit', compact('attendance', 'employees'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'time_in' => 'nullable|date_format:Y-m-d H:i',
            'time_out' => 'nullable|date_format:Y-m-d H:i',
            'date' => 'required|date',
            'qr_code' => 'nullable|string',
            'status' => 'required|in:present,absent,late,half_day',
        ]);

        $attendance->update($validated);

        return redirect()->route('attendance.index')->with('success', 'Attendance updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('attendance.index')->with('success', 'Attendance deleted successfully.');
    }
}
