<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\PayrollBatch;
use App\Notifications\PayrollNotification;
use App\Services\LeaveService;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollApprovalController extends Controller
{
    use LogsUserActivity;

    protected $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    public function index()
    {
        $this->logActivity('viewed', 'Payroll Approval List', request()->url());

        $allBatches = PayrollBatch::with(['payrolls'])
            ->whereIn('status', ['submitted', 'approved', 'rejected'])
            ->orderByDesc('period_start')
            ->get();

        $pendingStats  = $allBatches->where('status', 'submitted');
        $approvedStats = $allBatches->where('status', 'approved');
        $rejectedStats = $allBatches->where('status', 'rejected');

        $pendingCount  = $pendingStats->count();
        $approvedCount = $approvedStats->count();
        $rejectedCount = $rejectedStats->count();

        $totalGrossAll       = $pendingStats->sum(fn ($b) => $b->payrolls->sum(fn ($p) => $p->gross_pay));
        $totalDeductionsAll  = $pendingStats->sum(fn ($b) => $b->payrolls->sum(fn ($p) => $p->total_deductions));
        $totalNetAll         = $pendingStats->sum(fn ($b) => $b->payrolls->sum(fn ($p) => $p->net_pay));

        $totalGrossApproved       = $approvedStats->sum(fn ($b) => $b->payrolls->sum(fn ($p) => $p->gross_pay));
        $totalDeductionsApproved  = $approvedStats->sum(fn ($b) => $b->payrolls->sum(fn ($p) => $p->total_deductions));
        $totalNetApproved         = $approvedStats->sum(fn ($b) => $b->payrolls->sum(fn ($p) => $p->net_pay));

        $totalGrossRejected       = $rejectedStats->sum(fn ($b) => $b->payrolls->sum(fn ($p) => $p->gross_pay));
        $totalDeductionsRejected  = $rejectedStats->sum(fn ($b) => $b->payrolls->sum(fn ($p) => $p->total_deductions));
        $totalNetRejected         = $rejectedStats->sum(fn ($b) => $b->payrolls->sum(fn ($p) => $p->net_pay));

        $batches = $allBatches->map(function (PayrollBatch $b) {
            if ($b->isThirteenthMonth()) {
                return $this->formatThirteenthMonthBatch($b);
            }

            return $this->formatRegularBatch($b);
        })->values()->all();

        return view('accountant.payroll-approval.index', compact(
            'batches',
            'pendingCount', 'approvedCount', 'rejectedCount',
            'totalGrossAll', 'totalDeductionsAll', 'totalNetAll',
            'totalGrossApproved', 'totalDeductionsApproved', 'totalNetApproved',
            'totalGrossRejected', 'totalDeductionsRejected', 'totalNetRejected',
        ));
    }

    protected function formatRegularBatch(PayrollBatch $b): array
    {
        $payrolls = $b->payrolls;
        $start    = $b->period_start;
        $isFirst  = (int) $start->format('d') <= 15;

        return [
            'id'          => $b->id,
            'type'        => 'regular',
            'period_start'=> $b->period_start,
            'period_end'  => $b->period_end,
            'month_year'  => $start->format('F Y'),
            'half'        => $isFirst ? '1st' : '2nd',
            'half_label'  => $isFirst ? 'first' : 'second',
            'period_dates'=> $start->format('M d').' – '.$b->period_end->format('M d, Y'),
            'count'       => $payrolls->count(),
            'total_gross'      => (float) $payrolls->sum(fn ($p) => $p->gross_pay),
            'total_deductions' => (float) $payrolls->sum(fn ($p) => $p->total_deductions),
            'total_net'        => (float) $payrolls->sum(fn ($p) => $p->net_pay),
            'status'      => $b->status,
            'url'         => route('payroll-approval.batch', $b),
        ];
    }

    protected function formatThirteenthMonthBatch(PayrollBatch $b): array
    {
        $b->loadMissing('thirteenthMonthPays');
        $records = $b->thirteenthMonthPays;
        $totalPayable = $records->sum('thirteenth_month_pay');
        $totalPaid = $records->sum('amount_paid');

        return [
            'id'          => $b->id,
            'type'        => 'thirteenth_month',
            'period_start'=> $b->period_start,
            'period_end'  => $b->period_end,
            'month_year'  => $b->period_start->format('Y'),
            'half'        => null,
            'half_label'  => '13th Month',
            'period_dates'=> $b->period_start->format('Y'),
            'count'       => $records->count(),
            'total_gross'      => $totalPayable,
            'total_deductions' => 0,
            'total_net'        => $totalPayable,
            'status'      => $b->status,
            'url'         => route('payroll-approval.batch', $b),
        ];
    }

    public function showBatch(PayrollBatch $batch)
    {
        $this->logActivity('viewed', "Payroll Batch #{$batch->id}", request()->url(), 'payroll_batch', $batch->id);

        if ($batch->isThirteenthMonth()) {
            return $this->showThirteenthMonthBatch($batch);
        }

        $batch->load(['payrolls.user', 'payrolls.deductions', 'payrolls.allowances', 'generatedBy', 'finalizedBy']);

        $payrolls = $batch->payrolls;
        $totalGross = $payrolls->sum(fn ($p) => $p->gross_pay);
        $totalNet = $payrolls->sum(fn ($p) => $p->net_pay);
        $totalDeductions = $payrolls->sum(fn ($p) => $p->total_deductions);

        return view('accountant.payroll-approval.batch', compact(
            'batch', 'payrolls', 'totalGross', 'totalNet', 'totalDeductions',
        ));
    }

    protected function showThirteenthMonthBatch(PayrollBatch $batch)
    {
        $batch->load(['thirteenthMonthPays.user', 'generatedBy', 'finalizedBy']);

        $records = $batch->thirteenthMonthPays;
        $totalPayable = $records->sum('thirteenth_month_pay');
        $totalPaid = $records->sum('amount_paid');
        $totalRemaining = $records->sum('amount_remaining');

        return view('accountant.payroll-approval.thirteenth-month-batch', compact(
            'batch', 'records', 'totalPayable', 'totalPaid', 'totalRemaining',
        ));
    }

    public function show($id)
    {
        $this->logActivity('viewed', "Payroll #{$id}", request()->url(), 'payroll', (int) $id);

        $payroll = \App\Models\Payroll::with(['user', 'approvedBy', 'deductions', 'allowances', 'bonuses', 'batch'])->findOrFail($id);
        $overtimeUndertimeBreakdown = $payroll->getOvertimeUndertimeBreakdown();
        return view('accountant.payroll-approval.show', compact('payroll', 'overtimeUndertimeBreakdown'));
    }

    public function approveBatch(Request $request)
    {
        $batch = PayrollBatch::findOrFail($request->input('batch_id'));
        $this->logActivity('approved', ( $batch->isThirteenthMonth() ? '13th Month ' : '' ) . "Batch #{$batch->id}", request()->url(), 'payroll_batch', $batch->id);

        if ($batch->status !== 'submitted') {
            return redirect()->route('payroll-approval.batch', $batch)
                ->with('error', 'This batch is no longer awaiting approval.');
        }

        if ($batch->isThirteenthMonth()) {
            $records = $batch->thirteenthMonthPays()->where('status', 'pending')->get();
            $updated = $records->count();
        } else {
            $updated = $batch->payrolls()->where('status', 'submitted')->update(['status' => 'approved']);
        }

        $batch->update([
            'status'       => 'approved',
            'approved_by'  => auth()->id(),
            'approved_at'  => now(),
            'rejected_by'  => null,
            'rejected_at'  => null,
            'rejection_note' => null,
        ]);

        if (!$batch->isThirteenthMonth()) {
            $periodStart = Carbon::parse($batch->period_start);
            $periodEnd   = Carbon::parse($batch->period_end);
            foreach ($batch->payrolls as $payroll) {
                $this->leaveService->markLeavesAsPaid($payroll->user_id, $periodStart, $periodEnd);
            }

            PayrollNotification::notifyHrPayrollApproved(
                $batch->period_start,
                $batch->period_end,
                $updated
            );
        }

        return redirect()->route('payroll-approval.batch', $batch)
            ->with('success', "Batch approved — {$updated} record(s) approved.");
    }

    public function rejectBatch(Request $request)
    {
        $validated = $request->validate([
            'batch_id'       => 'required|exists:payroll_batches,id',
            'rejection_note' => 'required|string|min:3',
        ]);

        $batch = PayrollBatch::findOrFail($validated['batch_id']);
        $this->logActivity('rejected', ( $batch->isThirteenthMonth() ? '13th Month ' : '' ) . "Batch #{$batch->id}", request()->url(), 'payroll_batch', $batch->id);

        if ($batch->status !== 'submitted') {
            return redirect()->route('payroll-approval.batch', $batch)
                ->with('error', 'This batch is no longer awaiting approval.');
        }

        if ($batch->isThirteenthMonth()) {
            $updated = $batch->thirteenthMonthPays()->count();
        } else {
            $updated = $batch->payrolls()->where('status', 'submitted')->update(['status' => 'rejected']);
        }

        $batch->update([
            'status'         => 'rejected',
            'rejected_by'    => auth()->id(),
            'rejected_at'    => now(),
            'rejection_note' => $validated['rejection_note'],
        ]);

        if (!$batch->isThirteenthMonth()) {
            PayrollNotification::notifyHrPayrollRejected(
                $batch->period_start,
                $batch->period_end,
                $updated,
                $validated['rejection_note']
            );
        }

        return redirect()->route('payroll-approval.batch', $batch)
            ->with('success', "Batch rejected — {$updated} record(s) rejected.");
    }

    public function approve(\App\Models\Payroll $payroll)
    {
        $this->logActivity('approved', "Payroll for {$payroll->user->name}", request()->url(), 'payroll', $payroll->id);

        if (!in_array($payroll->status, ['pending', 'submitted'])) {
            return redirect()->back()->with('error', 'Payroll already processed.');
        }
        $payroll->update(['status' => 'approved']);
        $this->leaveService->markLeavesAsPaid(
            $payroll->user_id,
            Carbon::parse($payroll->payroll_period_start),
            Carbon::parse($payroll->payroll_period_end)
        );
        PayrollNotification::notifyHrPayrollApproved($payroll->payroll_period_start, $payroll->payroll_period_end, 1);
        return redirect()->route('payroll-approval.index')->with('success', 'Payroll approved.');
    }

    public function reject(\App\Models\Payroll $payroll)
    {
        $this->logActivity('rejected', "Payroll for {$payroll->user->name}", request()->url(), 'payroll', $payroll->id);

        if (!in_array($payroll->status, ['pending', 'submitted'])) {
            return redirect()->back()->with('error', 'Payroll already processed.');
        }
        $payroll->update(['status' => 'rejected']);
        return redirect()->route('payroll-approval.index')->with('success', 'Payroll rejected.');
    }
}
