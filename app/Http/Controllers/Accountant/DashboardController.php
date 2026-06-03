<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\SalaryLoan;
use App\Models\DailyRemittance;
use App\Models\CashAdvance;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use LogsUserActivity;
        // Statuses that represent finalized / actionable payrolls for the accountant view.
        private const ACTIVE_STATUSES = ['submitted', 'approved'];

    public function index()
    {
        $this->logActivity('viewed', 'Accountant Dashboard', request()->url());

        // Exclude superadmin and qr_admin from employee counts
        $totalEmployees = Employee::whereNotIn('role', ['superadmin', 'qr_admin'])->count();

        // Load only finalized payrolls (submitted → approved → released/paid).
        // Draft / pending / rejected records are excluded from all stats.
        $payrolls = Payroll::with(['employee', 'allowances', 'deductions'])
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->get();

        // ── Monetary totals (approved + released only) ──────────────────────
        $approvedPayrolls = $payrolls->whereIn('status', ['approved']);

        $totalPayroll       = $approvedPayrolls->sum(fn($p) => $p->net_pay);
        $totalAllowances    = $approvedPayrolls->sum(fn($p) => $p->total_allowances);
        $totalDeductions    = $approvedPayrolls->sum(fn($p) => $p->total_deductions);
        $averageBasicSalary = $approvedPayrolls->avg(fn($p) => $p->basic_salary) ?? 0;

        // ── Status counts ────────────────────────────────────────────────────
        // Count by BATCH, not individual payroll rows — one batch = one approval request
        $processingPayroll = \App\Models\PayrollBatch::where('status', 'submitted')->count();
        $approvedPayroll   = \App\Models\PayrollBatch::where('status', 'approved')->count();
        // Rejected is outside the active set; query separately for the pipeline bar.
        $rejectedPayroll   = \App\Models\PayrollBatch::where('status', 'rejected')->count();

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

        // ── Status-mix donut (by batch count) ───────────────────────────────
        $statusOrder = ['submitted', 'approved', 'rejected'];
        $payrollStatusChartLabels = [];
        $payrollStatusChartSeries = [];
        foreach ($statusOrder as $st) {
            $n = \App\Models\PayrollBatch::where('status', $st)->count();
            if ($n > 0) {
                $payrollStatusChartLabels[] = ucfirst($st);
                $payrollStatusChartSeries[] = $n;
            }
        }

        // ── Pipeline bar ─────────────────────────────────────────────────────
        $pipelineBar = [
            'labels' => ['Awaiting action', 'Approved', 'Rejected'],
            'values' => [
                $processingPayroll,
                $approvedPayroll,
                $rejectedPayroll,
            ],
        ];

        // ── Pending To-Do Items ──────────────────────────────────────────────
        $pendingItems = [];
        
        // Pending Payroll Batches
        if ($processingPayroll > 0) {
            $pendingItems[] = [
                'text' => $processingPayroll === 1
                    ? '1 Payroll Batch Needs Approval'
                    : "{$processingPayroll} Payroll Batches Need Approval",
                'count' => $processingPayroll,
                'url' => route('payroll-approval.index'),
                'icon' => 'feather-check-square',
                'color' => '#f0fdf4',
                'iconColor' => '#16a34a'
            ];
        }
        
        // Pending Remittance Approvals
        $pendingRemittances = DailyRemittance::where('status', 'pending')->count();
        if ($pendingRemittances > 0) {
            $pendingItems[] = [
                'text' => $pendingRemittances === 1
                    ? '1 Remittance Needs Approval'
                    : "{$pendingRemittances} Remittances Need Approval",
                'count' => $pendingRemittances,
                'url' => route('remittance-approval.index'),
                'icon' => 'feather-send',
                'color' => '#f5f3ff',
                'iconColor' => '#7c3aed'
            ];
        }
        
        // Pending Cash Advances
        $pendingCashAdvancesCount = CashAdvance::where('status', 'pending')->count();
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
        
        // Pending Salary Loans
        $pendingSalaryLoansCount = SalaryLoan::where('status', 'pending')->count();
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

        // Calendar data
        $calMonth = now()->month;
        $calYear  = now()->year;

        $totalPendingItems = count($pendingItems);

        return view('accountant.index', compact(
            'totalEmployees',
            'totalPayroll',
            'totalAllowances',
            'totalDeductions',
            'processingPayroll',
            'approvedPayroll',
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
            'pipelineBar',
            'pendingItems',
            'pendingRemittances',
            'pendingCashAdvancesCount',
            'pendingSalaryLoansCount',
            'calMonth',
            'calYear',
            'totalPendingItems'
        ));
    }
}
