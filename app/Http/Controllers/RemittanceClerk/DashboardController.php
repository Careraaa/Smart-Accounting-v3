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
        // Only finalized remittances should affect business-level totals/trends.
        $finalizedStatuses = ['approved', 'completed'];
        $finalizedRemittances = DailyRemittance::whereIn('status', $finalizedStatuses);
        $totalRemittances = $finalizedRemittances->count();
        $totalCollections = $finalizedRemittances->sum('total_collection') ?? 0;
        $totalExpenses = $finalizedRemittances->sum('total_expenses') ?? 0;
        $totalNetRemittance = $finalizedRemittances->sum('net_remittance') ?? 0;
        
        $pendingRemittances = DailyRemittance::where('status', 'pending')->count();
        $completedRemittances = DailyRemittance::whereIn('status', $finalizedStatuses)->count();
        
        $activeDrivers = Driver::where('status', 'active')->count();
        $activePAOs = PAO::where('status', 'active')->count();
        $activeVehicles = Vehicle::where('status', 'active')->count();
        
        $averageCollection = $totalRemittances > 0 ? $totalCollections / $totalRemittances : 0;
        $averageExpenses = $totalRemittances > 0 ? $totalExpenses / $totalRemittances : 0;
        
        // Compare finalized collections in the latest 30-day window vs previous 30 days.
        $thirtyDaysAgo = now()->subDays(30);
        $sixtyDaysAgo = now()->subDays(60);
        $recentCollection = DailyRemittance::whereIn('status', $finalizedStatuses)
            ->whereBetween('remittance_date', [$thirtyDaysAgo, now()])
            ->sum('total_collection') ?? 0;
        $previousCollection = DailyRemittance::whereIn('status', $finalizedStatuses)
            ->whereBetween('remittance_date', [$sixtyDaysAgo, $thirtyDaysAgo])
            ->sum('total_collection') ?? 0;
        $collectionGrowth = $previousCollection > 0 ? (($recentCollection - $previousCollection) / $previousCollection) * 100 : 0;

        // Monthly collection trend (finalized remittances only; pure collections).
        $monthlyCollectionTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthTotal = DailyRemittance::whereIn('status', $finalizedStatuses)
                ->whereBetween('remittance_date', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->sum('total_collection') ?? 0;

            $monthlyCollectionTrend[] = [
                'month' => $month->format('M Y'),
                'total_collection' => $monthTotal,
            ];
        }
        
        // Recent remittances
        $recentRemittances = DailyRemittance::with('driver', 'vehicle', 'route')
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
