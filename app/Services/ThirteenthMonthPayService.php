<?php

namespace App\Services;

use App\Models\Payroll;
use App\Models\PayrollBatch;
use App\Models\ThirteenthMonthPay;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ThirteenthMonthPayService
{
    protected array $countableStatuses = ['released', 'paid', 'approved'];

    public function generateBatch(int $year, int $generatedBy): PayrollBatch
    {
        $batch = PayrollBatch::create([
            'type' => 'thirteenth_month',
            'period_start' => Carbon::create($year, 1, 1),
            'period_end' => Carbon::create($year, 12, 31),
            'status' => 'draft',
            'generated_by' => $generatedBy,
            'generated_at' => now(),
        ]);

        $records = $this->computeForYear($year, $generatedBy);

        foreach ($records as $record) {
            $record->update(['batch_id' => $batch->id]);
        }

        return $batch->fresh();
    }

    public function computeForYear(int $year, ?int $computedBy = null): Collection
    {
        $employees = $this->eligibleEmployees($year);
        $results = collect();

        foreach ($employees as $employee) {
            $results->push($this->computeForEmployee($employee, $year, $computedBy));
        }

        return $results;
    }

    public function computeForEmployee(User $employee, int $year, ?int $computedBy = null): ThirteenthMonthPay
    {
        $breakdown = $this->buildComputationBreakdown($employee, $year);
        $totalBasic = $breakdown['total_basic_salary'];

        $thirteenthPay = $totalBasic / 12;

        $existing = ThirteenthMonthPay::withTrashed()
            ->where('user_id', $employee->id)
            ->where('calendar_year', $year)
            ->first();

        if ($existing) {
            $amountPaid = (float) $existing->amount_paid;
            $existing->fill([
                'batch_id' => null,
                'total_basic_salary_earned' => $totalBasic,
                'thirteenth_month_pay' => $thirteenthPay,
                'months_worked' => $breakdown['months_worked'],
                'is_eligible' => $totalBasic > 0,
                'amount_remaining' => max(0, $thirteenthPay - $amountPaid),
                'status' => $this->resolvePaymentStatus($thirteenthPay, $amountPaid),
                'computed_by' => $computedBy,
                'computed_at' => now(),
                'computation_breakdown' => $breakdown,
            ]);
            if ($existing->trashed()) {
                $existing->restore();
            }
            $existing->save();
            $record = $existing->fresh(['user']);
        } else {
            $amountPaid = 0;
            $record = ThirteenthMonthPay::create([
                'user_id' => $employee->id,
                'calendar_year' => $year,
                'total_basic_salary_earned' => $totalBasic,
                'thirteenth_month_pay' => $thirteenthPay,
                'months_worked' => $breakdown['months_worked'],
                'is_eligible' => $totalBasic > 0,
                'amount_remaining' => max(0, $thirteenthPay),
                'status' => 'pending',
                'computed_by' => $computedBy,
                'computed_at' => now(),
                'computation_breakdown' => $breakdown,
            ]);
        }

        return $record->fresh(['user']);
    }

    public function buildComputationBreakdown(User $employee, int $year): array
    {
        $yearStart = Carbon::create($year, 1, 1)->startOfDay();
        $yearEnd = Carbon::create($year, 12, 31)->endOfDay();

        $employmentStart = $employee->date_of_hire
            ? Carbon::parse($employee->date_of_hire)->startOfDay()
            : $yearStart->copy();

        $periodStart = $employmentStart->greaterThan($yearStart) ? $employmentStart : $yearStart;
        $periodEnd = $yearEnd;

        $payrolls = Payroll::query()
            ->where('user_id', $employee->id)
            ->whereIn('status', $this->countableStatuses)
            ->whereYear('payroll_period_start', $year)
            ->orderBy('payroll_period_start')
            ->get(['id', 'payroll_period_start', 'payroll_period_end', 'basic_salary', 'total_allowances', 'days_worked']);

        $payrollLines = $payrolls->map(function (Payroll $payroll) {
            return [
                'period_start' => $payroll->payroll_period_start?->toDateString(),
                'period_end' => $payroll->payroll_period_end?->toDateString(),
                'basic_salary' => round((float) $payroll->basic_salary, 2),
                'excluded' => [
                    'overtime' => 'Excluded from 13th month',
                    'allowances' => round((float) $payroll->total_allowances, 2),
                    'holiday_pay' => 'Excluded',
                    'night_differential' => 'Excluded',
                    'commissions' => 'Excluded',
                    'incentives' => 'Excluded',
                    'deductions' => 'Excluded',
                ],
            ];
        })->values()->all();

        $totalBasic = round($payrolls->sum(fn (Payroll $p) => (float) $p->basic_salary), 2);
        $monthsWorked = $this->calculateMonthsWorked($employee, $year, $periodStart, $periodEnd);

        $latest = $payrolls->last();

        return [
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'calendar_year' => $year,
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'employment_status' => $employee->status,
            'formula' => '(total_basic_salary / 12)',
            'total_basic_salary' => $totalBasic,
            'months_worked' => $monthsWorked,
            'latest_basic_salary' => $latest ? (float) $latest->basic_salary : 0,
            'latest_gross_pay' => $latest
                ? (float) $latest->basic_salary + (float) $latest->total_allowances
                : 0,
            'attendance_days' => $latest ? (float) $latest->days_worked : 0,
            'payroll_lines' => $payrollLines,
            'notes' => 'Only basic salary is included. Resigned employees remain eligible for prorated amounts based on months worked.',
        ];
    }

    protected function calculateMonthsWorked(
        User $employee,
        int $year,
        Carbon $periodStart,
        Carbon $periodEnd
    ): float {
        $hasAnyPayrolls = Payroll::query()
            ->where('user_id', $employee->id)
            ->whereIn('status', $this->countableStatuses)
            ->whereYear('payroll_period_start', $year)
            ->exists();

        $distinctMonths = Payroll::query()
            ->where('user_id', $employee->id)
            ->whereIn('status', $this->countableStatuses)
            ->whereYear('payroll_period_start', $year)
            ->where('basic_salary', '>', 0)
            ->get()
            ->map(fn (Payroll $p) => $p->payroll_period_start?->format('Y-m'))
            ->filter()
            ->unique()
            ->count();

        if ($distinctMonths > 0) {
            return (float) min(12, $distinctMonths);
        }

        if ($hasAnyPayrolls || !$employee->date_of_hire) {
            return 0;
        }

        $hireYear = (int) Carbon::parse($employee->date_of_hire)->format('Y');

        if ($hireYear < $year) {
            return 0;
        }

        $effectiveEnd = $year === (int) now()->year
            ? min($periodEnd, now()->endOfMonth())
            : $periodEnd;

        $months = $periodStart->diffInMonths($effectiveEnd) + 1;

        return (float) min(12, max(0, $months));
    }

    protected function eligibleEmployees(int $year): Collection
    {
        $userIdsWithSalary = Payroll::query()
            ->whereIn('status', $this->countableStatuses)
            ->whereYear('payroll_period_start', $year)
            ->where('basic_salary', '>', 0)
            ->distinct()
            ->pluck('user_id');

        return User::query()
            ->whereIn('id', $userIdsWithSalary)
            ->whereIn('role', ['employee', 'hr', 'remittance_clerk', 'accountant'])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();
    }

    protected function resolvePaymentStatus(float $total, float $paid): string
    {
        if ($paid <= 0) {
            return 'pending';
        }

        if ($paid >= $total) {
            return 'paid';
        }

        return 'partial';
    }
}
