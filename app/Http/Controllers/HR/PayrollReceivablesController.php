<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollReceivablesController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'approved');
        $search = $request->get('search');
        $periodFilter = $request->get('period');

        $query = Payroll::with(['employee', 'allowances', 'deductions', 'approvedBy'])->whereIn('status', ['approved', 'paid']);

        // Filter by status tab
        if ($status === 'paid') {
            $query->where('status', 'paid');
        } elseif ($status === 'approved') {
            $query->where('status', 'approved');
        }

        // Search by employee name
        if ($search) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Filter by period
        if ($periodFilter) {
            $query->where(function ($q) use ($periodFilter) {
                $q->whereYear('payroll_period_start', Carbon::parse($periodFilter)->year)->whereMonth('payroll_period_start', Carbon::parse($periodFilter)->month);
            });
        }

        $payrolls = $query->orderBy('payroll_period_start', 'desc')->paginate(15);

        // Summary stats
        $totalReceivable = Payroll::where('status', 'approved')
            ->with(['allowances', 'deductions'])
            ->get()
            ->sum(fn($p) => $p->net_pay);

        $approvedCount = Payroll::where('status', 'approved')->count();
        $paidCount = Payroll::where('status', 'paid')->count();
        $paidThisMonth = Payroll::where('status', 'paid')->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->count();

        return view('hr.payroll.receivables.index', compact('payrolls', 'totalReceivable', 'approvedCount', 'paidCount', 'paidThisMonth', 'status', 'search', 'periodFilter'));
    }

    public function markAsPaid(Request $request, Payroll $payroll)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|in:cash,bank_transfer,check',
        ]);

        if ($payroll->status !== 'approved') {
            return redirect()
                ->back()
                ->withErrors(['error' => 'Only approved payrolls can be marked as paid.']);
        }

        $payroll->update([
            'status' => 'paid',
            'payment_date' => $request->payment_date,
        ]);

        return redirect()
            ->route('payroll.receivables.index')
            ->with('success', "Payroll for {$payroll->employee->first_name} {$payroll->employee->last_name} marked as paid.");
    }

    public function markBatchPaid(Request $request)
    {
        $request->validate([
            'payroll_ids' => 'required|array',
            'payroll_ids.*' => 'exists:payrolls,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|in:cash,bank_transfer,check',
        ]);

        $count = Payroll::whereIn('id', $request->payroll_ids)
            ->where('status', 'approved')
            ->update([
                'status' => 'paid',
                'payment_date' => $request->payment_date,
            ]);

        return redirect()
            ->route('payroll.receivables.index')
            ->with('success', "{$count} payroll(s) marked as paid.");
    }
}
