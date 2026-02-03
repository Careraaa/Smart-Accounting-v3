<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\DailyRemittance;
use App\Models\Payroll;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function remittanceReports()
    {
        $remittances = DailyRemittance::with('driver', 'pao', 'route')->get();
        return view('accounting.reports.remittance', compact('remittances'));
    }

    public function payrollReports()
    {
        $payrolls = Payroll::with('employee')->where('status', 'paid')->get();
        return view('accounting.reports.payroll', compact('payrolls'));
    }

    public function payslips()
    {
        $payrolls = Payroll::with('employee')->where('status', 'paid')->get();
        return view('accounting.reports.payslips', compact('payrolls'));
    }

    public function payrollSummary()
    {
        $payrolls = Payroll::with('employee')->get();
        return view('accounting.reports.payroll-summary', compact('payrolls'));
    }

    public function deductionSummary()
    {
        $payrolls = Payroll::with('deductions')->get();
        return view('accounting.reports.deduction-summary', compact('payrolls'));
    }

    public function governmentContributionSummary()
    {
        $payrolls = Payroll::with('deductions')->get();
        return view('accounting.reports.government-contribution-summary', compact('payrolls'));
    }
}
