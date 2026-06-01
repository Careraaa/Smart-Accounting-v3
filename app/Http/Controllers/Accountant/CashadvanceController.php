<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\CashAdvance;
use App\Notifications\CashAdvanceNotification;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;

class CashAdvanceController extends Controller
{
    use LogsUserActivity;
    public function approve(CashAdvance $cashAdvance)
    {
        $this->logActivity('approved', "Cash Advance #{$cashAdvance->id} for {$cashAdvance->user->name}", request()->url(), 'cash_advance', $cashAdvance->id);

        if ($cashAdvance->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This request has already been processed.');
        }

        $cashAdvance->update([
            'status'        => 'approved',
            'approved_by'   => auth()->id(),
            'approved_at'   => now(),
            'approval_date' => now()->toDateString(),
        ]);

        $name = $cashAdvance->user->name ?? 'Employee';

        CashAdvanceNotification::approved($cashAdvance);

        return redirect()->back()
            ->with('success', "Cash advance for {$name} approved. It will be deducted on their next payroll.");
    }

    public function reject(Request $request, CashAdvance $cashAdvance)
    {
        $this->logActivity('rejected', "Cash Advance #{$cashAdvance->id} for {$cashAdvance->user->name}", request()->url(), 'cash_advance', $cashAdvance->id);

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($cashAdvance->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This request has already been processed.');
        }

        $cashAdvance->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
        ]);

        CashAdvanceNotification::rejected($cashAdvance);

        return redirect()->back()
            ->with('success', 'Cash advance request rejected.');
    }
}