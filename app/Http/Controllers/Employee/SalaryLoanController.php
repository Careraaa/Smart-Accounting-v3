<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalaryLoan;
use App\Notifications\SalaryLoanNotification;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;

class SalaryLoanController extends Controller
{
    use LogsUserActivity;
    public function index()
    {
        $loans = SalaryLoan::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $this->logActivity('viewed', 'Salary Loan', request()->url());

        return view('employee.salary-loans.index', compact('loans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'loan_amount'  => 'required|numeric|min:1',
            'total_months' => 'required|integer|min:1|max:48',
            'notes'        => 'nullable|string|max:500',
        ]);

        $hasActive = SalaryLoan::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'active'])
            ->exists();

        if ($hasActive) {
            return redirect()->back()
                ->withErrors(['error' => 'You already have an active or pending salary loan.']);
        }

        $monthlyDeduction = round($request->loan_amount / $request->total_months, 2);

        $salaryLoan = SalaryLoan::create([
            'user_id'           => auth()->id(),
            'loan_amount'       => $request->loan_amount,
            'total_months'      => $request->total_months,
            'monthly_deduction' => $monthlyDeduction,
            'remaining_balance' => $request->loan_amount,
            'start_date'        => now()->toDateString(),
            'notes'             => $request->notes,
            'status'            => 'pending',
        ]);

        SalaryLoanNotification::submitted($salaryLoan);

        $this->logActivity('submitted', 'Salary Loan ' . $salaryLoan->id, request()->url(), 'salary_loan', $salaryLoan->id);

        return redirect()->route('employee.salary-loans.index')
            ->with('success', 'Salary loan application submitted successfully.');
    }
}