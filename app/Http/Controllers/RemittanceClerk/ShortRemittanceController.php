<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Http\Controllers\Controller;
use App\Exceptions\BoundaryPaymentException;
use App\Models\DailyRemittance;
use App\Services\BoundaryPaymentService;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

    public function recordPayment(Request $request, DailyRemittance $shortRemittance, BoundaryPaymentService $payments)
    {
        if (! $shortRemittance->is_short_remittance) {
            abort(404, 'This remittance is not a short remittance.');
        }

        $validated = $request->validate([
            'payment_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|min:0.01|decimal:0,2|max:9999999999999.99',
            'paid_by' => 'required|in:driver,pao',
            'source_payment_key' => 'required|uuid',
        ]);

        try {
            $payment = $payments->recordPayment(
                $shortRemittance->id,
                $validated['payment_date'],
                (float) $validated['amount'],
                $validated['paid_by'],
                $validated['source_payment_key'],
                (int) auth()->id()
            );
        } catch (BoundaryPaymentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        $this->logActivity(
            'submitted',
            "Boundary Payment #{$payment->id} for Remittance #{$shortRemittance->id}",
            request()->url(),
            'boundary_payment',
            $payment->id
        );

        return redirect()->route('short-remittances.edit', $shortRemittance)
            ->with('success', "Boundary payment BP-{$payment->id} was submitted for accountant approval.");
    }

    public function edit(DailyRemittance $shortRemittance)
    {
        if (!$shortRemittance->is_short_remittance) {
            abort(404, 'This remittance is not a short remittance.');
        }

        $shortRemittance->load('driver', 'pao', 'route', 'vehicle', 'boundaryPayments.recordedBy', 'boundaryPayments.approvedBy');
        $paymentSubmissionKey = (string) Str::uuid();

        return view('remittance-clerk.short-remittances.edit', compact('shortRemittance', 'paymentSubmissionKey'));
    }

    public function update(Request $request, DailyRemittance $shortRemittance)
    {
        if (!$shortRemittance->is_short_remittance) {
            abort(404, 'This remittance is not a short remittance.');
        }

        return redirect()->route('short-remittances.edit', $shortRemittance)
            ->with('error', 'Use the payment form on this page to submit a shortage payment for accountant approval.');
    }
}
