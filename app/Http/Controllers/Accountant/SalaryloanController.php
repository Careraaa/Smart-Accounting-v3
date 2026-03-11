<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\SalaryLoan;
use Illuminate\Http\Request;

class SalaryLoanController extends Controller
{
    public function approve(SalaryLoan $salaryLoan)
    {
        if ($salaryLoan->status !== 'pending') {
            return redirect()->back()
                ->withErrors(['error' => 'This request has already been processed.']);
        }

        $salaryLoan->update([
            'status'      => 'active',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $name = $salaryLoan->user->name ?? 'Employee';

        return redirect()->back()
            ->with('success', "Salary loan for {$name} approved. Monthly deduction of ₱" . number_format($salaryLoan->monthly_deduction, 2) . " will begin on their next payroll.");
    }

    public function reject(Request $request, SalaryLoan $salaryLoan)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($salaryLoan->status !== 'pending') {
            return redirect()->back()
                ->withErrors(['error' => 'This request has already been processed.']);
        }

        $salaryLoan->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Salary loan request rejected.');
    }
}