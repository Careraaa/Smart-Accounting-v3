<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Exceptions\BoundaryPaymentException;
use App\Http\Controllers\Controller;
use App\Models\DailyRemittance;
use App\Models\Driver;
use App\Models\PAO;
use App\Models\Route;
use App\Models\Vehicle;
use App\Notifications\RemittanceNotification;
use App\Traits\LogsUserActivity;
use Carbon\Carbon;
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
        $drivers = Driver::where('status', 'active')->get()->sortBy(fn (Driver $driver) => $driver->formatted_name, SORT_NATURAL | SORT_FLAG_CASE)->values();
        $paos = PAO::where('status', 'active')->get()->sortBy(fn (PAO $pao) => $pao->formatted_name, SORT_NATURAL | SORT_FLAG_CASE)->values();
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
            'net_remittance' => 'required|numeric|min:0|decimal:0,2|max:99999999.99',
            'vehicle_status' => 'required|in:active,under_maintenance',
            'is_short_remittance' => 'nullable|boolean',
            'short_amount' => 'nullable|numeric',
            'driver_share' => 'nullable|numeric',
            'pao_share' => 'nullable|numeric',
            'driver_share_amount' => 'nullable|numeric|min:0',
        ]);

        $vehicleStatus = $validated['vehicle_status'];
        unset($validated['vehicle_status']);

        // Get the vehicle and its associated route
        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);

        // Keep the vehicle's status in sync with the clerk's selection
        if ($vehicle->status !== $vehicleStatus) {
            $vehicle->update(['status' => $vehicleStatus]);
        }

        $validated['route_id'] = $vehicle->route_id;

        // Disregard the boundary rate while the vehicle is under maintenance
        $isMaintenance = $vehicleStatus === 'under_maintenance';
        $boundary = $this->calculateBoundaryRate($vehicle, $validated, $isMaintenance);
        $validated['boundary'] = $boundary;
        $validated['total_expenses'] = $this->calculateTotalExpenses($validated);

        // Check if net remittance is less than the boundary
        $netRemittance = $validated['net_remittance'];

        if (! $isMaintenance && $netRemittance < $boundary) {
            $validated['is_short_remittance'] = true;
            $validated['short_amount'] = round($boundary - $netRemittance, 2);
            $driverShareAmount = array_key_exists('driver_share_amount', $validated) && $validated['driver_share_amount'] !== null
                ? (float) $validated['driver_share_amount']
                : $validated['short_amount'] / 2;
            $validated['driver_share'] = round(min($validated['short_amount'], $driverShareAmount), 2);
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
        $drivers = Driver::where('status', 'active')->get()->sortBy(fn (Driver $driver) => $driver->formatted_name, SORT_NATURAL | SORT_FLAG_CASE)->values();
        $paos = PAO::where('status', 'active')->get()->sortBy(fn (PAO $pao) => $pao->formatted_name, SORT_NATURAL | SORT_FLAG_CASE)->values();
        $vehicles = Vehicle::where('status', 'active')->orWhere('id', $remittance->vehicle_id)->get();
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
            'net_remittance' => 'required|numeric|min:0|decimal:0,2|max:99999999.99',
            'vehicle_status' => 'required|in:active,under_maintenance',
            'is_short_remittance' => 'nullable|boolean',
            'short_amount' => 'nullable|numeric',
            'driver_share' => 'nullable|numeric',
            'pao_share' => 'nullable|numeric',
            'driver_share_amount' => 'nullable|numeric|min:0',
        ]);

        $vehicleStatus = $validated['vehicle_status'];
        unset($validated['vehicle_status']);

        try {
            $remittance = \Illuminate\Support\Facades\DB::transaction(function () use ($remittance, $validated, $vehicleStatus) {
                $lockedRemittance = DailyRemittance::whereKey($remittance->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($lockedRemittance->boundaryPayments()->exists()) {
                    throw new BoundaryPaymentException('A remittance with recorded boundary payments cannot be edited. Contact accounting for a correction.');
                }

                $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
                if ($vehicle->status !== $vehicleStatus) {
                    $vehicle->update(['status' => $vehicleStatus]);
                }

                $validated['route_id'] = $vehicle->route_id;
                $isMaintenance = $vehicleStatus === 'under_maintenance';
                $boundary = $this->calculateBoundaryRate($vehicle, $validated, $isMaintenance);
                $validated['boundary'] = $boundary;
                $validated['total_expenses'] = $this->calculateTotalExpenses($validated);

                $netRemittance = $validated['net_remittance'];
                if (!$isMaintenance && $netRemittance < $boundary) {
                    $validated['is_short_remittance'] = true;
                    $validated['short_amount'] = round($boundary - $netRemittance, 2);
                    $driverShareAmount = array_key_exists('driver_share_amount', $validated) && $validated['driver_share_amount'] !== null
                        ? (float) $validated['driver_share_amount']
                        : $validated['short_amount'] / 2;
                    $validated['driver_share'] = round(min($validated['short_amount'], $driverShareAmount), 2);
                    $validated['pao_share'] = round($validated['short_amount'] - $validated['driver_share'], 2);
                } else {
                    $validated['is_short_remittance'] = false;
                    $validated['short_amount'] = null;
                    $validated['driver_share'] = null;
                    $validated['pao_share'] = null;
                }

                if (in_array($lockedRemittance->status, ['approved', 'rejected'], true)) {
                    $validated['status'] = 'pending';
                }

                $lockedRemittance->update($validated);

                return $lockedRemittance->fresh();
            });
        } catch (BoundaryPaymentException $e) {
            return redirect()->route('remittances.show', $remittance)->with('error', $e->getMessage());
        }

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

    private function calculateBoundaryRate(Vehicle $vehicle, array $values, bool $isMaintenance): float
    {
        if ($isMaintenance) {
            return 0;
        }

        $baseBoundary = $vehicle->route?->boundaryForDate(Carbon::parse($values['remittance_date'])) ?? 0;
        $boundaryDeductions = (float) ($values['food_allowance'] ?? 0) + (float) ($values['others'] ?? 0);

        return round(max($baseBoundary - $boundaryDeductions, 0), 2);
    }

    public function destroy(DailyRemittance $remittance)
    {
        try {
            $remittance = \Illuminate\Support\Facades\DB::transaction(function () use ($remittance) {
                $lockedRemittance = DailyRemittance::whereKey($remittance->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();
                if ($lockedRemittance->boundaryPayments()->exists()) {
                    throw new BoundaryPaymentException('A remittance with recorded boundary payments cannot be deleted. Contact accounting for a correction.');
                }

                $lockedRemittance->load('driver', 'pao', 'vehicle');
                $lockedRemittance->delete();

                return $lockedRemittance;
            });
        } catch (BoundaryPaymentException $e) {
            return redirect()->route('remittances.show', $remittance)->with('error', $e->getMessage());
        }

        RemittanceNotification::remittanceDeleted($remittance);

        $this->logActivity('deleted', "Daily Remittance #{$remittance->id}", request()->url(), 'daily_remittance', $remittance->id);

        return redirect()->route('remittances.index')->with('success', 'Daily Remittance deleted successfully.');
    }
}
