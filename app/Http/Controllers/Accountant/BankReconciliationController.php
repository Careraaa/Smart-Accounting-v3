<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankReconciliation;
use App\Models\CashReceipt;
use Illuminate\Http\Request;

class BankReconciliationController extends Controller
{
    /**
     * Display list of reconciliations
     */
    public function index(Request $request)
    {
        $query = BankReconciliation::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        $reconciliations = $query->orderBy('statement_date', 'desc')
            ->paginate(20);

        $bankAccounts = BankAccount::active()->get();

        return view('accountant.reconciliation.index', compact('reconciliations', 'bankAccounts'));
    }

    /**
     * Create new bank reconciliation
     */
    public function create()
    {
        $bankAccounts = BankAccount::active()->get();

        return view('accountant.reconciliation.create', compact('bankAccounts'));
    }

    /**
     * Store bank reconciliation data
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'statement_date' => 'required|date',
            'bank_balance' => 'required|numeric',
            'total_deposits_in_transit' => 'nullable|numeric|min:0',
            'total_outstanding_checks' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $bankAccount = BankAccount::find($validated['bank_account_id']);
        $bookBalance = $bankAccount->current_balance;

        $reconciliation = BankReconciliation::create([
            'bank_account_id' => $validated['bank_account_id'],
            'statement_date' => $validated['statement_date'],
            'bank_balance' => $validated['bank_balance'],
            'book_balance' => $bookBalance,
            'total_deposits_in_transit' => $validated['total_deposits_in_transit'] ?? 0,
            'total_outstanding_checks' => $validated['total_outstanding_checks'] ?? 0,
            'notes' => $validated['notes'],
            'created_by' => auth()->id(),
            'status' => 'In Progress',
        ]);

        $reconciliation->calculateReconciliation();
        $reconciliation->save();

        return redirect()->route('accountant.reconciliation.show', $reconciliation)
            ->with('success', 'Bank reconciliation created');
    }

    /**
     * Show bank reconciliation detail
     */
    public function show(BankReconciliation $reconciliation)
    {
        $reconciliation->load(['bankAccount', 'createdBy', 'verifiedBy']);

        $reconciled = $reconciliation->bank_balance 
            + $reconciliation->total_deposits_in_transit 
            - $reconciliation->total_outstanding_checks;

        $isBalanced = abs($reconciliation->book_balance - $reconciled) < 0.01;

        return view('accountant.reconciliation.show', compact('reconciliation', 'reconciled', 'isBalanced'));
    }

    /**
     * Complete reconciliation
     */
    public function complete(Request $request, BankReconciliation $reconciliation)
    {
        $reconciliation->update(['status' => 'Completed']);

        return redirect()->back()->with('success', 'Bank reconciliation completed');
    }

    /**
     * Verify reconciliation (supervisor)
     */
    public function verify(Request $request, BankReconciliation $reconciliation)
    {
        if (!$reconciliation->isBalanced()) {
            return redirect()->back()->with('error', 'Reconciliation is not balanced');
        }

        $reconciliation->update([
            'status' => 'Verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Bank reconciliation verified');
    }
}
