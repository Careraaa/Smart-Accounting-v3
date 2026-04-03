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

    public function payrollReports(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $week   = $request->get('week',  now()->week);
        $month  = $request->get('month', now()->month);
        $year   = $request->get('year',  now()->year);
 
        $query = Payroll::where('status', 'released');
 
        if ($period === 'weekly') {
            $query->whereYear('payroll_period_start', $year)
                  ->whereRaw('WEEK(payroll_period_start) = ?', [$week]);
        } elseif ($period === 'monthly') {
            $query->whereYear('payroll_period_start', $year)
                  ->whereMonth('payroll_period_start', $month);
        } else {
            $query->whereYear('payroll_period_start', $year);
        }
 
        $payrolls = $query->orderBy('payroll_period_start')->get();
 
        // Group into batches keyed by period start–end
        $batchData = $payrolls
            ->groupBy(fn($p) => $p->payroll_period_start->format('Y-m-d') . '_' . $p->payroll_period_end->format('Y-m-d'))
            ->map(fn($group) => [
                'period_start'     => $group->first()->payroll_period_start,
                'period_end'       => $group->first()->payroll_period_end,
                'count'            => $group->count(),
                'total_gross'      => $group->sum('gross_pay'),
                'total_deductions' => $group->sum('total_deductions'),
                'total_net'        => $group->sum('net_pay'),
            ])
            ->values()
            ->toArray();
 
        return view('accountant.reports.payroll', compact(
            'batchData', 'period', 'week', 'month', 'year'
        ));
    }
 
    public function printPayrollReport(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $week   = $request->get('week',  now()->week);
        $month  = $request->get('month', now()->month);
        $year   = $request->get('year',  now()->year);
 
        $query = Payroll::where('status', 'released');
 
        if ($period === 'weekly') {
            $query->whereYear('payroll_period_start', $year)
                  ->whereRaw('WEEK(payroll_period_start) = ?', [$week]);
        } elseif ($period === 'monthly') {
            $query->whereYear('payroll_period_start', $year)
                  ->whereMonth('payroll_period_start', $month);
        } else {
            $query->whereYear('payroll_period_start', $year);
        }
 
        $payrolls = $query->orderBy('payroll_period_start')->get();
 
        $batchData = $payrolls
            ->groupBy(fn($p) => $p->payroll_period_start->format('Y-m-d') . '_' . $p->payroll_period_end->format('Y-m-d'))
            ->map(fn($group) => [
                'period_start'     => $group->first()->payroll_period_start,
                'period_end'       => $group->first()->payroll_period_end,
                'count'            => $group->count(),
                'total_gross'      => $group->sum('gross_pay'),
                'total_deductions' => $group->sum('total_deductions'),
                'total_net'        => $group->sum('net_pay'),
            ])
            ->values()
            ->toArray();
 
        return view('accountant.reports.payroll-print', compact(
            'batchData', 'period', 'week', 'month', 'year'
        ));
    }
}
