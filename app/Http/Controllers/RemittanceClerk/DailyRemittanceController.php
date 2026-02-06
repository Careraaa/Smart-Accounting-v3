<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Http\Controllers\Controller;
use App\Models\DailyRemittance;
use App\Models\Driver;
use App\Models\PAO;
use App\Models\Route;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DailyRemittanceController extends Controller
{
    public function index()
    {
        $remittances = DailyRemittance::with('driver', 'pao', 'route', 'vehicle')->get();
        return view('remittance-clerk.remittances.index', compact('remittances'));
    }

    public function create()
    {
        $drivers = Driver::where('status', 'active')->get();
        $paos = PAO::where('status', 'active')->get();
        $vehicles = Vehicle::where('status', 'active')->get();
        return view('remittance-clerk.remittances.create', compact('drivers', 'paos', 'vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'pao_id' => 'required|exists:paos,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'remittance_date' => 'required|date',
            'total_collection' => 'required|numeric',
            'total_expenses' => 'required|numeric',
        ]);

        // Get the vehicle and its associated route
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $validated['route_id'] = $vehicle->route_id;
        $validated['net_remittance'] = $validated['total_collection'] - $validated['total_expenses'];
        $validated['status'] = 'pending'; // Set default status to pending
        
        DailyRemittance::create($validated);

        return redirect()->route('remittances.index')->with('success', 'Daily Remittance created successfully.');
    }

    public function show(DailyRemittance $remittance)
    {
        $remittance->load('driver', 'pao', 'route', 'vehicle', 'fares', 'expenses');
        return view('remittance-clerk.remittances.show', compact('remittance'));
    }

    public function edit(DailyRemittance $remittance)
    {
        $drivers = Driver::where('status', 'active')->get();
        $paos = PAO::where('status', 'active')->get();
        $vehicles = Vehicle::where('status', 'active')->get();
        return view('remittance-clerk.remittances.edit', compact('remittance', 'drivers', 'paos', 'vehicles'));
    }

    public function update(Request $request, DailyRemittance $remittance)
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'pao_id' => 'required|exists:paos,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'remittance_date' => 'required|date',
            'total_collection' => 'required|numeric',
            'total_expenses' => 'required|numeric',
        ]);

        // Get the vehicle and its associated route
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $validated['route_id'] = $vehicle->route_id;
        $validated['net_remittance'] = $validated['total_collection'] - $validated['total_expenses'];
        
        // If remittance is approved or rejected, reset status to pending when changes are made
        if (in_array($remittance->status, ['approved', 'rejected'])) {
            $validated['status'] = 'pending';
        }
        
        $remittance->update($validated);

        return redirect()->route('remittances.index')->with('success', 'Daily Remittance updated successfully.');
    }

    public function destroy(DailyRemittance $remittance)
    {
        $remittance->delete();
        return redirect()->route('remittances.index')->with('success', 'Daily Remittance deleted successfully.');
    }
}
