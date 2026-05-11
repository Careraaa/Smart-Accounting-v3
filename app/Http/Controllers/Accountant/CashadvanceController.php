<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\CashAdvance;
use App\Notifications\CashAdvanceNotification;
use Illuminate\Http\Request;

class CashAdvanceController extends Controller
{
    public function approve(CashAdvance $cashAdvance)
    {
        if ($cashAdvance->status !== 'pending') {
            return redirect()->back()
                ->withErrors(['error' => 'This request has already been processed.']);
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
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($cashAdvance->status !== 'pending') {
            return redirect()->back()
                ->withErrors(['error' => 'This request has already been processed.']);
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