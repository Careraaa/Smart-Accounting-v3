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
        $status = $request->query('status', 'all');
        $year = $request->query('year', now()->year);

        $query = Holiday::query();

        // Filter by year
        $query->whereYear('date', $year);

        // Filter by status (active = future + current, inactive = past)
        if ($status === 'active') {
            $query->where('date', '>=', now()->startOfDay());
        } elseif ($status === 'inactive') {
            $query->where('date', '<', now()->startOfDay());
        }

        // Sort by date
        $holidays = $query->orderBy('date')->paginate(15);

        // Get available years for filter
        $availableYears = Holiday::selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        return view('hr.holiday.index', compact('holidays', 'status', 'year', 'availableYears'));
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
            'name' => 'required|string|max:255|unique:holidays,name,NULL,id,date,' . $request->date,
            'date' => 'required|date|unique:holidays,date',
            'type' => 'required|in:regular,special',
            'description' => 'nullable|string|max:500',
        ], [
            'name.unique' => 'A holiday with this name already exists on that date.',
            'date.unique' => 'A holiday already exists on this date.',
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
            'name' => 'required|string|max:255|unique:holidays,name,' . $holiday->id . ',id,date,' . $request->date,
            'date' => 'required|date|unique:holidays,date,' . $holiday->id,
            'type' => 'required|in:regular,special',
            'description' => 'nullable|string|max:500',
        ], [
            'name.unique' => 'A holiday with this name already exists on that date.',
            'date.unique' => 'A holiday already exists on this date.',
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
     * Display calendar view of holidays.
     */
    public function calendar(Request $request)
    {
        $month = $request->query('month');
        $currentMonth = $month ? Carbon::createFromFormat('Y-m', $month)->startOfMonth() : now()->startOfMonth();

        $holidays = Holiday::whereBetween('date', [
            $currentMonth->copy()->startOfMonth(),
            $currentMonth->copy()->endOfMonth(),
        ])->get();

        $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');

        // Get holidays for the whole year for sidebar stats
        $yearHolidays = Holiday::whereYear('date', $currentMonth->year)->get();
        $regularCount = $yearHolidays->where('type', 'regular')->count();
        $specialCount = $yearHolidays->where('type', 'special')->count();

        return view('hr.holiday.calendar', compact(
            'holidays',
            'currentMonth',
            'prevMonth',
            'nextMonth',
            'regularCount',
            'specialCount'
        ));
    }
}
