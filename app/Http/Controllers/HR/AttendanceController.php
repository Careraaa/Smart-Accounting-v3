<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\AttendanceToken;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

        // Generate 8 random alphanumeric characters (uppercase)
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

        // Store current token in cache for monitoring
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

        $last = AttendanceLog::where('user_id', $user->id)
            ->latest('logged_at')
            ->first();

        $type = $last && $last->type === 'time_in' ? 'time_out' : 'time_in';

        AttendanceLog::create([
            'user_id' => $user->id,
            'type' => $type,
            'logged_at' => now()
        ]);

        // Save to Attendance table
        if ($user->employee) {
            $today = today();
            $currentTime = now()->format('H:i:s');
            
            if ($type === 'time_in') {
                // Create new attendance record for today
                Attendance::updateOrCreate(
                    [
                        'employee_id' => $user->employee->id,
                        'date' => $today
                    ],
                    [
                        'time_in' => $currentTime,
                        'status' => 'present'
                    ]
                );
            } else { // time_out
                // Update existing attendance record
                $attendance = Attendance::where('employee_id', $user->employee->id)
                    ->where('date', $today)
                    ->first();
                
                if ($attendance) {
                    $attendance->update(['time_out' => $currentTime]);
                }
            }
        }

        $token->update(['used' => true]);

        $employeeName = $user->employee ? $user->employee->name : $user->name;

        return response()->json([
            'message' => ucfirst(str_replace('_', ' ', $type)) . ' recorded',
            'type' => $type,
            'employee_name' => $employeeName
        ]);
    }

    public function scanPage()
    {
        $lastLog = AttendanceLog::where('user_id', auth()->user()->id)
            ->latest('logged_at')
            ->select(['type', 'logged_at'])
            ->first();

        return view('attendance.scan', ['lastLog' => $lastLog]);
    }

    public function getLastLog()
    {
        $lastLog = AttendanceLog::where('user_id', auth()->user()->id)
            ->latest('logged_at')
            ->select(['type', 'logged_at'])
            ->first();

        if ($lastLog) {
            return response()->json([
                'type' => $lastLog->type,
                'time' => $lastLog->logged_at->format('g:i A')
            ]);
        }

        return response()->json(null);
    }

    public function showMonitorDisplay()
    {
        return view('hr.attendance.monitor');
    }

    public function getRecentAttendance()
    {
        $recentLogs = AttendanceLog::with(['user.employee'])
            ->latest('logged_at')
            ->limit(20)
            ->get()
            ->map(function ($log) {
                // Convert from UTC to the app's timezone
                $localTime = $log->logged_at->setTimezone(config('app.timezone'));
                
                return [
                    'id' => $log->id,
                    'employee_name' => $log->user->employee ? $log->user->employee->name : $log->user->name,
                    'type' => $log->type,
                    'time' => $localTime->format('g:i A'),
                    'date' => $localTime->format('M d, Y'),
                    'badge_color' => $log->type === 'time_in' ? 'success' : 'warning'
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
            'token' => $token->token
        ]);
    }
}