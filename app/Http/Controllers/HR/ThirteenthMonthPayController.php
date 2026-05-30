<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Bonus;
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

        $allRecords = ThirteenthMonthPay::with('user')
            ->where('calendar_year', $calendarYear)
            ->orderByDesc('thirteenth_month_pay')
            ->get();

        $bonus = Bonus::where('code', Bonus::CODE_THIRTEENTH_MONTH)->first();

        $stats = [
            'total_employees' => $allRecords->count(),
            'total_payable' => ThirteenthMonthPay::where('calendar_year', $calendarYear)->sum('thirteenth_month_pay'),
            'total_paid' => ThirteenthMonthPay::where('calendar_year', $calendarYear)->sum('amount_paid'),
        ];

        $this->logActivity('viewed', "13th month pay for {$calendarYear}", request()->url(), 'thirteenth_month');

        return view('hr.bonuses.thirteenth-month-pay.index', compact(
            'allRecords',
            'calendarYear',
            'bonus',
            'stats'
        ));
    }

    public function compute(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000|max:2100',
        ]);

        $year = (int) $request->input('year');
        $results = $this->service->computeForYear($year, auth()->id());

        $this->logActivity('created', "Computed 13th month pay for {$year}", request()->url(), 'thirteenth_month');

        if ($request->boolean('return_bonus_edit')) {
            $bonusId = $request->input('bonus_id');

            return redirect()
                ->route('bonuses.edit', ['bonus' => $bonusId, 'year' => $year])
                ->with('success', "Computed 13th month pay for {$results->count()} employee(s).");
        }

        return redirect()
            ->route('payroll.thirteenth-month-pay.index', ['year' => $year])
            ->with('success', "Computed 13th month pay for {$results->count()} employee(s).");
    }

    public function show(ThirteenthMonthPay $thirteenthMonthPay)
    {
        $thirteenthMonthPay->load(['user', 'computedBy', 'paidBy']);
        $breakdown = $thirteenthMonthPay->computation_breakdown
            ?? $this->service->buildComputationBreakdown($thirteenthMonthPay->user, $thirteenthMonthPay->calendar_year);

        $this->logActivity('viewed', "13th month pay record #{$thirteenthMonthPay->id}", request()->url(), 'thirteenth_month', $thirteenthMonthPay->id);

        return view('hr.bonuses.thirteenth-month-pay.show', compact('thirteenthMonthPay', 'breakdown'));
    }

    public function edit(ThirteenthMonthPay $thirteenthMonthPay)
    {
        $thirteenthMonthPay->load('user');
        $breakdown = $thirteenthMonthPay->computation_breakdown
            ?? $this->service->buildComputationBreakdown($thirteenthMonthPay->user, $thirteenthMonthPay->calendar_year);
        $bonus = Bonus::where('code', Bonus::CODE_THIRTEENTH_MONTH)->first();

        return view('hr.bonuses.thirteenth-month-pay.edit', compact('thirteenthMonthPay', 'breakdown', 'bonus'));
    }

    public function update(Request $request, ThirteenthMonthPay $thirteenthMonthPay)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:2000',
            'is_eligible' => 'nullable|boolean',
        ]);

        $thirteenthMonthPay->update([
            'notes' => $validated['notes'] ?? $thirteenthMonthPay->notes,
            'is_eligible' => $request->boolean('is_eligible', $thirteenthMonthPay->is_eligible),
        ]);

        $this->logActivity('updated', "13th month pay record #{$thirteenthMonthPay->id}", request()->url(), 'thirteenth_month', $thirteenthMonthPay->id);

        if ($request->boolean('return_bonus_edit')) {
            return redirect()
                ->route('bonuses.edit', [
                    'bonus' => $request->input('bonus_id'),
                    'year' => $thirteenthMonthPay->calendar_year,
                    'record' => $thirteenthMonthPay->id,
                ])
                ->with('success', '13th month pay record updated.');
        }

        return redirect()
            ->route('payroll.thirteenth-month-pay.show', $thirteenthMonthPay)
            ->with('success', '13th month pay record updated.');
    }

    public function recordPaymentForm(ThirteenthMonthPay $thirteenthMonthPay)
    {
        $thirteenthMonthPay->load('user');

        return view('hr.bonuses.thirteenth-month-pay.record-payment', compact('thirteenthMonthPay'));
    }

    public function recordPayment(Request $request, ThirteenthMonthPay $thirteenthMonthPay)
    {
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:2000',
        ]);

        $newPaid = (float) $thirteenthMonthPay->amount_paid + (float) $validated['amount_paid'];
        $total = (float) $thirteenthMonthPay->thirteenth_month_pay;

        $thirteenthMonthPay->update([
            'amount_paid' => $newPaid,
            'amount_remaining' => max(0, $total - $newPaid),
            'status' => $newPaid >= $total ? 'paid' : ($newPaid > 0 ? 'partial' : 'pending'),
            'payment_date' => $validated['payment_date'],
            'paid_by' => auth()->id(),
            'notes' => $validated['notes'] ?? $thirteenthMonthPay->notes,
        ]);

        $this->logActivity('updated', "13th month pay payment recorded for #{$thirteenthMonthPay->id}", request()->url(), 'thirteenth_month', $thirteenthMonthPay->id);

        return redirect()
            ->route('payroll.thirteenth-month-pay.index', ['year' => $thirteenthMonthPay->calendar_year])
            ->with('success', 'Payment recorded successfully.');
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
