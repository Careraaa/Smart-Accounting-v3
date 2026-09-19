<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    use LogsUserActivity;
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        // Whitelist allowed columns to prevent SQL injection
        $allowedColumns = ['name', 'contact_number', 'status'];
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'name';
        }
        
        // Validate sort order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }
        
        $drivers = Driver::orderBy($sortBy, $sortOrder)->get();
        
        // Calculate statistics
        $totalDrivers = Driver::count();
        $activeDrivers = Driver::where('status', 'active')->count();
        $inactiveDrivers = Driver::where('status', 'inactive')->count();
        
        $this->logActivity('viewed', 'Drivers list', request()->url(), 'driver');

        return view('remittance-clerk.drivers.index', compact('drivers', 'sortBy', 'sortOrder', 'totalDrivers', 'activeDrivers', 'inactiveDrivers'));
    }

    public function create()
    {
        return view('remittance-clerk.drivers.create');
    }

    public function store(Request $request)
    {
        $request->merge([
            'contact_number' => preg_replace('/\D/', '', (string) $request->contact_number),
        ]);

        $validated = $request->validate([
            'name' => 'required|string',
            'license_number' => ['required', 'unique:drivers', 'regex:/^[A-Za-z]\d{2}-\d{2}-\d{6}$/'],
            'contact_number' => ['required', 'digits:11', 'regex:/^09\d{9}$/'],
            'email' => 'nullable|email|unique:drivers',
            'gender' => 'nullable|string',
            'address' => 'nullable|string',
            'date_of_hire' => 'nullable|date',
        ]);

        $validated['license_number'] = strtoupper($validated['license_number']);
        $validated['status'] = 'active';
        $driver = Driver::create($validated);

        $this->logActivity('created', "Driver: {$driver->name}", request()->url(), 'driver', $driver->id);

        return redirect()->route('drivers.index')->with('success', 'Driver created successfully.');
    }

    public function show(Driver $driver)
    {
        $this->logActivity('viewed', "Driver: {$driver->name}", request()->url(), 'driver', $driver->id);

        return view('remittance-clerk.drivers.show', compact('driver'));
    }

    public function edit(Driver $driver)
    {
        return view('remittance-clerk.drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $request->merge([
            'contact_number' => preg_replace('/\D/', '', (string) $request->contact_number),
        ]);

        $validated = $request->validate([
            'name' => 'required|string',
            'license_number' => ['required', 'unique:drivers,license_number,' . $driver->id, 'regex:/^[A-Za-z]\d{2}-\d{2}-\d{6}$/'],
            'contact_number' => ['required', 'digits:11', 'regex:/^09\d{9}$/'],
            'email' => 'nullable|email|unique:drivers,email,' . $driver->id,
            'gender' => 'nullable|string',
            'address' => 'nullable|string',
            'date_of_hire' => 'nullable|date',
            'status' => 'nullable|in:active,inactive',
        ]);

        $validated['license_number'] = strtoupper($validated['license_number']);
        $driver->update($validated);

        $this->logActivity('updated', "Driver: {$driver->name}", request()->url(), 'driver', $driver->id);

        return redirect()->route('drivers.index')->with('success', 'Driver updated successfully.');
    }

    public function destroy(Driver $driver)
    {
        $this->logActivity('deleted', "Driver: {$driver->name}", request()->url(), 'driver', $driver->id);

        $driver->delete();
        return redirect()->route('drivers.index')->with('success', 'Driver deleted successfully.');
    }
}
