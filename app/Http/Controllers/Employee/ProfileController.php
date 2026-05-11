<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the employee's profile/personal records
     */
    public function show()
    {
        $employee = Auth::user();
        return view('employee.profile.show', compact('employee'));
    }

    /**
     * Show the edit form for the employee's profile
     */
    public function edit()
    {
        $employee = Auth::user();
        return view('employee.profile.edit', compact('employee'));
    }

    /**
     * Update the employee's profile
     * Only allows editing of personal information fields
     * HR-restricted fields are not updated here
     */
    public function update(Request $request)
    {
        $employee = Auth::user();

        // Validate only editable fields (HR-restricted fields are excluded)
        $validated = $request->validate([
            // Personal Information - EDITABLE
            'first_name'              => 'required|string|max:100',
            'middle_name'             => 'nullable|string|max:100',
            'last_name'               => 'required|string|max:100',
            'email'                   => 'nullable|email|unique:users,email,' . $employee->id,
            'phone'                   => ['required', 'regex:/^(09\d{9}|\+639\d{9})$/'],
            'gender'                  => 'nullable|string|in:male,female,prefer_not_to_say',
            'civil_status'            => 'nullable|string|max:50',
            'spouse_name'             => 'nullable|string|max:150',
            'date_of_birth'           => 'nullable|date',
            'place_of_birth'          => 'nullable|string|max:150',
            'educational_attainment'  => 'nullable|string|max:150',
            'address_street'          => 'required|string|max:255',
            'address_barangay'        => 'required|string|max:150',
            'address_city'            => 'required|string|max:150',
            'address_province'        => 'required|string|max:150',
            'driver_license_number'   => 'nullable|string|max:50',
            'driver_license_validity' => 'nullable|date',
            
            // Employment & Government - READ-ONLY (not validated, won't be updated)
            // date_of_hire, position, department, status, salary_rate
            // sss_number, tin_number, pagibig_number, has_sss, has_tin, has_pagibig
        ], [
            'phone.regex'   => 'Phone must be 09XXXXXXXXX or +639XXXXXXXXX format.',
            'first_name.required' => 'First name is required.',
            'last_name.required'  => 'Last name is required.',
            'phone.required'      => 'Phone is required.',
        ]);

        // Normalize phone number
        $validated['phone'] = $this->normalizePhone($validated['phone']);

        // Assemble address as JSON (matches User model's address accessors)
        $validated['address'] = json_encode([
            'street'   => $request->input('address_street', ''),
            'barangay' => $request->input('address_barangay', ''),
            'city'     => $request->input('address_city', ''),
            'province' => $request->input('address_province', ''),
        ]);

        // Remove virtual address sub-fields — they are not real DB columns
        unset($validated['address_street'], $validated['address_barangay'],
              $validated['address_city'], $validated['address_province']);

        // Update the employee
        $employee->update($validated);

        return redirect()->route('employee.profile.show')
            ->with('success', 'Your profile has been updated successfully.');
    }

    /**
     * Normalize phone number to consistent format
     */
    private function normalizePhone($phone)
    {
        // Remove all non-numeric characters except +
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        // Convert 09 to +639
        if (str_starts_with($phone, '09')) {
            $phone = '+63' . substr($phone, 1);
        }
        
        return $phone;
    }

    /**
     * Assemble address from individual fields
     */
    private function assembleAddress($request)
    {
        $parts = [
            $request->input('address_street'),
            $request->input('address_barangay'),
            $request->input('address_city'),
            $request->input('address_province'),
        ];
        
        return implode(', ', array_filter($parts));
    }

    /**
     * Show password change form
     */
    public function editPassword()
    {
        return view('employee.profile.change-password');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('employee.profile.show')
            ->with('success', 'Password changed successfully.');
    }
}
