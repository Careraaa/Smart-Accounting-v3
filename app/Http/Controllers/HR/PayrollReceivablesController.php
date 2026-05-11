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

        $cashAdvances = collect();
        $salaryLoans  = collect();

        if ($tab === 'cash_advances') {
            $cashAdvances = CashAdvance::with(['user', 'approver', 'deductedPayroll'])
                ->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString();
        }

        if ($tab === 'salary_loans') {
            $salaryLoans = SalaryLoan::with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString();
        }

        // Stats are always loaded regardless of active tab,
        // and only count approved/active records (not pending or rejected).
        $caApprovedTotal   = CashAdvance::whereIn('status', ['approved', 'deducted'])->sum('amount') ?? 0;
        $caApprovedCount   = CashAdvance::whereIn('status', ['approved', 'deducted'])->count();
        $caPendingCount    = CashAdvance::where('status', 'pending')->count();

        $loanApprovedTotal = SalaryLoan::where('status', 'active')->sum('loan_amount') ?? 0;
        $loanActiveCount   = SalaryLoan::where('status', 'active')->count();

        return view('hr.payroll.receivables.index', compact(
            'tab', 'cashAdvances', 'salaryLoans',
            'caApprovedTotal', 'caApprovedCount', 'caPendingCount',
            'loanApprovedTotal', 'loanActiveCount'
        ));
    }
}