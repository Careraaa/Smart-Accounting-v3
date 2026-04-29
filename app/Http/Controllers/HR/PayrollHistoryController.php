<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\PayrollBatch;
use App\Models\User;
use App\Models\PayrollCutoffSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollHistoryController extends Controller
{
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

        $totalPaid = Payroll::where('status', 'paid')->count();

        $activeEmployees = User::whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])
            ->where('status', 'active')
            ->count();

        $nextCutoffDate = PayrollCutoffSchedule::getNextCutoffDate();

        return view('hr.payroll.history.index', compact(
            'batches',
            'totalBatches',
            'totalPayroll',
            'totalPaid',
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

        // Calculate totals safely
        $totalGross = $payrolls->sum(function ($payroll) {
            return ($payroll->basic_salary ?? 0) + ($payroll->total_allowances ?? 0);
        });

        $totalDeductions = $payrolls->sum('total_deductions') ?? 0;

        $totalNetPay = $payrolls->sum(function ($payroll) {
            return ($payroll->basic_salary ?? 0) 
                 + ($payroll->total_allowances ?? 0) 
                 - ($payroll->total_deductions ?? 0);
        });

        $paidCount    = $payrolls->where('status', 'paid')->count();
        $pendingCount = $payrolls->where('status', '!=', 'paid')->count();

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
            'paidCount', 
            'pendingCount'
        ));
    }
}