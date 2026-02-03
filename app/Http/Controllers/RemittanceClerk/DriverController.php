<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::all();
        return view('remittance-clerk.drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('remittance-clerk.drivers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'license_number' => 'required|unique:drivers',
            'contact_number' => 'required',
            'email' => 'required|email|unique:drivers',
            'address' => 'nullable|string',
            'date_of_hire' => 'required|date',
        ]);

        Driver::create($validated);

        return redirect()->route('drivers.index')->with('success', 'Driver created successfully.');
    }

    public function show(Driver $driver)
    {
        return view('remittance-clerk.drivers.show', compact('driver'));
    }

    public function edit(Driver $driver)
    {
        return view('remittance-clerk.drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'license_number' => 'required|unique:drivers,license_number,' . $driver->id,
            'contact_number' => 'required',
            'email' => 'required|email|unique:drivers,email,' . $driver->id,
            'address' => 'nullable|string',
            'date_of_hire' => 'required|date',
        ]);

        $driver->update($validated);

        return redirect()->route('drivers.index')->with('success', 'Driver updated successfully.');
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();
        return redirect()->route('drivers.index')->with('success', 'Driver deleted successfully.');
    }
}
