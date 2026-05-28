<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\PayrollBatch;
use App\Models\User;
use App\Models\PayrollCutoffSchedule;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollHistoryController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }
    // ====================== INDEX ======================
    public function index(Request $request)
    {
        $filterMonth = $request->get('filter_month');
        $filterYear  = $request->get('filter_year');

        // Get batches with relationships
        $query = PayrollBatch::with([
            'payrolls.user', 
            'payrolls.allowances', 
            'payrolls.deductions'
        ])->orderBy('period_start', 'desc');

        if ($filterYear) {
            $query->whereYear('period_start', $filterYear);
        }
        if ($filterMonth) {
            $query->whereMonth('period_start', $filterMonth);
        }

        $batches = $query->paginate(12);

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

        return view('hr.payroll.history.index', compact(
            'batches',
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
        $payrolls = Payroll::with(['user', 'allowances', 'deductions'])
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
                ->reject(fn ($d) => in_array($d->deduction_type, ['SSS', 'Pag-IBIG', 'PhilHealth'])
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

            $payroll->setAttribute('basic_salary', $computed['basicSalary']);
            $payroll->setAttribute('gross_pay', $computed['grossPay']);
            $payroll->setAttribute('total_deductions', $computed['totalDeductions']);
            $payroll->setAttribute('net_pay', $computed['netPay']);
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