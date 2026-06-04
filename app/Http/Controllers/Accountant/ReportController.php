<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\DailyRemittance;
use App\Models\Payroll;
use App\Models\User;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use LogsUserActivity;
    public function remittanceReports(Request $request)
    {
        $this->logActivity('viewed', 'Remittance Report', request()->url());

        $period = $request->get('period', 'monthly');
        $week   = $request->get('week',  now()->week);
        $month  = $request->get('month', now()->month);
        $year   = $request->get('year',  now()->year);
        $day    = $request->get('day',   now()->day);

        $query = DailyRemittance::where('status', 'approved');

        if ($period === 'daily') {
            $query->whereDate('remittance_date', $year.'-'.$month.'-'.$day);
        } elseif ($period === 'weekly') {
            $query->whereYear('remittance_date', $year)
                  ->whereRaw('WEEK(remittance_date) = ?', [$week]);
        } elseif ($period === 'monthly') {
            $query->whereYear('remittance_date', $year)
                  ->whereMonth('remittance_date', $month);
        } else {
            $query->whereYear('remittance_date', $year);
        }

        $remittances = $query->with('driver', 'pao', 'route', 'vehicle')
            ->orderByDesc('remittance_date')
            ->get();

        $groupedRemittances = collect($remittances)->groupBy(function ($item) {
            return $item->remittance_date->format('Y-m-d');
        })->map(function ($group) {
            return [
                'remittance_date' => $group->first()->remittance_date,
                'total_collection' => $group->sum('total_collection'),
                'total_expenses' => $group->sum('total_expenses'),
                'net_remittance' => $group->sum('net_remittance'),
                'is_short_remittance' => $group->where('is_short_remittance', true)->count() > 0,
            ];
        })->sortBy('remittance_date')->values();

        $page = $request->get('page', 1);
        $perPage = 10;
        $groupedRemittances = new \Illuminate\Pagination\Paginator(
            $groupedRemittances->forPage($page, $perPage),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $totalCollection = $remittances->sum('total_collection');
        $totalExpenses = $remittances->sum('total_expenses');
        $totalNetRemittance = $remittances->sum('net_remittance');
        $shortRemittances = $remittances->where('is_short_remittance', true)->count();

        return view('accountant.reports.remittance', compact(
            'remittances', 'groupedRemittances', 'period', 'week', 'month', 'year', 'day',
            'totalCollection', 'totalExpenses', 'totalNetRemittance', 'shortRemittances'
        ));
    }

    public function payslips()
    {
        $this->logActivity('viewed', 'Payslips Report', request()->url());

        $payrolls = Payroll::with('employee')->where('status', 'approved')->get();
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
 
        $query = Payroll::whereHas('batch', fn($q) => $q->where('status', 'approved'));

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
  
        $query = Payroll::whereHas('batch', fn($q) => $q->where('status', 'approved'));
  
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
  
        $preparedBy = User::where('role', 'remittance_clerk')->first();
        $checkedBy = auth()->user();

        return view('accountant.reports.payroll-print', compact(
            'batchData', 'period', 'week', 'month', 'year', 'preparedBy', 'checkedBy'
        ));
    }

    public function printRemittanceReport(Request $request)
    {
        $this->logActivity('viewed', 'Remittance Report (Print)', request()->url());

        $period = $request->get('period', 'monthly');
        $month  = $request->get('month', now()->month);
        $year   = $request->get('year',  now()->year);
        $week   = $request->get('week',  1);
        $day    = $request->get('day',   now()->day);

        $query = DailyRemittance::with('driver', 'pao', 'route', 'vehicle')
            ->where('status', 'approved');

        if ($period === 'daily') {
            $date = \Carbon\Carbon::createFromDate($year, $month, $day);
            $remittances = $query->whereDate('remittance_date', $date)->get();
        } elseif ($period === 'weekly') {
            $startDate = \Carbon\Carbon::now()->setISODate($year, $week)->startOfWeek();
            $endDate = $startDate->copy()->endOfWeek();
            $remittances = $query->whereBetween('remittance_date', [$startDate, $endDate])->get();
        } elseif ($period === 'monthly') {
            $remittances = $query->whereYear('remittance_date', $year)
                ->whereMonth('remittance_date', $month)->get();
        } else {
            $remittances = $query->whereYear('remittance_date', $year)->get();
        }

        $groupedRemittances = $remittances->groupBy(function($item) {
            return $item->remittance_date->format('Y-m-d');
        })->map(function($group) {
            return [
                'remittance_date' => $group->first()->remittance_date,
                'total_collection' => $group->sum('total_collection'),
                'total_expenses' => $group->sum('total_expenses'),
                'net_remittance' => $group->sum('net_remittance'),
                'is_short_remittance' => $group->where('is_short_remittance', true)->count() > 0,
            ];
        })->sortBy('remittance_date')->values();

        $totalCollection = $remittances->sum('total_collection');
        $totalExpenses = $remittances->sum('total_expenses');
        $totalNetRemittance = $remittances->sum('net_remittance');
        $shortRemittances = $remittances->where('is_short_remittance', true)->count();

        $preparedBy = User::where('role', 'remittance_clerk')->first();
        $checkedBy = auth()->user();

        return view('accountant.reports.remittance-print', compact(
            'groupedRemittances', 'period', 'month', 'year', 'week', 'day',
            'totalCollection', 'totalExpenses', 'totalNetRemittance', 'shortRemittances',
            'preparedBy', 'checkedBy'
        ));
    }
}
