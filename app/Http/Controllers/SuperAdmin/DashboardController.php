<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Traits\LogsUserActivity;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\OvertimeUndertime;
use App\Models\Payroll;
use App\Models\SalaryLoan;
use App\Models\CashAdvance;
use App\Models\DailyRemittance;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use LogsUserActivity;
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
        
        $attendanceRate = $totalEmployees > 0 
            ? (($presentToday + $lateToday) / $totalEmployees) * 100 
            : 0;

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
        $totalPayroll = $payrolls->sum(fn($p) => $p->net_pay);
        $totalAllowances = $payrolls->sum(fn($p) => $p->total_allowances);
        $totalDeductions = $payrolls->sum(fn($p) => $p->total_deductions);

        // Payroll status breakdown
        $approvedPayroll = $payrolls->where('status', 'approved')->count();
        $rejectedPayroll = $payrolls->where('status', 'rejected')->count();
        $processingPayroll = $payrolls->where('status', 'processing')->count();

        // Average basic salary
        $averageBasicSalary = $payrolls->avg(fn($p) => $p->basic_salary);

        // Deduction vs Allowance ratio
        $baseAmount = $totalAllowances + $totalDeductions;
        $deductionPercentage = $baseAmount > 0 ? ($totalDeductions / $baseAmount) * 100 : 0;
        $allowancePercentage = $baseAmount > 0 ? ($totalAllowances / $baseAmount) * 100 : 0;

        // ============ SALARY LOAN & CASH ADVANCE STATISTICS ============
        $totalOutstandingLoans = SalaryLoan::where('status', '!=', 'fully_paid')->sum('remaining_balance') ?? 0;
        $activeSalaryLoans = SalaryLoan::where('status', 'active')->count();
        $pendingSalaryLoansCount = SalaryLoan::where('status', 'pending')->count();
        
        $totalCashAdvances = CashAdvance::sum('amount') ?? 0;
        $pendingCashAdvances = CashAdvance::where('status', 'pending')->sum('amount') ?? 0;
        $pendingCashAdvancesCount = CashAdvance::where('status', 'pending')->count();

        // ============ REMITTANCE STATISTICS ============
        $totalCollections = DailyRemittance::whereIn('status', ['approved', 'completed'])->sum('total_collection') ?? 0;
        $totalExpenses = DailyRemittance::whereIn('status', ['approved', 'completed'])->sum('total_expenses') ?? 0;
        $totalNetRemittance = DailyRemittance::whereIn('status', ['approved', 'completed'])->sum('net_remittance') ?? 0;
        $pendingRemittancesCount = DailyRemittance::where('status', 'pending')->count();
        $completedRemittances = DailyRemittance::where('status', 'completed')->count();

        // ============ ATTENDANCE TREND (Last 7 days) ============
        $attendanceTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
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

        // ============ MONTHLY PAYROLL TREND (Last 6 months) ============
        $monthlyPayrollTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $total = $payrolls
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

        if ($pendingSalaryLoansCount > 0) {
            $pendingItems[] = [
                'text' => $pendingSalaryLoansCount === 1
                    ? '1 Salary Loan Needs Approval'
                    : "{$pendingSalaryLoansCount} Salary Loans Need Approval",
                'count' => $pendingSalaryLoansCount,
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
                'url' => route('overtime.index'),
                'icon' => 'feather-clock',
                'color' => '#f5f3ff',
                'iconColor' => '#7c3aed'
            ];
        }

        $totalPending = $pendingLeaves + $pendingOT + $pendingUT + $pendingCashAdvancesCount + $pendingSalaryLoansCount;

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
            'totalOutstandingLoans',
            'activeSalaryLoans',
            'pendingSalaryLoansCount',
            'totalCashAdvances',
            'pendingCashAdvances',
            'pendingCashAdvancesCount',
            'totalCollections',
            'totalExpenses',
            'totalNetRemittance',
            'pendingRemittancesCount',
            'completedRemittances',
            'attendanceTrend',
            'monthlyPayrollTrend',
            'leaveTrend',
            'calMonth',
            'calYear',
            'totalPending',
            'pendingItems',
            'recentLeaves',
            'recentAttendance',
            'recentPayroll'
        ));
    }
}
