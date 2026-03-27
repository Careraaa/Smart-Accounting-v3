<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollApprovalController extends Controller
{
    public function index()
    {
        // Get distinct batch periods with pending or submitted payrolls
        $batches = Payroll::whereIn('status', ['pending', 'submitted'])
            ->select('payroll_period_start', 'payroll_period_end')
            ->distinct()
            ->orderBy('payroll_period_start', 'desc')
            ->get();

        $batchData = [];
        foreach ($batches as $batch) {
            $payrolls = Payroll::with('user')
                ->where('payroll_period_start', $batch->payroll_period_start)
                ->where('payroll_period_end', $batch->payroll_period_end)
                ->whereIn('status', ['pending', 'submitted'])
                ->get();

            if ($payrolls->count() > 0) {
                $totalGross = $payrolls->sum(function ($p) {
                    return $p->gross_pay;
                });
                $totalNet = $payrolls->sum(function ($p) {
                    return $p->net_pay;
                });

                $batchData[] = [
                    'period_start' => $batch->payroll_period_start,
                    'period_end' => $batch->payroll_period_end,
                    'count' => $payrolls->count(),
                    'total_gross' => $totalGross,
                    'total_net' => $totalNet,
                    'status' => $payrolls->first()->status,
                    'payrolls' => $payrolls,
                ];
            }
        }

        return view('accountant.payroll-approval.index', compact('batchData'));
    }

    public function show($id)
    {
        $payroll = Payroll::with('employee', 'deductions', 'allowances')->findOrFail($id);
        return view('accountant.payroll-approval.show', compact('payroll'));
    }

    public function showBatch(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');
        $status = $request->query('status', 'pending');

        $startDate = Carbon::createFromFormat('Y-m-d', $start);
        $endDate = Carbon::createFromFormat('Y-m-d', $end);

        $allowed = ['pending', 'submitted', 'approved', 'rejected'];
        if (!in_array($status, $allowed)) {
            $status = 'pending';
        }

        $query = Payroll::with('user', 'deductions', 'allowances')
            ->whereDate('payroll_period_start', $startDate)
            ->whereDate('payroll_period_end', $endDate)
            ->orderBy('created_at');

        if ($status === 'pending') {
            $query->whereIn('status', ['pending', 'submitted']);
        } else {
            $query->where('status', $status);
        }

        $payrolls = $query->get();

        if ($payrolls->isEmpty()) {
            return redirect()->route('payroll-approval.index')->with('error', 'Batch not found.');
        }

        $totalGross = $payrolls->sum(function ($p) {
            return $p->gross_pay;
        });
        $totalNet = $payrolls->sum(function ($p) {
            return $p->net_pay;
        });
        $totalDeductions = $payrolls->sum(function ($p) {
            return $p->total_deductions;
        });

        return view('accountant.payroll-approval.batch', compact(
            'payrolls',
            'startDate',
            'endDate',
            'totalGross',
            'totalNet',
            'totalDeductions',
            'status'
        ));
    }

    public function approveBatch(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $startDate = Carbon::createFromFormat('Y-m-d', $start);
        $endDate = Carbon::createFromFormat('Y-m-d', $end);

        $updated = Payroll::whereDate('payroll_period_start', $startDate)
            ->whereDate('payroll_period_end', $endDate)
            ->whereIn('status', ['pending', 'submitted'])
            ->update(['status' => 'approved']);

        if ($updated > 0) {
            return redirect()->route('payroll-approval.batch', ['start' => $start, 'end' => $end])
                ->with('success', "Batch approved! {$updated} payroll records approved.");
        }

        return redirect()->route('payroll-approval.batch', ['start' => $start, 'end' => $end])
            ->with('error', 'No payrolls to approve in this batch.');
    }

    public function rejectBatch(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $startDate = Carbon::createFromFormat('Y-m-d', $start);
        $endDate = Carbon::createFromFormat('Y-m-d', $end);

        $updated = Payroll::whereDate('payroll_period_start', $startDate)
            ->whereDate('payroll_period_end', $endDate)
            ->whereIn('status', ['pending', 'submitted'])
            ->update(['status' => 'rejected']);

        if ($updated > 0) {
            return redirect()->route('payroll-approval.batch', ['start' => $start, 'end' => $end])
                ->with('success', "Batch rejected! {$updated} payroll records rejected.");
        }

        return redirect()->route('payroll-approval.batch', ['start' => $start, 'end' => $end])
            ->with('error', 'No payrolls to reject in this batch.');
    }

    public function approve(Payroll $payroll)
    {
        if ($payroll->status != 'pending') {
            return redirect()->back()->with('error', 'Payroll already processed.');
        }

        $payroll->status = 'approved';
        $payroll->save();

        return redirect()->route('payroll-approval.index')->with('success', 'Payroll approved.');
    }

    public function reject(Payroll $payroll)
    {
        if ($payroll->status != 'pending') {
            return redirect()->back()->with('error', 'Payroll already processed.');
        }

        $payroll->status = 'rejected';
        $payroll->save();

        return redirect()->route('payroll-approval.index')->with('success', 'Payroll rejected.');
    }
}
