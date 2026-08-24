<?php

namespace App\Http\Controllers;

use App\Http\Controllers\HR\AttendanceController;
use App\Models\AttendanceToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class QRLoginController extends Controller
{
    public function showLoginForm($token)
    {
        $tokenRecord = AttendanceToken::where('token', $token)
            ->where('used', false)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$tokenRecord) {
            return redirect()->route('login')->withErrors(['token' => 'This QR code is invalid or has expired.']);
        }

        return view('attendance.qr-login', ['token' => $token]);
    }

    public function login(Request $request, $token)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $tokenRecord = AttendanceToken::where('token', $token)
            ->where('used', false)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$tokenRecord) {
            return back()->withErrors(['token' => 'This QR code is invalid or has expired.']);
        }

        $user = User::where(DB::raw('BINARY username'), $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['username' => 'Invalid credentials.'])->withInput();
        }

        if ($user->status !== 'active') {
            return back()->withErrors(['username' => 'Your account is inactive.'])->withInput();
        }

        Auth::loginUsingId($user->id);
        $request->session()->regenerate();

        $user->update([
            'is_logged_in' => true,
            'last_login_at' => now(),
        ]);

        // Record attendance (token consumed inside)
        try {
            AttendanceController::processQRAttendance($user, $tokenRecord);
        } catch (\Throwable $e) {
            logger()->error('QR login attendance failed: ' . $e->getMessage());
        }

        $request->session()->regenerate();

        return match ($user->role) {
            'superadmin' => redirect()->route('superadmin.dashboard'),
            'remittance_clerk' => redirect()->route('remittance-clerk.index'),
            'accountant' => redirect()->route('accountant.index'),
            'hr' => redirect()->route('hr.index'),
            'qr_admin' => redirect()->route('admin.dashboard'),
            'employee' => redirect()->route('employee.index'),
            default => redirect()->intended(route('dashboard', absolute: false)),
        };
    }
}
