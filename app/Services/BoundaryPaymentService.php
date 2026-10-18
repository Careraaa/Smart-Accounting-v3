<?php

namespace App\Services;

use App\Exceptions\BoundaryPaymentException;
use App\Models\AccountingAuditLog;
use App\Models\AccountMapping;
use App\Models\BoundaryPayment;
use App\Models\ChartOfAccount;
use App\Models\DailyRemittance;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;

class BoundaryPaymentService
{
    public function approveRemittance(
        int $remittanceId,
        ?string $paymentDate,
        float $paymentAmount,
        string $paidBy,
        int $userId
    ): array {
        return DB::transaction(function () use ($remittanceId, $paymentDate, $paymentAmount, $paidBy, $userId) {
            $remittance = DailyRemittance::whereKey($remittanceId)->lockForUpdate()->firstOrFail();

            if ($remittance->status !== 'pending') {
                return [$remittance, false];
            }

            if ($paymentAmount > 0) {
                $this->assertPaymentAmountDoesNotExceedBalance($paymentAmount, (float) $remittance->boundary);
            }

            $remittance->update(['status' => 'approved']);

            if ($paymentAmount > 0) {
                $this->createPostedPayment(
                    $remittance,
                    $paymentDate,
                    $paymentAmount,
                    $paidBy,
                    "daily-remittance-{$remittance->id}-initial",
                    $userId
                );
            }

            $this->updateShortageBalance(
                $remittance,
                max(round((float) $remittance->boundary - $paymentAmount, 2), 0)
            );

            AccountingAuditLog::log(
                'approved',
                'Daily Remittance',
                $remittance->id,
                "Approved daily remittance #{$remittance->id}",
                ['status' => 'pending'],
                ['status' => 'approved', 'boundary_payment_amount' => $paymentAmount]
            );

            return [$remittance->fresh(), true];
        });
    }

    public function recordPayment(
        int $remittanceId,
        string $paymentDate,
        float $paymentAmount,
        string $paidBy,
        string $sourcePaymentKey,
        int $userId
    ): BoundaryPayment {
        return DB::transaction(function () use (
            $remittanceId,
            $paymentDate,
            $paymentAmount,
            $paidBy,
            $sourcePaymentKey,
            $userId
        ) {
            $remittance = DailyRemittance::whereKey($remittanceId)->lockForUpdate()->firstOrFail();

            $existingPayment = BoundaryPayment::where('source_payment_key', $sourcePaymentKey)->first();
            if ($existingPayment) {
                if ((int) $existingPayment->daily_remittance_id !== $remittanceId) {
                    throw new BoundaryPaymentException('This payment submission key has already been used for another remittance.');
                }

                return $existingPayment;
            }

            if ($remittance->status !== 'approved') {
                throw new BoundaryPaymentException('Only approved remittances can record boundary payments.');
            }

            $remaining = $this->remainingBoundaryBalance($remittance);
            $this->assertPaymentAmountDoesNotExceedBalance($paymentAmount, $remaining);

            $payment = $this->createPostedPayment(
                $remittance,
                $paymentDate,
                $paymentAmount,
                $paidBy,
                $sourcePaymentKey,
                $userId
            );

            $this->updateShortageBalance($remittance, max(round($remaining - $paymentAmount, 2), 0));

            return $payment;
        });
    }

    private function assertPaymentAmountDoesNotExceedBalance(float $amount, float $remaining): void
    {
        if ($amount <= 0 || round($amount, 2) > $remaining) {
            throw new BoundaryPaymentException(
                'Payment must be greater than zero and cannot exceed the remaining boundary of ₱'.number_format($remaining, 2).'.'
            );
        }
    }

