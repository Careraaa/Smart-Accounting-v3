<?php

namespace App\Http\Controllers\RemittanceClerk;

use App\Traits\LogsUserActivity;
use App\Http\Controllers\Controller;
use App\Models\DailyRemittance;
use App\Models\Driver;
use App\Models\PAO;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use LogsUserActivity;
    public function index()
    {
        $this->logActivity('viewed', 'dashboard');

        $finalizedStatuses = ['approved', 'completed'];

        $totalRemittances = DailyRemittance::whereIn('status', $finalizedStatuses)->count();
        $totalCollections = DailyRemittance::whereIn('status', $finalizedStatuses)->sum('total_collection') ?? 0;
        $totalExpenses = DailyRemittance::whereIn('status', $finalizedStatuses)->sum('total_expenses') ?? 0;
        $totalNetRemittance = DailyRemittance::whereIn('status', $finalizedStatuses)->sum('net_remittance') ?? 0;

        $pendingRemittances = DailyRemittance::where('status', 'pending')->count();
        $completedRemittances = $totalRemittances;

        $activeDrivers = Driver::where('status', 'active')->count();
        $activePAOs = PAO::where('status', 'active')->count();
        $activeVehicles = Vehicle::where('status', 'active')->count();

        $averageCollection = $totalRemittances > 0 ? $totalCollections / $totalRemittances : 0;
        $averageExpenses = $totalRemittances > 0 ? $totalExpenses / $totalRemittances : 0;

        // Compare finalized collections in the latest 30-day window vs previous 30 days.
        $currentWindowStart = now()->subDays(30);
        $previousWindowStart = now()->subDays(60);

        $currentCollections = DailyRemittance::whereIn('status', $finalizedStatuses)
            ->whereBetween('created_at', [$currentWindowStart, now()])
            ->sum('total_collection') ?? 0;

        $previousCollections = DailyRemittance::whereIn('status', $finalizedStatuses)
            ->whereBetween('created_at', [$previousWindowStart, $currentWindowStart])
            ->sum('total_collection') ?? 0;

        $collectionGrowth = $previousCollections > 0
            ? (($currentCollections - $previousCollections) / $previousCollections) * 100
            : 0;

        $monthlyCollectionTrend = DailyRemittance::selectRaw('DATE_FORMAT(remittance_date, "%b") as month, SUM(total_collection) as total_collection')
            ->whereIn('status', $finalizedStatuses)
            ->whereNotNull('remittance_date')
            ->where('remittance_date', '>=', now()->subMonths(6)->startOfMonth())
            ->groupByRaw('YEAR(remittance_date), MONTH(remittance_date), DATE_FORMAT(remittance_date, "%b")')
            ->orderByRaw('YEAR(remittance_date), MONTH(remittance_date)')
            ->get()
            ->map(fn($row) => [
                'month' => $row->month,
                'total_collection' => (float) $row->total_collection,
            ])
            ->toArray();

        // Recent remittances
        $recentRemittances = DailyRemittance::with('driver', 'vehicle', 'route')
            ->whereIn('status', $finalizedStatuses)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // ── Pending To-Do Items ──────────────────────────────────────────────
        $pendingItems = [];
        if ($pendingRemittances > 0) {
            $pendingItems[] = [
                'text' => 'Pending Remittances',
                'count' => $pendingRemittances,
                'url' => route('remittances.index'),
                'icon' => 'feather-layers',
                'color' => '#fff0f0',
                'iconColor' => '#c8292a'
            ];
        }

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
            'recentRemittances',
            'pendingItems'
        ));
    }
}
