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
            return (float) $remittance->short_amount > 0;
        });
        
        $fullyPaidRemittances = $allShortRemittances->filter(function ($remittance) {
            return (float) $remittance->short_amount <= 0;
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

        $shortRemittance->load('driver', 'pao', 'route', 'vehicle', 'boundaryPayments.recordedBy', 'boundaryPayments.approvedBy');

        $this->logActivity('viewed', "Short Remittance #{$shortRemittance->id}", request()->url(), 'short_remittance', $shortRemittance->id);

        return view('remittance-clerk.short-remittances.show', compact('shortRemittance'));
    }

    public function edit(DailyRemittance $shortRemittance)
    {
        if (!$shortRemittance->is_short_remittance) {
            abort(404, 'This remittance is not a short remittance.');
        }

        $shortRemittance->load('driver', 'pao', 'route', 'vehicle', 'boundaryPayments.recordedBy', 'boundaryPayments.approvedBy');
        return view('remittance-clerk.short-remittances.edit', compact('shortRemittance'));
    }

    public function update(Request $request, DailyRemittance $shortRemittance)
    {
        if (!$shortRemittance->is_short_remittance) {
            abort(404, 'This remittance is not a short remittance.');
        }

        return redirect()->route('short-remittances.show', $shortRemittance)
            ->with('error', 'Shortage payments must be recorded and verified through Accountant Remittance Approval. This screen cannot mark payments as received.');
    }
}
