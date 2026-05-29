<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\PayrollBatch;
use App\Models\User;
use App\Models\PayrollCutoffSchedule;
use App\Services\LeaveService;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollHistoryController extends Controller
{
    protected $payrollService;
    protected $leaveService;

    public function __construct(PayrollService $payrollService, LeaveService $leaveService)
    {
        $this->payrollService = $payrollService;
        $this->leaveService = $leaveService;
    }
    // ====================== INDEX ======================
    public function index(Request $request)
    {
        $allBatches = PayrollBatch::with([
            'payrolls.user', 
            'payrolls.allowances', 
            'payrolls.deductions'
        ])->orderBy('period_start', 'desc')->get();

        // Statistics
        $totalBatches = PayrollBatch::count();

        // Safe total payroll calculation (net_pay is an accessor)
        $totalPayroll = Payroll::with(['allowances', 'deductions'])
                               ->get()
                               ->sum('net_pay') ?? 0;

        $totalReleased = Payroll::whereIn('status', ['released', 'paid'])->count();

        $activeEmployees = User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])
            ->where('status', 'active')
            ->count();

        $nextCutoffDate = PayrollCutoffSchedule::getNextCutoffDate();

        $filterMonth = $request->get('month', '');
        $filterYear  = $request->get('year', '');

        return view('hr.payroll.history.index', compact(
            'allBatches',
            'totalBatches',
            'totalPayroll',
            'totalReleased',
            'activeEmployees',
            'nextCutoffDate',
            'filterMonth',
            'filterYear'
        ));
    }

    // ====================== SHOW ======================
    public function show(Payroll $payroll)
    {
        $payroll->load(['user', 'allowances', 'deductions']);
        return view('hr.payroll.history.show', compact('payroll'));
    }

    // ====================== BATCH ======================
    public function batch(Request $request)
    {
        $start = $request->query('start');
        $end   = $request->query('end');

        if (!$start || !$end) {
            abort(404, 'Invalid period');
        }

        $startDate = Carbon::createFromFormat('Y-m-d', $start)->startOfDay();
        $endDate   = Carbon::createFromFormat('Y-m-d', $end)->endOfDay();

        // Get payrolls for this batch period
        $payrolls = Payroll::with(['user', 'allowances', 'deductions', 'bonuses'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('payroll_period_start', [$startDate, $endDate])
                  ->orWhereBetween('payroll_period_end', [$startDate, $endDate]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Recompute each payroll so the list shows live values, not stale DB zeros
        foreach ($payrolls as $payroll) {
            $manualAllowances = $payroll->allowances
                ->reject(fn ($a) => str_starts_with((string) ($a->allowance_type ?? ''), 'Overtime Pay')
                    || str_starts_with((string) ($a->allowance_type ?? ''), 'Leave Pay'))
                ->map(fn ($a) => ['name' => $a->allowance_type, 'amount' => $a->amount])
                ->values()
                ->toArray();

            $manualDeductions = $payroll->deductions
                ->reject(fn ($d) => in_array($d->deduction_type, ['SSS', 'Pag-IBIG', 'PhilHealth', 'Late Deduction', 'Withholding Tax'])
                    || str_starts_with((string) ($d->deduction_type ?? ''), 'Undertime Deduction'))
                ->map(fn ($d) => ['name' => $d->deduction_type, 'amount' => $d->amount])
                ->values()
                ->toArray();

            $computed = $this->payrollService->computePayroll(
                $payroll->user,
                $payroll->payroll_period_start,
                $payroll->payroll_period_end,
                $manualAllowances,
                $manualDeductions,
            );

            // Recompute leave pay (same as generatePayrollForEmployee does)
            $dailyRate = (float) ($payroll->user->salary_rate ?? 0);
            $leaveAllowances = $this->leaveService->getApprovedLeavesAllowances(
                $payroll->user_id,
                $payroll->payroll_period_start,
                $payroll->payroll_period_end,
                $dailyRate
            );
            $leavePay = collect($leaveAllowances)->sum('amount');

            // Sum bonuses from stored records
            $bonusTotal = (float) $payroll->bonuses->sum('amount');

            $fullGrossPay = $computed['grossPay'] + $leavePay + $bonusTotal;

            $payroll->setAttribute('basic_salary', $computed['basicSalary']);
            $payroll->setAttribute('gross_pay', $fullGrossPay);
            // Loan deductions are stored columns, not part of computePayroll(). Re-add them.
            $loanTotal = (float)($payroll->cash_advance_deduction ?? 0)
                       + (float)($payroll->salary_loan_deduction ?? 0);
            $totalDeductions = $computed['totalDeductions'] + $loanTotal;
            $payroll->setAttribute('total_deductions', $totalDeductions);
            $payroll->setAttribute('net_pay', $fullGrossPay - $totalDeductions);
        }

        // Calculate totals from recomputed values
        $totalGross      = $payrolls->sum('gross_pay');
        $totalDeductions = $payrolls->sum('total_deductions');
        $totalNetPay     = $payrolls->sum('net_pay');

        $releasedCount = $payrolls->whereIn('status', ['released', 'paid'])->count();
        $pendingCount = $payrolls->whereNotIn('status', ['released', 'paid'])->count();

        $batch = PayrollBatch::whereDate('period_start', $startDate->toDateString())
            ->whereDate('period_end', $endDate->toDateString())
            ->latest('id')
            ->first();

        return view('hr.payroll.history.batch', compact(
            'startDate', 
            'endDate', 
            'batch',
            'payrolls', 
            'totalGross', 
            'totalDeductions', 
            'totalNetPay', 
            'releasedCount', 
            'pendingCount'
        ));
    }
}