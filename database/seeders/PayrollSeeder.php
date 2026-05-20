<?php

namespace Database\Seeders;

use App\Models\Payroll;
use App\Models\PayrollBatch;
use App\Models\User;
use App\Services\PayrollDeductionService;
use App\Services\PayrollService;
use Carbon\Carbon;
use Database\Seeders\Support\SeedConfig;
use Illuminate\Database\Seeder;

class PayrollSeeder extends Seeder
{
    public function run(): void
    {
        $generatedBy = User::where('role', 'hr')->orderBy('id')->value('id');

        $employees = User::where('role', 'employee')
            ->whereIn('id', SeedConfig::employeeIds())
            ->orderBy('id')
            ->get();

        if ($employees->isEmpty()) {
            $this->command?->warn('No employees found; skipping PayrollSeeder.');
            return;
        }

        /** @var PayrollService $payrollService */
        $payrollService = app(PayrollService::class);

        $periods = SeedConfig::payrollPeriods();
        $totalPayrolls = 0;

        foreach ($periods as $index => $period) {
            $periodStart = Carbon::parse($period['start'])->startOfDay();
            $periodEnd = Carbon::parse($period['end'])->endOfDay();
            $isLatest = $index === count($periods) - 1;

            $batch = PayrollBatch::updateOrCreate(
                ['period_start' => $periodStart->toDateString(), 'period_end' => $periodEnd->toDateString()],
                [
                    'status' => $isLatest ? 'submitted' : 'submitted',
                    'generated_by' => $generatedBy,
                    'finalized_at' => $isLatest ? null : $periodEnd->copy()->addDays(3),
                ]
            );

            foreach ($employees as $employee) {
                $existing = Payroll::where('user_id', $employee->id)
                    ->whereDate('payroll_period_start', $periodStart->toDateString())
                    ->whereDate('payroll_period_end', $periodEnd->toDateString())
                    ->first();

                if ($existing) {
                    $existing->forceFill(['batch_id' => $batch->id])->save();
                    continue;
                }

                [$manualAllowances, $manualDeductions] = $this->manualLinesFor($employee, $index);

                $payroll = $payrollService->generatePayrollForEmployee(
                    $employee,
                    $periodStart->copy(),
                    $periodEnd->copy(),
                    $manualAllowances,
                    $manualDeductions
                );

                $payroll->forceFill(['batch_id' => $batch->id])->save();
                PayrollDeductionService::applyLoanDeductions($payroll);

                $status = $this->payrollStatusFor($employee->id, $index, $isLatest);
                $payroll->forceFill([
                    'status' => $status,
                    'approved_by' => $status === 'approved' ? User::where('role', 'accountant')->value('id') : null,
                    'payment_date' => $status === 'approved' ? $periodEnd->copy()->addDays(5)->toDateString() : null,
                ])->save();

                $totalPayrolls++;
            }
        }

        $this->command?->info("PayrollSeeder: {$totalPayrolls} payroll records across " . count($periods) . ' periods (Feb–Apr 2026).');
    }

    /**
     * @return array{0: list<array{name: string, amount: float}>, 1: list<array{name: string, amount: float}>}
     */
    private function manualLinesFor(User $employee, int $periodIndex): array
    {
        $allowances = [];
        $deductions = [];
        $h = SeedConfig::hashFloat($employee->id, 'manual-' . $periodIndex);

        if ($h > 0.7 && in_array($employee->department, ['Operation', 'Maintenance'], true)) {
            $allowances[] = ['name' => 'Transport Allowance', 'amount' => 200.00];
        }
        if ($h > 0.85) {
            $allowances[] = ['name' => 'Meal Allowance', 'amount' => 300.00];
        }
        if ($h < 0.15 && $periodIndex % 2 === 1) {
            $deductions[] = ['name' => 'Uniform Installment', 'amount' => 150.00];
        }

        return [$allowances, $deductions];
    }

    private function payrollStatusFor(int $userId, int $periodIndex, bool $isLatest): string
    {
        if ($isLatest) {
            return SeedConfig::hashFloat($userId, 'latest') > 0.5 ? 'submitted' : 'submitted';
        }

        if ($periodIndex >= 4) {
            return SeedConfig::hashFloat($userId, 'apr') > 0.3 ? 'approved' : 'submitted';
        }

        return 'approved';
    }
}
