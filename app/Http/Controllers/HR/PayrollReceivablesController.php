<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\CashAdvance;
use App\Models\SalaryLoan;
use Illuminate\Http\Request;

class PayrollReceivablesController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'cash_advances');

        // Load ALL data upfront so tabs can switch client-side
        $allCashAdvances = CashAdvance::with(['user', 'approver', 'deductedPayroll'])
            ->orderBy('created_at', 'desc')
            ->get();

        $allSalaryLoans = SalaryLoan::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Stats: count by status
        $caPendingCount    = CashAdvance::where('status', 'pending')->count();
        $caApprovedCount   = CashAdvance::where('status', 'approved')->count();
        $caReleasedTotal   = CashAdvance::where('status', 'released')->sum('amount') ?? 0;

        $loanPendingCount   = SalaryLoan::where('status', 'pending')->count();
        $loanApprovedCount  = SalaryLoan::where('status', 'approved')->count();
        $loanReleasedTotal  = SalaryLoan::where('status', 'released')->sum('loan_amount') ?? 0;

        $totalCashAdvances = CashAdvance::count();
        $totalSalaryLoans = SalaryLoan::count();

        // Outstanding amount = approved and released remaining balances + approved and released cash advances
        $approvedLoanRemaining = SalaryLoan::whereIn('status', ['approved', 'released'])->sum('remaining_balance') ?? 0;
        $approvedCashNotReleased = CashAdvance::where('status', 'approved')->sum('amount') ?? 0;
        $outstandingAmount = '₱' . number_format($approvedLoanRemaining + $approvedCashNotReleased, 2);

        return view('hr.payroll.receivables.index', compact(
            'tab', 'allCashAdvances', 'allSalaryLoans',
            'caPendingCount', 'caApprovedCount', 'caReleasedTotal',
            'loanPendingCount', 'loanApprovedCount', 'loanReleasedTotal',
            'totalCashAdvances', 'totalSalaryLoans', 'outstandingAmount'
        ));
    }

    /**
     * Show cash advance details page
     */
    public function showCashAdvance(CashAdvance $cashAdvance)
    {
        $cashAdvance->load(['user', 'approver', 'deductedPayroll']);
        return view('hr.payroll.receivables.cash-advance-detail', compact('cashAdvance'));
    }

    /**
     * Show salary loan details page
     */
    public function showSalaryLoan(SalaryLoan $salaryLoan)
    {
        $salaryLoan->load('user');
        return view('hr.payroll.receivables.salary-loan-detail', compact('salaryLoan'));
    }

    /**
     * Approve a cash advance (HR action: pending → approved)
     */
    public function approveCashAdvance(CashAdvance $cashAdvance, Request $request)
    {
        // Only HR and superadmin can approve
        if (!in_array(auth()->user()->role, ['hr', 'superadmin'])) {
            abort(403, 'Unauthorized');
        }

        // Can only approve if pending
        if ($cashAdvance->status !== 'pending') {
            return back()->with('error', 'Cash advance is not in pending status');
        }

        $cashAdvance->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Cash advance approved successfully');
    }

    /**
     * Reject a cash advance (HR action: pending → rejected)
     */
    public function rejectCashAdvance(CashAdvance $cashAdvance, Request $request)
    {
        // Only HR and superadmin can reject
        if (!in_array(auth()->user()->role, ['hr', 'superadmin'])) {
            abort(403, 'Unauthorized');
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

    /**
     * Approve a salary loan (HR action: pending → approved)
     */
    public function approveSalaryLoan(SalaryLoan $salaryLoan, Request $request)
    {
        // Only HR and superadmin can approve
        if (!in_array(auth()->user()->role, ['hr', 'superadmin'])) {
            abort(403, 'Unauthorized');
        }

        // Can only approve if pending
        if ($salaryLoan->status !== 'pending') {
            return back()->with('error', 'Salary loan is not in pending status');
        }

        $salaryLoan->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Salary loan approved successfully');
    }

    /**
     * Reject a salary loan (HR action: pending → rejected)
     */
    public function rejectSalaryLoan(SalaryLoan $salaryLoan, Request $request)
    {
        // Only HR and superadmin can reject
        if (!in_array(auth()->user()->role, ['hr', 'superadmin'])) {
            abort(403, 'Unauthorized');
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