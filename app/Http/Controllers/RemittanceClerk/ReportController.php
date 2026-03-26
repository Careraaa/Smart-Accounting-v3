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
        return view('remittance-clerk.reports.index');
    }

    public function remittanceReport(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $week = $request->get('week', 1);

        $query = DailyRemittance::with('driver', 'pao', 'route', 'vehicle');

        if ($period === 'weekly') {
            $startDate = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $endDate = $startDate->copy()->endOfWeek();
            $remittances = $query->whereBetween('remittance_date', [$startDate, $endDate])->get();
        } elseif ($period === 'monthly') {
            $remittances = $query->whereYear('remittance_date', $year)
                ->whereMonth('remittance_date', $month)->get();
        } else { // yearly
            $remittances = $query->whereYear('remittance_date', $year)->get();
        }

        $totalCollection = $remittances->sum('total_collection');
        $totalExpenses = $remittances->sum('total_expenses');
        $totalNetRemittance = $remittances->sum('net_remittance');
        $shortRemittances = $remittances->where('is_short_remittance', true)->count();

        return view('remittance-clerk.reports.remittance-report', compact(
            'remittances',
            'period',
            'month',
            'year',
            'week',
            'totalCollection',
            'totalExpenses',
            'totalNetRemittance',
            'shortRemittances'
        ));
    }

    public function driverReport()
    {
        $drivers = Driver::where('status', 'active')->get();
        return view('remittance-clerk.reports.driver-report', compact('drivers'));
    }

    public function paoReport()
    {
        $paos = PAO::where('status', 'active')->get();
        return view('remittance-clerk.reports.pao-report', compact('paos'));
    }

    public function vehicleRouteReport()
    {
        $vehicles = Vehicle::with('route')->where('status', 'active')->get();
        $routes = Route::where('status', 'active')->get();

        $vehicleStats = $vehicles->map(function ($vehicle) {
            return [
                'id' => $vehicle->id,
                'plate_number' => $vehicle->plate_number,
                'model' => $vehicle->model,
                'route' => $vehicle->route->route_name ?? 'N/A',
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

        return view('remittance-clerk.reports.vehicle-route-report', compact('vehicleStats', 'routeStats'));
    }

    public function printRemittanceReport(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $week = $request->get('week', 1);

        $query = DailyRemittance::with('driver', 'pao', 'route', 'vehicle');

        if ($period === 'weekly') {
            $startDate = Carbon::now()->setISODate($year, $week)->startOfWeek();
            $endDate = $startDate->copy()->endOfWeek();
            $remittances = $query->whereBetween('remittance_date', [$startDate, $endDate])->get();
        } elseif ($period === 'monthly') {
            $remittances = $query->whereYear('remittance_date', $year)
                ->whereMonth('remittance_date', $month)->get();
        } else { // yearly
            $remittances = $query->whereYear('remittance_date', $year)->get();
        }

        $totalCollection = $remittances->sum('total_collection');
        $totalExpenses = $remittances->sum('total_expenses');
        $totalNetRemittance = $remittances->sum('net_remittance');
        $shortRemittances = $remittances->where('is_short_remittance', true)->count();

        return view('remittance-clerk.reports.print.remittance-report-print', compact(
            'remittances',
            'period',
            'month',
            'year',
            'week',
            'totalCollection',
            'totalExpenses',
            'totalNetRemittance',
            'shortRemittances'
        ));
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
