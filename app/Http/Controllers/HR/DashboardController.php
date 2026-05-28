<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Bonus;
use App\Models\CashAdvance;
use App\Models\Holiday;
use App\Models\Leave;
use App\Models\OvertimeUndertime;
use App\Models\PayrollBatch;
use App\Models\SalaryLoan;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Employee Stats ──
        $totalEmployees = User::where('role', 'employee')->where('status', 'active')->count();
        $newHiresThisMonth = User::where('role', 'employee')->where('status', 'active')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

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
        $td = $attendanceTrend[array_key_last($attendanceTrend)];
        $tdTotal = $td['present'] + $td['late'] + $td['absent'];
        $tdRate = $tdTotal > 0 ? round(($td['present'] / $tdTotal) * 100) : 0;

        // ── Leave Stats ──
        $approvedLeaves = Leave::whereIn('status', ['approved', 'paid'])->count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $rejectedLeaves = Leave::where('status', 'rejected')->count();

        // ── Leave Trend (last 6 months) ──
        $leaveTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $start = (clone $month)->startOfMonth();
            $end = (clone $month)->endOfMonth();
            $total = Leave::whereBetween('created_at', [$start, $end])->count();
            $approved = Leave::whereBetween('created_at', [$start, $end])
                ->whereIn('status', ['approved', 'paid'])->count();
            $leaveTrend[] = [
                'label' => $month->format('M'),
                'total' => $total,
                'approved' => $approved,
            ];
        }

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

        // ── Payroll Batch Stats ──
        $totalBatches = PayrollBatch::count();
        $batchSubmitted = PayrollBatch::where('status', 'submitted')->count();
        $batchApproved = PayrollBatch::where('status', 'approved')->count();
        $batchPaid = PayrollBatch::where('status', 'paid')->count();
        $batchRejected = PayrollBatch::where('status', 'rejected')->count();

        $currentInProgress = PayrollBatch::inProgressForCurrentPeriod();

        $totalReleasedPayroll = PayrollBatch::where('status', 'paid')
            ->withSum('payrolls', 'net_pay')
            ->get()
            ->sum('payrolls_sum_net_pay') ?? 0;

        // ── Receivables Totals ──
        $totalApprovedCA = CashAdvance::where('status', 'approved')->sum('amount') ?? 0;
        $totalApprovedLoans = SalaryLoan::where('status', 'approved')->sum('loan_amount') ?? 0;
        $totalPaidCA = CashAdvance::where('status', 'paid')->sum('amount') ?? 0;
        $totalPaidLoans = SalaryLoan::where('status', 'paid')->sum('loan_amount') ?? 0;

        // ── Holiday Stats ──
        $upcomingHolidays = Holiday::where('date', '>=', now())
            ->where('date', '<=', now()->addDays(30))
            ->count();

        // ── Bonus Stats ──
        $activeBonuses = Bonus::where('status', 'active')->count();

        return view('hr.index', compact(
            'totalEmployees',
            'newHiresThisMonth',
            'attendanceTrend',
            'tdRate',
            'approvedLeaves',
            'pendingLeaves',
            'rejectedLeaves',
            'leaveTrend',
            'pendingOT',
            'pendingUT',
            'otHoursThisWeek',
            'totalOTRecords',
            'pendingCashAdvances',
            'pendingSalaryLoans',
            'totalBatches',
            'batchSubmitted',
            'batchApproved',
            'batchPaid',
            'batchRejected',
            'currentInProgress',
            'totalReleasedPayroll',
            'totalApprovedCA',
            'totalApprovedLoans',
            'totalPaidCA',
            'totalPaidLoans',
            'upcomingHolidays',
            'activeBonuses',
        ));
    }
}
