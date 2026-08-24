<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\PayrollBatch;
use App\Models\ThirteenthMonthPay;
use App\Services\ThirteenthMonthPayService;
use App\Traits\LogsUserActivity;
use Illuminate\Http\Request;

class ThirteenthMonthPayController extends Controller
{
    use LogsUserActivity;

    public function __construct(
        protected ThirteenthMonthPayService $service
    ) {}

    public function index(Request $request)
    {
        $calendarYear = (int) $request->get('year', now()->year);

        $batches = PayrollBatch::thirteenthMonth()
            ->whereYear('period_start', $calendarYear)
            ->withCount('thirteenthMonthPays')
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total_payable' => ThirteenthMonthPay::where('calendar_year', $calendarYear)->sum('thirteenth_month_pay'),
            'total_paid' => ThirteenthMonthPay::where('calendar_year', $calendarYear)->sum('amount_paid'),
            'total_employees' => ThirteenthMonthPay::where('calendar_year', $calendarYear)->count(),
        ];

        $this->logActivity('viewed', "13th month pay batches for {$calendarYear}", request()->url(), 'thirteenth_month');

        return view('hr.payroll.thirteenth-month.index', compact('batches', 'calendarYear', 'stats'));
    }

    public function generate(Request $request)
    {
        $request->validate(['year' => 'required|integer|min:2000|max:2100']);

        $year = (int) $request->input('year');
        $batch = $this->service->generateBatch($year, auth()->id());

        $this->logActivity('created', "Generated 13th month pay batch #{$batch->id} for {$year}", request()->url(), 'thirteenth_month', $batch->id);

        return redirect()
            ->route('payroll.thirteenth-month-pay.batch.details', $batch)
            ->with('success', "13th month pay batch generated for {$year}. Review the records below.");
    }

    public function batchDetails(PayrollBatch $batch)
    {
        if ($batch->type !== 'thirteenth_month') {
            abort(404);
        }

        $batch->load(['thirteenthMonthPays.user']);

        $this->logActivity('viewed', "13th month pay batch #{$batch->id} details", request()->url(), 'thirteenth_month', $batch->id);

        return view('hr.payroll.thirteenth-month.details', compact('batch'));
    }

    public function batchConfirm(PayrollBatch $batch)
    {
        if ($batch->type !== 'thirteenth_month') {
            abort(404);
        }

        $batch->load(['thirteenthMonthPays.user']);

        $this->logActivity('viewed', "13th month pay batch #{$batch->id} confirmation", request()->url(), 'thirteenth_month', $batch->id);

        return view('hr.payroll.thirteenth-month.confirm', compact('batch'));
    }

    public function submit(Request $request, PayrollBatch $batch)
    {
        if ($batch->type !== 'thirteenth_month') {
            abort(404);
        }

        if (!in_array($batch->status, ['draft', 'submitted'])) {
            return back()->with('error', 'This batch cannot be submitted.');
        }

        $batch->update(['status' => 'submitted']);

        $this->logActivity('submitted', "13th month pay batch #{$batch->id} submitted for approval", request()->url(), 'thirteenth_month', $batch->id);

        return redirect()
            ->route('payroll.thirteenth-month-pay.batch.details', $batch)
            ->with('success', 'Batch submitted for accountant approval.');
    }

    public function batchPayslips(PayrollBatch $batch)
    {
        if ($batch->type !== 'thirteenth_month') {
            abort(404);
        }

        $batch->load(['thirteenthMonthPays.user']);

        return view('hr.payroll.thirteenth-month.payslips', compact('batch'));
    }

    public function payslip(PayrollBatch $batch, ThirteenthMonthPay $record)
    {
        if ($batch->type !== 'thirteenth_month') {
            abort(404);
        }

        $record->load(['user', 'computedBy']);

        $breakdown = $record->computation_breakdown
            ?? $this->service->buildComputationBreakdown($record->user, $record->calendar_year);

        return view('hr.payroll.thirteenth-month.payslip', compact('batch', 'record', 'breakdown'));
    }

    public function reopen(PayrollBatch $batch)
    {
        if ($batch->type !== 'thirteenth_month') {
            abort(404);
        }

        if (!in_array($batch->status, ['submitted', 'approved', 'rejected'])) {
            return back()->with('error', 'This batch cannot be reopened.');
        }

        $batch->update(['status' => 'draft']);

        $this->logActivity('updated', "13th month pay batch #{$batch->id} reopened", request()->url(), 'thirteenth_month', $batch->id);

        return redirect()
            ->route('payroll.thirteenth-month-pay.batch.details', $batch)
            ->with('success', 'Batch reopened. You can make changes and resubmit.');
    }

    public function show(ThirteenthMonthPay $thirteenthMonthPay)
    {
        $thirteenthMonthPay->load(['user', 'computedBy', 'paidBy', 'batch']);
        $breakdown = $thirteenthMonthPay->computation_breakdown
            ?? $this->service->buildComputationBreakdown($thirteenthMonthPay->user, $thirteenthMonthPay->calendar_year);

        $this->logActivity('viewed', "13th month pay record #{$thirteenthMonthPay->id}", request()->url(), 'thirteenth_month', $thirteenthMonthPay->id);

        return view('hr.payroll.thirteenth-month.show', compact('thirteenthMonthPay', 'breakdown'));
    }

    public function destroyBatch(PayrollBatch $batch)
    {
        if ($batch->type !== 'thirteenth_month') {
            abort(404);
        }

        if (!in_array($batch->status, ['draft', 'submitted'])) {
            return back()->with('error', 'Only draft or submitted batches can be deleted.');
        }

        $batch->thirteenthMonthPays()->forceDelete();
        $batch->forceDelete();

        $this->logActivity('deleted', "13th month pay batch #{$batch->id} deleted", request()->url(), 'thirteenth_month');

        return redirect()
            ->route('payroll.salary-computation.index')
            ->with('success', 'Batch deleted successfully.');
    }

    public function recompute(ThirteenthMonthPay $thirteenthMonthPay)
    {
        $this->service->computeForEmployee(
            $thirteenthMonthPay->user,
            $thirteenthMonthPay->calendar_year,
            auth()->id()
        );

        $this->logActivity('updated', "13th month pay recomputed for #{$thirteenthMonthPay->id}", request()->url(), 'thirteenth_month', $thirteenthMonthPay->id);

        return back()->with('success', 'Employee 13th month pay recomputed.');
    }
}
