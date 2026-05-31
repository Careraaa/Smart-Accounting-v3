<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Traits\LogsUserActivity;

class EmployeeController extends Controller
{
    use LogsUserActivity;
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        $sortBy = $request->get('sort_by', 'first_name');
        $sortOrder = $request->get('sort_order', 'asc');

        $allowedColumns = ['first_name', 'last_name', 'gender', 'position', 'department', 'salary_rate', 'status'];
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'first_name';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        // Base query
        $baseQuery = User::whereNotIn('role', ['superadmin', 'qr_admin']);

        // Tab-specific counts
        $allCount = (clone $baseQuery)->count();
        $activeCount = (clone $baseQuery)->where('status', 'active')->count();
        $inactiveCount = (clone $baseQuery)->where('status', 'inactive')->count();

        // Paginated employees for display (filtered by tab)
        $query = clone $baseQuery;
        if ($tab === 'active') {
            $query->where('status', 'active');
        } elseif ($tab === 'inactive') {
            $query->where('status', 'inactive');
        }
        $employees = $query->orderBy($sortBy, $sortOrder)->paginate(10);

        $this->logActivity('viewed', 'Employee list', request()->url(), 'employee');

        // All employees for client-side filtering
        $allQuery = clone $baseQuery;
        if ($tab === 'active') {
            $allQuery->where('status', 'active');
        } elseif ($tab === 'inactive') {
            $allQuery->where('status', 'inactive');
        }
        $allEmployees = $allQuery->orderBy($sortBy, $sortOrder)->get();

        // Get unique departments for filter
        $departments = User::whereNotIn('role', ['superadmin', 'qr_admin'])
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->sort()
            ->values();

