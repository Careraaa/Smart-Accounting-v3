<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\DailyRemittance;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function remittanceReports(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $month  = $request->get('month', now()->month);
        $year   = $request->get('year', now()->year);
        $week   = $request->get('week', 1);
        $date   = $request->get('date', now()->format('Y-m-d'));

        $query = DailyRemittance::with('driver', 'pao', 'route', 'vehicle')
            ->where('status', 'approved');

        if ($period === 'daily') {
            $remittances = $query->whereDate('remittance_date', $date)->get();
        } elseif ($period === 'weekly') {
            $startDate = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $endDate = $startDate->copy()->endOfWeek();
            $remittances = $query->whereBetween('remittance_date', [$startDate, $endDate])->get();
        } elseif ($period === 'monthly') {
            $remittances = $query->whereYear('remittance_date', $year)
                ->whereMonth('remittance_date', $month)->get();
        } else {
            $remittances = $query->whereYear('remittance_date', $year)->get();
        }

        $grouped = collect($remittances)->groupBy(fn($item) => $item->remittance_date->format('Y-m-d'))
            ->map(fn($group) => [
                'remittance_date'    => $group->first()->remittance_date,
                'total_collection'   => $group->sum('total_collection'),
                'total_expenses'     => $group->sum('total_expenses'),
                'net_remittance'     => $group->sum('net_remittance'),
                'is_short_remittance' => $group->where('is_short_remittance', true)->count() > 0,
            ])->sortBy('remittance_date')->values();

        $totalCollection   = $remittances->sum('total_collection');
        $totalExpenses     = $remittances->sum('total_expenses');
        $totalNetRemittance = $remittances->sum('net_remittance');
        $shortRemittances  = $remittances->where('is_short_remittance', true)->count();

        return view('accountant.reports.remittance', compact(
            'grouped', 'period', 'month', 'year', 'week', 'date',
            'totalCollection', 'totalExpenses', 'totalNetRemittance', 'shortRemittances'
        ));
    }

    public function payslips()
    {
        $payrolls = Payroll::with('employee')->whereIn('status', ['approved', 'released', 'paid'])->get();
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

    public function getDailyDetails(Request $request)
    {
        $date = $request->get('date');

        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        $remittances = DailyRemittance::with('driver', 'pao', 'route', 'vehicle')
            ->where('status', 'approved')
            ->whereDate('remittance_date', $date)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'driver' => $item->driver->name ?? '—',
                    'pao' => $item->pao->name ?? '—',
                    'vehicle' => $item->vehicle->plate_number ?? '—',
                    'route' => $item->route->route_name ?? '—',
                    'total_collection' => number_format($item->total_collection, 2),
                    'total_expenses' => number_format($item->total_expenses, 2),
                    'net_remittance' => number_format($item->net_remittance, 2),
                    'is_short' => $item->is_short_remittance,
                ];
            });

        return response()->json($remittances);
    }

    public function printRemittanceReport(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $month  = $request->get('month', now()->month);
        $year   = $request->get('year', now()->year);
        $week   = $request->get('week', 1);
        $date   = $request->get('date', now()->format('Y-m-d'));

        $query = DailyRemittance::with('driver', 'pao', 'route', 'vehicle')
            ->where('status', 'approved');

        if ($period === 'daily') {
            $remittances = $query->whereDate('remittance_date', $date)->get();
        } elseif ($period === 'weekly') {
            $startDate = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $endDate = $startDate->copy()->endOfWeek();
            $remittances = $query->whereBetween('remittance_date', [$startDate, $endDate])->get();
        } elseif ($period === 'monthly') {
            $remittances = $query->whereYear('remittance_date', $year)
                ->whereMonth('remittance_date', $month)->get();
        } else {
            $remittances = $query->whereYear('remittance_date', $year)->get();
        }

        $groupedRemittances = $remittances->groupBy(fn($item) => $item->remittance_date->format('Y-m-d'))
            ->map(fn($group) => [
                'remittance_date'    => $group->first()->remittance_date,
                'total_collection'   => $group->sum('total_collection'),
                'total_expenses'     => $group->sum('total_expenses'),
                'net_remittance'     => $group->sum('net_remittance'),
                'is_short_remittance' => $group->where('is_short_remittance', true)->count() > 0,
            ])->sortBy('remittance_date')->values();

        $totalCollection   = $remittances->sum('total_collection');
        $totalExpenses     = $remittances->sum('total_expenses');
        $totalNetRemittance = $remittances->sum('net_remittance');
        $shortRemittances  = $remittances->where('is_short_remittance', true)->count();

        return view('accountant.reports.remittance-print', compact(
            'groupedRemittances', 'period', 'month', 'year', 'week', 'date',
            'totalCollection', 'totalExpenses', 'totalNetRemittance', 'shortRemittances'
        ));
    }
}
