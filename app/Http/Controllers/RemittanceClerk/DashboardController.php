<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Http\Controllers\Controller;
use App\Models\DailyRemittance;
use App\Models\Driver;
use App\Models\PAO;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRemittances = DailyRemittance::count();
        $totalCollections = DailyRemittance::sum('total_collection') ?? 0;
        $totalExpenses = DailyRemittance::sum('total_expenses') ?? 0;
        $totalNetRemittance = DailyRemittance::sum('net_remittance') ?? 0;
        
        $pendingRemittances = DailyRemittance::where('status', 'pending')->count();
        $completedRemittances = DailyRemittance::whereIn('status', $finalizedStatuses)->count();
        
        $activeDrivers = Driver::where('status', 'active')->count();
        $activePAOs = PAO::where('status', 'active')->count();
        $activeVehicles = Vehicle::where('status', 'active')->count();
        
        $averageCollection = $totalRemittances > 0 ? $totalCollections / $totalRemittances : 0;
        $averageExpenses = $totalRemittances > 0 ? $totalExpenses / $totalRemittances : 0;
        
        // Compare finalized collections in the latest 30-day window vs previous 30 days.
        $thirtyDaysAgo = now()->subDays(30);
        $previousRemittances = DailyRemittance::where('created_at', '<', $thirtyDaysAgo)->sum('total_collection') ?? 0;
        $collectionGrowth = $previousRemittances > 0 ? (($totalCollections - $previousRemittances) / $previousRemittances) * 100 : 0;
        
        // Recent remittances
        $recentRemittances = DailyRemittance::with('driver', 'vehicle', 'route')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('remittance-clerk.index', compact(
            'totalRemittances',
            'totalCollections',
            'totalExpenses',
            'totalNetRemittance',
            'pendingRemittances',
            'completedRemittances',
            'activeDrivers',
            'activePAOs',
            'activeVehicles',
            'averageCollection',
            'averageExpenses',
            'collectionGrowth',
            'monthlyCollectionTrend',
            'recentRemittances'
        ));
    }
}
