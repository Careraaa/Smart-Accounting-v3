<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\DailyRemittance;
use App\Models\Payroll;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use LogsUserActivity;
    public function remittanceReports(Request $request)
    {
        $this->logActivity('viewed', 'Remittance Report', request()->url());

        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $remittances = DailyRemittance::where('status', 'approved')
            ->whereYear('remittance_date', $year)
            ->whereMonth('remittance_date', $month)
            ->with('driver', 'pao', 'route')
            ->orderByDesc('remittance_date')
            ->get();

        return view('accountant.reports.remittance', compact('remittances', 'month', 'year'));
    }

    public function payslips()
    {
        $this->logActivity('viewed', 'Payslips Report', request()->url());

        $payrolls = Payroll::with('employee')->whereIn('status', ['approved', 'released', 'paid'])->get();
        return view('accountant.reports.payslips', compact('payrolls'));
    }

    public function payrollSummary()
    {
        $this->logActivity('viewed', 'Payroll Summary Report', request()->url());

        $payrolls = Payroll::with('employee')->get();
        return view('accountant.reports.payroll-summary', compact('payrolls'));
    }

    public function deductionSummary()
    {
        $this->logActivity('viewed', 'Deduction Summary Report', request()->url());

        $payrolls = Payroll::with('deductions')->get();
        return view('accountant.reports.deduction-summary', compact('payrolls'));
    }

    public function governmentContributionSummary()
    {
        $this->logActivity('viewed', 'Government Contribution Summary Report', request()->url());

        $payrolls = Payroll::with('deductions')->get();
        return view('accountant.reports.government-contribution-summary', compact('payrolls'));
    }

    public function payrollReports(Request $request)
    {
        $this->logActivity('viewed', 'Payroll Report', request()->url());

        $period = $request->get('period', 'monthly');
        $week   = $request->get('week',  now()->week);
        $month  = $request->get('month', now()->month);
        $year   = $request->get('year',  now()->year);
 
        $query = Payroll::where('status', 'approved');
 
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
        $this->logActivity('viewed', 'Payroll Report (Print)', request()->url());

        $period = $request->get('period', 'monthly');
        $week   = $request->get('week',  now()->week);
        $month  = $request->get('month', now()->month);
        $year   = $request->get('year',  now()->year);
  
        $query = Payroll::where('status', 'approved');
  
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

    public function printRemittanceReport(Request $request)
    {
        $this->logActivity('viewed', 'Remittance Report (Print)', request()->url());

        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $remittances = DailyRemittance::where('status', 'approved')
            ->whereYear('remittance_date', $year)
            ->whereMonth('remittance_date', $month)
            ->with('driver', 'pao', 'route')
            ->orderByDesc('remittance_date')
            ->get();

        return view('accountant.reports.remittance-print', compact('remittances', 'month', 'year'));
    }
}
