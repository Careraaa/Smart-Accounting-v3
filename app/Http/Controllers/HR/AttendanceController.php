<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\AttendanceToken;
use App\Models\Employee;
use App\Notifications\AttendanceNotification;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('employee')
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('hr.attendance.index', compact('attendances'));
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
            'token'      => $token,
            'expires_at' => now()->addSeconds(60),
            'user_id'    => auth()->id(),
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

        $token = AttendanceToken::where('token', $request->token)
            ->where('used', false)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$token) {
            return response()->json(['message' => 'Invalid or expired QR'], 403);
        }

        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Determine whether this scan is a time_in or time_out
        $last = AttendanceLog::where('user_id', $user->id)
            ->latest('logged_at')
            ->first();

        $type = ($last && $last->type === 'time_in') ? 'time_out' : 'time_in';

        AttendanceLog::create([
            'user_id'   => $user->id,
            'type'      => $type,
            'logged_at' => now(),
        ]);

        // ── FIX: was using 'employee_id' which is NOT a column on the
        //         attendance table. The model fillable + seeder both use
        //         'user_id', so we write directly with the auth user's id. ──
        $today       = today();
        $currentTime = now()->format('H:i:s');

        if ($type === 'time_in') {
            Attendance::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'date'    => $today,
                ],
                [
                    'time_in'   => $currentTime,
                    'status'    => 'present',
                    'is_manual' => false,
                ]
            );
        } else {
            $attendance = Attendance::where('user_id', $user->id)
                ->where('date', $today)
                ->first();

            if ($attendance) {
                $attendance->update(['time_out' => $currentTime]);
            }
        }

        $token->update(['used' => true]);

        // Notification — guard against missing employee relationship
        try {
            if ($user->employee) {
                AttendanceNotification::attendanceRecorded($user, $type, $today);
            }
        } catch (\Throwable $e) {
            // Non-critical — don't let a notification failure break the scan
            logger()->warning('AttendanceNotification failed: ' . $e->getMessage());
        }

        return response()->json([
            'message'       => ucfirst(str_replace('_', ' ', $type)) . ' recorded',
            'type'          => $type,
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
        $today  = now()->toDateString();

        $logs = AttendanceLog::where('user_id', $userId)
            ->whereDate('logged_at', $today)
            ->orderBy('logged_at')
            ->get()
            ->map(fn ($log) => [
                'type' => $log->type,
                'time' => $log->logged_at
                    ->setTimezone(config('app.timezone'))
                    ->format('g:i A'),
            ]);

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
                    'id'            => $log->id,
                    'employee_name' => $log->user->name ?? 'Unknown',
                    'type'          => $log->type,
                    'time'          => $localTime->format('g:i A'),
                    'date'          => $localTime->format('M d, Y'),
                    'badge_color'   => $log->type === 'time_in' ? 'success' : 'warning',
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
            'used'   => (bool) $token->used,
            'token'  => $token->token,
        ]);
    }

    public function create()
    {
        $employees = Employee::where('role', '!=', 'superadmin')->get();
        return view('hr.attendance.create', compact('employees'));
    }

    public function store(Request $request)
    {
        // ── FIX: create blade sends 'user_id' but validation was checking
        //         'employee_id' — they must match, and the column is user_id. ──
        $request->validate([
            'user_id'  => 'required|exists:users,id',
            'date'     => 'required|date',
            'time_in'  => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'status'   => 'required|in:present,absent,late,early_leave',
        ]);

        Attendance::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'date'    => $request->date,
            ],
            [
                'time_in'   => $request->time_in   ? $request->time_in  . ':00' : null,
                'time_out'  => $request->time_out  ? $request->time_out . ':00' : null,
                'status'    => $request->status,
                'is_manual' => true,
            ]
        );

        // Notification — find user for the notification
        try {
            $user = \App\Models\User::find($request->user_id);
            if ($user) {
                AttendanceNotification::attendanceRecorded(
                    $user,
                    'manual_entry',
                    \Carbon\Carbon::parse($request->date)
                );
            }
        } catch (\Throwable $e) {
            logger()->warning('AttendanceNotification failed: ' . $e->getMessage());
        }

        return redirect()->route('attendance.index')
            ->with('success', 'Attendance record saved successfully.');
    }
}