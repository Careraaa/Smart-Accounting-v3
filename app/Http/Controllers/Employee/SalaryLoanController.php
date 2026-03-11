<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalaryLoan;
use Illuminate\Http\Request;

class SalaryLoanController extends Controller
{
    public function index()
    {
        $loans = SalaryLoan::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('employee.salary-loans.index', compact('loans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'loan_amount'       => 'required|numeric|min:1',
            'monthly_deduction' => 'required|numeric|min:1',
            'start_date'        => 'required|date',
            'notes'             => 'nullable|string|max:500',
        ]);

        $hasActive = SalaryLoan::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'active'])
            ->exists();

        if ($hasActive) {
            return redirect()->back()
                ->withErrors(['error' => 'You already have an active or pending salary loan.']);
        }

        SalaryLoan::create([
            'user_id'           => auth()->id(),
            'loan_amount'       => $request->loan_amount,
            'monthly_deduction' => $request->monthly_deduction,
            'remaining_balance' => $request->loan_amount,
            'start_date'        => $request->start_date,
            'notes'             => $request->notes,
            'status'            => 'pending',
        ]);

        return redirect()->route('employee.salary-loans.index')
            ->with('success', 'Salary loan application submitted successfully.');
    }
}