<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Traits\LogsUserActivity;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\OvertimeUndertime;
use App\Models\Payroll;
use App\Models\CashAdvance;
use App\Models\DailyRemittance;
use App\Services\AccountingReportService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use LogsUserActivity;

    public function __construct(private readonly AccountingReportService $accountingReports) {}

    public function index()
    {
        $this->logActivity('viewed', 'dashboard');

        // ============ EMPLOYEE STATISTICS ============
        $totalEmployees = Employee::whereNotIn('role', ['superadmin', 'qr_admin'])->count();
        $activeEmployees = Employee::whereNotIn('role', ['superadmin', 'qr_admin'])->where('status', 'active')->count();
        $inactiveEmployees = Employee::whereNotIn('role', ['superadmin', 'qr_admin'])->where('status', 'inactive')->count();
        $onLeaveEmployees = Leave::where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->distinct('user_id')
            ->count();

        // ============ ATTENDANCE STATISTICS (Today) ============
        $today = now()->startOfDay();
        $presentToday = Attendance::whereDate('date', $today)
            ->where('status', 'present')
            ->count();
        $absentToday = Attendance::whereDate('date', $today)
            ->where('status', 'absent')
            ->count();
        $lateToday = Attendance::whereDate('date', $today)
            ->where('status', 'late')
            ->count();
        
        // ============ LEAVE STATISTICS ============
        $totalLeaves = Leave::count();
        $approvedLeaves = Leave::whereIn('status', ['approved', 'paid'])->count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $rejectedLeaves = Leave::where('status', 'rejected')->count();

        // ============ OVERTIME/UNDERTIME STATISTICS ============
        $totalOvertimeHours = OvertimeUndertime::where('status', 'approved')->where('type', 'overtime')
            ->sum('hours') ?? 0;
        $totalUndertimeHours = OvertimeUndertime::where('status', 'approved')->where('type', 'undertime')
            ->sum('hours') ?? 0;
        $totalOvertimeRecords = OvertimeUndertime::where('status', 'approved')->where('type', 'overtime')->count();
        $pendingOT = OvertimeUndertime::where('status', 'pending')->where('type', 'overtime')->count();
        $pendingUT = OvertimeUndertime::where('status', 'pending')->where('type', 'undertime')->count();

        // ============ PAYROLL STATISTICS ============
        $payrolls = Payroll::with(['employee', 'allowances', 'deductions'])->get();
        $approvedPayrolls = $payrolls->where('status', 'approved');
        $totalPayroll = $approvedPayrolls->sum(fn($p) => $p->net_pay);
        $totalAllowances = $approvedPayrolls->sum(fn($p) => $p->total_allowances);
        $totalDeductions = $approvedPayrolls->sum(fn($p) => $p->total_deductions);

        // Payroll status breakdown
        $approvedPayroll = $payrolls->where('status', 'approved')->count();
        $rejectedPayroll = $payrolls->where('status', 'rejected')->count();
        $processingPayroll = $payrolls->where('status', 'processing')->count();

        // Average basic salary
        $averageBasicSalary = $approvedPayrolls->avg(fn($p) => $p->basic_salary);

        // Deduction vs Allowance ratio
        $baseAmount = $totalAllowances + $totalDeductions;
        $deductionPercentage = $baseAmount > 0 ? ($totalDeductions / $baseAmount) * 100 : 0;
        $allowancePercentage = $baseAmount > 0 ? ($totalAllowances / $baseAmount) * 100 : 0;

        // ============ CASH ADVANCE STATISTICS ============
        $totalCashAdvances = CashAdvance::sum('amount') ?? 0;
        $pendingCashAdvances = CashAdvance::where('status', 'pending')->sum('amount') ?? 0;
        $pendingCashAdvancesCount = CashAdvance::where('status', 'pending')->count();

        // ============ REMITTANCE STATISTICS ============
        $totalCollections = DailyRemittance::whereIn('status', ['approved', 'completed'])->sum('total_collection') ?? 0;
        $totalExpenses = DailyRemittance::whereIn('status', ['approved', 'completed'])->sum('total_expenses') ?? 0;
        $totalNetRemittance = DailyRemittance::whereIn('status', ['approved', 'completed'])->sum('net_remittance') ?? 0;
        $pendingRemittancesCount = DailyRemittance::where('status', 'pending')->count();
        $completedRemittances = DailyRemittance::where('status', 'completed')->count();

        // Daily trend uses the same finalized remittance statuses as the clerk dashboard.
        $remittanceTrendStart = $today->copy()->subDays(6);
        $remittanceTotalsByDay = DailyRemittance::query()
            ->selectRaw('DATE(remittance_date) as remittance_day, SUM(net_remittance) as net, COUNT(*) as count')
            ->whereIn('status', ['approved', 'completed'])
            ->whereBetween('remittance_date', [$remittanceTrendStart->toDateString(), $today->toDateString()])
            ->groupByRaw('DATE(remittance_date)')
            ->get()
            ->keyBy('remittance_day');
        $dailyRemittanceTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $day = $date->toDateString();
            $remittanceTotals = $remittanceTotalsByDay->get($day);
            $dailyRemittanceTrend[] = [
                'date' => $day,
                'label' => $date->format('M d'),
                'net' => (float) ($remittanceTotals->net ?? 0),
                'count' => (int) ($remittanceTotals->count ?? 0),
            ];
        }

        // ============ ATTENDANCE TREND (Last 7 days) ============
        $attendanceTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i)->startOfDay();
            $present = Attendance::whereDate('date', $date)
                ->where('status', 'present')
                ->count();
            $absent = Attendance::whereDate('date', $date)
                ->where('status', 'absent')
                ->count();
            $late = Attendance::whereDate('date', $date)
                ->where('status', 'late')
                ->count();
            
            $attendanceTrend[] = [
                'date' => $date->format('m-d'),
                'date_iso' => $date->format('Y-m-d'),
                'present' => $present,
                'absent' => $absent,
                'late' => $late
            ];
        }
        $todayAttendance = $attendanceTrend[array_key_last($attendanceTrend)];
        $todayAttendanceCount = $todayAttendance['present'] + $todayAttendance['late'] + $todayAttendance['absent'];
        $attendanceRate = $todayAttendanceCount > 0
            ? round(($todayAttendance['present'] / $todayAttendanceCount) * 100)
            : 0;

        // ============ MONTHLY PAYROLL TREND (Last 6 months) ============
        $monthlyPayrollTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $total = $approvedPayrolls
                ->where('created_at', '>=', $month->copy()->startOfMonth())
                ->where('created_at', '<=', $month->copy()->endOfMonth())
                ->sum(fn($p) => $p->net_pay);

            $monthlyPayrollTrend[] = [
                'month' => $month->format('M'),
                'total' => $total,
            ];
        }

        // ============ LEAVE TREND (Last 6 months) ============
        $leaveTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            $total = Leave::whereBetween('created_at', [$start, $end])->count();
            $approved = Leave::whereBetween('created_at', [$start, $end])->whereIn('status', ['approved', 'paid'])->count();
            $leaveTrend[] = [
                'label' => $month->format('M'),
                'total' => $total,
                'approved' => $approved,
            ];
        }

        // ============ FINANCIAL OVERVIEW ============
        // Use the same posted journal-entry logic as the Income Statement page.
        $financialDateTo = now()->toDateString();
        $financialDateFrom = Carbon::parse($financialDateTo)->startOfMonth()->toDateString();
        $financialOverview = $this->accountingReports->incomeStatement($financialDateFrom, $financialDateTo);
        $financialOverview['dateFrom'] = $financialDateFrom;
        $financialOverview['dateTo'] = $financialDateTo;

        $financialTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthFrom = $month->copy()->startOfMonth()->toDateString();
            $monthTo = $month->copy()->endOfMonth()->toDateString();
            $monthStatement = $this->accountingReports->incomeStatement($monthFrom, $monthTo);
            $financialTrend[] = [
                'month' => $month->format('M'),
                'revenue' => (float) $monthStatement['netRevenue'],
                'netIncome' => (float) $monthStatement['netIncome'],
            ];
        }

        // ============ CALENDAR DATA ============
        $calMonth = now()->month;
        $calYear = now()->year;

        // ============ PENDING TOTALS ============
        $pendingItems = [];

        if ($pendingCashAdvancesCount > 0) {
            $pendingItems[] = [
                'text' => $pendingCashAdvancesCount === 1
                    ? '1 Cash Advance Needs Approval'
                    : "{$pendingCashAdvancesCount} Cash Advances Need Approval",
                'count' => $pendingCashAdvancesCount,
                'url' => route('payroll.receivables.index', ['tab' => 'cash_advances']),
                'icon' => 'feather-dollar-sign',
                'color' => '#fef3c7',
                'iconColor' => '#d97706'
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
                'url' => route('overtime.index'),
                'icon' => 'feather-clock',
                'color' => '#f5f3ff',
                'iconColor' => '#7c3aed'
            ];
        }

        $totalPending = $pendingLeaves + $pendingOT + $pendingUT + $pendingCashAdvancesCount;

        // ============ RECENT RECORDS ============
        $recentLeaves = Leave::with('employee')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentAttendance = Attendance::with('employee')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        $recentPayroll = $payrolls->sortByDesc('created_at')->take(10);

        return view('superadmin.dashboard', compact(
            'totalEmployees',
            'activeEmployees',
            'inactiveEmployees',
            'onLeaveEmployees',
            'presentToday',
            'absentToday',
            'lateToday',
            'attendanceRate',
            'totalLeaves',
            'approvedLeaves',
            'pendingLeaves',
            'rejectedLeaves',
            'totalOvertimeHours',
            'totalUndertimeHours',
            'totalOvertimeRecords',
            'pendingOT',
            'pendingUT',
            'totalPayroll',
            'totalAllowances',
            'totalDeductions',
            'processingPayroll',
            'approvedPayroll',
            'rejectedPayroll',
            'averageBasicSalary',
            'deductionPercentage',
            'allowancePercentage',
            'totalCashAdvances',
            'pendingCashAdvances',
            'pendingCashAdvancesCount',
            'totalCollections',
            'totalExpenses',
            'totalNetRemittance',
            'pendingRemittancesCount',
            'completedRemittances',
            'dailyRemittanceTrend',
            'attendanceTrend',
            'monthlyPayrollTrend',
            'leaveTrend',
            'calMonth',
            'calYear',
            'totalPending',
            'pendingItems',
            'recentLeaves',
            'recentAttendance',
            'recentPayroll',
            'financialOverview',
            'financialTrend'
        ));
    }
}