        return view('hr.employees.index', compact('employees', 'allEmployees', 'departments', 'sortBy', 'sortOrder', 'tab', 'allCount', 'activeCount', 'inactiveCount'));
    }

    public function create()
    {
        $employee = new User();
        return view('hr.employees.create', compact('employee'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'first_name' => 'required|string|max:100',
                'middle_name' => 'nullable|string|max:100',
                'last_name' => 'required|string|max:100',
                'gender' => 'required|string|in:male,female,prefer_not_to_say',
                'email' => 'nullable|email|unique:users,email',
                'phone' => ['required', 'regex:/^(09\d{9}|\+639\d{9})$/'],
                'address_street' => 'required|string|max:150',
                'address_barangay' => 'required|string|max:100',
                'address_city' => 'required|string|max:100',
                'address_province' => 'required|string|max:100',
                'address' => 'nullable|string',
                'civil_status' => 'required|string|max:50',
                'spouse_name' => 'nullable|string|max:150',
                'date_of_birth' => 'required|date',
                'place_of_birth' => 'required|string|max:150',
                'educational_attainment' => 'required|string|max:150',
                'driver_license_number' => 'nullable|string|max:50',
                'driver_license_validity' => 'nullable|date',
                'date_of_hire' => 'required|date',
                'position' => 'required|string|max:150',
                'department' => 'required|string|max:150',
                'status' => 'required|string|in:active,inactive',
                'salary_rate' => 'required|numeric|between:0,999999.99', // DAILY RATE
                'sss_number' => 'nullable|string|max:50',
                'tin_number' => 'nullable|string|max:50',
                'pagibig_number' => 'nullable|string|max:50',
                'philhealth_number' => 'nullable|string|max:50',
                'signature_path' => 'nullable|string',
                'generated_password' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'attachments_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ],
            [
                'phone.regex' => 'Phone must be 09XXXXXXXXX or +639XXXXXXXXX format.',
                'salary_rate.between' => 'Daily rate must be between 0.00 and 999,999.99.',
                'attachments_files.*.mimes' => 'Attachments must be a JPG, PNG, or PDF file.',
                'attachments_files.*.max' => 'Each attachment must not exceed 5MB.',
            ],
        );

        $validated['phone'] = $this->normalizePhone($validated['phone']);
        $validated['has_sss'] = $request->has('has_sss');
        $validated['has_tin'] = $request->has('has_tin');
        $validated['has_pagibig'] = $request->has('has_pagibig');
        $validated['has_philhealth'] = $request->has('has_philhealth');
        $validated['role'] = 'employee';
        $validated['address'] = $this->assembleAddress($request);

        // Auto-generate username
        $baseUsername = strtolower(preg_replace('/\s+/', '', $validated['first_name']) . '.' . preg_replace('/\s+/', '', $validated['last_name']));
        $username = $baseUsername;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter++;
        }
        $validated['username'] = $username;
        $validated['name'] = trim($validated['first_name'] . ' ' . ($validated['middle_name'] ?? '') . ' ' . $validated['last_name']);

        // Use generated password from form; if missing/blank, generate a fallback.
        $plainPassword = trim((string) ($validated['generated_password'] ?? ''));
        if ($plainPassword === '') {
            $plainPassword = $this->generateDefaultPassword($validated['first_name'] ?? '');
        }
        $validated['password'] = Hash::make($plainPassword);

        $employee = User::create($validated);

        $this->handlePhoto($request, $employee);

        $this->logActivity('created', "Employee: {$employee->first_name} {$employee->last_name}", request()->url(), 'employee', $employee->id);

        $this->handleAttachments($request, $employee);
        $this->handleRelations($request, $employee);

        return redirect()
            ->route('employees.index')
            ->with('success', "Employee created. Username: {$username} | Default password: {$plainPassword}");
    }

    public function show(User $employee)
    {
        $employee->load(['workExperiences', 'specialSkills', 'beneficiaries', 'charRefs', 'employeeAttachments']);
        $this->logActivity('viewed', "Employee: {$employee->first_name} {$employee->last_name}", request()->url(), 'employee', $employee->id);
        return view('hr.employees.show', compact('employee'));
    }

    public function edit(User $employee)
    {
        $employee->load(['workExperiences', 'specialSkills', 'beneficiaries', 'charRefs']);
        return view('hr.employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        $validated = $request->validate(
            [
                'first_name' => 'required|string|max:100',
                'middle_name' => 'nullable|string|max:100',
                'last_name' => 'required|string|max:100',
                'gender' => 'required|string|in:male,female,prefer_not_to_say',
                'email' => 'nullable|email|unique:users,email,' . $employee->id,
                'phone' => ['required', 'regex:/^(09\d{9}|\+639\d{9})$/'],
                'address_street' => 'required|string|max:150',
                'address_barangay' => 'required|string|max:100',
                'address_city' => 'required|string|max:100',
                'address_province' => 'required|string|max:100',
                'address' => 'nullable|string',
                'civil_status' => 'required|string|max:50',
                'spouse_name' => 'nullable|string|max:150',
                'date_of_birth' => 'required|date',
                'place_of_birth' => 'required|string|max:150',
                'educational_attainment' => 'required|string|max:150',
                'driver_license_number' => 'nullable|string|max:50',
                'driver_license_validity' => 'nullable|date',
                'date_of_hire' => 'required|date',
                'position' => 'required|string|max:150',
                'department' => 'required|string|max:150',
                'status' => 'required|string|in:active,inactive',
                'salary_rate' => 'required|numeric|between:0,999999.99', // DAILY RATE
                'sss_number' => 'nullable|string|max:50',
                'tin_number' => 'nullable|string|max:50',
                'pagibig_number' => 'nullable|string|max:50',
                'signature_path' => 'nullable|string',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'attachments_files.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ],
            [
                'phone.regex' => 'Phone must be 09XXXXXXXXX or +639XXXXXXXXX format.',
                'salary_rate.between' => 'Daily rate must be between 0.00 and 999,999.99.',
                'attachments_files.*.mimes' => 'Attachments must be a JPG, PNG, or PDF file.',
                'attachments_files.*.max' => 'Each attachment must not exceed 5MB.',
            ],
        );

        $validated['phone'] = $this->normalizePhone($validated['phone']);
        $validated['has_sss'] = $request->has('has_sss');
        $validated['has_tin'] = $request->has('has_tin');
        $validated['has_pagibig'] = $request->has('has_pagibig');
        $validated['has_philhealth'] = $request->has('has_philhealth');
        $validated['name'] = trim($validated['first_name'] . ' ' . ($validated['middle_name'] ?? '') . ' ' . $validated['last_name']);
        $validated['address'] = $this->assembleAddress($request);

        $employee->update($validated);

        $this->handlePhoto($request, $employee);

        $this->logActivity('updated', "Employee: {$employee->first_name} {$employee->last_name}", request()->url(), 'employee', $employee->id);

        $this->handleAttachments($request, $employee);

        $employee->workExperiences()->delete();
        $employee->specialSkills()->delete();
        $employee->beneficiaries()->delete();
        $employee->charRefs()->delete();

        $this->handleRelations($request, $employee);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(User $employee)
    {
        // Delete files from the new employee_attachments table
        foreach ($employee->employeeAttachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }
        $employee->employeeAttachments()->delete();

        // Also clean up any legacy JSON attachments
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
        $employee->charRefs()->delete();
        $this->logActivity('deleted', "Employee: {$employee->first_name} {$employee->last_name}", request()->url(), 'employee', $employee->id);
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }

    // ── Private helpers ────────────────────────────────────────────────

    private function assembleAddress(Request $request): string
    {
        if ($request->filled('address') && str_starts_with(trim($request->input('address')), '{')) {
            return $request->input('address');
        }

        return json_encode([
            'street' => $request->input('address_street', ''),
            'barangay' => $request->input('address_barangay', ''),
            'city' => $request->input('address_city', ''),
            'province' => $request->input('address_province', ''),
        ]);
    }

    private function handleAttachments(Request $request, User $employee): void
    {
        if (!$request->hasFile('attachments_files')) {
            return;
        }

        $validKeys = EmployeeAttachment::attachmentTypes();

        foreach ($request->file('attachments_files') as $key => $file) {
            if (!array_key_exists($key, $validKeys)) {
                continue;
            }

            // HR uploads are auto-approved; store via the shared helper
            EmployeeAttachmentController::saveAttachment($file, $employee, $key, 'hr');
        }
    }

    private function handleRelations(Request $request, User $employee): void
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
                    $employee->charRefs()->create($c);
                }
            }
        }
    }

    private function generateDefaultPassword(string $firstName = ''): string
    {
        $base = preg_replace('/[^a-zA-Z]/', '', $firstName) ?: 'Employee';
        $base = ucfirst(strtolower(substr($base, 0, 6)));
        $suffix = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        // Example: Juan1234 (easy to type and communicate)
        return $base . $suffix;
    }

    private function normalizePhone($phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        if (str_starts_with($phone, '+63')) {
            $phone = '0' . substr($phone, 3);
        }
        return $phone;
    }

    private function handlePhoto(Request $request, User $employee): void
    {
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($employee->profile_picture) {
                Storage::disk('public')->delete($employee->profile_picture);
            }
            $path = $request->file('photo')->store('photos', 'public');
            $employee->update(['profile_picture' => $path]);
        }
    }
}
