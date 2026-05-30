<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\AttendanceToken;
use App\Models\Employee;
use App\Models\OvertimeUndertime;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\User;
use App\Notifications\AttendanceNotification;
use App\Notifications\OvertimeNotification;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'records');
        // All employees (excluding system roles) - paginated for display
        $employees = Employee::whereNotIn('role', ['superadmin', 'qr_admin'])
            ->orderBy('department')
            ->orderBy('last_name')
            ->paginate(10);

        // ALL employees (excluding system roles) for search - unpaginated
        $allEmployees = Employee::whereNotIn('role', ['superadmin', 'qr_admin'])
            ->orderBy('department')
            ->orderBy('last_name')
            ->get();

        // All distinct departments from database
        $departments = Employee::whereNotIn('role', ['superadmin', 'qr_admin'])
            ->distinct()
            ->whereNotNull('department')
            ->pluck('department')
            ->filter()
            ->sort()
            ->values();

        // Today's attendance keyed by employee id
        $todayAttendance = Attendance::whereDate('date', today())
            ->get()
            ->keyBy('user_id');

        // Recent logs for the side panel (last 15)
        $recentLogs = AttendanceLog::with('user')
            ->latest('logged_at')
            ->limit(15)
            ->get();

        // OT/UT summary data
        $pendingOT = OvertimeUndertime::where('status', 'pending')->where('type', 'overtime')->count();
        $pendingUT = OvertimeUndertime::where('status', 'pending')->where('type', 'undertime')->count();

        $allOtRequests = OvertimeUndertime::with('employee')
            ->whereHas('employee', fn($q) => $q->whereNotIn('role', ['superadmin', 'qr_admin']))
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingCount  = OvertimeUndertime::where('status', 'pending')->count();
        $approvedCount = OvertimeUndertime::where('status', 'approved')->count();
        $rejectedCount = OvertimeUndertime::where('status', 'rejected')->count();

        return view('hr.attendance.combined', compact(
            'tab', 'employees', 'allEmployees', 'departments', 'todayAttendance', 'recentLogs',
            'pendingOT', 'pendingUT', 'allOtRequests', 'pendingCount', 'approvedCount', 'rejectedCount'
        ));
    }

    /**
     * Show the per-employee attendance calendar.
     */
    public function employeeCalendar(Request $request, $employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        // Default to current month; allow ?month=YYYY-MM
        $monthParam = $request->query('month');
        $month = $monthParam ? \Carbon\Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth() : now()->startOfMonth();

        // Fetch all attendance records for this employee in the displayed month
        $attendances = Attendance::where('user_id', $employeeId)
            ->whereBetween('date', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
            ->get()
            ->keyBy(fn($a) => $a->date->format('Y-m-d'));

        // Fetch OT/UT records for the same month, grouped by date
        $otutRecords = OvertimeUndertime::where('user_id', $employeeId)
            ->whereBetween('date', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
            ->where('status', 'approved')
            ->get()
            ->groupBy(fn($r) => $r->date->format('Y-m-d'));

        $prevMonth = $month->copy()->subMonth()->format('Y-m');
        $nextMonth = $month->copy()->addMonth()->format('Y-m');

        return view('hr.attendance.calendar', compact('employee', 'month', 'attendances', 'otutRecords', 'prevMonth', 'nextMonth'));
    }

    public function generateQR()
    {
        AttendanceToken::where('expires_at', '<', now())->delete();

        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $token = '';
        for ($i = 0; $i < 8; $i++) {
            $token .= $characters[rand(0, strlen($characters) - 1)];
        }

        AttendanceToken::create([
            'token' => $token,
            'expires_at' => now()->addSeconds(60),
            'user_id' => auth()->id(),
        ]);

        cache()->put('current_qr_token', $token, 65);

        return response()->json(['token' => $token]);
    }

    public function showQR()
    {
        return view('hr.QR.index');
    }

    public function scan()
    {
        return view('attendance.scan');
    }

    public function submit(Request $request)
    {
        $request->validate(['token' => 'required']);
        $token = AttendanceToken::where('token', $request->token)->where('used', false)->where('expires_at', '>=', now())->first();

        if (!$token) {
            return response()->json(['message' => 'Invalid or expired QR'], 403);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // New QR scan rules:
        // - First scan of the day for employee => time_in
        // - All subsequent scans => time_out (always overwrite to keep the last scan)
        // - If an existing attendance was created manually (`is_manual`), do not overwrite any non-null manual fields
        $today = today();
        $currentTime = now();

        // Get today's logs to decide if this is the first scan
        $todayLogs = AttendanceLog::where('user_id', $user->id)
            ->whereDate('logged_at', $today)
            ->orderBy('logged_at')
            ->get();

        $isFirstScan = $todayLogs->isEmpty();
        $type = $isFirstScan ? 'time_in' : 'time_out';

        // Create a log entry for the scan
        AttendanceLog::create([
            'user_id' => $user->id,
            'type' => $type,
            'logged_at' => $currentTime,
        ]);

        // Load or initialize today's attendance record
        $attendance = Attendance::firstOrNew(['user_id' => $user->id, 'date' => $today]);

        // Respect manual attendance entries: do not overwrite existing manual fields
        $isManual = (bool) $attendance->is_manual;

        if ($type === 'time_in') {
            // Only set time_in if it doesn't already exist. Never overwrite a manual time_in.
            if (empty($attendance->time_in)) {
                $attendance->time_in = $currentTime->format('H:i:s');
                $attendance->status = $attendance->status ?? 'present';
                $attendance->is_manual = false;
                $attendance->save();
            }
        } else {
            // time_out: set/overwrite the time_out so the last scan is preserved,
            // but do not overwrite an existing manual time_out.
            if ($isManual) {
                if (empty($attendance->time_out)) {
                    $attendance->time_out = $currentTime->format('H:i:s');
                    $attendance->save();
                }
            } else {
                // If attendance is brand-new (no record), ensure a record exists with time_out
                $attendance->time_out = $currentTime->format('H:i:s');
                $attendance->is_manual = false;
                $attendance->save();
            }
        }

        $token->update(['used' => true]);

        // Notification (safe)
        try {
            if ($user && $user->salary_rate) {
                AttendanceNotification::attendanceRecorded($user, $type, $today);
            }
        } catch (\Throwable $e) {
            logger()->warning('AttendanceNotification failed: ' . $e->getMessage());
        }

        // ── Auto OT/UT detection (run after QR scan) ────────────────────────
        // Reuse same logic as manual `store()` to auto-detect OT/UT when both
        // time_in and time_out are present for the day.
        try {
            $userId = $user->id;
            if ($attendance && $attendance->time_in && $attendance->time_out) {
                $dateStr = $attendance->date->format('Y-m-d');

                // Get employee and shift
                $employee = Employee::find($userId);
                $shift = null;
                if ($employee && isset($employee->shift_id) && $employee->shift_id) {
                    $shift = Shift::find($employee->shift_id);
                }
                if (!$shift) {
                    $shift = Shift::where('is_active', true)->first();
                }

                if ($shift) {
                    $attendanceService = new AttendanceService();
                    $otutResult = $attendanceService->calculateOvertimeAndUndertime($attendance, $shift);

                    $overtimeHours = $otutResult['overtime_hours'];
                    $undertimeHours = $otutResult['undertime_hours'];
                    $hourlyRate = $employee ? ($employee->salary_rate / 8) : 0;

                    $hasApprovedRecord = OvertimeUndertime::where('user_id', $userId)
                        ->where('date', $dateStr)
                        ->whereIn('type', ['overtime', 'undertime'])
                        ->where('status', 'approved')
                        ->exists();

                    if (!$hasApprovedRecord) {
                        // Remove existing pending OT/UT for the day to avoid duplicates
                        OvertimeUndertime::where('user_id', $userId)
                            ->where('date', $dateStr)
                            ->where('status', 'pending')
                            ->delete();

                        if ($overtimeHours > 0) {
                            $otAmount = $overtimeHours * $hourlyRate * 1.25;
                            OvertimeUndertime::create([
                                'user_id' => $userId,
                                'date' => $dateStr,
                                'type' => 'overtime',
                                'hours' => $overtimeHours,
                                'reason' => "Auto-detected from QR scan (time out: " . Carbon::parse($dateStr . ' ' . $attendance->time_out)->format('g:i A') . ")",
                                'status' => 'pending',
                                'amount' => $otAmount,
                                'hourly_rate_used' => $hourlyRate,
                            ]);

                            try {
                                $otRecord = OvertimeUndertime::where('user_id', $userId)
                                    ->where('date', $dateStr)
                                    ->where('type', 'overtime')
                                    ->latest()
                                    ->first();
                                if ($otRecord) {
                                    $otRecord->load('employee');
                                    OvertimeNotification::submitted($otRecord);
                                }
                            } catch (\Throwable $e) {
                                logger()->warning('OvertimeNotification failed: ' . $e->getMessage());
                            }
                        } elseif ($undertimeHours > 0) {
                            $utAmount = -($undertimeHours * $hourlyRate);
                            OvertimeUndertime::create([
                                'user_id' => $userId,
                                'date' => $dateStr,
                                'type' => 'undertime',
                                'hours' => $undertimeHours,
                                'reason' => "Auto-detected from QR scan (time in: " . Carbon::parse($dateStr . ' ' . $attendance->time_in)->format('g:i A') . ")",
                                'status' => 'pending',
                                'amount' => $utAmount,
                                'hourly_rate_used' => $hourlyRate,
                            ]);

                            try {
                                $utRecord = OvertimeUndertime::where('user_id', $userId)
                                    ->where('date', $dateStr)
                                    ->where('type', 'undertime')
                                    ->latest()
                                    ->first();
                                if ($utRecord) {
                                    $utRecord->load('employee');
                                    OvertimeNotification::submitted($utRecord);
                                }
                            } catch (\Throwable $e) {
                                logger()->warning('OvertimeNotification failed: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            logger()->warning('QR OT/UT detection failed: ' . $e->getMessage());
        }
        // ─────────────────────────────────────────────────────────────────────

        return response()->json([
            'message' => ucfirst(str_replace('_', ' ', $type)) . ' recorded',
            'type' => $type,
            'employee_name' => $user->name,
        ]);
    }

    public function scanPage()
    {
        $lastLog = AttendanceLog::where('user_id', auth()->id())
            ->latest('logged_at')
            ->select(['type', 'logged_at'])
            ->first();

        return view('attendance.scan', ['lastLog' => $lastLog]);
    }

    /**
     * Returns the authenticated user's last log entry for today,
     * plus the full list of today's logs — used by the employee dashboard.
     */
    public function getLastLog()
    {
        $userId = auth()->id();
        $today = now()->toDateString();

        $logs = AttendanceLog::where('user_id', $userId)->whereDate('logged_at', $today)->orderBy('logged_at')->get()->map(
            fn($log) => [
                'type' => $log->type,
                'time' => $log->logged_at->setTimezone(config('app.timezone'))->format('g:i A'),
            ],
        );

        $last = $logs->last();

        return response()->json([
            'type' => $last['type'] ?? null,
            'time' => $last['time'] ?? null,
            'logs' => $logs->values(),
        ]);
    }

    public function showMonitorDisplay()
    {
        // The QR monitor is part of the attendance index view
        // Redirect to the attendance list which includes the QR monitor panel
        return redirect()->route('attendance.index');
    }

    public function getRecentAttendance()
    {
        $recentLogs = AttendanceLog::with('user')
            ->latest('logged_at')
            ->limit(20)
            ->get()
            ->map(function ($log) {
                $localTime = $log->logged_at->setTimezone(config('app.timezone'));

                return [
                    'id' => $log->id,
                    'employee_name' => $log->user->name ?? 'Unknown',
                    'type' => $log->type,
                    'time' => $localTime->format('g:i A'),
                    'date' => $localTime->format('M d, Y'),
                    'badge_color' => $log->type === 'time_in' ? 'success' : 'warning',
                ];
            });

        return response()->json($recentLogs);
    }

    public function checkQRTokenStatus()
    {
        $currentToken = cache()->get('current_qr_token');

        if (!$currentToken) {
            return response()->json(['status' => 'no_token', 'used' => false]);
        }

        $token = AttendanceToken::where('token', $currentToken)->first();

        if (!$token) {
            return response()->json(['status' => 'expired', 'used' => false]);
        }

        return response()->json([
            'status' => 'active',
            'used' => (bool) $token->used,
            'token' => $token->token,
        ]);
    }

    public function create()
    {
        $employees = Employee::whereNotIn('role', ['superadmin', 'qr_admin'])->get();
        $gracePeriodMinutes = (int) Setting::get('attendance.grace_period_minutes', 5);
        return view('hr.attendance.create', compact('employees', 'gracePeriodMinutes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'employee_identifier' => 'nullable|string|max:255',
            'date' => 'required|date',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
        ]);

        $userId = $request->input('user_id');
        $identifier = trim((string) $request->input('employee_identifier', ''));

        if (!$userId && $identifier !== '') {
            $typedEmployee = User::query()
                ->where('username', $identifier)
                ->orWhereRaw('LOWER(name) = ?', [strtolower($identifier)])
                ->first();

            if ($typedEmployee) {
                $userId = $typedEmployee->id;
            }
        }

        if (!$userId) {
            return back()
                ->withErrors(['user_id' => 'Please select an employee or enter a valid username.'])
                ->withInput();
        }

        // Require at least one time value
        if (!$request->time_in && !$request->time_out) {
            return back()
                ->withErrors(['time' => 'Please set at least Time In or Time Out.'])
                ->withInput();
        }

        // Build data to save — only include provided fields so we don't overwrite existing values
        $data = ['is_manual' => true];

        // Determine status when time_in is provided (and optionally time_out).
        // If both provided, compute status using both; if only time_in provided, compute late/present.
        $gracePeriodMinutes = (int) Setting::get('attendance.grace_period_minutes', 5);

        if ($request->time_in) {
            $data['time_in'] = $request->time_in ? $request->time_in . ':00' : null;

            try {
                $timeIn = Carbon::createFromFormat('H:i', $request->time_in);
                $officeStart = Carbon::createFromFormat('H:i', '08:00');
                $lateThreshold = $officeStart->copy()->addMinutes($gracePeriodMinutes);

                $data['status'] = $timeIn->isAfter($lateThreshold) ? 'late' : 'present';
            } catch (\Throwable $e) {
                $data['status'] = 'present';
            }
        }

        if ($request->time_out) {
            $data['time_out'] = $request->time_out ? $request->time_out . ':00' : null;
        }

        // If both provided, run the stricter status logic using both times
        if ($request->time_in && $request->time_out) {
            try {
                $timeIn = Carbon::createFromFormat('H:i', $request->time_in);
                $timeOut = Carbon::createFromFormat('H:i', $request->time_out);
                $officeStart = Carbon::createFromFormat('H:i', '08:00');
                $lateThreshold = $officeStart->copy()->addMinutes($gracePeriodMinutes);

                $data['status'] = $timeIn->isAfter($lateThreshold) ? 'late' : 'present';
            } catch (\Throwable $e) {
                $data['status'] = $data['status'] ?? 'present';
            }
        }

        // Find existing attendance or create new one without mass-nullifying fields
        $attendance = Attendance::firstOrNew(['user_id' => $userId, 'date' => $request->date]);
        $attendance->fill($data);
        $attendance->is_manual = true;
        $attendance->save();

        try {
            $user = User::find($userId);
            if ($user) {
                AttendanceNotification::attendanceRecorded($user, 'manual_entry', Carbon::parse($request->date));
            }
        } catch (\Throwable $e) {
            logger()->warning('AttendanceNotification failed: ' . $e->getMessage());
        }

        // ── Auto OT/UT detection ──────────────────────────────────────────────
        // Uses AttendanceService with proper shift-based calculation.
        // Requirements:
        // 1. Follow set work hours from shift schedule
        // 2. Lunch break is unpaid (deducted from total hours)
        // 3. Overtime only starts if employee renders ≥30 mins beyond scheduled timeout
        // 4. Both OT and UT use 0.5-hour increments (formula: floor(minutes/30)*0.5)
        // 5. Overtime and undertime do NOT offset each other
        $otutMessage = null;

        // Run OT/UT detection if the saved attendance now has both time_in and time_out
        if ($attendance->time_in && $attendance->time_out) {
            $dateStr = $attendance->date->format('Y-m-d');

            if ($attendance) {
                // Get employee to find their shift assignment
                $employee = Employee::find($userId);
                
                // Try to get shift from employee (if shift_id exists) or use default shift
                $shift = null;
                if ($employee) {
                    // Check if employee has a shift_id attribute
                    if (isset($employee->shift_id) && $employee->shift_id) {
                        $shift = Shift::find($employee->shift_id);
                    }
                }
                
                // Fallback to active shift if not assigned to employee
                if (!$shift) {
                    $shift = Shift::where('is_active', true)->first();
                }

                // If we have a shift, calculate OT/UT using the new logic
                if ($shift) {
                    $attendanceService = new AttendanceService();
                    $otutResult = $attendanceService->calculateOvertimeAndUndertime($attendance, $shift);

                    $overtimeHours = $otutResult['overtime_hours'];
                    $undertimeHours = $otutResult['undertime_hours'];
                    $hourlyRate = $employee ? ($employee->salary_rate / 8) : 0;

                    // Remove any existing OT/UT records for this day (both pending and approved)
                    // so they are recalculated based on the updated attendance times.
                    OvertimeUndertime::where('user_id', $userId)
                        ->where('date', $dateStr)
                        ->delete();

                    // Create overtime record if applicable
                    if ($overtimeHours > 0) {
                        $otAmount = $overtimeHours * $hourlyRate * 1.25;
                        
                        OvertimeUndertime::create([
                            'user_id'          => $userId,
                            'date'             => $dateStr,
                            'type'             => 'overtime',
                            'hours'            => $overtimeHours,
                            'reason'           => "Auto-detected from manual attendance log (time out: " . Carbon::parse($dateStr . ' ' . $attendance->time_out)->format('g:i A') . ")",
                            'status'           => 'pending',
                            'amount'           => $otAmount,
                            'hourly_rate_used' => $hourlyRate,
                        ]);

                        try {
                            $otRecord = OvertimeUndertime::where('user_id', $userId)
                                ->where('date', $dateStr)
                                ->where('type', 'overtime')
                                ->latest()
                                ->first();
                            if ($otRecord) {
                                $otRecord->load('employee');
                                OvertimeNotification::submitted($otRecord);
                            }
                        } catch (\Throwable $e) {
                            logger()->warning('OvertimeNotification failed: ' . $e->getMessage());
                        }

                        $otutMessage = "Overtime of " . number_format($overtimeHours, 1) . "h auto-logged and pending approval.";
                    }
                    // Create undertime record if applicable
                    elseif ($undertimeHours > 0) {
                        $utAmount = -($undertimeHours * $hourlyRate);
                        
                        OvertimeUndertime::create([
                            'user_id'          => $userId,
                            'date'             => $dateStr,
                            'type'             => 'undertime',
                            'hours'            => $undertimeHours,
                            'reason'           => "Auto-detected from manual attendance log (time in: " . Carbon::parse($dateStr . ' ' . $attendance->time_in)->format('g:i A') . ")",
                            'status'           => 'pending',
                            'amount'           => $utAmount,
                            'hourly_rate_used' => $hourlyRate,
                        ]);

                        try {
                            $utRecord = OvertimeUndertime::where('user_id', $userId)
                                ->where('date', $dateStr)
                                ->where('type', 'undertime')
                                ->latest()
                                ->first();
                            if ($utRecord) {
                                $utRecord->load('employee');
                                OvertimeNotification::submitted($utRecord);
                            }
                        } catch (\Throwable $e) {
                            logger()->warning('OvertimeNotification failed: ' . $e->getMessage());
                        }

                        $otutMessage = "Undertime of " . number_format($undertimeHours, 1) . "h auto-logged and pending approval.";
                    }
                }
            }
        }
        // ─────────────────────────────────────────────────────────────────────

        $successMsg = 'Attendance record saved successfully.';
        if ($otutMessage) {
            $successMsg .= ' ' . $otutMessage;
        }

        return redirect()->route('attendance.index')->with('success', $successMsg);
    }

    public function getAttendanceTableRows()
    {
        $attendances = Attendance::with('employee')
            ->orderBy('date', 'desc')
            ->take(50)
            ->get()
            ->map(function ($att) {
                return [
                    'employee' => $att->employee ? $att->employee->first_name . ' ' . $att->employee->last_name : 'Unknown',
                    'date' => $att->date ? \Carbon\Carbon::parse($att->date)->format('M d, Y') : '—',
                    'time_in' => $att->time_in ? \Carbon\Carbon::createFromFormat('H:i:s', $att->time_in)->format('g:i A') : '—',
                    'time_out' => $att->time_out ? \Carbon\Carbon::createFromFormat('H:i:s', $att->time_out)->format('g:i A') : '—',
                    'status' => $att->status ?? 'unknown',
                    'is_manual' => (bool) $att->is_manual,
                ];
            });

        return response()->json(['rows' => $attendances]);
    }

    public function getShiftBreakTimes()
    {
        $userId = auth()->id();
        $employee = Employee::find($userId);

        // Try to get shift from employee or use default
        $shift = null;
        if ($employee && isset($employee->shift_id) && $employee->shift_id) {
            $shift = Shift::find($employee->shift_id);
        } else {
            $shift = Shift::where('is_active', true)->first();
        }

        // Get grace period from settings
        $gracePeriodMinutes = (int) Setting::get('attendance.grace_period_minutes', 5);

        if ($shift && $shift->break_start && $shift->break_end) {
            return response()->json([
                'break_start' => $shift->break_start,
                'break_end' => $shift->break_end,
                'start_time' => $shift->start_time,
                'grace_period_minutes' => $gracePeriodMinutes,
            ]);
        }

        // Fallback: 1 hour break (12:00-13:00), shift starts at 8:00 AM
        return response()->json([
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
            'start_time' => '08:00:00',
            'grace_period_minutes' => $gracePeriodMinutes,
        ]);
    }
}
