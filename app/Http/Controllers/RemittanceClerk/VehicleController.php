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
        return view('remittance-clerk.management.index', compact('routes', 'vehicles', 'sortBy', 'sortOrder'));
    }

    public function create()
    {
        return view('remittance-clerk.management.vehicles-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:vehicles',
            'origin' => 'required|string',
            'destination' => 'required|string',
            'operator' => 'required|string',
            'vehicle_type' => 'nullable|string',
            'make' => 'nullable|string',
            'model' => 'nullable|string',
            'year' => 'nullable|integer',
        ]);

        // Find or create route with the given origin and destination
        $route = Route::firstOrCreate(
            [
                'origin' => $validated['origin'],
                'destination' => $validated['destination'],
            ],
            [
                'route_name' => $validated['origin'] . ' - ' . $validated['destination'],
            ],
        );

        $vehicleData = [
            'plate_number' => $validated['plate_number'],
            'operator' => $validated['operator'],
            'route_id' => $route->id,
            'vehicle_type' => $validated['vehicle_type'] ?? null,
            'make' => $validated['make'] ?? null,
            'model' => $validated['model'] ?? null,
            'year' => $validated['year'] ?? null,
        ];

        Vehicle::create($vehicleData);

        return redirect()->route('routes.index')->with('success', 'Vehicle created successfully.');
    }

    public function show(Vehicle $vehicle)
    {
        return view('remittance-clerk.management.vehicles-show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        return view('remittance-clerk.management.vehicles-edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|unique:vehicles,plate_number,' . $vehicle->id,
            'origin' => 'required|string',
            'destination' => 'required|string',
            'operator' => 'required|string',
            'vehicle_type' => 'nullable|string',
            'make' => 'nullable|string',
            'model' => 'nullable|string',
            'year' => 'nullable|integer',
        ]);

        // Find or create route with the given origin and destination
        $route = Route::firstOrCreate(
            [
                'origin' => $validated['origin'],
                'destination' => $validated['destination'],
            ],
            [
                'route_name' => $validated['origin'] . ' - ' . $validated['destination'],
            ],
        );

        $vehicleData = [
            'plate_number' => $validated['plate_number'],
            'operator' => $validated['operator'],
            'route_id' => $route->id,
            'vehicle_type' => $validated['vehicle_type'] ?? null,
            'make' => $validated['make'] ?? null,
            'model' => $validated['model'] ?? null,
            'year' => $validated['year'] ?? null,
        ];

        $vehicle->update($vehicleData);

        return redirect()->route('routes.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }
}
