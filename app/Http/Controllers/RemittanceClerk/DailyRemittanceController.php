<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Http\Controllers\Controller;
use App\Models\DailyRemittance;
use App\Models\Driver;
use App\Models\PAO;
use App\Models\Route;
use App\Models\Vehicle;
use App\Notifications\RemittanceNotification;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;

class DailyRemittanceController extends Controller
{
    use LogsUserActivity;
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
        
        // Get all pending remittances
        $pendingRemittances = DailyRemittance::with('driver', 'pao', 'route', 'vehicle')
            ->where('status', 'pending')
            ->orderBy($sortBy, $sortOrder)
            ->get();

        // Get all approved remittances
        $approvedRemittances = DailyRemittance::with('driver', 'pao', 'route', 'vehicle')
            ->where('status', 'approved')
            ->orderBy($sortBy, $sortOrder)
            ->get();
        
        // Tab parameter
        $tab = $request->get('tab', 'pending');
        
        // Pre-map data for client-side JS
        $pendingData = $pendingRemittances->map(fn($r) => [
            'id' => $r->id,
            'date' => $r->remittance_date?->format('M d, Y'),
            'route' => $r->route->route_name ?? '—',
            'vehicle' => $r->vehicle->plate_number ?? '—',
            'net' => (float) $r->net_remittance,
            'is_short' => (bool) $r->is_short_remittance,
            'status' => 'pending',
            'show_url' => route('remittances.show', $r),
        ])->values();
        
        $approvedData = $approvedRemittances->map(fn($r) => [
            'id' => $r->id,
            'date' => $r->remittance_date?->format('M d, Y'),
            'route' => $r->route->route_name ?? '—',
            'vehicle' => $r->vehicle->plate_number ?? '—',
            'net' => (float) $r->net_remittance,
            'is_short' => (bool) $r->is_short_remittance,
            'status' => 'approved',
            'show_url' => route('remittances.show', $r),
        ])->values();
        
        // Calculate statistics
        $totalRemittances = DailyRemittance::count();
        $approvedCount = DailyRemittance::where('status', 'approved')->count();
        $pendingCount = DailyRemittance::where('status', 'pending')->count();
        $rejectedRemittances = DailyRemittance::where('status', 'rejected')->count();
        
        $this->logActivity('viewed', 'Daily Remittances list', request()->url(), 'daily_remittance');

        return view('remittance-clerk.remittances.index', compact('pendingRemittances', 'approvedRemittances', 'pendingData', 'approvedData', 'sortBy', 'sortOrder', 'totalRemittances', 'approvedCount', 'pendingCount', 'rejectedRemittances', 'tab'));
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
            'diesel' => 'nullable|numeric|min:0',
            'parking' => 'nullable|numeric|min:0',
            'dispatcher' => 'nullable|numeric|min:0',
            'food_allowance' => 'nullable|numeric|min:0',
            'barker' => 'nullable|numeric|min:0',
            'others' => 'nullable|numeric|min:0',
            'net_remittance' => 'required|numeric',
            'is_short_remittance' => 'nullable|boolean',
            'short_amount' => 'nullable|numeric',
            'driver_share' => 'nullable|numeric',
            'pao_share' => 'nullable|numeric',
            'driver_share_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        // Get the vehicle and its associated route
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $validated['route_id'] = $vehicle->route_id;
        $boundary = $vehicle->route->boundary ?? 0;
        $validated['boundary'] = $boundary;
        $validated['total_expenses'] = $this->calculateTotalExpenses($validated);

        // Check if net remittance is less than the boundary
        $netRemittance = $validated['net_remittance'];
        
        if ($netRemittance < $boundary) {
            $validated['is_short_remittance'] = true;
            $validated['short_amount'] = round($boundary - $netRemittance, 2);
            $driverSharePercent = (float) ($validated['driver_share_percent'] ?? 50);
            $validated['driver_share'] = round($validated['short_amount'] * ($driverSharePercent / 100), 2);
            $validated['pao_share'] = round($validated['short_amount'] - $validated['driver_share'], 2);
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

        $this->logActivity('created', "Daily Remittance #{$remittance->id} - {$remittance->remittance_date?->format('Y-m-d')}", request()->url(), 'daily_remittance', $remittance->id);

        return redirect()->route('remittances.index')->with('success', 'Daily Remittance created successfully.');
    }

    public function show(DailyRemittance $remittance)
    {
        $remittance->load('driver', 'pao', 'route', 'vehicle');

        $this->logActivity('viewed', "Daily Remittance #{$remittance->id}", request()->url(), 'daily_remittance', $remittance->id);

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
            'diesel' => 'nullable|numeric|min:0',
            'parking' => 'nullable|numeric|min:0',
            'dispatcher' => 'nullable|numeric|min:0',
            'food_allowance' => 'nullable|numeric|min:0',
            'barker' => 'nullable|numeric|min:0',
            'others' => 'nullable|numeric|min:0',
            'net_remittance' => 'required|numeric',
            'is_short_remittance' => 'nullable|boolean',
            'short_amount' => 'nullable|numeric',
            'driver_share' => 'nullable|numeric',
            'pao_share' => 'nullable|numeric',
            'driver_share_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        // Get the vehicle and its associated route
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        $validated['route_id'] = $vehicle->route_id;
        $boundary = $vehicle->route->boundary ?? 0;
        $validated['boundary'] = $boundary;
        $validated['total_expenses'] = $this->calculateTotalExpenses($validated);

        // Check if net remittance is less than the boundary
        $netRemittance = $validated['net_remittance'];
        
        if ($netRemittance < $boundary) {
            $validated['is_short_remittance'] = true;
            $validated['short_amount'] = round($boundary - $netRemittance, 2);
            $driverSharePercent = (float) ($validated['driver_share_percent'] ?? 50);
            $validated['driver_share'] = round($validated['short_amount'] * ($driverSharePercent / 100), 2);
            $validated['pao_share'] = round($validated['short_amount'] - $validated['driver_share'], 2);
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

        $this->logActivity('updated', "Daily Remittance #{$remittance->id}", request()->url(), 'daily_remittance', $remittance->id);

        return redirect()->route('remittances.index')->with('success', 'Daily Remittance updated successfully.');
    }

    private function calculateTotalExpenses(array $values): float
    {
        return round(collect(['diesel', 'parking', 'dispatcher', 'food_allowance', 'barker', 'others'])
            ->sum(fn ($field) => (float) ($values[$field] ?? 0)), 2);
    }

    public function destroy(DailyRemittance $remittance)
    {
        $remittance->load('driver', 'pao', 'vehicle');
        
        RemittanceNotification::remittanceDeleted($remittance);

        $this->logActivity('deleted', "Daily Remittance #{$remittance->id}", request()->url(), 'daily_remittance', $remittance->id);
        
        $remittance->delete();
        return redirect()->route('remittances.index')->with('success', 'Daily Remittance deleted successfully.');
    }
}
