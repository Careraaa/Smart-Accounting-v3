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

        // Auto-determine status based on office hours and grace period
        $status = 'present'; // default
        if (!$request->time_in || !$request->time_out) {
            $status = 'absent';
        } else {
            $timeIn = Carbon::createFromFormat('H:i', $request->time_in);
            $timeOut = Carbon::createFromFormat('H:i', $request->time_out);
            $officeStart = Carbon::createFromFormat('H:i', '08:00');
            $officeEnd = Carbon::createFromFormat('H:i', '17:00');
            
            // Get grace period from settings (default: 5 minutes)
            $gracePeriodMinutes = (int) Setting::get('attendance.grace_period_minutes', 5);
            
            // Apply grace period: only mark as late if beyond (officeStart + gracePeriod)
            $lateThreshold = $officeStart->copy()->addMinutes($gracePeriodMinutes);
            
            // Check if employee came in late (after grace period)
            if ($timeIn->isAfter($lateThreshold)) {
                $status = 'late';
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
        // Uses AttendanceService with proper shift-based calculation.
        // Requirements:
        // 1. Follow set work hours from shift schedule
        // 2. Lunch break is unpaid (deducted from total hours)
        // 3. Overtime only starts if employee renders ≥30 mins beyond scheduled timeout
        // 4. Both OT and UT use 0.5-hour increments (formula: floor(minutes/30)*0.5)
        // 5. Overtime and undertime do NOT offset each other
        $otutMessage = null;

        if ($request->time_in && $request->time_out) {
            $dateStr  = $request->date;
            
            // Fetch the attendance record we just created
            $attendance = Attendance::where('user_id', $userId)
                ->where('date', $dateStr)
                ->first();

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

                    // Remove any existing auto-detected OT/UT records for this day
                    OvertimeUndertime::where('user_id', $userId)
                        ->where('date', $dateStr)
                        ->where('reason', 'like', 'Auto-detected from manual attendance log%')
                        ->delete();

                    // Create overtime record if applicable
                    if ($overtimeHours > 0) {
                        $otAmount = $overtimeHours * $hourlyRate * 1.25;
                        
                        OvertimeUndertime::create([
                            'user_id'          => $userId,
                            'date'             => $dateStr,
                            'type'             => 'overtime',
                            'hours'            => $overtimeHours,
                            'reason'           => "Auto-detected from manual attendance log (time out: " . Carbon::parse($dateStr . ' ' . $request->time_out)->format('g:i A') . ")",
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
                            'reason'           => "Auto-detected from manual attendance log (time in: " . Carbon::parse($dateStr . ' ' . $request->time_in)->format('g:i A') . ")",
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
