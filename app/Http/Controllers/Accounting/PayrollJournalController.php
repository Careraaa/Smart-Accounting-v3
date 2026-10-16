<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\PayrollBatch;
use App\Services\PayrollJournalService;
use App\Models\AccountingAuditLog;

class PayrollJournalController extends Controller
{
    protected $journalService;

    public function __construct(PayrollJournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function generate(PayrollBatch $batch)
    {
        if ($batch->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved payroll batches can have journal entries generated.');
        }

        try {
            if ($batch->isThirteenthMonth()) {
                $journalEntry = $this->journalService->generateJournalEntryFor13thMonth($batch);
            } else {
                $journalEntry = $this->journalService->generateJournalEntryForBatch($batch);
            }

            return redirect()->route('accounting.journal-entries.show', $journalEntry)
                ->with('success', "Journal entry {$journalEntry->journal_number} generated successfully from payroll batch.");
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function refresh(PayrollBatch $batch)
    {
        if ($batch->status !== 'approved' || $batch->isThirteenthMonth()) {
            return redirect()->back()->with('error', 'Only approved regular payroll batches can have their journal entries refreshed.');
        }

        $journalEntry = $this->journalService->getJournalEntryForBatch($batch);
        if (!$journalEntry || $journalEntry->status !== 'Draft') {
            return redirect()->back()->with('error', 'Only an existing draft journal entry can be refreshed.');
        }

        try {
            $journalEntry = $this->journalService->generateJournalEntryForBatch($batch, $journalEntry);

            return redirect()->route('accounting.journal-entries.show', $journalEntry)
                ->with('success', "Draft journal entry {$journalEntry->journal_number} refreshed from the current payroll totals.");
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
