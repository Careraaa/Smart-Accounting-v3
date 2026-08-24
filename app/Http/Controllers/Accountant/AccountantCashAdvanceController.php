<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\CashAdvance;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;

class AccountantCashAdvanceController extends Controller
{
    use LogsUserActivity;
    /**
     * Show cash advance details page
     */
    public function show(CashAdvance $cashAdvance)
    {
        $this->logActivity('viewed', "Cash Advance #{$cashAdvance->id}", request()->url(), 'cash_advance', $cashAdvance->id);

        $cashAdvance->load(['user', 'approver', 'deductedPayroll']);
        return view('hr.payroll.receivables.cash-advance-detail', compact('cashAdvance'));
    }

    /**
     * Release a cash advance (Accountant action: approved → released)
     */
    public function release(CashAdvance $cashAdvance, Request $request)
    {
        $this->logActivity('updated', "Cash Advance #{$cashAdvance->id} released", request()->url(), 'cash_advance', $cashAdvance->id);

        // Only accountant and superadmin can release
        if (!in_array(auth()->user()->role, ['accountant', 'superadmin'])) {
            abort(403, 'Unauthorized');
        }

        // Can only release if approved
        if ($cashAdvance->status !== 'approved') {
            return back()->with('error', 'Cash advance is not in approved status');
        }

        $cashAdvance->update([
            'status' => 'released',
        ]);

        return back()->with('success', 'Cash advance released successfully');
    }

    /**
     * Reject a cash advance (Accountant action: approved/pending → rejected)
     */
    public function reject(CashAdvance $cashAdvance, Request $request)
    {
        $this->logActivity('rejected', "Cash Advance #{$cashAdvance->id}", request()->url(), 'cash_advance', $cashAdvance->id);

        // Only accountant and superadmin can reject
        if (!in_array(auth()->user()->role, ['accountant', 'superadmin'])) {
            abort(403, 'Unauthorized');
        }

        // Can reject if approved or pending
        if (!in_array($cashAdvance->status, ['approved', 'pending'])) {
            return back()->with('error', 'Cannot reject cash advance in current status');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $cashAdvance->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Cash advance rejected successfully');
    }
}
