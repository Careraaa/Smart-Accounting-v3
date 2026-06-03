<?php

namespace App\Http\Controllers\HR;

use App\Traits\LogsUserActivity;
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
    use LogsUserActivity;
    public function index()
    {
        $this->logActivity('viewed', 'dashboard');

        // ── Employee Stats ──
        $totalEmployees = User::where('role', 'employee')->where('status', 'active')->count();
        $newHiresThisMonth = User::where('role', 'employee')->where('status', 'active')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ── Attendance trend (last 7 days) ──
        $attendanceRaw = Attendance::whereDate('date', '>=', now()->subDays(6)->startOfDay())
            ->whereDate('date', '<=', now()->endOfDay())
            ->selectRaw("DATE(`date`) as dt, status, COUNT(*) as cnt")
            ->groupBy('dt', 'status')
            ->get()
            ->groupBy('dt');
        $attendanceTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $key = $date->toDateString();
            $rows = $attendanceRaw->get($key, collect());
            $attendanceTrend[] = [
                'date_iso' => $key,
                'label' => $date->format('D') . ' · ' . $date->format('M j'),
                'present' => (int) $rows->where('status','present')->sum('cnt'),
                'absent'  => (int) $rows->where('status','absent')->sum('cnt'),
                'late'    => (int) $rows->where('status','late')->sum('cnt'),
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
        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();
        $leaveRaw = Leave::where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw("YEAR(created_at) as yr, MONTH(created_at) as mo, IF(status IN ('approved','paid'),1,0) as is_approved, COUNT(*) as cnt")
            ->groupBy('yr', 'mo', 'is_approved')
            ->get();
        $leaveTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $key = $month->format('Y-m');
            $rows = $leaveRaw->filter(fn($r) => $r->yr == $month->year && $r->mo == $month->month);
            $leaveTrend[] = [
                'label'    => $month->format('M'),
                'total'    => (int) $rows->sum('cnt'),
                'approved' => (int) $rows->where('is_approved', 1)->sum('cnt'),
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

        // ── Pending To-Do Items ──────────────────────────────────────────────
        $pendingItems = [];

        if ($pendingCashAdvances > 0) {
            $pendingItems[] = [
                'text' => $pendingCashAdvances === 1
                    ? '1 Cash Advance Needs Approval'
                    : "{$pendingCashAdvances} Cash Advances Need Approval",
                'count' => $pendingCashAdvances,
                'url' => route('payroll.receivables.index', ['tab' => 'cash_advances']),
                'icon' => 'feather-dollar-sign',
                'color' => '#fef3c7',
                'iconColor' => '#d97706'
            ];
        }

        if ($pendingSalaryLoans > 0) {
            $pendingItems[] = [
                'text' => $pendingSalaryLoans === 1
                    ? '1 Salary Loan Needs Approval'
                    : "{$pendingSalaryLoans} Salary Loans Need Approval",
                'count' => $pendingSalaryLoans,
                'url' => route('payroll.receivables.index', ['tab' => 'salary_loans']),
                'icon' => 'feather-credit-card',
                'color' => '#e0f2fe',
                'iconColor' => '#0284c7'
            ];
        }

        if ($pendingLeaves > 0) {
            $pendingItems[] = [
                'text' => $pendingLeaves === 1
                    ? '1 Leave Request Needs Review'
                    : "{$pendingLeaves} Leave Requests Need Review",
                'count' => $pendingLeaves,
                'url' => route('leave.pending'),
                'icon' => 'feather-calendar',
                'color' => '#fef3c7',
                'iconColor' => '#d97706'
            ];
        }

        $pendingOTUT = $pendingOT + $pendingUT;
        if ($pendingOTUT > 0) {
            $pendingItems[] = [
                'text' => $pendingOTUT === 1
                    ? '1 Overtime/Undertime Request Pending'
                    : "{$pendingOTUT} Overtime/Undertime Requests Pending",
                'count' => $pendingOTUT,
                'url' => route('attendance.index', ['tab' => 'otut']),
                'icon' => 'feather-clock',
                'color' => '#f5f3ff',
                'iconColor' => '#7c3aed'
            ];
        }

        $totalPending = array_sum(array_column($pendingItems, 'count'));

        // ── Payroll Batch Stats ──
        $batchCounts = PayrollBatch::selectRaw("COUNT(*) as total, status")->groupBy('status')->pluck('total','status');
        $totalBatches   = $batchCounts->sum();
        $batchSubmitted = (int) ($batchCounts['submitted'] ?? 0);
        $batchApproved  = (int) ($batchCounts['approved'] ?? 0);
        $batchRejected  = (int) ($batchCounts['rejected'] ?? 0);

        $currentInProgress = PayrollBatch::inProgressForCurrentPeriod();

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
            'batchRejected',
            'currentInProgress',
            'totalApprovedCA',
            'totalApprovedLoans',
            'totalPaidCA',
            'totalPaidLoans',
            'upcomingHolidays',
            'activeBonuses',
            'pendingItems',
            'totalPending',
        ));
    }
}
