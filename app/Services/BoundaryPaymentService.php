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
        bool $confirmedReceived,
        int $userId
    ): array {
        return DB::transaction(function () use ($remittanceId, $paymentDate, $confirmedReceived, $userId) {
            $remittance = DailyRemittance::whereKey($remittanceId)->lockForUpdate()->firstOrFail();

            if ($remittance->status !== 'pending') {
                return [$remittance, false, 0.0];
            }

            $paymentAmount = min(
                max(round((float) $remittance->net_remittance, 2), 0),
                max(round((float) $remittance->boundary, 2), 0)
            );

            if ($paymentAmount > 0 && (! $confirmedReceived || ! $paymentDate)) {
                throw new BoundaryPaymentException('Confirm that the boundary amount was received and provide its payment date before approval.');
            }

            $remittance->update(['status' => 'approved']);

            if ($paymentAmount > 0) {
                $this->createPostedPayment(
                    $remittance,
                    $paymentDate,
                    $paymentAmount,
                    null,
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

            return [$remittance->fresh(), true, $paymentAmount];
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
            $this->assertPaymentAmountDoesNotExceedPartnerShare($remittance, $paidBy, $paymentAmount);

            $payment = $this->createPostedPayment(
                $remittance,
                $paymentDate,
                $paymentAmount,
                $paidBy,
                $sourcePaymentKey,
                $userId
            );

            $this->updateShortageBalance(
                $remittance,
                max(round($remaining - $paymentAmount, 2), 0),
                $paidBy,
                $paymentAmount
            );

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

    private function assertPaymentAmountDoesNotExceedPartnerShare(
        DailyRemittance $remittance,
        string $paidBy,
        float $amount
    ): void {
        if (! in_array($paidBy, ['driver', 'pao'], true)) {
            throw new BoundaryPaymentException('Shortage installments must be assigned to the driver or PAO share.');
        }

        $share = (float) ($paidBy === 'driver' ? $remittance->driver_share : $remittance->pao_share);
        $paid = (float) ($paidBy === 'driver' ? $remittance->driver_amount_paid : $remittance->pao_amount_paid);
        $remainingShare = max(round($share - $paid, 2), 0);

        if (round($amount, 2) > $remainingShare) {
            throw new BoundaryPaymentException(
                ucfirst($paidBy).' payment cannot exceed their remaining shortage share of ₱'.number_format($remainingShare, 2).'.'
            );
        }
    }

    private function createPostedPayment(
        DailyRemittance $remittance,
        string $paymentDate,
        float $amount,
        ?string $paidBy,
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
        $remittanceDetails = collect([
            'Driver '.($remittance->driver?->name ?? 'Unknown'),
            'PAO '.($remittance->pao?->name ?? 'Unknown'),
            $remittance->vehicle?->plate_number,
            $remittance->route?->route_name,
        ])->filter()->implode(' / ');
        $paymentOrigin = $paidBy
            ? ucfirst($paidBy).' '.($paidBy === 'driver' ? ($remittance->driver?->name ?? 'Unknown') : ($remittance->pao?->name ?? 'Unknown'))
            : 'driver and PAO partners';
        $description = sprintf(
            'Boundary payment BP-%06d for remittance #%d from %s (%s)',
            $payment->id,
            $remittance->id,
            $paymentOrigin,
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
                'description' => "Boundary payment BP-{$payment->id} received from {$paymentOrigin}",
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
                'shortage_share' => $paidBy,
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

    private function updateShortageBalance(
        DailyRemittance $remittance,
        float $remaining,
        ?string $paidBy = null,
        float $paymentAmount = 0
    ): void {
        $remaining = max(round($remaining, 2), 0);
        $updates = [
            'short_amount' => $remaining,
            'is_short_remittance' => (bool) $remittance->is_short_remittance || $remaining > 0,
        ];

        if ($paidBy === 'driver') {
            $driverPaid = round((float) $remittance->driver_amount_paid + $paymentAmount, 2);
            $updates['driver_amount_paid'] = $driverPaid;
            $updates['driver_status'] = $driverPaid >= (float) $remittance->driver_share ? 'paid' : 'partial';
        } elseif ($paidBy === 'pao') {
            $paoPaid = round((float) $remittance->pao_amount_paid + $paymentAmount, 2);
            $updates['pao_amount_paid'] = $paoPaid;
            $updates['pao_status'] = $paoPaid >= (float) $remittance->pao_share ? 'paid' : 'partial';
        }

        if ($remaining === 0.0) {
            $updates['driver_status'] = 'paid';
            $updates['pao_status'] = 'paid';
        }

        $remittance->update($updates);
    }
}
