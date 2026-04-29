<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\PayrollBatch;
use Illuminate\Http\Request;

class PayrollPaymentsController extends Controller
{
    public function index()
    {
        $batches = PayrollBatch::with(['payrolls.user'])
            ->whereIn('status', ['approved', 'paid'])
            ->orderByDesc('period_start')
            ->get();

        return view('accountant.payroll-payments.index', compact('batches'));
    }

    public function show(PayrollBatch $batch)
    {
        $batch->load(['payrolls.user']);
        abort_if(!in_array($batch->status, ['approved', 'paid'], true), 403, 'Batch must be approved before payments can be recorded.');

        return view('accountant.payroll-payments.show', compact('batch'));
    }

    public function markPaid(Request $request, Payroll $payroll)
    {
        abort_if(!$payroll->batch_id, 403, 'Payroll is not part of a batch.');

        $batch = PayrollBatch::with('payrolls')->findOrFail($payroll->batch_id);
        abort_if(!in_array($batch->status, ['approved', 'paid'], true), 403, 'Batch must be approved before marking as paid.');

        $payroll->forceFill([
            'status' => 'paid',
            'payment_date' => now(),
        ])->save();

        $this->syncBatchPaidState($batch);

        return redirect()
            ->route('payroll-payments.show', $batch)
            ->with('success', 'Employee marked as paid.');
    }

    public function markUnpaid(Request $request, Payroll $payroll)
    {
        abort_if(!$payroll->batch_id, 403, 'Payroll is not part of a batch.');

        $batch = PayrollBatch::with('payrolls')->findOrFail($payroll->batch_id);
        abort_if(!in_array($batch->status, ['approved', 'paid'], true), 403, 'Batch must be approved before updating payment status.');

        // Revert to approved (still accepted, just not yet paid).
        $payroll->forceFill([
            'status' => 'approved',
            'payment_date' => null,
        ])->save();

        $this->syncBatchPaidState($batch);

        return redirect()
            ->route('payroll-payments.show', $batch)
            ->with('success', 'Employee set back to unpaid.');
    }

    private function syncBatchPaidState(PayrollBatch $batch): void
    {
        $batch->loadMissing('payrolls');

        $allPaid = $batch->payrolls->isNotEmpty() && $batch->payrolls->every(fn ($p) => $p->status === 'paid');

        if ($allPaid) {
            $batch->update([
                'status' => 'paid',
                'paid_by' => auth()->id(),
                'paid_at' => now(),
            ]);
        } else {
            // If any payroll is unpaid, batch is not fully paid yet.
            if ($batch->status === 'paid') {
                $batch->update([
                    'status' => 'approved',
                    'paid_by' => null,
                    'paid_at' => null,
                ]);
            }
        }
    }
}

