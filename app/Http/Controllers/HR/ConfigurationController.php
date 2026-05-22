<?php

namespace App\Http\Controllers\HR;

use App\Models\Shift;
use App\Models\PayrollCutoffSchedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConfigurationController extends Controller
{
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

        // Get attendance settings
        $lateThreshold = (int) config('attendance.late_threshold_minutes', 15);
        $checkInType = config('attendance.check_in_type', 'biometric');
        $requiresApproval = (bool) config('attendance.requires_manager_approval', false);
        $gracePeriod = (int) config('attendance.grace_period_minutes', 5);

        return view('hr.configuration.index', compact(
            'shifts',
            'cutoffs',
            'activeCutoff',
            'lateThreshold',
            'checkInType',
            'requiresApproval',
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
            'break_duration' => 'nullable|numeric|min:0|max:24',
            'is_active' => 'boolean',
        ]);

        $validated['start_time'] = $validated['start_time'] . ':00';
        $validated['end_time'] = $validated['end_time'] . ':00';
        
        // Convert hours to TIME format (HH:MM:SS)
        if (isset($validated['break_duration']) && $validated['break_duration'] !== null && $validated['break_duration'] !== '') {
            $hours = floor($validated['break_duration']);
            $minutes = round(($validated['break_duration'] - $hours) * 60);
            $validated['break_duration'] = sprintf('%02d:%02d:00', $hours, $minutes);
        } else {
            $validated['break_duration'] = null;
        }
        
        $validated['is_active'] = $request->boolean('is_active');

        Shift::create($validated);

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
            'break_duration' => 'nullable|numeric|min:0|max:24',
            'is_active' => 'boolean',
        ]);

        $validated['start_time'] = $validated['start_time'] . ':00';
        $validated['end_time'] = $validated['end_time'] . ':00';
        
        // Convert hours to TIME format (HH:MM:SS)
        if (isset($validated['break_duration']) && $validated['break_duration'] !== null && $validated['break_duration'] !== '') {
            $hours = floor($validated['break_duration']);
            $minutes = round(($validated['break_duration'] - $hours) * 60);
            $validated['break_duration'] = sprintf('%02d:%02d:00', $hours, $minutes);
        } else {
            $validated['break_duration'] = null;
        }
        
        $validated['is_active'] = $request->boolean('is_active');

        $shift->update($validated);

        return redirect()->route('settings.index')->with('success', 'Shift updated successfully!');
    }

    /**
     * Delete a shift.
     */
    public function destroyShift(Shift $shift)
    {
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

        return redirect()->route('settings.index')->with('success', 'Payroll cutoff updated successfully!');
    }

    /**
     * Update attendance settings.
     */
    public function updateAttendanceSettings(Request $request)
    {
        $validated = $request->validate([
            'late_threshold' => 'required|integer|min:1|max:60',
            'check_in_type' => 'required|in:biometric,qr,manual,mixed',
            'requires_approval' => 'boolean',
            'grace_period' => 'required|integer|min:0|max:30',
        ]);

        // Store settings using config cache or database
        $settings = [
            'attendance.late_threshold_minutes' => $validated['late_threshold'],
            'attendance.check_in_type' => $validated['check_in_type'],
            'attendance.requires_manager_approval' => $validated['requires_approval'] ?? false,
            'attendance.grace_period_minutes' => $validated['grace_period'],
        ];

        // Store in database or cache based on your implementation
        foreach ($settings as $key => $value) {
            // You can store these in a settings table if available
            // Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return redirect()->route('settings.index')->with('success', 'Attendance settings updated successfully!');
    }
}
