<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Http\Controllers\Controller;
use App\Models\DailyRemittance;
use Illuminate\Http\Request;

class ShortRemittanceController extends Controller
{
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'remittance_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $status = $request->get('status', 'all');
        
        // Whitelist allowed columns to prevent SQL injection
        $allowedColumns = ['remittance_date', 'short_amount', 'status'];
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'remittance_date';
        }
        
        // Validate sort order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        // Query short remittances only
        $query = DailyRemittance::with('driver', 'pao', 'route', 'vehicle')
            ->where('is_short_remittance', true);

        // Filter by resolution status if specified
        if ($status !== 'all') {
            // In your actual implementation, you may want to add a 'short_status' field
            // For now, we'll show all short remittances
        }

        $shortRemittances = $query->orderBy($sortBy, $sortOrder)->get();

        // Calculate statistics
        $totalShortRemittances = DailyRemittance::where('is_short_remittance', true)->count();
        $totalShortAmount = DailyRemittance::where('is_short_remittance', true)->sum('short_amount');
        $totaldriverShares = DailyRemittance::where('is_short_remittance', true)->sum('driver_share');
        $totalPaoShares = DailyRemittance::where('is_short_remittance', true)->sum('pao_share');

        return view('remittance-clerk.short-remittances.index', compact(
            'shortRemittances',
            'sortBy',
            'sortOrder',
            'status',
            'totalShortRemittances',
            'totalShortAmount',
            'totaldriverShares',
            'totalPaoShares'
        ));
    }

    public function show(DailyRemittance $shortRemittance)
    {
        if (!$shortRemittance->is_short_remittance) {
            abort(404, 'This remittance is not a short remittance.');
        }

        $shortRemittance->load('driver', 'pao', 'route', 'vehicle');
        return view('remittance-clerk.short-remittances.show', compact('shortRemittance'));
    }

    public function edit(DailyRemittance $shortRemittance)
    {
        if (!$shortRemittance->is_short_remittance) {
            abort(404, 'This remittance is not a short remittance.');
        }

        $shortRemittance->load('driver', 'pao', 'route', 'vehicle');
        return view('remittance-clerk.short-remittances.edit', compact('shortRemittance'));
    }

    public function update(Request $request, DailyRemittance $shortRemittance)
    {
        if (!$shortRemittance->is_short_remittance) {
            abort(404, 'This remittance is not a short remittance.');
        }

        $validated = $request->validate([
            'driver_amount_paid' => 'required|numeric|min:0|max:' . $shortRemittance->driver_share,
            'driver_status' => 'required|in:pending,partial,paid',
            'pao_amount_paid' => 'required|numeric|min:0|max:' . $shortRemittance->pao_share,
            'pao_status' => 'required|in:pending,partial,paid',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Update short remittance with payment tracking information
        $shortRemittance->update([
            'driver_amount_paid' => $validated['driver_amount_paid'],
            'driver_status' => $validated['driver_status'],
            'pao_amount_paid' => $validated['pao_amount_paid'],
            'pao_status' => $validated['pao_status'],
            'resolution_notes' => $validated['notes'],
            'resolved_at' => now(),
        ]);

        return redirect()->route('short-remittances.index')
            ->with('success', 'Short remittance resolution updated successfully.');
    }
}
