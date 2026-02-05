<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::all();
        $vehicles = Vehicle::all();
        return view('remittance-clerk.management.index', compact('routes', 'vehicles'));
    }

    public function create()
    {
        // Routes are now managed through vehicles, redirect to vehicle creation
        return redirect()->route('vehicles.create')->with('info', 'Create a vehicle to add a new route.');
    }

    public function store(Request $request)
    {
        // Routes are now managed through vehicles, redirect back
        return redirect()->route('routes.index')->with('info', 'Routes are managed through vehicle creation.');
    }

    public function show(Route $route)
    {
        // Routes are now managed through vehicles
        return redirect()->route('routes.index')->with('info', 'Routes are managed through vehicles.');
    }

    public function edit(Route $route)
    {
        // Routes are now managed through vehicles, redirect to vehicle management
        return redirect()->route('routes.index')->with('info', 'Routes are managed through vehicle editing.');
    }

    public function update(Request $request, Route $route)
    {
        // Routes are now managed through vehicles
        return redirect()->route('routes.index')->with('info', 'Routes are updated through vehicle management.');
    }

    public function destroy(Route $route)
    {
        // Routes are now managed through vehicles
        return redirect()->route('routes.index')->with('info', 'Route management is handled through vehicles.');
    }
}
