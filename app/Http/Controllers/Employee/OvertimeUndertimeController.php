<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\OvertimeUndertime;
use App\Notifications\OvertimeNotification;
use Illuminate\Http\Request;

class OvertimeUndertimeController extends Controller
{
    /**
     * Display a listing of the user's overtime/undertime requests.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $query = OvertimeUndertime::where('user_id', $userId)->with('employee');

        // Filter by status
        $status = $request->status ?? 'all';
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by type
        $type = $request->type ?? 'all';
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        // Sort by date descending (recent first)
        $query->orderBy('date', 'desc');

        $requests = $query->paginate(10);

        // Statistics for the user's requests
        $totalRequests = OvertimeUndertime::where('user_id', $userId)->count();
        $pendingRequests = OvertimeUndertime::where('user_id', $userId)->where('status', 'pending')->count();
        $approvedRequests = OvertimeUndertime::where('user_id', $userId)->where('status', 'approved')->count();
        $rejectedRequests = OvertimeUndertime::where('user_id', $userId)->where('status', 'rejected')->count();
        
        $overtimeRequests = OvertimeUndertime::where('user_id', $userId)->where('type', 'overtime')->count();
        $undertimeRequests = OvertimeUndertime::where('user_id', $userId)->where('type', 'undertime')->count();
        
        $totalOvertimeHours = OvertimeUndertime::where('user_id', $userId)->where('type', 'overtime')->sum('hours');
        $totalUndertimeHours = OvertimeUndertime::where('user_id', $userId)->where('type', 'undertime')->sum('hours');

        return view('employee.overtime-undertime.index', compact(
            'requests',
            'totalRequests',
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests',
            'overtimeRequests',
            'undertimeRequests',
            'totalOvertimeHours',
            'totalUndertimeHours',
            'status',
            'type'
        ));
    }

    /**
     * Show the form for creating a new overtime/undertime request.
     */
    public function create()
    {
        $types = ['overtime' => 'Overtime', 'undertime' => 'Undertime'];

        return view('employee.overtime-undertime.create', compact('types'));
    }

    /**
     * Store a newly created overtime/undertime request in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:overtime,undertime',
            'date' => 'required|date|before_or_equal:today',
            'hours' => 'required|numeric|min:0.5|max:24',
            'reason' => 'required|string|max:1000',
        ]);

        // Get the authenticated user (User model is used for employees)
        $user = auth()->user();
        
        if (!$user || !$user->salary_rate) {
            return redirect()->back()->with('error', 'No salary rate linked to your account.');
        }

        $hourlyRate = $user->salary_rate / 8;

        // Compute amount
        // OT rate = hourlyRate * 1.25
        // OT pay = otHours * otRate
        // UT deduction = utHours * hourlyRate (negative)
        $amount = $validated['type'] === 'overtime'
            ? $validated['hours'] * $hourlyRate * 1.25
            : -($validated['hours'] * $hourlyRate);

        // Add the authenticated user's ID
        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';
        $validated['amount'] = $amount;
        $validated['hourly_rate_used'] = $hourlyRate;

        $overtimeRequest = OvertimeUndertime::create($validated);
        $overtimeRequest->load('employee');

        // Send notifications to managers/HR
        OvertimeNotification::submitted($overtimeRequest);
        OvertimeNotification::notifyManagersForApproval($overtimeRequest);

        return redirect()->route('employee.overtime-undertime.index')->with('success', 'Overtime/Undertime request submitted successfully. Awaiting approval.');
    }

    /**
     * Display the specified overtime/undertime request.
     */
    public function show(OvertimeUndertime $overtimeUndertime)
    {
        // Check if the request belongs to the authenticated user
        if ($overtimeUndertime->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this request.');
        }

        $overtimeUndertime->load('employee');

        return view('employee.overtime-undertime.show', compact('overtimeUndertime'));
    }

    /**
     * Show the form for editing the specified overtime/undertime request.
     */
    public function edit(OvertimeUndertime $overtimeUndertime)
    {
        // Check if the request belongs to the authenticated user and can be edited
        if ($overtimeUndertime->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this request.');
        }

        if ($overtimeUndertime->status !== 'pending') {
            abort(403, 'Only pending requests can be edited.');
        }

        $types = ['overtime' => 'Overtime', 'undertime' => 'Undertime'];

        return view('employee.overtime-undertime.edit', compact('overtimeUndertime', 'types'));
    }

    /**
     * Update the specified overtime/undertime request in storage.
     */
    public function update(Request $request, OvertimeUndertime $overtimeUndertime)
    {
        // Check if the request belongs to the authenticated user and can be edited
        if ($overtimeUndertime->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this request.');
        }

        if ($overtimeUndertime->status !== 'pending') {
            abort(403, 'Only pending requests can be edited.');
        }

        $validated = $request->validate([
            'type' => 'required|in:overtime,undertime',
            'date' => 'required|date|before_or_equal:today',
            'hours' => 'required|numeric|min:0.5|max:24',
            'reason' => 'required|string|max:1000',
        ]);

        // Get the authenticated user's hourly rate
        $user = auth()->user();
        $hourlyRate = $user->salary_rate / 8;

        // Compute amount
        // OT rate = hourlyRate * 1.25
        // OT pay = otHours * otRate
        // UT deduction = utHours * hourlyRate (negative)
        $amount = $validated['type'] === 'overtime'
            ? $validated['hours'] * $hourlyRate * 1.25
            : -($validated['hours'] * $hourlyRate);

        $validated['amount'] = $amount;
        $validated['hourly_rate_used'] = $hourlyRate;

        $overtimeUndertime->update($validated);
        $overtimeUndertime->load('employee');

        // Send notification about the update
        OvertimeNotification::updated($overtimeUndertime);

        return redirect()->route('employee.overtime-undertime.show', $overtimeUndertime->id)->with('success', 'Overtime/Undertime request updated successfully.');
    }

    /**
     * Remove the specified overtime/undertime request from storage.
     */
    public function destroy(OvertimeUndertime $overtimeUndertime)
    {
        // Check if the request belongs to the authenticated user and can be deleted
        if ($overtimeUndertime->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this request.');
        }

        if ($overtimeUndertime->status !== 'pending') {
            abort(403, 'Only pending requests can be deleted.');
        }

        $overtimeUndertime->load('employee');
        OvertimeNotification::deleted($overtimeUndertime);
        $overtimeUndertime->delete();

        return redirect()->route('employee.overtime-undertime.index')->with('success', 'Overtime/Undertime request deleted successfully.');
    }
}
