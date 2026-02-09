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

        $token = Str::random(20);

        AttendanceToken::create([
            'token' => $token,
            'expires_at' => now()->addSeconds(10)
        ]);

        // Generate a complete URL for the QR code
        $qrUrl = route('hr.qr.submit', [], false) . '?token=' . $token;

        return response()->json(['token' => $qrUrl]);
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

        $token->update(['used' => true]);

        return response()->json([
            'message' => ucfirst(str_replace('_', ' ', $type)) . ' recorded'
        ]);
    }

    public function scanPage()
    {
        return view('attendance.scan');
    }
}
