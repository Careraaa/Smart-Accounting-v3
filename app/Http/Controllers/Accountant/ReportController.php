<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\DailyRemittance;
use App\Models\Payroll;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function remittanceReports()
    {
        $remittances = DailyRemittance::with('driver', 'pao', 'route')->get();
        return view('accountant.reports.remittance', compact('remittances'));
    }

    public function payrollReports()
    {
        // Return summary of approved payroll batches for the payroll reports page
        $approvedBatches = Payroll::where('status', 'approved')
            ->select('payroll_period_start', 'payroll_period_end')
            ->distinct()
            ->orderBy('payroll_period_start', 'desc')
            ->get();

        $batchData = [];
        foreach ($approvedBatches as $batch) {
            $payrolls = Payroll::with('user')
                ->whereDate('payroll_period_start', $batch->payroll_period_start)
                ->whereDate('payroll_period_end', $batch->payroll_period_end)
                ->where('status', 'approved')
                ->get();

            $totalGross = $payrolls->sum('gross_pay');
            $totalNet = $payrolls->sum('net_pay');

            $batchData[] = [
                'period_start' => $batch->payroll_period_start,
                'period_end' => $batch->payroll_period_end,
                'count' => $payrolls->count(),
                'total_gross' => $totalGross,
                'total_net' => $totalNet,
                'total_deductions' => $totalGross - $totalNet,
            ];
        }

        return view('accountant.reports.payroll', compact('batchData'));
    }

    public function payslips()
    {
        $payrolls = Payroll::with('employee')->where('status', 'paid')->get();
        return view('accountant.reports.payslips', compact('payrolls'));
    }

    public function payrollSummary()
    {
        $payrolls = Payroll::with('employee')->get();
        return view('accountant.reports.payroll-summary', compact('payrolls'));
    }

    public function deductionSummary()
    {
        $payrolls = Payroll::with('deductions')->get();
        return view('accountant.reports.deduction-summary', compact('payrolls'));
    }

    public function governmentContributionSummary()
    {
        $payrolls = Payroll::with('deductions')->get();
        return view('accountant.reports.government-contribution-summary', compact('payrolls'));
    }
}
