<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::all();
        return view('remittance-clerk.routes.index', compact('routes'));
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
        ]);

        $validated['route_name'] = $validated['origin'] . ' - ' . $validated['destination'];

        Route::create($validated);

        return redirect()->route('routes.index')->with('success', 'Route created successfully.');
    }

    public function show(Route $route)
    {
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
        ]);

        $validated['route_name'] = $validated['origin'] . ' - ' . $validated['destination'];

        $route->update($validated);

        return redirect()->route('routes.index')->with('success', 'Route updated successfully.');
    }

    public function destroy(Route $route)
    {
        $route->delete();
        return redirect()->route('routes.index')->with('success', 'Route deleted successfully.');
    }
}
