<?php

namespace App\Http\Controllers\HR;

use App\Models\Holiday;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class HolidayController extends Controller
{
    /**
     * Display a listing of the holidays.
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');
        $year = $request->query('year', now()->year);

        // Get available years for filter
        $availableYears = Holiday::selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        // Load ALL holidays for the selected year (small dataset, no pagination needed)
        $yearHolidays = Holiday::whereYear('date', $year)->orderBy('date')->get();

        $allHolidays = $yearHolidays;
        $activeHolidays = $yearHolidays->where('date', '>=', now()->startOfDay());

        $totalCount = $allHolidays->count();
        $regularCount = $allHolidays->where('type', 'regular')->count();
        $specialCount = $allHolidays->where('type', 'special')->count();
        $upcomingCount = $activeHolidays->count();

        return view('hr.holiday.index', compact(
            'tab', 'year', 'availableYears',
            'allHolidays', 'activeHolidays',
            'totalCount', 'regularCount', 'specialCount', 'upcomingCount'
        ));
    }

    /**
     * Show the form for creating a new holiday.
     */
    public function create()
    {
        return view('hr.holiday.create');
    }

    /**
     * Store a newly created holiday in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:regular,special',
        ]);

        Holiday::create($validated);

        return redirect()->route('holiday.index')->with('success', 'Holiday created successfully.');
    }

    /**
     * Display the specified holiday.
     */
    public function show(Holiday $holiday)
    {
        return view('hr.holiday.show', compact('holiday'));
    }

    /**
     * Show the form for editing the specified holiday.
     */
    public function edit(Holiday $holiday)
    {
        return view('hr.holiday.edit', compact('holiday'));
    }

    /**
     * Update the specified holiday in storage.
     */
    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'type' => 'required|in:regular,special',
        ]);

        $holiday->update($validated);

        return redirect()->route('holiday.index')->with('success', 'Holiday updated successfully.');
    }

    /**
     * Remove the specified holiday from storage.
     */
    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return redirect()->route('holiday.index')->with('success', 'Holiday deleted successfully.');
    }

    /**
     * Get holidays as JSON (API endpoint).
     */
    public function indexApi(Request $request)
    {
        $year = $request->get('year', now()->year);

        $holidays = Holiday::whereYear('date', $year)
            ->get()
            ->map(function ($holiday) {
                return [
                    'date' => $holiday->date->format('Y-m-d'),
                    'name' => $holiday->name,
                ];
            });

        return response()->json($holidays);
    }
}
