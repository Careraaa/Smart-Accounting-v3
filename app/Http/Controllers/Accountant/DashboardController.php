<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\SalaryLoan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Statuses that represent finalized / actionable payrolls for the accountant view.
    private const ACTIVE_STATUSES = ['submitted', 'approved', 'released', 'paid'];

    public function index()
    {
        // Exclude superadmin and qr_admin from employee counts
        $totalEmployees = Employee::whereNotIn('role', ['superadmin', 'qr_admin'])->count();

        // Load only finalized payrolls (submitted → approved → released/paid).
        // Draft / pending / rejected records are excluded from all stats.
        $payrolls = Payroll::with(['employee', 'allowances', 'deductions'])
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->get();

        // ── Monetary totals (approved + released only) ──────────────────────
        $approvedPayrolls = $payrolls->whereIn('status', ['approved', 'released', 'paid']);

        $totalPayroll       = $approvedPayrolls->sum(fn($p) => $p->net_pay);
        $totalAllowances    = $approvedPayrolls->sum(fn($p) => $p->total_allowances);
        $totalDeductions    = $approvedPayrolls->sum(fn($p) => $p->total_deductions);
        $averageBasicSalary = $approvedPayrolls->avg(fn($p) => $p->basic_salary) ?? 0;

        // ── Status counts ────────────────────────────────────────────────────
        $processingPayroll = $payrolls->where('status', 'submitted')->count();
        $approvedPayroll   = $payrolls->where('status', 'approved')->count();
        $releasedPayroll   = $payrolls->whereIn('status', ['released', 'paid'])->count();
        // Rejected is outside the active set; query separately for the pipeline bar.
        $rejectedPayroll   = Payroll::where('status', 'rejected')->count();

        // ── Salary Loan Statistics ───────────────────────────────────────────
        $totalOutstandingLoans = SalaryLoan::where('status', 'active')->sum('remaining_balance') ?? 0;
        $activeSalaryLoans     = SalaryLoan::where('status', 'active')->count();

        // ── Deduction vs Allowance ratio ─────────────────────────────────────
        $baseAmount          = $totalAllowances + $totalDeductions;
        $deductionPercentage = $baseAmount > 0 ? ($totalDeductions / $baseAmount) * 100 : 0;
        $allowancePercentage = $baseAmount > 0 ? ($totalAllowances / $baseAmount) * 100 : 0;

        // ── Recent payroll records (approved + released, newest first) ────────
        $recentPayroll = $approvedPayrolls->sortByDesc('created_at')->take(10)->values();

        // ── Monthly net-pay trend (last 6 months, approved + released only) ──
        $monthlyPayrollTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $total = $approvedPayrolls
                ->where('created_at', '>=', $month->copy()->startOfMonth())
                ->where('created_at', '<=', $month->copy()->endOfMonth())
                ->sum(fn($p) => $p->net_pay);

            $monthlyPayrollTrend[] = [
                'month' => $month->format('M'),
                'label' => $month->format("M 'y"),
                'total' => (float) $total,
            ];
        }

        // ── Status-mix donut (submitted + approved + released + rejected) ────
        $statusOrder = ['submitted', 'approved', 'released', 'paid', 'rejected'];
        $payrollStatusChartLabels = [];
        $payrollStatusChartSeries = [];
        foreach ($statusOrder as $st) {
            $n = $st === 'rejected'
                ? $rejectedPayroll
                : $payrolls->where('status', $st)->count();
            if ($n > 0) {
                $payrollStatusChartLabels[] = ucfirst($st);
                $payrollStatusChartSeries[] = $n;
            }
        }

        // ── Pipeline bar ─────────────────────────────────────────────────────
        $pipelineBar = [
            'labels' => ['Awaiting action', 'Approved', 'Released', 'Rejected'],
            'values' => [
                $payrolls->where('status', 'submitted')->count(),
                $payrolls->where('status', 'approved')->count(),
                $payrolls->whereIn('status', ['released', 'paid'])->count(),
                $rejectedPayroll,
            ],
        ];

        return view('accountant.index', compact(
            'totalEmployees',
            'totalPayroll',
            'totalAllowances',
            'totalDeductions',
            'processingPayroll',
            'approvedPayroll',
            'releasedPayroll',
            'rejectedPayroll',
            'totalOutstandingLoans',
            'activeSalaryLoans',
            'averageBasicSalary',
            'deductionPercentage',
            'allowancePercentage',
            'recentPayroll',
            'monthlyPayrollTrend',
            'payrollStatusChartLabels',
            'payrollStatusChartSeries',
            'pipelineBar'
        ));
    }
}
