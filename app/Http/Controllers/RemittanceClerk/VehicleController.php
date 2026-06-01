<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\Driver;
use App\Models\PAO;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    use LogsUserActivity;
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
        $vehicles = Vehicle::with('route')->orderBy($sortBy, $sortOrder)->get();
        
        // Calculate statistics
        $totalVehicles = Vehicle::count();
        $activeVehicles = Vehicle::where('status', 'active')->count();
        $underMaintenanceVehicles = Vehicle::where('status', 'under_maintenance')->count();
        
        $this->logActivity('viewed', 'Vehicles list', request()->url(), 'vehicle');

        return view('remittance-clerk.vehicles.index', compact('routes', 'vehicles', 'sortBy', 'sortOrder', 'totalVehicles', 'activeVehicles', 'underMaintenanceVehicles'));
    }

    public function create()
    {
        $routes = Route::all();
        $operators = Driver::query()
            ->where('status', 'active')
            ->pluck('name')
            ->merge(PAO::query()->where('status', 'active')->pluck('name'))
            ->unique()
            ->sort()
            ->values();
        return view('remittance-clerk.vehicles.create', compact('routes', 'operators'));
    }

    public function store(Request $request)
    {
        $operatorOptions = Driver::query()
            ->where('status', 'active')
            ->pluck('name')
            ->merge(PAO::query()->where('status', 'active')->pluck('name'))
            ->unique()
            ->values()
            ->all();

        $validated = $request->validate([
            'plate_number' => 'required|string|unique:vehicles',
            'route_id' => 'required|exists:routes,id',
            'operator' => ['required', 'string', Rule::in($operatorOptions)],
            'status' => 'required|in:active,under_maintenance',
        ]);

        $vehicleData = [
            'plate_number' => $validated['plate_number'],
            'operator' => $validated['operator'],
            'route_id' => $validated['route_id'],
            'status' => $validated['status'],
        ];

        $vehicle = Vehicle::create($vehicleData);

        $this->logActivity('created', "Vehicle: {$vehicle->plate_number}", request()->url(), 'vehicle', $vehicle->id);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle created successfully.');
    }

    public function show(Vehicle $vehicle)
    {
        $this->logActivity('viewed', "Vehicle: {$vehicle->plate_number}", request()->url(), 'vehicle', $vehicle->id);

        return view('remittance-clerk.vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $routes = Route::all();
        $operators = Driver::query()
            ->where('status', 'active')
            ->pluck('name')
            ->merge(PAO::query()->where('status', 'active')->pluck('name'))
            ->push($vehicle->operator)
            ->unique()
            ->sort()
            ->values();
        return view('remittance-clerk.vehicles.edit', compact('vehicle', 'routes', 'operators'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $operatorOptions = Driver::query()
            ->where('status', 'active')
            ->pluck('name')
            ->merge(PAO::query()->where('status', 'active')->pluck('name'))
            ->push($vehicle->operator)
            ->unique()
            ->values()
            ->all();

        $validated = $request->validate([
            'plate_number' => 'required|string|unique:vehicles,plate_number,' . $vehicle->id,
            'route_id' => 'required|exists:routes,id',
            'operator' => ['required', 'string', Rule::in($operatorOptions)],
            'status' => 'required|in:active,under_maintenance',
        ]);

        $vehicleData = [
            'plate_number' => $validated['plate_number'],
            'operator' => $validated['operator'],
            'route_id' => $validated['route_id'],
            'status' => $validated['status'],
        ];

        $vehicle->update($vehicleData);

        $this->logActivity('updated', "Vehicle: {$vehicle->plate_number}", request()->url(), 'vehicle', $vehicle->id);

        return redirect()->route('vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->logActivity('deleted', "Vehicle: {$vehicle->plate_number}", request()->url(), 'vehicle', $vehicle->id);

        $vehicle->delete();
        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }
}
