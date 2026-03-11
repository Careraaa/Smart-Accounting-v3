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
        $tab = $request->get('tab', 'payroll');

        $payrolls     = collect();
        $cashAdvances = collect();
        $salaryLoans  = collect();

        if ($tab === 'payroll') {
            $payrolls = Payroll::with('user')
                ->whereIn('status', ['approved', 'paid'])
                ->orderBy('payroll_period_start', 'desc')
                ->paginate(15)
                ->withQueryString();
        }

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

        return view('hr.payroll.receivables.index', compact(
            'tab', 'payrolls', 'cashAdvances', 'salaryLoans'
        ));
    }

    public function markAsPaid(Request $request, Payroll $payroll)
    {
        $request->validate([
            'payment_date' => 'required|date',
        ]);

        $payroll->update([
            'status'       => 'paid',
            'payment_date' => $request->payment_date,
        ]);

        return redirect()->route('payroll.receivables.index', ['tab' => 'payroll'])
            ->with('success', 'Payroll marked as paid.');
    }

    public function markBatchPaid(Request $request)
    {
        $request->validate([
            'payroll_ids'   => 'required|array',
            'payroll_ids.*' => 'integer|exists:payrolls,id',
            'payment_date'  => 'required|date',
        ]);

        Payroll::whereIn('id', $request->payroll_ids)
            ->where('status', 'approved')
            ->update([
                'status'       => 'paid',
                'payment_date' => $request->payment_date,
            ]);

        return redirect()->route('payroll.receivables.index', ['tab' => 'payroll'])
            ->with('success', 'Selected payrolls marked as paid.');
    }
}