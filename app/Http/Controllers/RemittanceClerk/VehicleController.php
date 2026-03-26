<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Route;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'plate_number');
        $sortOrder = $request->get('sort_order', 'asc');
        
        // Whitelist allowed columns to prevent SQL injection
        $allowedColumns = ['plate_number', 'status'];
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'plate_number';
        }
        
        // Validate sort order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }
        
        $routes = Route::all();
        $vehicles = Vehicle::orderBy($sortBy, $sortOrder)->get();
        
        // Calculate statistics
        $totalVehicles = Vehicle::count();
        $activeVehicles = Vehicle::where('status', 'active')->count();
        $underMaintenanceVehicles = Vehicle::where('status', 'under_maintenance')->count();
        
        return view('remittance-clerk.vehicles.index', compact('routes', 'vehicles', 'sortBy', 'sortOrder', 'totalVehicles', 'activeVehicles', 'underMaintenanceVehicles'));
    }

    public function create()
    {
        $routes = Route::all();
        return view('remittance-clerk.vehicles.create', compact('routes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:vehicles',
            'route_id' => 'required|exists:routes,id',
            'operator' => 'required|string',
            'status' => 'required|in:active,under_maintenance',
        ]);

        $vehicleData = [
            'plate_number' => $validated['plate_number'],
            'operator' => $validated['operator'],
            'route_id' => $validated['route_id'],
            'status' => $validated['status'],
        ];

        Vehicle::create($vehicleData);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle created successfully.');
    }

    public function show(Vehicle $vehicle)
    {
        return view('remittance-clerk.vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $routes = Route::all();
        return view('remittance-clerk.vehicles.edit', compact('vehicle', 'routes'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:vehicles,plate_number,' . $vehicle->id,
            'route_id' => 'required|exists:routes,id',
            'operator' => 'required|string',
            'status' => 'required|in:active,under_maintenance',
        ]);

        $vehicleData = [
            'plate_number' => $validated['plate_number'],
            'operator' => $validated['operator'],
            'route_id' => $validated['route_id'],
            'status' => $validated['status'],
        ];

        $vehicle->update($vehicleData);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }
}
