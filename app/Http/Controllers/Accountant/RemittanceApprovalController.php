<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Exceptions\BoundaryPaymentException;
use App\Models\DailyRemittance;
use App\Notifications\RemittanceNotification;
use App\Services\BoundaryPaymentService;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RemittanceApprovalController extends Controller
{
    use LogsUserActivity;
    public function index()
    {
        $this->logActivity('viewed', 'Remittance Approval List', request()->url());

        $remittances = DailyRemittance::with('driver', 'pao', 'route', 'vehicle')
            ->orderByDesc('remittance_date')
            ->get();
        return view('accountant.remittances-approval.index', compact('remittances'));
    }

    public function show(DailyRemittance $remittance)
    {
        $this->logActivity('viewed', "Remittance #{$remittance->id}", request()->url(), 'daily_remittance', $remittance->id);

        $remittance->load('driver', 'pao', 'route', 'vehicle', 'boundaryPayments.journalEntry', 'boundaryPayments.recordedBy', 'boundaryPayments.approvedBy');
        $paymentSubmissionKey = (string) Str::uuid();
        return view('accountant.remittances-approval.show', compact('remittance', 'paymentSubmissionKey'));
    }

    public function approve(Request $request, DailyRemittance $remittance, BoundaryPaymentService $payments)
    {
        $validated = $request->validate([
            'boundary_payment_amount' => 'nullable|numeric|min:0|decimal:0,2|max:99999999.99',
            'boundary_payment_date' => 'nullable|date|before_or_equal:today',
            'boundary_paid_by' => 'nullable|in:driver,pao,joint',
            'confirm_boundary_received' => 'sometimes|accepted',
        ]);
        $amount = (float) ($validated['boundary_payment_amount'] ?? 0);

        if ($amount > 0) {
            $request->validate([
                'boundary_payment_date' => 'required|date|before_or_equal:today',
                'boundary_paid_by' => 'required|in:driver,pao,joint',
                'confirm_boundary_received' => 'required|accepted',
            ]);
        }

        try {
            [$remittance, $approvedNow] = $payments->approveRemittance(
                $remittance->id,
                $amount > 0 ? $validated['boundary_payment_date'] : null,
                $amount,
                $validated['boundary_paid_by'] ?? 'joint',
                (int) auth()->id()
            );
        } catch (BoundaryPaymentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        if (!$approvedNow) {
            if ($remittance->status === 'approved') {
                return redirect()->back()->with('success', 'Remittance was already approved; no duplicate payment was posted.');
            }

            return redirect()->back()->with('error', 'Only pending remittances can be approved.');
        }

        $this->logActivity('approved', "Remittance #{$remittance->id}", request()->url(), 'daily_remittance', $remittance->id);
        $remittance->load('driver', 'pao', 'vehicle');
        RemittanceNotification::remittanceApproved($remittance);

        return redirect()->back()->with('success', $amount > 0
            ? 'Remittance approved and confirmed boundary payment posted successfully.'
            : 'Remittance approved. No boundary payment was recorded.');
    }

    public function recordPayment(Request $request, DailyRemittance $remittance, BoundaryPaymentService $payments)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|min:0.01|decimal:0,2|max:9999999999999.99',
            'paid_by' => 'required|in:driver,pao,joint',
            'source_payment_key' => 'required|uuid',
            'confirm_boundary_received' => 'required|accepted',
        ]);

        try {
            $payment = $payments->recordPayment(
                $remittance->id,
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
            'posted',
            "Boundary Payment #{$payment->id} for Remittance #{$remittance->id}",
            request()->url(),
            'boundary_payment',
            $payment->id
        );

        return redirect()->back()->with('success', "Boundary payment BP-{$payment->id} was recorded and posted.");
    }

    public function reject(Request $request, DailyRemittance $remittance)
    {
        $rejected = \Illuminate\Support\Facades\DB::transaction(function () use ($remittance) {
            $lockedRemittance = DailyRemittance::whereKey($remittance->getKey())->lockForUpdate()->firstOrFail();
            if ($lockedRemittance->status !== 'pending') {
                return false;
            }
            $lockedRemittance->update(['status' => 'rejected']);

            return true;
        });

        if (!$rejected) {
            return redirect()->back()->with('error', 'Only pending remittances can be rejected.');
        }

        $this->logActivity('rejected', "Remittance #{$remittance->id}", request()->url(), 'daily_remittance', $remittance->id);

        return redirect()->back()->with('success', 'Remittance rejected successfully.');
    }
}
