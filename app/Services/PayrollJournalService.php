<?php

namespace App\Services;

use App\Models\PayrollBatch;
use App\Models\Payroll;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\AccountMapping;
use App\Models\AccountingAuditLog;
use Illuminate\Support\Facades\DB;

class PayrollJournalService
{
    public function generateJournalEntryForBatch(PayrollBatch $batch): JournalEntry
    {
        if ($batch->status !== 'approved') {
            throw new \RuntimeException('Cannot generate journal entry for a batch that is not approved.');
        }

        if ($this->batchHasJournalEntry($batch)) {
            throw new \RuntimeException('A journal entry already exists for this payroll batch.');
        }

        $batch->load('payrolls');

        if ($batch->payrolls->isEmpty()) {
            throw new \RuntimeException('No payroll records found for this batch.');
        }

        $mappingKeys = [
            'payroll_expense'      => 'Salaries and Wages Expense',
            'sss_payable'          => 'SSS Payable',
            'philhealth_payable'   => 'PhilHealth Payable',
            'pagibig_payable'      => 'Pag-IBIG Payable',
            'withholding_tax_payable' => 'Withholding Tax Payable',
            'salaries_payable'     => 'Salaries Payable',
        ];

        $accountIds = [];
        foreach ($mappingKeys as $key => $label) {
            $accountId = AccountMapping::getAccountIdForKey($key);
            if (!$accountId) {
                throw new \RuntimeException("Account mapping not configured for: {$label} (key: {$key}). Please configure account mappings in Accounting Settings.");
            }
            $accountIds[$key] = $accountId;
        }

        $totalGrossPay     = (float) $batch->payrolls->sum('gross_pay');
        $totalSSS          = (float) $batch->payrolls->sum('sss');
        $totalPhilHealth   = (float) $batch->payrolls->sum('philhealth');
        $totalPagIBIG      = (float) $batch->payrolls->sum('pagibig');
        $totalWithholding  = (float) $batch->payrolls->sum('withholding_tax');
        $totalNetPay       = (float) $batch->payrolls->sum('net_pay');

        if ($totalGrossPay <= 0) {
            throw new \RuntimeException('Total gross pay is zero. Cannot generate journal entry.');
        }

        $periodStart = $batch->period_start->format('M d, Y');
        $periodEnd   = $batch->period_end->format('M d, Y');
        $description = "Payroll – {$periodStart} to {$periodEnd}";

        DB::beginTransaction();
        try {
            $journalEntry = JournalEntry::create([
                'journal_number'   => JournalEntry::generateJournalNumber(),
                'transaction_date' => $batch->approved_at?->date ?? now()->toDateString(),
                'description'      => $description,
                'reference_type'   => 'Payroll',
                'reference_id'     => $batch->id,
                'status'           => 'Draft',
                'created_by'       => auth()->id(),
            ]);

            $lines = [
                [
                    'account_id'  => $accountIds['payroll_expense'],
                    'description' => "Gross payroll – {$periodStart} to {$periodEnd}",
                    'debit'       => $totalGrossPay,
                    'credit'      => 0,
                ],
            ];

            $creditLines = [
                'sss_payable'          => ['amount' => $totalSSS, 'label' => 'SSS employee contribution'],
                'philhealth_payable'   => ['amount' => $totalPhilHealth, 'label' => 'PhilHealth employee contribution'],
                'pagibig_payable'      => ['amount' => $totalPagIBIG, 'label' => 'Pag-IBIG employee contribution'],
                'withholding_tax_payable' => ['amount' => $totalWithholding, 'label' => 'Withholding tax withheld'],
                'salaries_payable'     => ['amount' => $totalNetPay, 'label' => 'Net payroll payable'],
            ];

            foreach ($creditLines as $key => $data) {
                if ($data['amount'] > 0) {
                    $lines[] = [
                        'account_id'  => $accountIds[$key],
                        'description' => $data['label'],
                        'debit'       => 0,
                        'credit'      => $data['amount'],
                    ];
                }
            }

            foreach ($lines as $line) {
                $journalEntry->lines()->create($line);
            }

            AccountingAuditLog::log(
                'created_from_payroll',
                'Journal Entry',
                $journalEntry->id,
                "Generated journal entry {$journalEntry->journal_number} from payroll batch #{$batch->id}",
                null,
                [
                    'journal_number' => $journalEntry->journal_number,
                    'batch_id'       => $batch->id,
                    'total_debit'    => $totalGrossPay,
                    'total_credit'   => $totalNetPay + $totalSSS + $totalPhilHealth + $totalPagIBIG + $totalWithholding,
                ]
            );

            DB::commit();

            return $journalEntry;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function batchHasJournalEntry(PayrollBatch $batch): bool
    {
        return JournalEntry::where('reference_type', 'Payroll')
            ->where('reference_id', $batch->id)
            ->where('status', '!=', 'Void')
            ->exists();
    }

    public function getJournalEntryForBatch(PayrollBatch $batch): ?JournalEntry
    {
        return JournalEntry::where('reference_type', 'Payroll')
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
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
