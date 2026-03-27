<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollHistoryController extends Controller
{
    // ====================== INDEX ======================
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'payroll_period_start');
        $sortOrder = $request->get('sort_order', 'desc');
        $filterMonth = $request->get('filter_month');
        $filterYear = $request->get('filter_year');
        $filterStatus = $request->get('filter_status');

        $allowedColumns = ['payroll_period_start', 'gross_pay', 'net_pay', 'status'];
        if (!in_array($sortBy, $allowedColumns)) {
            $sortBy = 'payroll_period_start';
        }
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        // Build query
        $query = Payroll::with(['user', 'allowances', 'deductions']);

        // Filter by month
        if ($filterMonth) {
            $query->whereMonth('payroll_period_start', $filterMonth);
        }

        // Filter by year
        if ($filterYear) {
            $query->whereYear('payroll_period_start', $filterYear);
        }

        // Filter by status
        if ($filterStatus && $filterStatus !== 'all') {
            $query->where('status', $filterStatus);
        }

        $payrolls = $query->orderBy($sortBy, $sortOrder)->paginate(15);

        // Statistics - Calculate net_pay in PHP since it's a computed attribute
        $allPayrolls = Payroll::with(['allowances', 'deductions'])->get();
        $totalPayroll = $allPayrolls->sum(function ($payroll) {
            return $payroll->net_pay;
        });
        
        $totalBatches = Payroll::distinct('payroll_period_start')->count();
        $totalPaid = Payroll::where('status', 'paid')->count();
        $totalPending = Payroll::where('status', '!=', 'paid')->where('status', '!=', 'rejected')->count();

        // For batch summary cards
        $latestBatches = Payroll::select('payroll_period_start', 'payroll_period_end')
            ->distinct()
            ->orderBy('payroll_period_start', 'desc')
            ->limit(3)
            ->get();

        $batchData = [];
        foreach ($latestBatches as $batch) {
            $batchPayrolls = Payroll::with(['allowances', 'deductions'])
                ->whereBetween('payroll_period_start', [$batch->payroll_period_start, $batch->payroll_period_end])
                ->get();
            
            $totalAmount = $batchPayrolls->sum(function ($payroll) {
                return $payroll->net_pay;
            });
            
            $batchData[] = [
                'period_start' => $batch->payroll_period_start,
                'period_end' => $batch->payroll_period_end,
                'count' => $batchPayrolls->count(),
                'total_amount' => $totalAmount,
                'paid_count' => $batchPayrolls->where('status', 'paid')->count(),
            ];
        }

        return view('hr.payroll.history.index', compact(
            'payrolls',
            'sortBy',
            'sortOrder',
            'filterMonth',
            'filterYear',
            'filterStatus',
            'totalPayroll',
            'totalBatches',
            'totalPaid',
            'totalPending',
            'batchData'
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
        $end = $request->query('end');

        $startDate = Carbon::createFromFormat('Y-m-d', $start)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $end)->endOfDay();

        // Get all payrolls in this batch - match by period overlap
        $payrolls = Payroll::with(['user', 'allowances', 'deductions'])
            ->whereBetween('payroll_period_start', [$startDate, $endDate])
            ->orWhereBetween('payroll_period_end', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate batch totals using database fields
        $totalGross = $payrolls->sum(function ($payroll) {
            return ($payroll->basic_salary ?? 0) + ($payroll->total_allowances ?? 0);
        });

        $totalDeductions = $payrolls->sum('total_deductions');

        $totalNetPay = $payrolls->sum(function ($payroll) {
            return (($payroll->basic_salary ?? 0) + ($payroll->total_allowances ?? 0)) - ($payroll->total_deductions ?? 0);
        });

        $paidCount = $payrolls->where('status', 'paid')->count();
        $pendingCount = $payrolls->where('status', '!=', 'paid')->count();

        return view('hr.payroll.history.batch', compact(
            'startDate',
            'endDate',
            'payrolls',
            'totalGross',
            'totalDeductions',
            'totalNetPay',
            'paidCount',
            'pendingCount'
        ));
    }
}
