<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'first_name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        // Whitelist allowed columns to prevent SQL injection
        $allowedColumns = ['first_name', 'last_name', 'position', 'department', 'salary_rate', 'status'];
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'first_name';
        }
        
        // Validate sort order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }
        
        // Exclude superadmin from employee list
        $employees = Employee::where('role', '!=', 'superadmin')->orderBy($sortBy, $sortOrder)->get();
        
        return view('hr.employees.index', compact('employees', 'sortBy', 'sortOrder'));
    }

    public function create()
    {
        $employee = new Employee();
        return view('hr.employees.form', compact('employee'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'first_name' => 'required|string|max:100',
                'middle_name' => 'nullable|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|unique:employees,email',
                'phone' => ['required', 'regex:/^(09\d{9}|\+639\d{9})$/'],
                'address' => 'nullable|string',
                'civil_status' => 'nullable|string|max:50',
                'spouse_name' => 'nullable|string|max:150',
                'date_of_birth' => 'nullable|date',
                'place_of_birth' => 'nullable|string|max:150',
                'educational_attainment' => 'nullable|string|max:150',
                'driver_license_number' => 'nullable|string|max:50',
                'driver_license_validity' => 'nullable|date',
                'date_of_hire' => 'required|date',
                'position' => 'required|string|max:150',
                'department' => 'required|string|max:150',
                'status' => 'required|string|in:active,inactive',
                'salary_rate' => 'required|numeric|min:0',
                'sss_number' => 'nullable|string|max:50',
                'tin_number' => 'nullable|string|max:50',
                'pagibig_number' => 'nullable|string|max:50',
                'signature_path' => 'nullable|string',
                'attachments_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ],
            [
                'phone.regex' => 'Phone must be 09XXXXXXXXX or +639XXXXXXXXX format.',
                'attachments_files.*.mimes' => 'Attachments must be a JPG, PNG, or PDF file.',
                'attachments_files.*.max' => 'Each attachment must not exceed 5MB.',
            ],
        );

        $validated['phone'] = $this->normalizePhone($validated['phone']);
        $validated['has_sss'] = $request->has('has_sss');
        $validated['has_tin'] = $request->has('has_tin');
        $validated['has_pagibig'] = $request->has('has_pagibig');

        $employee = Employee::create($validated);

        $this->handleAttachments($request, $employee);
        $this->handleRelations($request, $employee);

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['workExperiences', 'specialSkills', 'beneficiaries', 'characterReferences']);

        return view('hr.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $employee->load(['workExperiences', 'specialSkills', 'beneficiaries', 'characterReferences']);

        return view('hr.employees.form', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate(
            [
                'first_name' => 'required|string|max:100',
                'middle_name' => 'nullable|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|unique:employees,email,' . $employee->id,
                'phone' => ['required', 'regex:/^(09\d{9}|\+639\d{9})$/'],
                'address' => 'nullable|string',
                'civil_status' => 'nullable|string|max:50',
                'spouse_name' => 'nullable|string|max:150',
                'date_of_birth' => 'nullable|date',
                'place_of_birth' => 'nullable|string|max:150',
                'educational_attainment' => 'nullable|string|max:150',
                'driver_license_number' => 'nullable|string|max:50',
                'driver_license_validity' => 'nullable|date',
                'date_of_hire' => 'required|date',
                'position' => 'required|string|max:150',
                'department' => 'required|string|max:150',
                'status' => 'required|string|in:active,inactive',
                'salary_rate' => 'required|numeric|min:0',
                'sss_number' => 'nullable|string|max:50',
                'tin_number' => 'nullable|string|max:50',
                'pagibig_number' => 'nullable|string|max:50',
                'signature_path' => 'nullable|string',
                'attachments_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ],
            [
                'phone.regex' => 'Phone must be 09XXXXXXXXX or +639XXXXXXXXX format.',
                'attachments_files.*.mimes' => 'Attachments must be a JPG, PNG, or PDF file.',
                'attachments_files.*.max' => 'Each attachment must not exceed 5MB.',
            ],
        );

        $validated['phone'] = $this->normalizePhone($validated['phone']);
        $validated['has_sss'] = $request->has('has_sss');
        $validated['has_tin'] = $request->has('has_tin');
        $validated['has_pagibig'] = $request->has('has_pagibig');

        $employee->update($validated);

        $this->handleAttachments($request, $employee);

        // Delete old related records and re-save
        $employee->workExperiences()->delete();
        $employee->specialSkills()->delete();
        $employee->beneficiaries()->delete();
        $employee->characterReferences()->delete();

        $this->handleRelations($request, $employee);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        // Delete stored attachments
        if ($employee->attachments) {
            foreach ($employee->attachments as $path) {
                if ($path) {
                    Storage::disk('public')->delete($path);
                }
            }
        }

        $employee->workExperiences()->delete();
        $employee->specialSkills()->delete();
        $employee->beneficiaries()->delete();
        $employee->characterReferences()->delete();
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    // -------------------------
    // Private helpers
    // -------------------------

    private function handleAttachments(Request $request, Employee $employee)
    {
        $attachments = $employee->attachments ?? [];

        if ($request->hasFile('attachments_files')) {
            foreach ($request->file('attachments_files') as $key => $file) {
                // Delete old file if replacing
                if (isset($attachments[$key])) {
                    Storage::disk('public')->delete($attachments[$key]);
                }
                $attachments[$key] = $file->store('employees/attachments/' . $employee->id, 'public');
            }
        }

        // Always persist the attachments array, even if nothing new was uploaded
        $employee->update(['attachments' => $attachments]);
    }

    private function handleRelations(Request $request, Employee $employee)
    {
        if ($request->work_experiences) {
            foreach ($request->work_experiences as $we) {
                if (!empty($we['company_name'])) {
                    $employee->workExperiences()->create($we);
                }
            }
        }

        if ($request->special_skills) {
            foreach ($request->special_skills as $skill) {
                if (!empty($skill['skill_name'])) {
                    $employee->specialSkills()->create($skill);
                }
            }
        }

        if ($request->beneficiaries) {
            foreach ($request->beneficiaries as $b) {
                if (!empty($b['name'])) {
                    $employee->beneficiaries()->create($b);
                }
            }
        }

        if ($request->character_references) {
            foreach ($request->character_references as $c) {
                if (!empty($c['name'])) {
                    $employee->characterReferences()->create($c);
                }
            }
        }
    }

    private function normalizePhone($phone)
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        if (str_starts_with($phone, '+63')) {
            $phone = '0' . substr($phone, 3);
        }
        return $phone;
    }
}
