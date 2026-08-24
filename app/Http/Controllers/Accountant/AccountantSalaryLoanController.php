<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\SalaryLoan;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;

class AccountantSalaryLoanController extends Controller
{
    use LogsUserActivity;
    /**
     * Show salary loan details page
     */
    public function show(SalaryLoan $salaryLoan)
    {
        $this->logActivity('viewed', "Salary Loan #{$salaryLoan->id}", request()->url(), 'salary_loan', $salaryLoan->id);

        $salaryLoan->load('user');
        return view('hr.payroll.receivables.salary-loan-detail', compact('salaryLoan'));
    }

    /**
     * Release a salary loan (Accountant action: approved → released)
     */
    public function release(SalaryLoan $salaryLoan, Request $request)
    {
        $this->logActivity('updated', "Salary Loan #{$salaryLoan->id} released", request()->url(), 'salary_loan', $salaryLoan->id);

        // Only accountant and superadmin can release
        if (!in_array(auth()->user()->role, ['accountant', 'superadmin'])) {
            abort(403, 'Unauthorized');
        }

        // Can only release if approved
        if ($salaryLoan->status !== 'approved') {
            return back()->with('error', 'Salary loan is not in approved status');
        }

        $salaryLoan->update([
            'status' => 'released',
        ]);

        return back()->with('success', 'Salary loan released successfully');
    }

    /**
     * Reject a salary loan (Accountant action: approved/pending → rejected)
     */
    public function reject(SalaryLoan $salaryLoan, Request $request)
    {
        $this->logActivity('rejected', "Salary Loan #{$salaryLoan->id}", request()->url(), 'salary_loan', $salaryLoan->id);

        // Only accountant and superadmin can reject
        if (!in_array(auth()->user()->role, ['accountant', 'superadmin'])) {
            abort(403, 'Unauthorized');
        }

        // Can reject if approved or pending
        if (!in_array($salaryLoan->status, ['approved', 'pending'])) {
            return back()->with('error', 'Cannot reject salary loan in current status');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $salaryLoan->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Salary loan rejected successfully');
    }
}
