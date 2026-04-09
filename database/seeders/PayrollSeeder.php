<?php

namespace Database\Seeders;

use App\Models\Payroll;
use App\Models\PayrollBatch;
use App\Models\User;
use App\Services\PayrollDeductionService;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PayrollSeeder extends Seeder
{
    public function run(): void
    {
        // Match the seeded attendance/OT window (Mar 16–31, 2026)
        $periodStart = Carbon::parse('2026-03-16')->startOfDay();
        $periodEnd   = Carbon::parse('2026-03-31')->endOfDay();

        $generatedBy = User::where('role', 'hr')->orderBy('id')->value('id');

        // Create or fetch batch (unique on period)
        $batch = PayrollBatch::updateOrCreate(
            ['period_start' => $periodStart->toDateString(), 'period_end' => $periodEnd->toDateString()],
            ['status' => 'draft', 'generated_by' => $generatedBy]
        );

        // Keep this scoped: seed only the demo employee accounts so we don't disturb other roles.
        $employees = User::where('role', 'employee')
            ->whereIn('username', ['john.doe', 'angela.fernandez', 'juan.trabaho', 'maria.halos', 'carlo.pahinga'])
            ->get();

        if ($employees->isEmpty()) {
            $this->command?->warn('No demo employees found; skipping PayrollSeeder.');
            return;
        }

        /** @var PayrollService $payrollService */
        $payrollService = app(PayrollService::class);

        foreach ($employees as $employee) {
            // Ensure idempotency
            $existing = Payroll::where('user_id', $employee->id)
                ->whereDate('payroll_period_start', $periodStart->toDateString())
                ->whereDate('payroll_period_end', $periodEnd->toDateString())
                ->first();

            if ($existing) {
                // Refresh batch link
                $existing->forceFill(['batch_id' => $batch->id])->save();
                continue;
            }

            // A little contextual “manual” lines so payslips look real
            $manualAllowances = [];
            $manualDeductions = [];

            if ($employee->username === 'juan.trabaho') {
                $manualAllowances[] = ['name' => 'Meal Allowance', 'amount' => 300.00];
            }
            if ($employee->username === 'maria.halos') {
                $manualDeductions[] = ['name' => 'Uniform (Installment)', 'amount' => 200.00];
            }
            if ($employee->username === 'john.doe') {
                $manualAllowances[] = ['name' => 'Communication Allowance', 'amount' => 250.00];
            }

            $payroll = $payrollService->generatePayrollForEmployee(
                $employee,
                $periodStart->copy(),
                $periodEnd->copy(),
                $manualAllowances,
                $manualDeductions
            );

            // Attach to batch
            $payroll->forceFill(['batch_id' => $batch->id])->save();

            // Apply seeded cash advance + active salary loan deductions (ONCE)
            PayrollDeductionService::applyLoanDeductions($payroll);

            // Give variety: some draft, some approved, some paid
            $status = match ($employee->username) {
                'angela.fernandez' => 'paid',
                'juan.trabaho'     => 'approved',
                default            => 'draft',
            };

            $payroll->forceFill([
                'status' => $status,
                'approved_by' => $status !== 'draft' ? $generatedBy : null,
                'payment_date' => $status === 'paid' ? Carbon::parse('2026-04-05') : null,
            ])->save();
        }

        $count = Payroll::whereDate('payroll_period_start', $periodStart->toDateString())
            ->whereDate('payroll_period_end', $periodEnd->toDateString())
            ->whereIn('user_id', $employees->pluck('id')->all())
            ->count();

        $this->command?->info("✅ PayrollSeeder: seeded {$count} payroll records + line items for payslips.");
    }
}

