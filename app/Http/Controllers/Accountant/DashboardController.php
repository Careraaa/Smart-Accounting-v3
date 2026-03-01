<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\SalaryLoan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Exclude superadmin from employee counts
        $totalEmployees = Employee::where('role', '!=', 'superadmin')->count();

        // Load payrolls with relationships to compute dynamically
        $payrolls = Payroll::with(['employee', 'allowances', 'deductions'])->get();

        // Total payroll = sum of net pay
        $totalPayroll = $payrolls->sum(fn($p) => $p->net_pay);

        // Total allowances & deductions
        $totalAllowances = $payrolls->sum(fn($p) => $p->total_allowances);
        $totalDeductions = $payrolls->sum(fn($p) => $p->total_deductions);

        // Payroll status breakdown
        $processingPayroll = $payrolls->where('status', 'processing')->count();
        $approvedPayroll = $payrolls->where('status', 'approved')->count();
        $paidPayroll = $payrolls->where('status', 'paid')->count();
        $rejectedPayroll = $payrolls->where('status', 'rejected')->count();

        // Salary Loan Statistics
        $totalOutstandingLoans = SalaryLoan::where('status', '!=', 'fully_paid')->sum('remaining_balance') ?? 0;
        $activeSalaryLoans = SalaryLoan::where('status', 'active')->count();

        // Average basic salary (computed dynamically)
        $averageBasicSalary = $payrolls->avg(fn($p) => $p->basic_salary);

        // Deduction vs Allowance ratio
        $baseAmount = $totalAllowances + $totalDeductions;
        $deductionPercentage = $baseAmount > 0 ? ($totalDeductions / $baseAmount) * 100 : 0;
        $allowancePercentage = $baseAmount > 0 ? ($totalAllowances / $baseAmount) * 100 : 0;

        // Recent payroll records
        $recentPayroll = $payrolls->sortByDesc('created_at')->take(10);

        // Monthly payroll trend (last 6 months)
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

        return view('accountant.index', compact(
            'totalEmployees',
            'totalPayroll',
            'totalAllowances',
            'totalDeductions',
            'processingPayroll',
            'approvedPayroll',
            'paidPayroll',
            'rejectedPayroll',
            'totalOutstandingLoans',
            'activeSalaryLoans',
            'averageBasicSalary',
            'deductionPercentage',
            'allowancePercentage',
            'recentPayroll',
            'monthlyPayrollTrend'
        ));
    }
}