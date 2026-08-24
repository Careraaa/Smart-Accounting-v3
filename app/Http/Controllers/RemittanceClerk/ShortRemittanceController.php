<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Http\Controllers\Controller;
use App\Models\DailyRemittance;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;

class ShortRemittanceController extends Controller
{
    use LogsUserActivity;
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
        $allShortRemittances = DailyRemittance::with('driver', 'pao', 'route', 'vehicle')
            ->where('is_short_remittance', true)
            ->orderBy($sortBy, $sortOrder)
            ->get();

        // Separate into pending/partial and fully paid
        $pendingRemittances = $allShortRemittances->filter(function ($remittance) {
            // If both are paid, it's fully paid
            if ($remittance->driver_status === 'paid' && $remittance->pao_status === 'paid') {
                return false;
            }
            return true;
        });
        
        $fullyPaidRemittances = $allShortRemittances->filter(function ($remittance) {
            // Both must be paid to be fully paid
            return $remittance->driver_status === 'paid' && $remittance->pao_status === 'paid';
        });

        // Calculate statistics
        $totalShortRemittances = DailyRemittance::where('is_short_remittance', true)->where('status', 'approved')->count();
        $totalShortAmount = DailyRemittance::where('is_short_remittance', true)->where('status', 'approved')->sum('short_amount');
        $totaldriverShares = DailyRemittance::where('is_short_remittance', true)->where('status', 'approved')->sum('driver_share');
        $totalPaoShares = DailyRemittance::where('is_short_remittance', true)->where('status', 'approved')->sum('pao_share');

        $this->logActivity('viewed', 'Short Remittances list', request()->url(), 'short_remittance');

        return view('remittance-clerk.short-remittances.index', compact(
            'pendingRemittances',
            'fullyPaidRemittances',
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

        $this->logActivity('viewed', "Short Remittance #{$shortRemittance->id}", request()->url(), 'short_remittance', $shortRemittance->id);

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

        $this->logActivity('updated', "Short Remittance #{$shortRemittance->id}", request()->url(), 'short_remittance', $shortRemittance->id);

        return redirect()->route('short-remittances.index')
            ->with('success', 'Short remittance resolution updated successfully.');
    }
}
