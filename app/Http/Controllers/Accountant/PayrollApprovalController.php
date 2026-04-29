<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\PayrollBatch;
use App\Notifications\PayrollNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollApprovalController extends Controller
{
    public function index()
    {
        $batches = PayrollBatch::with(['payrolls.user'])
            ->orderByDesc('period_start')
            ->get();

        $batchData = $batches
            ->filter(fn (PayrollBatch $b) => $b->status === 'submitted')
            ->map(function (PayrollBatch $b) {
                $payrolls = $b->payrolls;

                return [
                    'batch_id' => $b->id,
                    'period_start' => $b->period_start,
                    'period_end' => $b->period_end,
                    'count' => $payrolls->count(),
                    'total_gross' => $payrolls->sum(fn ($p) => $p->gross_pay),
                    'total_net' => $payrolls->sum(fn ($p) => $p->net_pay),
                    'status' => $b->status,
                    'batch' => $b,
                ];
            })
            ->values()
            ->all();

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

        $startDate = Carbon::createFromFormat('Y-m-d', $start);
        $endDate = Carbon::createFromFormat('Y-m-d', $end);

        $batch = PayrollBatch::with(['payrolls.user', 'payrolls.deductions', 'payrolls.allowances'])
            ->whereDate('period_start', $startDate)
            ->whereDate('period_end', $endDate)
            ->first();

        if (!$batch) {
            return redirect()->route('payroll-approval.index')->with('error', 'Batch not found.');
        }

        $payrolls = $batch->payrolls;
        $totalGross = $payrolls->sum(fn ($p) => $p->gross_pay);
        $totalNet = $payrolls->sum(fn ($p) => $p->net_pay);
        $totalDeductions = $payrolls->sum(fn ($p) => $p->total_deductions);

        $status = $batch->status;

        return view('accountant.payroll-approval.batch', compact(
            'batch',
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

        $batch = PayrollBatch::with('payrolls')
            ->whereDate('period_start', $startDate)
            ->whereDate('period_end', $endDate)
            ->first();

        if (!$batch) {
            return redirect()->route('payroll-approval.index')->with('error', 'Batch not found.');
        }

        if ($batch->status !== 'submitted') {
            return redirect()->route('payroll-approval.batch', ['start' => $start, 'end' => $end])
                ->with('error', 'This batch is no longer awaiting approval.');
        }

        $updated = $batch->payrolls()->where('status', 'submitted')->update(['status' => 'approved']);

        $batch->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_note' => null,
        ]);

        PayrollNotification::notifyHrPayrollApproved($startDate, $endDate, $updated);

        return redirect()->route('payroll-approval.batch', ['start' => $start, 'end' => $end])
            ->with('success', "Batch approved! {$updated} payroll record(s) approved.");
    }

    public function rejectBatch(Request $request)
    {
        $validated = $request->validate([
            'rejection_note' => 'required|string|min:3',
        ]);

        $start = $request->input('start');
        $end = $request->input('end');

        $startDate = Carbon::createFromFormat('Y-m-d', $start);
        $endDate = Carbon::createFromFormat('Y-m-d', $end);

        $batch = PayrollBatch::with('payrolls')
            ->whereDate('period_start', $startDate)
            ->whereDate('period_end', $endDate)
            ->first();

        if (!$batch) {
            return redirect()->route('payroll-approval.index')->with('error', 'Batch not found.');
        }

        if ($batch->status !== 'submitted') {
            return redirect()->route('payroll-approval.batch', ['start' => $start, 'end' => $end])
                ->with('error', 'This batch is no longer awaiting approval.');
        }

        $updated = $batch->payrolls()->where('status', 'submitted')->update(['status' => 'rejected']);

        $batch->update([
            'status' => 'rejected',
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'rejection_note' => $validated['rejection_note'],
        ]);

        PayrollNotification::notifyHrPayrollRejected($startDate, $endDate, $updated, $validated['rejection_note']);

        return redirect()->route('payroll-approval.batch', ['start' => $start, 'end' => $end])
            ->with('success', "Batch rejected! {$updated} payroll record(s) rejected.");
    }

    public function approve(Payroll $payroll)
    {
        if (!in_array($payroll->status, ['pending', 'submitted'])) {
            return redirect()->back()->with('error', 'Payroll already processed.');
        }

        $payroll->status = 'approved';
        $payroll->save();
        PayrollNotification::notifyHrPayrollApproved($payroll->payroll_period_start, $payroll->payroll_period_end, 1);

        return redirect()->route('payroll-approval.index')->with('success', 'Payroll approved.');
    }

    public function reject(Payroll $payroll)
    {
        if (!in_array($payroll->status, ['pending', 'submitted'])) {
            return redirect()->back()->with('error', 'Payroll already processed.');
        }

        $payroll->status = 'rejected';
        $payroll->save();

        return redirect()->route('payroll-approval.index')->with('success', 'Payroll rejected.');
    }
}
