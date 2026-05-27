<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\OvertimeUndertime;
use App\Models\Payroll;
use App\Models\SalaryLoan;
use App\Models\CashAdvance;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
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

        // ============ PAYROLL STATISTICS ============
        $payrolls = Payroll::with(['employee', 'allowances', 'deductions'])->get();
        $totalPayroll = $payrolls->sum(fn($p) => $p->net_pay);
        $totalAllowances = $payrolls->sum(fn($p) => $p->total_allowances);
        $totalDeductions = $payrolls->sum(fn($p) => $p->total_deductions);

        // Payroll status breakdown
        $processingPayroll = $payrolls->where('status', 'processing')->count();
        $approvedPayroll = $payrolls->where('status', 'approved')->count();
        $releasedPayroll = $payrolls->whereIn('status', ['released', 'paid'])->count();
        $rejectedPayroll = $payrolls->where('status', 'rejected')->count();

        // Average basic salary
        $averageBasicSalary = $payrolls->avg(fn($p) => $p->basic_salary);

        // Deduction vs Allowance ratio
        $baseAmount = $totalAllowances + $totalDeductions;
        $deductionPercentage = $baseAmount > 0 ? ($totalDeductions / $baseAmount) * 100 : 0;
        $allowancePercentage = $baseAmount > 0 ? ($totalAllowances / $baseAmount) * 100 : 0;

        // ============ SALARY LOAN & CASH ADVANCE STATISTICS ============
        $totalOutstandingLoans = SalaryLoan::where('status', '!=', 'fully_paid')->sum('remaining_balance') ?? 0;
        $activeSalaryLoans = SalaryLoan::where('status', 'active')->count();
        
        $totalCashAdvances = CashAdvance::sum('amount') ?? 0;
        $pendingCashAdvances = CashAdvance::where('status', 'pending')->sum('amount') ?? 0;

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
            'totalPayroll',
            'totalAllowances',
            'totalDeductions',
            'processingPayroll',
            'approvedPayroll',
            'releasedPayroll',
            'rejectedPayroll',
            'averageBasicSalary',
            'deductionPercentage',
            'allowancePercentage',
            'totalOutstandingLoans',
            'activeSalaryLoans',
            'totalCashAdvances',
            'pendingCashAdvances',
            'attendanceTrend',
            'monthlyPayrollTrend',
            'recentLeaves',
            'recentAttendance',
            'recentPayroll'
        ));
    }
}
