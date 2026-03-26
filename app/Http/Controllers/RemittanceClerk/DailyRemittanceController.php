<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Http\Controllers\Controller;
use App\Models\DailyRemittance;
use App\Models\Driver;
use App\Models\PAO;
use App\Models\Route;
use App\Models\Vehicle;
use App\Notifications\RemittanceNotification;
use Illuminate\Http\Request;

class DailyRemittanceController extends Controller
{
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'remittance_date');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Whitelist allowed columns to prevent SQL injection
        $allowedColumns = ['remittance_date', 'net_remittance', 'status'];
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'remittance_date';
        }
        
        // Validate sort order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }
        
        $allRemittances = DailyRemittance::with('driver', 'pao', 'route', 'vehicle')
            ->orderBy($sortBy, $sortOrder)->get();
        
        // Separate remittances by status
        $pendingRemittances = $allRemittances->filter(function ($remittance) {
            return $remittance->status === 'pending';
        });
        
        $approvedRemittances = $allRemittances->filter(function ($remittance) {
            return $remittance->status === 'approved';
        });
        
        // Calculate statistics
        $totalRemittances = DailyRemittance::count();
        $approvedCount = DailyRemittance::where('status', 'approved')->count();
        $pendingCount = DailyRemittance::where('status', 'pending')->count();
        $rejectedRemittances = DailyRemittance::where('status', 'rejected')->count();
        
        return view('remittance-clerk.remittances.index', compact('pendingRemittances', 'approvedRemittances', 'sortBy', 'sortOrder', 'totalRemittances', 'approvedCount', 'pendingCount', 'rejectedRemittances'));
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
            'net_remittance' => 'required|numeric',
            'is_short_remittance' => 'nullable|boolean',
            'short_amount' => 'nullable|numeric',
            'driver_share' => 'nullable|numeric',
            'pao_share' => 'nullable|numeric',
        ]);

        // Get the vehicle and its associated route
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $validated['route_id'] = $vehicle->route_id;
        $boundary = $vehicle->route->boundary ?? 0;
        $validated['boundary'] = $boundary;

        // Calculate net remittance for short remittance detection: total_collection - total_expenses - boundary
        $calculatedNetRemittance = $validated['total_collection'] - $validated['total_expenses'] - $boundary;
        
        // Keep the inputted net remittance value for display
        // But use the calculated value to determine if it's a short remittance
        if ($calculatedNetRemittance < 0) {
            $validated['is_short_remittance'] = true;
            $validated['short_amount'] = abs($calculatedNetRemittance);
            $validated['driver_share'] = abs($calculatedNetRemittance) / 2;
            $validated['pao_share'] = abs($calculatedNetRemittance) / 2;
        } else {
            $validated['is_short_remittance'] = false;
            $validated['short_amount'] = null;
            $validated['driver_share'] = null;
            $validated['pao_share'] = null;
        }

        $validated['status'] = 'pending'; // Set default status to pending
        
        $remittance = DailyRemittance::create($validated);
        $remittance->load('driver', 'pao', 'vehicle');

        RemittanceNotification::remittanceCreated($remittance);

        return redirect()->route('remittances.index')->with('success', 'Daily Remittance created successfully.');
    }

    public function show(DailyRemittance $remittance)
    {
        $remittance->load('driver', 'pao', 'route', 'vehicle');
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
            'net_remittance' => 'required|numeric',
            'is_short_remittance' => 'nullable|boolean',
            'short_amount' => 'nullable|numeric',
            'driver_share' => 'nullable|numeric',
            'pao_share' => 'nullable|numeric',
        ]);

        // Get the vehicle and its associated route
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $validated['route_id'] = $vehicle->route_id;
        $boundary = $vehicle->route->boundary ?? 0;
        $validated['boundary'] = $boundary;

        // Calculate net remittance for short remittance detection: total_collection - total_expenses - boundary
        $calculatedNetRemittance = $validated['total_collection'] - $validated['total_expenses'] - $boundary;
        
        // Keep the inputted net remittance value for display
        // But use the calculated value to determine if it's a short remittance
        if ($calculatedNetRemittance < 0) {
            $validated['is_short_remittance'] = true;
            $validated['short_amount'] = abs($calculatedNetRemittance);
            $validated['driver_share'] = abs($calculatedNetRemittance) / 2;
            $validated['pao_share'] = abs($calculatedNetRemittance) / 2;
        } else {
            $validated['is_short_remittance'] = false;
            $validated['short_amount'] = null;
            $validated['driver_share'] = null;
            $validated['pao_share'] = null;
        }
        
        // If remittance is approved or rejected, reset status to pending when changes are made
        if (in_array($remittance->status, ['approved', 'rejected'])) {
            $validated['status'] = 'pending';
        }
        
        $remittance->update($validated);
        $remittance->load('driver', 'pao', 'vehicle');

        RemittanceNotification::remittanceUpdated($remittance);

        return redirect()->route('remittances.index')->with('success', 'Daily Remittance updated successfully.');
    }

    public function destroy(DailyRemittance $remittance)
    {
        $remittance->load('driver', 'pao', 'vehicle');
        
        RemittanceNotification::remittanceDeleted($remittance);
        
        $remittance->delete();
        return redirect()->route('remittances.index')->with('success', 'Daily Remittance deleted successfully.');
    }
}
