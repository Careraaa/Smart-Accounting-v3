<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\ChartOfAccount;
use App\Models\AccountingAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $journalEntries = JournalEntry::with(['createdBy', 'postedBy'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();

        $stats = [
            'total'  => $journalEntries->count(),
            'draft'  => $journalEntries->where('status', 'Draft')->count(),
            'posted' => $journalEntries->where('status', 'Posted')->count(),
            'void'   => $journalEntries->where('status', 'Void')->count(),
        ];

        return view('accounting.journal-entries.index', compact('journalEntries', 'stats'));
    }

    public function create()
    {
        $accounts = ChartOfAccount::active()->orderBy('account_code')->get();
        return view('accounting.journal-entries.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'description'      => 'required|string|max:255',
            'reference_type'   => 'nullable|string|max:50',
            'reference_id'     => 'nullable|integer',
            'lines'            => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.description' => 'nullable|string|max:255',
            'lines.*.debit'    => 'required|numeric|min:0',
            'lines.*.credit'   => 'required|numeric|min:0',
        ]);

        $lines = $validated['lines'];

        foreach ($lines as $i => $line) {
            if ($line['debit'] == 0 && $line['credit'] == 0) {
                return redirect()->back()->withInput()
                    ->with('error', "Line " . ($i + 1) . ": Must have either debit or credit amount.");
            }
            if ($line['debit'] > 0 && $line['credit'] > 0) {
                return redirect()->back()->withInput()
                    ->with('error', "Line " . ($i + 1) . ": Cannot have both debit and credit amounts.");
            }
        }

        $totalDebit = array_sum(array_column($lines, 'debit'));
        $totalCredit = array_sum(array_column($lines, 'credit'));

        if (abs($totalDebit - $totalCredit) >= 0.01) {
            return redirect()->back()->withInput()
                ->with('error', "Journal entry is not balanced. Debits (₱" . number_format($totalDebit, 2) . ") must equal Credits (₱" . number_format($totalCredit, 2) . ").");
        }

        DB::beginTransaction();
        try {
            $journalEntry = JournalEntry::create([
                'journal_number'    => JournalEntry::generateJournalNumber(),
                'transaction_date'  => $validated['transaction_date'],
                'description'       => $validated['description'],
                'reference_type'    => $validated['reference_type'] ?? null,
                'reference_id'      => $validated['reference_id'] ?? null,
                'status'            => 'Draft',
                'created_by'        => auth()->id(),
            ]);

            foreach ($lines as $line) {
                $journalEntry->lines()->create([
                    'account_id'  => $line['account_id'],
                    'description' => $line['description'] ?? null,
                    'debit'       => $line['debit'],
                    'credit'      => $line['credit'],
                ]);
            }

            AccountingAuditLog::log(
                'created',
                'Journal Entry',
                $journalEntry->id,
                "Created journal entry {$journalEntry->journal_number}: {$journalEntry->description}",
                null,
                ['journal_number' => $journalEntry->journal_number, 'total' => $totalDebit]
            );

            DB::commit();

            return redirect()->route('accounting.journal-entries.show', $journalEntry)
                ->with('success', "Journal entry {$journalEntry->journal_number} created successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Failed to create journal entry: ' . $e->getMessage());
        }
    }

    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load(['lines.account', 'createdBy', 'postedBy']);

        return view('accounting.journal-entries.show', compact('journalEntry'));
    }

    public function edit(JournalEntry $journalEntry)
    {
        if (!$journalEntry->isEditable()) {
            return redirect()->route('accounting.journal-entries.show', $journalEntry)
                ->with('error', 'Only draft journal entries can be edited.');
        }

        $journalEntry->load('lines.account');
        $accounts = ChartOfAccount::active()->orderBy('account_code')->get();

        return view('accounting.journal-entries.edit', compact('journalEntry', 'accounts'));
    }

    public function update(Request $request, JournalEntry $journalEntry)
    {
        if (!$journalEntry->isEditable()) {
            return redirect()->route('accounting.journal-entries.show', $journalEntry)
                ->with('error', 'Only draft journal entries can be updated.');
        }

        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'description'      => 'required|string|max:255',
            'reference_type'   => 'nullable|string|max:50',
            'reference_id'     => 'nullable|integer',
            'lines'            => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:chart_of_accounts,id',
            'lines.*.description' => 'nullable|string|max:255',
            'lines.*.debit'    => 'required|numeric|min:0',
            'lines.*.credit'   => 'required|numeric|min:0',
        ]);

        $lines = $validated['lines'];

        foreach ($lines as $i => $line) {
            if ($line['debit'] == 0 && $line['credit'] == 0) {
                return redirect()->back()->withInput()
                    ->with('error', "Line " . ($i + 1) . ": Must have either debit or credit amount.");
            }
            if ($line['debit'] > 0 && $line['credit'] > 0) {
                return redirect()->back()->withInput()
                    ->with('error', "Line " . ($i + 1) . ": Cannot have both debit and credit amounts.");
            }
        }

        $totalDebit = array_sum(array_column($lines, 'debit'));
        $totalCredit = array_sum(array_column($lines, 'credit'));

        if (abs($totalDebit - $totalCredit) >= 0.01) {
            return redirect()->back()->withInput()
                ->with('error', "Journal entry is not balanced. Debits (₱" . number_format($totalDebit, 2) . ") must equal Credits (₱" . number_format($totalCredit, 2) . ").");
        }

        DB::beginTransaction();
        try {
            $oldValues = $journalEntry->toArray();

            $journalEntry->update([
                'transaction_date' => $validated['transaction_date'],
                'description'      => $validated['description'],
                'reference_type'   => $validated['reference_type'] ?? null,
                'reference_id'     => $validated['reference_id'] ?? null,
            ]);

            $journalEntry->lines()->delete();

            foreach ($lines as $line) {
                $journalEntry->lines()->create([
                    'account_id'  => $line['account_id'],
                    'description' => $line['description'] ?? null,
                    'debit'       => $line['debit'],
                    'credit'      => $line['credit'],
                ]);
            }

            AccountingAuditLog::log(
                'updated',
                'Journal Entry',
                $journalEntry->id,
                "Updated journal entry {$journalEntry->journal_number}",
                $oldValues,
                $journalEntry->toArray()
            );

            DB::commit();

            return redirect()->route('accounting.journal-entries.show', $journalEntry)
                ->with('success', "Journal entry {$journalEntry->journal_number} updated successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Failed to update journal entry: ' . $e->getMessage());
        }
    }

    public function post(JournalEntry $journalEntry)
    {
        if ($journalEntry->status !== 'Draft') {
            return redirect()->back()->with('error', 'Only draft journal entries can be posted.');
        }

        $journalEntry->load('lines');

        if ($journalEntry->lines->count() < 2) {
            return redirect()->back()->with('error', 'A journal entry must have at least two lines.');
        }

        $totalDebit = $journalEntry->lines->sum('debit');
        $totalCredit = $journalEntry->lines->sum('credit');

        if (abs($totalDebit - $totalCredit) >= 0.01) {
            return redirect()->back()->with('error', "Cannot post unbalanced entry. Debits (₱" . number_format($totalDebit, 2) . ") must equal Credits (₱" . number_format($totalCredit, 2) . ").");
        }

        $journalEntry->update([
            'status'    => 'Posted',
            'posted_by' => auth()->id(),
            'posted_at' => now(),
        ]);

        AccountingAuditLog::log(
            'posted',
            'Journal Entry',
            $journalEntry->id,
            "Posted journal entry {$journalEntry->journal_number}",
            ['status' => 'Draft'],
            ['status' => 'Posted']
        );

        return redirect()->route('accounting.journal-entries.show', $journalEntry)
            ->with('success', "Journal entry {$journalEntry->journal_number} posted successfully.");
    }

    public function void(JournalEntry $journalEntry)
    {
        if ($journalEntry->status !== 'Posted') {
            return redirect()->back()->with('error', 'Only posted journal entries can be voided.');
        }

        $journalEntry->update(['status' => 'Void']);

        AccountingAuditLog::log(
            'voided',
            'Journal Entry',
            $journalEntry->id,
            "Voided journal entry {$journalEntry->journal_number}",
            ['status' => 'Posted'],
            ['status' => 'Void']
        );

        return redirect()->route('accounting.journal-entries.show', $journalEntry)
            ->with('success', "Journal entry {$journalEntry->journal_number} has been voided.");
    }

    public function getLines(Request $request)
    {
        $account = ChartOfAccount::find($request->input('account_id'));
        if (!$account) {
            return response()->json(['error' => 'Account not found'], 404);
        }

        return response()->json([
            'account_code' => $account->account_code,
            'account_name' => $account->account_name,
        ]);
    }
}
