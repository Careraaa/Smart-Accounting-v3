<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\GLAccount;
use Illuminate\Http\Request;
use \Carbon\Carbon;

class JournalEntryController extends Controller
{
    /**
     * Display list of journal entries
     */
    public function index(Request $request)
    {
        $query = JournalEntry::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('je_type', $request->type);
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->where('je_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('je_date', '<=', $request->to_date);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('je_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        $entries = $query->orderBy('je_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('accountant.journal-entries.index', compact('entries'));
    }

    /**
     * Show journal entry detail
     */
    public function show(JournalEntry $entry)
    {
        $entry->load(['journalEntryLines.glAccount', 'createdBy', 'approvedBy', 'postedBy']);

        return view('accountant.journal-entries.show', compact('entry'));
    }

    /**
     * Create new manual journal entry
     */
    public function create()
    {
        $glAccounts = GLAccount::active()
            ->orderBy('account_code')
            ->get();

        return view('accountant.journal-entries.create', compact('glAccounts'));
    }

    /**
     * Store new journal entry  
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'je_date' => 'required|date',
            'description' => 'required|string',
            'lines' => 'required|array|min:2',
            'lines.*.gl_account_id' => 'required|exists:gl_accounts,id',
            'lines.*.debit_amount' => 'nullable|numeric|min:0',
            'lines.*.credit_amount' => 'nullable|numeric|min:0',
            'lines.*.line_description' => 'nullable|string',
        ]);

        // Generate JE number
        $jeNumber = 'JE-' . Carbon::now()->format('YmdHis');

        // Calculate totals
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($validated['lines'] as $line) {
            $totalDebit += $line['debit_amount'] ?? 0;
            $totalCredit += $line['credit_amount'] ?? 0;
        }

        $isBalanced = abs($totalDebit - $totalCredit) < 0.01;

        // Create journal entry
        $entry = JournalEntry::create([
            'je_number' => $jeNumber,
            'je_date' => $validated['je_date'],
            'description' => $validated['description'],
            'status' => 'Draft',
            'je_type' => 'Manual Entry',
            'created_by' => auth()->id(),
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'is_balanced' => $isBalanced,
        ]);

        // Create journal entry lines
        foreach ($validated['lines'] as $index => $line) {
            if (($line['debit_amount'] ?? 0) > 0 || ($line['credit_amount'] ?? 0) > 0) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'gl_account_id' => $line['gl_account_id'],
                    'debit_amount' => $line['debit_amount'] ?? 0,
                    'credit_amount' => $line['credit_amount'] ?? 0,
                    'line_description' => $line['line_description'] ?? null,
                    'line_number' => $index + 1,
                ]);
            }
        }

        $entry->update(['status' => $isBalanced ? 'Pending Approval' : 'Draft']);

        return redirect()->route('accountant.journal-entries.show', $entry)
            ->with('success', 'Journal Entry created successfully');
    }

    /**
     * Submit journal entry for approval
     */
    public function submitForApproval(JournalEntry $entry)
    {
        if ($entry->status !== 'Draft') {
            return redirect()->back()->with('error', 'Only draft entries can be submitted');
        }

        if (!$entry->isBalanced()) {
            return redirect()->back()->with('error', 'Entry must be balanced before submission');
        }

        $entry->update(['status' => 'Pending Approval']);

        return redirect()->back()->with('success', 'Journal Entry submitted for approval');
    }

    /**
     * Approve journal entry (supervisor)
     */
    public function approve(Request $request, JournalEntry $entry)
    {
        if ($entry->status !== 'Pending Approval') {
            return redirect()->back()->with('error', 'Only pending entries can be approved');
        }

        $entry->approve(auth()->user());

        return redirect()->back()->with('success', 'Journal Entry approved');
    }

    /**
     * Post journal entry to GL
     */
    public function post(JournalEntry $entry)
    {
        if ($entry->status !== 'Approved') {
            return redirect()->back()->with('error', 'Only approved entries can be posted');
        }

        $entry->post(auth()->user());

        return redirect()->back()->with('success', 'Journal Entry posted to GL');
    }

    /**
     * Reject journal entry
     */
    public function reject(Request $request, JournalEntry $entry)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $entry->reject($validated['rejection_reason'], auth()->user());

        return redirect()->back()->with('success', 'Journal Entry rejected');
    }

    /**
     * Create reversal entry
     */
    public function reversal(JournalEntry $entry)
    {
        if ($entry->status !== 'Posted') {
            return redirect()->back()->with('error', 'Only posted entries can be reversed');
        }

        $reversal = $entry->createReversal(auth()->user());
        $reversal->approve(auth()->user());
        $reversal->post(auth()->user());

        return redirect()->route('accountant.journal-entries.show', $reversal)
            ->with('success', 'Reversal entry created and posted');
    }
}
