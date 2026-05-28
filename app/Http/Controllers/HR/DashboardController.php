<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\CashAdvance;
use App\Models\Leave;
use App\Models\OvertimeUndertime;
use App\Models\SalaryLoan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Attendance trend (last 7 days) ──
        $attendanceTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $present = Attendance::whereDate('date', $date)->where('status', 'present')->count();
            $absent = Attendance::whereDate('date', $date)->where('status', 'absent')->count();
            $late = Attendance::whereDate('date', $date)->where('status', 'late')->count();

            $attendanceTrend[] = [
                'date_iso' => $date->toDateString(),
                'label' => $date->format('D') . ' · ' . $date->format('M j'),
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
            ];
        }

        // ── Leave Stats ──
        $approvedLeaves = Leave::whereIn('status', ['approved', 'paid'])->count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $rejectedLeaves = Leave::where('status', 'rejected')->count();

        // ── OT/UT Stats ──
        $pendingOT = OvertimeUndertime::where('status', 'pending')->where('type', 'overtime')->count();
        $pendingUT = OvertimeUndertime::where('status', 'pending')->where('type', 'undertime')->count();

        $weekStart = now()->startOfWeek(Carbon::MONDAY);
        $weekEnd = now()->endOfWeek(Carbon::SUNDAY);
        $otHoursThisWeek = OvertimeUndertime::where('status', 'approved')
            ->where('type', 'overtime')
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->sum('hours') ?? 0;

        $totalOTRecords = OvertimeUndertime::where('status', 'approved')
            ->where('type', 'overtime')->count();

        // ── Pending Approvals ──
        $pendingCashAdvances = CashAdvance::where('status', 'pending')->count();
        $pendingSalaryLoans  = SalaryLoan::where('status', 'pending')->count();

        return view('hr.index', compact(
            'attendanceTrend',
            'approvedLeaves',
            'pendingLeaves',
            'rejectedLeaves',
            'pendingOT',
            'pendingUT',
            'otHoursThisWeek',
            'totalOTRecords',
            'pendingCashAdvances',
            'pendingSalaryLoans',
        ));
    }
}
