<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Http\Controllers\Controller;
use App\Models\DailyRemittance;
use App\Models\Driver;
use App\Models\PAO;
use App\Models\Vehicle;
use App\Models\Route;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $period = 'monthly';
        $month = date('m');
        $year = date('Y');
        $week = 1;

        $remittances = $this->filterRemittances($period, $month, $year, $week);
        
        // Group remittances by date and paginate
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
        
        // Paginate the grouped results
        $page = 1;
        $perPage = 10;
        $groupedRemittances = new \Illuminate\Pagination\Paginator(
            $groupedRemittances->forPage($page, $perPage),
            $perPage,
            $page,
            [
                'path' => route('reports.remittance-report'),
                'query' => [
                    'period' => $period,
                    'month' => $month,
                    'year' => $year,
                    'week' => $week,
                ],
            ]
        );
        
        $totals = $this->calculateTotals($remittances);

        return view('remittance-clerk.reports.remittance-report', compact(
            'groupedRemittances',
            'period',
            'month',
            'year',
            'week'
        ) + $totals);
    }

    public function remittanceReport(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $week = $request->get('week', 1);

        $remittances = $this->filterRemittances($period, $month, $year, $week);
        
        // Group remittances by date and paginate
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
        
        // Paginate the grouped results
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
        
        $totals = $this->calculateTotals($remittances);

        return view('remittance-clerk.reports.remittance-report', compact(
            'groupedRemittances',
            'period',
            'month',
            'year',
            'week'
        ) + $totals);
    }

    public function printRemittanceReport(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $week = $request->get('week', 1);

        $remittances = $this->filterRemittances($period, $month, $year, $week);
        
        // Group remittances by date for printing
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

        $totals = $this->calculateTotals($remittances);

        return view('remittance-clerk.reports.print.remittance-report-print', compact(
            'groupedRemittances',
            'period',
            'month',
            'year',
            'week'
        ) + $totals);
    }

    /**
     * Filter remittances based on period parameters
     */
    private function filterRemittances($period, $month, $year, $week)
    {
        $query = DailyRemittance::with('driver', 'pao', 'route', 'vehicle')
            ->where('status', 'approved');

        if ($period === 'weekly') {
            $startDate = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $endDate = $startDate->copy()->endOfWeek();
            return $query->whereBetween('remittance_date', [$startDate, $endDate])->get();
        } elseif ($period === 'monthly') {
            return $query->whereYear('remittance_date', $year)
                ->whereMonth('remittance_date', $month)->get();
        } else { // yearly
            return $query->whereYear('remittance_date', $year)->get();
        }
    }

    /**
     * Calculate totals from remittances collection
     */
    private function calculateTotals($remittances)
    {
        return [
            'totalCollection' => $remittances->sum('total_collection'),
            'totalExpenses' => $remittances->sum('total_expenses'),
            'totalNetRemittance' => $remittances->sum('net_remittance'),
            'shortRemittances' => $remittances->where('is_short_remittance', true)->count(),
        ];
    }

    public function printDriverReport()
    {
        $drivers = Driver::where('status', 'active')->get();
        return view('remittance-clerk.reports.print.driver-report-print', compact('drivers'));
    }

    public function printPaoReport()
    {
        $paos = PAO::where('status', 'active')->get();
        return view('remittance-clerk.reports.print.pao-report-print', compact('paos'));
    }

    public function printVehicleRouteReport()
    {
        $vehicles = Vehicle::with('route')->where('status', 'active')->get();
        $routes = Route::where('status', 'active')->get();

        $vehicleStats = $vehicles->map(function ($vehicle) {
            return [
                'id' => $vehicle->id,
                'plate_number' => $vehicle->plate_number,
                'model' => $vehicle->model,
                'route' => $vehicle->route->route_name ?? 'N/A',
                'operator' => $vehicle->operator,
                'status' => $vehicle->status,
            ];
        });

        $routeStats = $routes->map(function ($route) {
            $vehicles = Vehicle::where('route_id', $route->id)->count();
            $remittances = DailyRemittance::where('route_id', $route->id)->count();
            return [
                'id' => $route->id,
                'route_name' => $route->route_name,
                'vehicles' => $vehicles,
                'total_remittances' => $remittances,
                'boundary' => $route->boundary,
            ];
        });

        return view('remittance-clerk.reports.print.vehicle-route-report-print', compact('vehicleStats', 'routeStats'));
    }
}
