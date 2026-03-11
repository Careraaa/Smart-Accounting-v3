<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\CashAdvance;
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
            'amount'       => 'required|numeric|min:1',
            'request_date' => 'required|date',
            'notes'        => 'nullable|string|max:500',
        ]);

        $hasPending = CashAdvance::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return redirect()->back()
                ->withErrors(['error' => 'You already have a pending cash advance request.']);
        }

        CashAdvance::create([
            'user_id'      => auth()->id(),
            'amount'       => $request->amount,
            'request_date' => $request->request_date,
            'notes'        => $request->notes,
            'status'       => 'pending',
        ]);

        return redirect()->route('employee.cash-advances.index')
            ->with('success', 'Cash advance request submitted successfully.');
    }
}