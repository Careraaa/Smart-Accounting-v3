<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Vehicle;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;

class RouteController extends Controller
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
        
        $routes = Route::with('vehicles')->get();
        $vehicles = Vehicle::orderBy($sortBy, $sortOrder)->get();
        
        // Calculate statistics
        $totalVehicles = Vehicle::count();
        $activeVehicles = Vehicle::where('status', 'active')->count();
        $underMaintenanceVehicles = Vehicle::where('status', 'under_maintenance')->count();
        
        $this->logActivity('viewed', 'Routes list', request()->url(), 'route');

        return view('remittance-clerk.routes.index', compact('routes', 'vehicles', 'sortBy', 'sortOrder', 'totalVehicles', 'activeVehicles', 'underMaintenanceVehicles'));
    }

    public function create()
    {
        return view('remittance-clerk.routes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string',
            'boundary' => 'required|numeric|min:0',
        ]);

        // Auto-generate route name from origin and destination
        $validated['route_name'] = $validated['origin'] . ' - ' . $validated['destination'];

        $route = Route::create($validated);

        $this->logActivity('created', "Route: {$route->route_name}", request()->url(), 'route', $route->id);

        return redirect()->route('routes.index')->with('success', 'Route created successfully.');
    }

    public function show(Route $route)
    {
        $route->load('vehicles');
        $this->logActivity('viewed', "Route: {$route->route_name}", request()->url(), 'route', $route->id);

        return view('remittance-clerk.routes.show', compact('route'));
    }

    public function edit(Route $route)
    {
        return view('remittance-clerk.routes.edit', compact('route'));
    }

    public function update(Request $request, Route $route)
    {
        $validated = $request->validate([
            'origin' => 'required|string',
            'destination' => 'required|string',
            'boundary' => 'required|numeric|min:0',
        ]);

        // Auto-generate route name from origin and destination
        $validated['route_name'] = $validated['origin'] . ' - ' . $validated['destination'];

        $route->update($validated);

        $this->logActivity('updated', "Route: {$route->route_name}", request()->url(), 'route', $route->id);

        return redirect()->route('routes.index')->with('success', 'Route updated successfully.');
    }

    public function destroy(Route $route)
    {
        // Check if route has vehicles before deleting
        if ($route->vehicles()->count() > 0) {
            return redirect()->route('routes.index')->with('warning', 'Cannot delete route with assigned vehicles.');
        }

        $this->logActivity('deleted', "Route: {$route->route_name}", request()->url(), 'route', $route->id);

        $route->delete();
        return redirect()->route('routes.index')->with('success', 'Route deleted successfully.');
    }
}
