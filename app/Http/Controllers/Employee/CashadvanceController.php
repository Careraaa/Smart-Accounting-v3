<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\CashAdvance;
use App\Notifications\CashAdvanceNotification;
use Illuminate\Http\Request;

class CashAdvanceController extends Controller
{
    public function index()
    {
        $advances = CashAdvance::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('employee.cash-advances.index', compact('advances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount'            => 'required|numeric|min:1',
            'repayment_months'  => 'required|integer|min:1|max:12',
            'notes'             => 'nullable|string|max:500',
        ]);

        $hasPending = CashAdvance::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return redirect()->back()
                ->withErrors(['error' => 'You already have a pending cash advance request.']);
        }

        $monthlyDeduction = round($request->amount / $request->repayment_months, 2);

        $cashAdvance = CashAdvance::create([
            'user_id'           => auth()->id(),
            'amount'            => $request->amount,
            'repayment_months'  => $request->repayment_months,
            'monthly_deduction' => $monthlyDeduction,
            'request_date'      => now()->toDateString(),
            'notes'             => $request->notes,
            'status'            => 'pending',
        ]);

        CashAdvanceNotification::submitted($cashAdvance);

        return redirect()->route('employee.cash-advances.index')
            ->with('success', 'Cash advance request submitted successfully.');
    }
}