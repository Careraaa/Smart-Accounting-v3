<?php

namespace App\Services;

use App\Models\PayrollBatch;
use App\Models\Payroll;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\AccountMapping;
use App\Models\AccountingAuditLog;
use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class PayrollJournalService
{
    protected $statutoryContributionService;

    public function __construct(?StatutoryContributionService $statutoryContributionService = null)
    {
        $this->statutoryContributionService = $statutoryContributionService ?? new StatutoryContributionService();
    }

    public function validatePayrollAccountMappings(array $accountIds): void
    {
        $expectedTypes = [
            'payroll_expense' => 'Expense',
            'sss_payable' => 'Liability',
            'philhealth_payable' => 'Liability',
            'pagibig_payable' => 'Liability',
            'withholding_tax_payable' => 'Liability',
            'salaries_payable' => 'Liability',
            'payroll_deductions' => 'Expense',
            'employee_receivable' => 'Asset',
            'other_payroll_deductions_payable' => 'Liability',
            'employer_contributions_expense' => 'Expense',
        ];
        $mappedAccounts = [];
        $accounts = ChartOfAccount::whereIn('id', array_values($accountIds))
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($expectedTypes as $key => $expectedType) {
            $accountId = $accountIds[$key] ?? null;
            if (!$accountId) {
                throw new \RuntimeException("Account mapping not configured for: {$key}. Please configure account mappings in Accounting Settings.");
            }

            $account = $accounts->get($accountId);
            if (!$account || !$account->is_active || $account->account_type !== $expectedType) {
                throw new \RuntimeException("Invalid account mapping for {$key}: an active {$expectedType} account is required.");
            }
            if ($key === 'payroll_deductions' && !$account->is_contra) {
                throw new \RuntimeException('Invalid account mapping for payroll_deductions: an active contra-expense account is required.');
            }

            $mappedAccounts[$key] = (int) $account->id;
        }

        foreach ([
            ['payroll_expense', 'employer_contributions_expense'],
            ['payroll_deductions', 'payroll_expense'],
            ['payroll_deductions', 'employer_contributions_expense'],
            ['other_payroll_deductions_payable', 'sss_payable'],
            ['other_payroll_deductions_payable', 'philhealth_payable'],
            ['other_payroll_deductions_payable', 'pagibig_payable'],
            ['other_payroll_deductions_payable', 'withholding_tax_payable'],
            ['other_payroll_deductions_payable', 'salaries_payable'],
            ['employee_receivable', 'other_payroll_deductions_payable'],
            ['employee_receivable', 'sss_payable'],
            ['employee_receivable', 'philhealth_payable'],
            ['employee_receivable', 'pagibig_payable'],
            ['employee_receivable', 'withholding_tax_payable'],
            ['employee_receivable', 'salaries_payable'],
        ] as [$left, $right]) {
            if ($mappedAccounts[$left] === $mappedAccounts[$right]) {
                throw new \RuntimeException("Account mappings {$left} and {$right} must use different accounts.");
            }
        }
    }

    public function getValidatedPayrollAccountIds(): array
    {
        $accountIds = AccountMapping::whereIn('mapping_key', [
            'payroll_expense',
            'sss_payable',
            'philhealth_payable',
            'pagibig_payable',
            'withholding_tax_payable',
            'salaries_payable',
            'payroll_deductions',
            'employee_receivable',
            'other_payroll_deductions_payable',
            'employer_contributions_expense',
        ])->lockForUpdate()->pluck('account_id', 'mapping_key')->map(fn ($id) => (int) $id)->all();

        $this->validatePayrollAccountMappings($accountIds);

        return $accountIds;
    }

    public function assertJournalEntryMatchesPayroll(JournalEntry $journalEntry): void
    {
        if ($journalEntry->reference_type !== 'Payroll' || !$journalEntry->reference_id) {
            throw new \RuntimeException('The journal entry is not linked to a payroll batch.');
        }

        $batch = PayrollBatch::whereKey($journalEntry->reference_id)
            ->lockForUpdate()
            ->first();
        if (!$batch || $batch->status !== 'approved' || $batch->isThirteenthMonth()) {
            throw new \RuntimeException('The linked payroll batch must exist and be approved before posting.');
        }
        $batch->load('payrolls.deductions', 'payrolls.user');
        if ($batch->payrolls->isEmpty()) {
            throw new \RuntimeException('No payroll records found for the linked batch.');
        }

        $this->populateLegacyEmployerContributions($batch->payrolls);
        $accountIds = $this->getValidatedPayrollAccountIds();

        $expected = $this->aggregateLines($this->buildLinesForPayrolls($batch->payrolls, $accountIds));
        $actual = $this->aggregateLines($journalEntry->lines()->get()->map(fn ($line) => [
            'account_id' => (int) $line->account_id,
            'debit' => (float) $line->debit,
            'credit' => (float) $line->credit,
        ])->all());

        if ($actual !== $expected) {
            throw new \RuntimeException('The payroll journal entry no longer matches the approved payroll and current account mappings. Refresh the draft before posting.');
        }
    }

    public function generateJournalEntryForBatch(PayrollBatch $batch, ?JournalEntry $journalEntry = null): JournalEntry
    {
        if ($batch->status !== 'approved') {
            throw new \RuntimeException('Cannot generate journal entry for a batch that is not approved.');
        }

        if ($journalEntry && (
            $journalEntry->reference_type !== 'Payroll'
            || (int) $journalEntry->reference_id !== (int) $batch->id
            || $journalEntry->status !== 'Draft'
        )) {
            throw new \RuntimeException('Only the draft journal entry for this payroll batch can be refreshed.');
        }

        if (!$journalEntry && $this->batchHasJournalEntry($batch)) {
            throw new \RuntimeException('A journal entry already exists for this payroll batch.');
        }

        DB::beginTransaction();
        try {
            $lockedBatch = PayrollBatch::whereKey($batch->getKey())->lockForUpdate()->firstOrFail();
            if ($lockedBatch->status !== 'approved') {
                throw new \RuntimeException('Cannot generate journal entry for a batch that is not approved.');
            }
            if (!$journalEntry && $this->batchHasJournalEntry($batch)) {
                throw new \RuntimeException('A journal entry already exists for this payroll batch.');
            }
            if ($journalEntry) {
                $journalEntry = JournalEntry::whereKey($journalEntry->getKey())->lockForUpdate()->firstOrFail();
                if (
                    $journalEntry->reference_type !== 'Payroll'
                    || (int) $journalEntry->reference_id !== (int) $batch->id
                    || $journalEntry->status !== 'Draft'
                ) {
                    throw new \RuntimeException('Only the draft journal entry for this payroll batch can be refreshed.');
                }
            }

            $lockedBatch->load('payrolls.deductions', 'payrolls.user');
            if ($lockedBatch->payrolls->isEmpty()) {
                throw new \RuntimeException('No payroll records found for this batch.');
            }

            $this->populateLegacyEmployerContributions($lockedBatch->payrolls);
            $accountIds = $this->getValidatedPayrollAccountIds();
            $lines = $this->buildLinesForPayrolls($lockedBatch->payrolls, $accountIds);
            $totalDebit = array_sum(array_column($lines, 'debit'));
            $totalCredit = array_sum(array_column($lines, 'credit'));

            $periodStart = $lockedBatch->period_start->format('M d, Y');
            $periodEnd = $lockedBatch->period_end->format('M d, Y');
            $description = "Payroll – {$periodStart} to {$periodEnd}";
            $previousTotals = $journalEntry ? [
                'total_debit' => (float) $journalEntry->lines()->sum('debit'),
                'total_credit' => (float) $journalEntry->lines()->sum('credit'),
            ] : null;

            $journalEntryData = [
                'transaction_date' => $lockedBatch->approved_at?->date ?? now()->toDateString(),
                'description'      => $description,
            ];

            if ($journalEntry) {
                $journalEntry->update($journalEntryData);
                $journalEntry->lines()->delete();
            } else {
                $journalEntry = JournalEntry::create($journalEntryData + [
                    'journal_number' => JournalEntry::generateJournalNumber(),
                    'reference_type' => 'Payroll',
                    'reference_id' => $batch->id,
                    'status' => 'Draft',
                    'created_by' => auth()->id(),
                ]);
            }

            foreach ($lines as $line) {
                $journalEntry->lines()->create($line);
            }

            AccountingAuditLog::log(
                $previousTotals ? 'refreshed_from_payroll' : 'created_from_payroll',
                'Journal Entry',
                $journalEntry->id,
                ($previousTotals ? 'Refreshed' : 'Generated') . " journal entry {$journalEntry->journal_number} from payroll batch #{$batch->id}",
                $previousTotals,
                [
                    'journal_number' => $journalEntry->journal_number,
                    'batch_id'       => $batch->id,
                    'total_debit'    => $totalDebit,
                    'total_credit'   => $totalCredit,
                ]
            );

            DB::commit();

            return $journalEntry;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function buildLinesForPayrolls(Collection $payrolls, array $accountIds): array
    {
        $totals = [
            'gross' => 0,
            'net' => 0,
            'employee_sss' => 0,
            'employee_philhealth' => 0,
            'employee_pagibig' => 0,
            'withholding' => 0,
            'employer_sss' => 0,
            'employer_philhealth' => 0,
            'employer_pagibig' => 0,
            'wage_reductions' => 0,
            'manual_payable' => 0,
            'manual_receivable' => 0,
            'employee_receivable' => 0,
        ];

        foreach ($payrolls as $payroll) {
            $totals['gross'] += $this->cents($payroll->gross_pay);
            $totals['net'] += $this->cents($payroll->net_pay);
            $totals['employee_sss'] += $this->cents($payroll->sss);
            $totals['employee_philhealth'] += $this->cents($payroll->philhealth);
            $totals['employee_pagibig'] += $this->cents($payroll->pagibig);
            $totals['withholding'] += $this->cents($payroll->withholding_tax);
            $totals['employer_sss'] += $this->cents($payroll->employer_sss);
            $totals['employer_philhealth'] += $this->cents($payroll->employer_philhealth);
            $totals['employer_pagibig'] += $this->cents($payroll->employer_pagibig);
            $totals['employee_receivable'] += $this->cents($payroll->cash_advance_deduction)
                + $this->cents($payroll->salary_loan_deduction);

            foreach ([
                'sss', 'philhealth', 'pagibig', 'withholding_tax',
                'employer_sss', 'employer_philhealth', 'employer_pagibig',
                'cash_advance_deduction', 'salary_loan_deduction',
            ] as $amountField) {
                if ($this->cents($payroll->{$amountField}) < 0) {
                    throw new \RuntimeException("Payroll #{$payroll->id} contains a negative payroll amount.");
                }
            }

            foreach ($payroll->deductions as $deduction) {
                $amount = $this->cents($deduction->amount);
                if ($amount < 0) {
                    throw new \RuntimeException("Payroll #{$payroll->id} contains a negative deduction.");
                }

                $type = (string) $deduction->deduction_type;
                $legacySystemTypes = ['SSS', 'Pag-IBIG', 'PhilHealth', 'Withholding Tax', 'Late Deduction'];
                $isLegacySystemRow = is_null($deduction->deduction_source)
                    && (in_array($type, $legacySystemTypes, true) || str_starts_with($type, 'Undertime Deduction'));
                $isSystemRow = $deduction->deduction_source === 'system' || $isLegacySystemRow;

                if ($isSystemRow) {
                    if ($type === 'Late Deduction' || str_starts_with($type, 'Undertime Deduction')) {
                        $totals['wage_reductions'] += $amount;
                    }
                    continue;
                }

                $treatment = $deduction->accounting_treatment;
                if (!in_array($treatment, ['contra_expense', 'payable', 'employee_receivable'], true)) {
                    throw new \RuntimeException(
                        "Payroll #{$payroll->id} deduction \"{$type}\" needs an accounting treatment before a journal entry can be generated."
                    );
                }

                if ($this->isReservedSystemDeduction($type)) {
                    throw new \RuntimeException(
                        "Payroll #{$payroll->id} deduction \"{$type}\" must be recorded through its statutory or attendance payroll field, not as a manual deduction."
                    );
                }
                if (
                    $this->isEmployeeReceivableDeduction($type)
                    && $treatment !== 'employee_receivable'
                ) {
                    throw new \RuntimeException(
                        "Payroll #{$payroll->id} deduction \"{$type}\" must reduce Employee Receivable."
                    );
                }

                if ($treatment === 'contra_expense') {
                    $totals['wage_reductions'] += $amount;
                } elseif ($treatment === 'payable') {
                    $totals['manual_payable'] += $amount;
                } else {
                    $totals['manual_receivable'] += $amount;
                }
            }
        }

        if ($totals['gross'] <= 0 || $totals['net'] < 0) {
            throw new \RuntimeException('Payroll totals are invalid. Gross pay must be positive and net pay cannot be negative.');
        }

        $totals['employee_receivable'] += $totals['manual_receivable'];
        $employerContributions = $totals['employer_sss']
            + $totals['employer_philhealth']
            + $totals['employer_pagibig'];
        $actualDeductions = $totals['gross'] - $totals['net'];
        $classifiedDeductions = $totals['employee_sss']
            + $totals['employee_philhealth']
            + $totals['employee_pagibig']
            + $totals['withholding']
            + $totals['wage_reductions']
            + $totals['manual_payable']
            + $totals['employee_receivable'];

        if (abs($actualDeductions - $classifiedDeductions) > 1) {
            throw new \RuntimeException('Payroll totals do not reconcile with the classified deductions.');
        }

        $lines = [];
        $addLine = function (string $key, string $label, int $debit, int $credit) use (&$lines, $accountIds): void {
            if ($debit === 0 && $credit === 0) {
                return;
            }
            if (empty($accountIds[$key])) {
                throw new \RuntimeException("Account mapping not configured for: {$label} (key: {$key}). Please configure account mappings in Accounting Settings.");
            }
            $lines[] = [
                'account_id' => $accountIds[$key],
                'description' => $label,
                'debit' => (float) ($debit / 100),
                'credit' => (float) ($credit / 100),
            ];
        };

        $addLine('payroll_expense', 'Gross salaries and wages expense', $totals['gross'], 0);
        $addLine('employer_contributions_expense', 'Employer government contributions expense', $employerContributions, 0);
        $addLine('sss_payable', 'SSS employee contribution payable', 0, $totals['employee_sss']);
        $addLine('sss_payable', 'SSS employer contribution payable', 0, $totals['employer_sss']);
        $addLine('philhealth_payable', 'PhilHealth employee contribution payable', 0, $totals['employee_philhealth']);
        $addLine('philhealth_payable', 'PhilHealth employer contribution payable', 0, $totals['employer_philhealth']);
        $addLine('pagibig_payable', 'Pag-IBIG employee contribution payable', 0, $totals['employee_pagibig']);
        $addLine('pagibig_payable', 'Pag-IBIG employer contribution payable', 0, $totals['employer_pagibig']);
        $addLine('withholding_tax_payable', 'Employee withholding tax payable', 0, $totals['withholding']);
        $addLine('salaries_payable', 'Net payroll payable', 0, $totals['net']);
        $addLine('payroll_deductions', 'Valid wage reductions (contra-expense)', 0, $totals['wage_reductions']);
        $addLine('other_payroll_deductions_payable', 'Other employee deductions payable', 0, $totals['manual_payable']);
        $addLine('employee_receivable', 'Employee advances and loans recovered', 0, $totals['employee_receivable']);

        $debits = array_sum(array_column($lines, 'debit'));
        $credits = array_sum(array_column($lines, 'credit'));
        if (abs($debits - $credits) >= 0.01) {
            throw new \RuntimeException('Payroll journal entry is not balanced after deduction classification.');
        }

        return $lines;
    }

    private function populateLegacyEmployerContributions(Collection $payrolls): void
    {
        foreach ($payrolls as $payroll) {
            if (
                !is_null($payroll->employer_sss)
                && !is_null($payroll->employer_pagibig)
                && !is_null($payroll->employer_philhealth)
            ) {
                continue;
            }

            if (!$payroll->user) {
                throw new \RuntimeException("Payroll #{$payroll->id} has no employee record to calculate employer contributions.");
            }

            $dailyRate = (float) ($payroll->daily_rate_snapshot ?? 0);
            if ($dailyRate <= 0) {
                $dailyRate = (float) ($payroll->user->salary_rate ?? 0);
            }
            $monthlyEquivalent = (int) ($payroll->user->work_days_per_week ?? 5) === 6
                ? $dailyRate * 26.08
                : $dailyRate * 21.75;
            $shares = $this->statutoryContributionService->employerShares(
                $payroll->user,
                $monthlyEquivalent,
                (float) $payroll->gross_pay > 0
            );

            $payroll->setAttribute('employer_sss', $payroll->employer_sss ?? $shares['sss']);
            $payroll->setAttribute('employer_pagibig', $payroll->employer_pagibig ?? $shares['pagibig']);
            $payroll->setAttribute('employer_philhealth', $payroll->employer_philhealth ?? $shares['philhealth']);
        }
    }

    private function cents($amount): int
    {
        return (int) round((float) $amount * 100, 0, PHP_ROUND_HALF_UP);
    }

    private function isReservedSystemDeduction(string $type): bool
    {
        $normalized = strtolower(trim($type));

        return in_array($normalized, [
            'sss',
            'social security system',
            'philhealth',
            'phil health',
            'pag-ibig',
            'pagibig',
            'withholding tax',
            'bir withholding tax',
            'late deduction',
            'undertime deduction',
        ], true);
    }

    private function isEmployeeReceivableDeduction(string $type): bool
    {
        $normalized = strtolower(trim($type));

        return str_contains($normalized, 'loan')
            || str_contains($normalized, 'cash advance')
            || str_contains($normalized, 'cash-advance')
            || str_contains($normalized, 'salary advance');
    }

    private function aggregateLines(array $lines): array
    {
        $totals = [];
        foreach ($lines as $line) {
            $accountId = (int) $line['account_id'];
            $totals[$accountId] ??= ['debit' => 0, 'credit' => 0];
            $totals[$accountId]['debit'] += $this->cents($line['debit']);
            $totals[$accountId]['credit'] += $this->cents($line['credit']);
        }

        ksort($totals);
        return $totals;
    }

    public function batchHasJournalEntry(PayrollBatch $batch): bool
    {
        return JournalEntry::where('reference_type', $batch->isThirteenthMonth() ? '13th Month Pay' : 'Payroll')
            ->where('reference_id', $batch->id)
            ->where('status', '!=', 'Void')
            ->exists();
    }

    public function getJournalEntryForBatch(PayrollBatch $batch): ?JournalEntry
    {
        return JournalEntry::where('reference_type', $batch->isThirteenthMonth() ? '13th Month Pay' : 'Payroll')
            ->where('reference_id', $batch->id)
            ->where('status', '!=', 'Void')
            ->first();
    }

    public function generateJournalEntryFor13thMonth(PayrollBatch $batch): JournalEntry
    {
        if ($batch->status !== 'approved') {
            throw new \RuntimeException('Cannot generate journal entry for a batch that is not approved.');
        }

        if ($this->batchHasJournalEntry($batch)) {
            throw new \RuntimeException('A journal entry already exists for this 13th month pay batch.');
        }

        $batch->load('thirteenthMonthPays');

        if ($batch->thirteenthMonthPays->isEmpty()) {
            throw new \RuntimeException('No 13th month pay records found for this batch.');
        }

        $expenseAccountId = AccountMapping::getAccountIdForKey('thirteenth_month_expense');
        $payableAccountId = AccountMapping::getAccountIdForKey('thirteenth_month_payable');

        if (!$expenseAccountId) {
            throw new \RuntimeException('Account mapping not configured for: 13th Month Expense. Please configure in Accounting Settings.');
        }
        if (!$payableAccountId) {
            throw new \RuntimeException('Account mapping not configured for: 13th Month Payable. Please configure in Accounting Settings.');
        }

        $totalPayable = (float) $batch->thirteenthMonthPays->sum('thirteenth_month_pay');

        if ($totalPayable <= 0) {
            throw new \RuntimeException('Total 13th month pay is zero. Cannot generate journal entry.');
        }

        $year = $batch->period_start->format('Y');
        $description = "13th Month Pay – {$year}";

        DB::beginTransaction();
        try {
            $lockedBatch = PayrollBatch::whereKey($batch->getKey())->lockForUpdate()->firstOrFail();
            if ($lockedBatch->status !== 'approved') {
                throw new \RuntimeException('Cannot generate journal entry for a batch that is not approved.');
            }
            if ($this->batchHasJournalEntry($batch)) {
                throw new \RuntimeException('A journal entry already exists for this 13th month pay batch.');
            }

            $journalEntry = JournalEntry::create([
                'journal_number'   => JournalEntry::generateJournalNumber(),
                'transaction_date' => $batch->approved_at?->date ?? now()->toDateString(),
                'description'      => $description,
                'reference_type'   => '13th Month Pay',
                'reference_id'     => $batch->id,
                'status'           => 'Draft',
                'created_by'       => auth()->id(),
            ]);

            $journalEntry->lines()->create([
                'account_id'  => $expenseAccountId,
                'description' => "13th Month Pay expense – {$year}",
                'debit'       => $totalPayable,
                'credit'      => 0,
            ]);

            $journalEntry->lines()->create([
                'account_id'  => $payableAccountId,
                'description' => "13th Month Pay payable – {$year}",
                'debit'       => 0,
                'credit'      => $totalPayable,
            ]);

            AccountingAuditLog::log(
                'created_from_13th_month',
                'Journal Entry',
                $journalEntry->id,
                "Generated journal entry {$journalEntry->journal_number} from 13th month batch #{$batch->id}",
                null,
                ['journal_number' => $journalEntry->journal_number, 'batch_id' => $batch->id, 'total' => $totalPayable]
            );

            DB::commit();

            return $journalEntry;
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
