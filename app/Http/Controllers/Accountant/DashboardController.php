<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Deduction;
use App\Models\Allowance;
use App\Models\SalaryLoan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Payroll Statistics
        $totalEmployees = Employee::count();
        $totalPayroll = Payroll::sum('net_pay') ?? 0;
        $totalAllowances = Allowance::sum('amount') ?? 0;
        $totalDeductions = Deduction::sum('amount') ?? 0;

        // Payroll status breakdown
        $processingPayroll = Payroll::where('status', 'processing')->count();
        $approvedPayroll = Payroll::where('status', 'approved')->count();
        $paidPayroll = Payroll::where('status', 'paid')->count();
        $rejectedPayroll = Payroll::where('status', 'rejected')->count();

        // Salary Loan Statistics
        $totalOutstandingLoans = SalaryLoan::where('status', '!=', 'fully_paid')->sum('remaining_balance') ?? 0;
        $activeSalaryLoans = SalaryLoan::where('status', 'active')->count();

        // Average calculations
        $averageBasicSalary = $totalEmployees > 0 ? Payroll::avg('basic_salary') ?? 0 : 0;

        // Deduction vs Allowance ratio
        $baseAmount = $totalAllowances + $totalDeductions;

        $deductionPercentage = $baseAmount > 0 ? ($totalDeductions / $baseAmount) * 100 : 0;

        $allowancePercentage = $baseAmount > 0 ? ($totalAllowances / $baseAmount) * 100 : 0;

        // Recent payroll records
        $recentPayroll = Payroll::with('employee')->orderBy('created_at', 'desc')->limit(10)->get();

        // Monthly payroll trend (last 6 months)
        $monthlyPayrollTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $total = Payroll::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->sum('net_pay') ?? 0;
            $monthlyPayrollTrend[] = [
                'month' => $month->format('M'),
                'total' => $total,
            ];
        }

        return view('accountant.index', compact('totalEmployees', 'totalPayroll', 'totalAllowances', 'totalDeductions', 'processingPayroll', 'approvedPayroll', 'paidPayroll', 'rejectedPayroll', 'totalOutstandingLoans', 'activeSalaryLoans', 'averageBasicSalary', 'deductionPercentage', 'allowancePercentage', 'recentPayroll', 'monthlyPayrollTrend'));
    }
}
