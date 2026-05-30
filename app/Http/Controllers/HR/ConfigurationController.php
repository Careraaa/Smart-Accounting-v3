<?php

namespace App\Http\Controllers\HR;

use App\Models\Shift;
use App\Models\PayrollCutoffSchedule;
use App\Models\Setting;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConfigurationController extends Controller
{
    use LogsUserActivity;
    /**
     * Display the configuration page with all settings.
     */
    public function index()
    {
        // Get shifts data
        $shifts = Shift::orderBy('start_time')->get();

        // Get payroll cutoff data
        $cutoffs = PayrollCutoffSchedule::all();
        $activeCutoff = PayrollCutoffSchedule::where('is_active', true)->first();

        // Get attendance settings from database
        $gracePeriod = (int) Setting::get('attendance.grace_period_minutes', 5);

        $this->logActivity('viewed', 'Configuration settings', request()->url(), 'configuration');

        return view('hr.configuration.index', compact(
            'shifts',
            'cutoffs',
            'activeCutoff',
            'gracePeriod'
        ));
    }

    /**
     * Store a new shift.
     */
    public function storeShift(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:shifts,name',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
        ]);

        $validated['start_time'] = $validated['start_time'] . ':00';
        $validated['end_time'] = $validated['end_time'] . ':00';
        
        // Convert break times to TIME format (HH:MM:SS)
        if ($validated['break_start']) {
            $validated['break_start'] = $validated['break_start'] . ':00';
        }
        if ($validated['break_end']) {
            $validated['break_end'] = $validated['break_end'] . ':00';
        }
        
        $validated['is_active'] = $request->boolean('is_active');

        Shift::create($validated);

        $this->logActivity('created', "Shift: {$validated['name']}", request()->url(), 'configuration');

        return redirect()->route('settings.index')->with('success', 'Shift created successfully!');
    }

    /**
     * Update a shift.
     */
    public function updateShift(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:shifts,name,' . $shift->id,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'break_start' => 'nullable|date_format:H:i',
            'break_end' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
        ]);

        $validated['start_time'] = $validated['start_time'] . ':00';
        $validated['end_time'] = $validated['end_time'] . ':00';
        
        // Convert break times to TIME format (HH:MM:SS)
        if ($validated['break_start']) {
            $validated['break_start'] = $validated['break_start'] . ':00';
        }
        if ($validated['break_end']) {
            $validated['break_end'] = $validated['break_end'] . ':00';
        }
        
        $validated['is_active'] = $request->boolean('is_active');

        $shift->update($validated);

        $this->logActivity('updated', "Shift: {$shift->name}", request()->url(), 'configuration', $shift->id);

        return redirect()->route('settings.index')->with('success', 'Shift updated successfully!');
    }

    /**
     * Delete a shift.
     */
    public function destroyShift(Shift $shift)
    {
        $this->logActivity('deleted', "Shift: {$shift->name}", request()->url(), 'configuration', $shift->id);
        $shift->delete();
        return redirect()->route('settings.index')->with('success', 'Shift deleted successfully!');
    }

    /**
     * Update payroll cutoff settings.
     */
    public function updatePayrollCutoff(Request $request)
    {
        $validated = $request->validate([
            'frequency' => 'required|in:weekly,bi-monthly,monthly,custom',
            'cutoff_day' => 'required|integer|min:1|max:31',
            'label' => 'nullable|string|max:100',
        ]);

        $cutoffDay = match($validated['frequency']) {
            'weekly' => 7,
            'bi-monthly' => 15,
            'monthly' => 30,
            'custom' => $validated['cutoff_day'],
            default => $validated['cutoff_day'],
        };

        PayrollCutoffSchedule::query()->update(['is_active' => false]);

        $cutoff = PayrollCutoffSchedule::where('cutoff_day', $cutoffDay)->first();

        if ($cutoff) {
            $cutoff->update([
                'is_active' => true,
                'label' => $validated['label'] ?? $validated['frequency'],
            ]);
        } else {
            PayrollCutoffSchedule::create([
                'cutoff_day' => $cutoffDay,
                'is_active' => true,
                'label' => $validated['label'] ?? $validated['frequency'],
            ]);
        }

        $this->logActivity('updated', 'Payroll cutoff schedule', request()->url(), 'configuration');

        return redirect()->route('settings.index')->with('success', 'Payroll cutoff updated successfully!');
    }

    /**
     * Update attendance settings.
     */
    public function updateAttendanceSettings(Request $request)
    {
        $validated = $request->validate([
            'grace_period' => 'required|integer|min:0|max:30',
        ]);

        // Store settings in database using Setting model
        Setting::set('attendance.grace_period_minutes', $validated['grace_period']);

        $this->logActivity('updated', 'Attendance settings', request()->url(), 'configuration');

        return redirect()->route('settings.index')->with('success', 'Attendance settings updated successfully!');
    }
}