    private function createPostedPayment(
        DailyRemittance $remittance,
        string $paymentDate,
        float $amount,
        string $paidBy,
        string $sourcePaymentKey,
        int $userId
    ): BoundaryPayment {
        $cashAccountId = $this->mappedActiveAccountId('boundary_cash', 'Asset');
        $revenueAccountId = $this->mappedActiveAccountId('boundary_revenue', 'Revenue');
        $initialPaymentRemittanceId = $remittance->boundaryPayments()->exists()
            ? null
            : $remittance->id;

        $payment = BoundaryPayment::create([
            'daily_remittance_id' => $remittance->id,
            'source_payment_key' => $sourcePaymentKey,
            'initial_payment_remittance_id' => $initialPaymentRemittanceId,
            'payment_date' => $paymentDate,
            'amount' => $amount,
            'paid_by' => $paidBy,
            'payment_kind' => $initialPaymentRemittanceId ? 'initial' : 'installment',
            'status' => 'Posted',
            'recorded_by' => $userId,
            'approved_by' => $userId,
        ]);

        $remittance->loadMissing(['driver', 'pao', 'vehicle', 'route']);
        $payer = match ($paidBy) {
            'driver' => 'Driver '.($remittance->driver?->name ?? 'Unknown'),
            'pao' => 'PAO '.($remittance->pao?->name ?? 'Unknown'),
            default => 'Driver and PAO jointly',
        };
        $remittanceDetails = collect([
            'Driver '.($remittance->driver?->name ?? 'Unknown'),
            'PAO '.($remittance->pao?->name ?? 'Unknown'),
            $remittance->vehicle?->plate_number,
            $remittance->route?->route_name,
        ])->filter()->implode(' / ');
        $description = sprintf(
            'Boundary payment BP-%06d for remittance #%d paid by %s (%s)',
            $payment->id,
            $remittance->id,
            $payer,
            $remittanceDetails
        );

        $journalEntry = JournalEntry::create([
            'journal_number' => JournalEntry::generateJournalNumber(),
            'transaction_date' => $paymentDate,
            'description' => $description,
            'reference_type' => 'Boundary Payment',
            'reference_id' => $payment->id,
            'status' => 'Draft',
            'created_by' => $userId,
        ]);

        $journalEntry->lines()->createMany([
            [
                'account_id' => $cashAccountId,
                'description' => "Boundary payment BP-{$payment->id} received from {$payer}",
                'debit' => $amount,
                'credit' => 0,
            ],
            [
                'account_id' => $revenueAccountId,
                'description' => "Boundary revenue for remittance #{$remittance->id}",
                'debit' => 0,
                'credit' => $amount,
            ],
        ]);

        $lines = $journalEntry->lines()->get();
        $totalDebitCents = (int) $lines->sum(fn ($line) => (int) round((float) $line->debit * 100));
        $totalCreditCents = (int) $lines->sum(fn ($line) => (int) round((float) $line->credit * 100));
        if ($lines->count() < 2 || $totalDebitCents !== $totalCreditCents) {
            throw new BoundaryPaymentException('Boundary payment journal entry is not balanced; the payment was not posted.');
        }

        $journalEntry->update([
            'status' => 'Posted',
            'posted_by' => $userId,
            'posted_at' => now(),
        ]);
        $payment->update(['journal_entry_id' => $journalEntry->id]);

        AccountingAuditLog::log(
            'created',
            'Journal Entry',
            $journalEntry->id,
            "Created journal entry {$journalEntry->journal_number} from boundary payment BP-{$payment->id}",
            null,
            ['reference_type' => 'Boundary Payment', 'reference_id' => $payment->id]
        );

        AccountingAuditLog::log(
            'posted',
            'Boundary Payment',
            $payment->id,
            "Posted boundary payment BP-{$payment->id} for remittance #{$remittance->id}",
            null,
            [
                'daily_remittance_id' => $remittance->id,
                'amount' => number_format($amount, 2, '.', ''),
                'payment_date' => $paymentDate,
                'paid_by' => $paidBy,
                'journal_entry_id' => $journalEntry->id,
            ]
        );

        AccountingAuditLog::log(
            'posted',
            'Journal Entry',
            $journalEntry->id,
            "Posted journal entry {$journalEntry->journal_number} from boundary payment BP-{$payment->id}",
            ['status' => 'Draft'],
            ['status' => 'Posted', 'boundary_payment_id' => $payment->id]
        );

        return $payment;
    }

    private function mappedActiveAccountId(string $mappingKey, string $expectedType): int
    {
        $mapping = AccountMapping::where('mapping_key', $mappingKey)->lockForUpdate()->first();
        $account = $mapping
            ? ChartOfAccount::whereKey($mapping->account_id)->lockForUpdate()->first()
            : null;

        if (! $account || ! $account->is_active || $account->account_type !== $expectedType) {
            throw new BoundaryPaymentException(
                "Configure an active {$expectedType} account for the {$mappingKey} mapping before recording boundary payments."
            );
        }

        return (int) $account->id;
    }

    private function remainingBoundaryBalance(DailyRemittance $remittance): float
    {
        if ($remittance->short_amount !== null) {
            return max(round((float) $remittance->short_amount, 2), 0);
        }

        if ($remittance->is_short_remittance) {
            return max(round((float) $remittance->boundary, 2), 0);
        }

        return max(round((float) $remittance->boundary - (float) $remittance->net_remittance, 2), 0);
    }

    private function updateShortageBalance(DailyRemittance $remittance, float $remaining): void
    {
        $remaining = max(round($remaining, 2), 0);
        $updates = [
            'short_amount' => $remaining,
            'is_short_remittance' => (bool) $remittance->is_short_remittance || $remaining > 0,
        ];

        if ($remaining === 0.0) {
            $updates['driver_status'] = 'paid';
            $updates['pao_status'] = 'paid';
        }

        $remittance->update($updates);
    }
}
