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
    /* ══════════════════════════════════════════════════════════════
     |  INDEX — list of submitted batches awaiting approval
     ══════════════════════════════════════════════════════════════ */
    public function index()
    {
        $batches = PayrollBatch::with(['payrolls'])
            ->whereIn('status', ['submitted', 'approved', 'rejected'])
            ->orderByDesc('period_start')
            ->get();

        $batchData = $batches->map(function (PayrollBatch $b) {
            $payrolls = $b->payrolls;
            return [
                'batch_id'    => $b->id,
                'period_start'=> $b->period_start,
                'period_end'  => $b->period_end,
                'count'       => $payrolls->count(),
                'total_gross' => $payrolls->sum(fn ($p) => $p->gross_pay),
                'total_net'   => $payrolls->sum(fn ($p) => $p->net_pay),
                'status'      => $b->status,
                'batch'       => $b,
            ];
        })->values()->all();

        $pendingCount = collect($batchData)->where('status', 'submitted')->count();

        return view('accountant.payroll-approval.index', compact('batchData', 'pendingCount'));
    }

    /* ══════════════════════════════════════════════════════════════
     |  SHOW BATCH — employee list inside a batch
     ══════════════════════════════════════════════════════════════ */
    public function showBatch(PayrollBatch $batch)
    {
        $batch->load(['payrolls.user', 'payrolls.deductions', 'payrolls.allowances', 'generatedBy', 'finalizedBy']);

        $payrolls       = $batch->payrolls;
        $totalGross     = $payrolls->sum(fn ($p) => $p->gross_pay);
        $totalNet       = $payrolls->sum(fn ($p) => $p->net_pay);
        $totalDeductions= $payrolls->sum(fn ($p) => $p->total_deductions);

        return view('accountant.payroll-approval.batch', compact(
            'batch',
            'payrolls',
            'totalGross',
            'totalNet',
            'totalDeductions',
        ));
    }

    /* ══════════════════════════════════════════════════════════════
     |  SHOW PAYROLL — individual employee payroll detail
     ══════════════════════════════════════════════════════════════ */
    public function show($id)
    {
        $payroll = Payroll::with(['user', 'deductions', 'allowances', 'batch'])->findOrFail($id);
        $overtimeUndertimeBreakdown = $payroll->getOvertimeUndertimeBreakdown();
        return view('accountant.payroll-approval.show', compact('payroll', 'overtimeUndertimeBreakdown'));
    }

    /* ══════════════════════════════════════════════════════════════
     |  APPROVE BATCH
     ══════════════════════════════════════════════════════════════ */
    public function approveBatch(Request $request)
    {
        $batch = PayrollBatch::with('payrolls')->findOrFail($request->input('batch_id'));

        if ($batch->status !== 'submitted') {
            return redirect()->route('payroll-approval.batch', $batch)
                ->with('error', 'This batch is no longer awaiting approval.');
        }

        $updated = $batch->payrolls()->where('status', 'submitted')->update(['status' => 'approved']);

        $batch->update([
            'status'       => 'approved',
            'approved_by'  => auth()->id(),
            'approved_at'  => now(),
            'rejected_by'  => null,
            'rejected_at'  => null,
            'rejection_note' => null,
        ]);

        PayrollNotification::notifyHrPayrollApproved(
            $batch->period_start,
            $batch->period_end,
            $updated
        );

        return redirect()->route('payroll-approval.batch', $batch)
            ->with('success', "Batch approved — {$updated} payroll record(s) approved.");
    }

    /* ══════════════════════════════════════════════════════════════
     |  REJECT BATCH
     ══════════════════════════════════════════════════════════════ */
    public function rejectBatch(Request $request)
    {
        $validated = $request->validate([
            'batch_id'       => 'required|exists:payroll_batches,id',
            'rejection_note' => 'required|string|min:3',
        ]);

        $batch = PayrollBatch::with('payrolls')->findOrFail($validated['batch_id']);

        if ($batch->status !== 'submitted') {
            return redirect()->route('payroll-approval.batch', $batch)
                ->with('error', 'This batch is no longer awaiting approval.');
        }

        $updated = $batch->payrolls()->where('status', 'submitted')->update(['status' => 'rejected']);

        $batch->update([
            'status'         => 'rejected',
            'rejected_by'    => auth()->id(),
            'rejected_at'    => now(),
            'rejection_note' => $validated['rejection_note'],
        ]);

        PayrollNotification::notifyHrPayrollRejected(
            $batch->period_start,
            $batch->period_end,
            $updated,
            $validated['rejection_note']
        );

        return redirect()->route('payroll-approval.batch', $batch)
            ->with('success', "Batch rejected — {$updated} payroll record(s) rejected.");
    }

    /* ══════════════════════════════════════════════════════════════
     |  LEGACY individual approve / reject (kept for compatibility)
     ══════════════════════════════════════════════════════════════ */
    public function approve(Payroll $payroll)
    {
        if (!in_array($payroll->status, ['pending', 'submitted'])) {
            return redirect()->back()->with('error', 'Payroll already processed.');
        }
        $payroll->update(['status' => 'approved']);
        PayrollNotification::notifyHrPayrollApproved($payroll->payroll_period_start, $payroll->payroll_period_end, 1);
        return redirect()->route('payroll-approval.index')->with('success', 'Payroll approved.');
    }

    public function reject(Payroll $payroll)
    {
        if (!in_array($payroll->status, ['pending', 'submitted'])) {
            return redirect()->back()->with('error', 'Payroll already processed.');
        }
        $payroll->update(['status' => 'rejected']);
        return redirect()->route('payroll-approval.index')->with('success', 'Payroll rejected.');
    }
}
