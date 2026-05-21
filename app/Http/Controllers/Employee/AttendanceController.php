<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\OvertimeUndertime;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $monthParam = $request->query('month');
        $month = $monthParam
            ? Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth()
            : now()->startOfMonth();

        $monthStart = $month->copy()->startOfMonth();
        $monthEnd   = $month->copy()->endOfMonth();

        // Attendance records keyed by date string
        $attendances = Attendance::where('user_id', $userId)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->get()
            ->keyBy(fn($a) => $a->date->format('Y-m-d'));

        // OT/UT records keyed by date string (approved only)
        $otutRecords = OvertimeUndertime::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->get()
            ->groupBy(fn($r) => $r->date->format('Y-m-d'));

        $prevMonth = $month->copy()->subMonth()->format('Y-m');
        $nextMonth = $month->copy()->addMonth()->format('Y-m');

        // Summary counts for the month
        $presentCount = $attendances->where('status', 'present')->count();
        $lateCount    = $attendances->where('status', 'late')->count();
        $absentCount  = $attendances->where('status', 'absent')->count();
        $earlyCount   = $attendances->where('status', 'early_leave')->count();

        $allOtut     = $otutRecords->flatten();
        $otHours     = $allOtut->where('type', 'overtime')->sum('hours');
        $utHours     = $allOtut->where('type', 'undertime')->sum('hours');

        return view('employee.attendance.index', compact(
            'month',
            'attendances',
            'otutRecords',
            'prevMonth',
            'nextMonth',
            'presentCount',
            'lateCount',
            'absentCount',
            'earlyCount',
            'otHours',
            'utHours'
        ));
    }
}
