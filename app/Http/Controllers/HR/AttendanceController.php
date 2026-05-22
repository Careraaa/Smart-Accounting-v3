<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\AttendanceToken;
use App\Models\Employee;
use App\Models\OvertimeUndertime;
use App\Models\Shift;
use App\Models\User;
use App\Notifications\AttendanceNotification;
use App\Notifications\OvertimeNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
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

        return view('hr.attendance.index', compact('employees', 'allEmployees', 'departments', 'todayAttendance', 'recentLogs'));
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

        $prevMonth = $month->copy()->subMonth()->format('Y-m');
        $nextMonth = $month->copy()->addMonth()->format('Y-m');

        return view('hr.attendance.calendar', compact('employee', 'month', 'attendances', 'prevMonth', 'nextMonth'));
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

        // Determine log type
        $last = AttendanceLog::where('user_id', $user->id)->latest('logged_at')->first();
        $type = $last && $last->type === 'time_in' ? 'time_out' : 'time_in';

        AttendanceLog::create([
            'user_id' => $user->id,
            'type' => $type,
            'logged_at' => now(),
        ]);

        $today = today();
        $currentTime = now()->format('H:i:s');

        if ($type === 'time_in') {
            Attendance::updateOrCreate(['user_id' => $user->id, 'date' => $today], ['time_in' => $currentTime, 'status' => 'present', 'is_manual' => false]);
        } else {
            $attendance = Attendance::where('user_id', $user->id)->where('date', $today)->first();
            if ($attendance) {
                $attendance->update(['time_out' => $currentTime]);
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
        return view('hr.attendance.create', compact('employees'));
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

        // Auto-determine status based on office hours (08:00 - 17:00)
        $status = 'present'; // default
        if (!$request->time_in || !$request->time_out) {
            $status = 'absent';
        } else {
            $timeIn = Carbon::createFromFormat('H:i', $request->time_in);
            $timeOut = Carbon::createFromFormat('H:i', $request->time_out);
            $officeStart = Carbon::createFromFormat('H:i', '08:00');
            $officeEnd = Carbon::createFromFormat('H:i', '17:00');
            
            // Check if employee came in late
            if ($timeIn->isAfter($officeStart)) {
                $status = 'late';
            }
            // Check if employee left early
            elseif ($timeOut->isBefore($officeEnd)) {
                $status = 'early_leave';
            }
        }

        Attendance::updateOrCreate(
            ['user_id' => $userId, 'date' => $request->date],
            [
                'time_in' => $request->time_in ? $request->time_in . ':00' : null,
                'time_out' => $request->time_out ? $request->time_out . ':00' : null,
                'status' => $status,
                'is_manual' => true,
            ],
        );

        try {
            $user = User::find($userId);
            if ($user) {
                AttendanceNotification::attendanceRecorded($user, 'manual_entry', Carbon::parse($request->date));
            }
        } catch (\Throwable $e) {
            logger()->warning('AttendanceNotification failed: ' . $e->getMessage());
        }

        // ── Auto OT/UT detection ──────────────────────────────────────────────
        $otutMessage = null;

        if ($request->time_in && $request->time_out) {
            $dateStr  = $request->date;
            $timeIn   = Carbon::parse($dateStr . ' ' . $request->time_in);
            $timeOut  = Carbon::parse($dateStr . ' ' . $request->time_out);

            // Get employee to find their shift assignment
            $employee = Employee::find($userId);
            
            // Try to get shift from employee (if shift_id exists) or use default shift
            $shift = null;
            if ($employee) {
                // Check if employee has a shift_id attribute
                if (isset($employee->shift_id) && $employee->shift_id) {
                    $shift = Shift::find($employee->shift_id);
                } else {
                    // Otherwise, try to get the active/default shift
                    $shift = Shift::where('is_active', true)->first();
                }
            }

            // Calculate break duration from shift's break times
            // Only count the break time that overlaps with the logged time period
            $breakMinutes = 0;
            if ($shift && $shift->break_start && $shift->break_end) {
                // Create break times with the same date as the logged time
                $breakStart = Carbon::parse($dateStr . ' ' . $shift->break_start);
                $breakEnd = Carbon::parse($dateStr . ' ' . $shift->break_end);
                
                // Handle break that spans across end of shift
                if ($breakEnd->lessThan($breakStart)) {
                    $breakEnd->addHours(24);
                }
                
                // Calculate the overlap between logged time and break time
                // Overlap starts at: max(time_in, break_start)
                // Overlap ends at: min(time_out, break_end)
                $overlapStart = $timeIn->copy();
                if ($breakStart->isAfter($overlapStart)) {
                    $overlapStart = $breakStart->copy();
                }
                
                $overlapEnd = $timeOut->copy();
                if ($breakEnd->isBefore($overlapEnd)) {
                    $overlapEnd = $breakEnd->copy();
                }
                
                // Only count overlap if it's valid (start before end)
                if ($overlapStart->isBefore($overlapEnd)) {
                    $breakMinutes = abs($overlapStart->diffInMinutes($overlapEnd));
                }
                
                logger()->info('Manual attendance: Shift found', [
                    'shift_name' => $shift->name,
                    'break_start' => $shift->break_start,
                    'break_end' => $shift->break_end,
                    'break_minutes' => $breakMinutes,
                    'user_id' => $userId,
                    'date' => $dateStr,
                    'time_in' => $request->time_in,
                    'time_out' => $request->time_out,
                ]);
            } else {
                // Default: 1 hour break if no shift assigned
                $breakMinutes = 60;
                
                logger()->info('Manual attendance: No shift found, using default 1 hour break', [
                    'user_id' => $userId,
                    'shift_id' => $employee ? ($employee->shift_id ?? 'none') : 'no employee',
                    'date' => $dateStr,
                    'time_in' => $request->time_in,
                    'time_out' => $request->time_out,
                ]);
            }

            // Standard schedule: 08:00 – 17:00 (8 working hours + break)
            $schedStart = Carbon::parse($dateStr . ' 08:00');
            $schedEnd   = Carbon::parse($dateStr . ' 17:00');

            // Net minutes worked (subtract calculated break)
            $workedMinutes = max(0, $timeIn->diffInMinutes($timeOut) - $breakMinutes);

            // Standard = 8h = 480 min
            $standardMinutes = 480;
            $diffMinutes     = $workedMinutes - $standardMinutes;

            // Only act if deviation is at least 1 minute
            if (abs($diffMinutes) >= 1) {
                $hourlyRate = $employee ? ($employee->salary_rate / 8) : 0;
                $hours      = round(abs($diffMinutes) / 60, 2);
                $type       = $diffMinutes > 0 ? 'overtime' : 'undertime';

                $amount = $type === 'overtime'
                    ? $hours * $hourlyRate * 1.25
                    : -($hours * $hourlyRate);

                $reason = $type === 'overtime'
                    ? "Auto-detected from manual attendance log (time out: " . Carbon::parse($dateStr . ' ' . $request->time_out)->format('g:i A') . ")"
                    : "Auto-detected from manual attendance log (time in: " . Carbon::parse($dateStr . ' ' . $request->time_in)->format('g:i A') . ")";

                // Upsert: one OT/UT record per employee per date (from manual log)
                OvertimeUndertime::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'date'    => $dateStr,
                        'type'    => $type,
                    ],
                    [
                        'hours'            => $hours,
                        'reason'           => $reason,
                        'status'           => 'approved',
                        'approved_by'      => auth()->id(),
                        'amount'           => $amount,
                        'hourly_rate_used' => $hourlyRate,
                    ]
                );

                // Remove any opposite-type record for the same day (e.g. old UT if now OT)
                $oppositeType = $type === 'overtime' ? 'undertime' : 'overtime';
                OvertimeUndertime::where('user_id', $userId)
                    ->where('date', $dateStr)
                    ->where('type', $oppositeType)
                    ->whereIn('reason', [
                        "Auto-detected from manual attendance log (time out: " . Carbon::parse($dateStr . ' ' . $request->time_out)->format('g:i A') . ")",
                        "Auto-detected from manual attendance log (time in: " . Carbon::parse($dateStr . ' ' . $request->time_in)->format('g:i A') . ")",
                    ])
                    ->delete();

                try {
                    $otRecord = OvertimeUndertime::where('user_id', $userId)
                        ->where('date', $dateStr)
                        ->where('type', $type)
                        ->latest()
                        ->first();
                    if ($otRecord) {
                        $otRecord->load('employee');
                        OvertimeNotification::submitted($otRecord);
                    }
                } catch (\Throwable $e) {
                    logger()->warning('OvertimeNotification failed: ' . $e->getMessage());
                }

                $hoursLabel = number_format($hours, 2);
                $otutMessage = ucfirst($type) . " of {$hoursLabel}h auto-logged and approved.";
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

        if ($shift && $shift->break_start && $shift->break_end) {
            return response()->json([
                'break_start' => $shift->break_start,
                'break_end' => $shift->break_end,
            ]);
        }

        // Fallback: 1 hour break (12:00-13:00)
        return response()->json([
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);
    }
}
