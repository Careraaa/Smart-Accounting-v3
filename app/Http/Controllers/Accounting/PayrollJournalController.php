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
}
