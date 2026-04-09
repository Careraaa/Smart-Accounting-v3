<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankReconciliation;
use App\Models\CashReceipt;
use Illuminate\Http\Request;

class CashManagementController extends Controller
{
    /**
     * Display cash position dashboard
     */
    public function dashboard()
    {
        $bankAccounts = BankAccount::active()->get();
        
        $cashOnHand = $bankAccounts->sum('current_balance');
        $totalBankAccounts = $bankAccounts->count();
        $undepositedFunds = CashReceipt::undeposited()->sum('amount');
        $pendingReceipts = CashReceipt::undeposited()->count();
        $recentReceipts = CashReceipt::latest()
            ->take(10)
            ->get();

        return view('accountant.cash.dashboard', compact(
            'bankAccounts',
            'cashOnHand',
            'totalBankAccounts',
            'undepositedFunds',
            'pendingReceipts',
            'recentReceipts'
        ));
    }

    /**
     * Display list of cash receipts
     */
    public function receipts(Request $request)
    {
        $query = CashReceipt::query();

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'undeposited') {
                $query->undeposited();
            } elseif ($status === 'deposited') {
                $query->deposited();
            }
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->where('receipt_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('receipt_date', '<=', $request->to_date);
        }

        $receipts = $query->orderBy('receipt_date', 'desc')
            ->paginate(20);

        return view('accountant.cash.receipts', compact('receipts'));
    }

    /**
     * Create cash receipt entry
     */
    public function createReceipt()
    {
        $bankAccounts = BankAccount::active()->get();

        return view('accountant.cash.create-receipt', compact('bankAccounts'));
    }

    /**
     * Store cash receipt
     */
    public function storeReceipt(Request $request)
    {
        $validated = $request->validate([
            'receipt_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,Check,PDC,Bank Transfer,Credit Card,Other',
            'payment_reference' => 'nullable|string',
            'source_type' => 'required|in:Daily Remittance,Customer Invoice,Other',
            'source_reference' => 'nullable|string',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'notes' => 'nullable|string',
        ]);

        $receiptNumber = 'CR-' . now()->format('YmdHis');

        $receipt = CashReceipt::create([
            'receipt_number' => $receiptNumber,
            'receipt_date' => $validated['receipt_date'],
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'],
            'source_type' => $validated['source_type'],
            'source_reference' => $validated['source_reference'],
            'bank_account_id' => $validated['bank_account_id'],
            'notes' => $validated['notes'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('accountant.cash.receipts')
            ->with('success', 'Cash receipt created successfully');
    }

    /**
     * Show cash receipt details
     */
    public function showReceipt(CashReceipt $receipt)
    {
        $receipt->load(['bankAccount', 'createdBy', 'journalEntry']);

        return view('accountant.cash.show', compact('receipt'));
    }

    /**
     * Edit cash receipt
     */
    public function editReceipt(CashReceipt $receipt)
    {
        $bankAccounts = BankAccount::active()->get();

        return view('accountant.cash.edit', compact('receipt', 'bankAccounts'));
    }

    /**
     * Update cash receipt
     */
    public function updateReceipt(Request $request, CashReceipt $receipt)
    {
        $validated = $request->validate([
            'receipt_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,Check,PDC,Bank Transfer,Credit Card,Other',
            'payment_reference' => 'nullable|string',
            'source_type' => 'nullable|string',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'notes' => 'nullable|string',
        ]);

        $receipt->update($validated);

        return redirect()->route('accountant.cash.receipt.show', $receipt)
            ->with('success', 'Cash receipt updated successfully');
    }

    /**
     * Deposit cash receipts
     */
    public function deposit(Request $request)
    {
        $validated = $request->validate([
            'receipt_ids' => 'required|array',
            'receipt_ids.*' => 'exists:cash_receipts,id',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'deposit_date' => 'required|date',
        ]);

        $totalAmount = CashReceipt::whereIn('id', $validated['receipt_ids'])->sum('amount');

        CashReceipt::whereIn('id', $validated['receipt_ids'])->update([
            'is_deposited' => true,
            'deposit_date' => $validated['deposit_date'],
            'bank_account_id' => $validated['bank_account_id'],
        ]);

        // Update bank account balance
        $bankAccount = BankAccount::find($validated['bank_account_id']);
        $bankAccount->increment('current_balance', $totalAmount);

        return redirect()->back()->with('success', 'Cash deposited successfully');
    }

    /**
     * Display bank accounts
     */
    public function bankAccounts()
    {
        $accounts = BankAccount::active()->get();

        return view('accountant.cash.bank-accounts', compact('accounts'));
    }

    /**
     * Create bank account
     */
    public function createBankAccount()
    {
        return view('accountant.cash.create-bank-account');
    }

    /**
     * Store bank account
     */
    public function storeBankAccount(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|unique:bank_accounts',
            'account_holder' => 'required|string|max:255',
            'branch_code' => 'nullable|string',
            'swift_code' => 'nullable|string',
            'currency' => 'required|string|default:PHP',
            'account_type' => 'required|in:Checking,Savings,Operating',
            'opening_balance' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        // Create GL Account first
        $glAccount = \App\Models\GLAccount::create([
            'account_code' => 'BANK-' . strtoupper(str_replace(' ', '', $request->bank_name)) . '-' . now()->timestamp,
            'account_name' => $request->bank_name . ' - ' . $request->account_number,
            'account_type' => 'Asset',
            'account_category' => 'Bank',
            'opening_balance' => $request->opening_balance ?? 0,
            'current_balance' => $request->opening_balance ?? 0,
            'is_active' => true,
        ]);

        BankAccount::create([
            'bank_name' => $validated['bank_name'],
            'account_number' => $validated['account_number'],
            'account_holder' => $validated['account_holder'],
            'branch_code' => $validated['branch_code'],
            'swift_code' => $validated['swift_code'],
            'currency' => $validated['currency'],
            'account_type' => $validated['account_type'],
            'gl_account_id' => $glAccount->id,
            'opening_balance' => $request->opening_balance ?? 0,
            'current_balance' => $request->opening_balance ?? 0,
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('accountant.cash.bank-accounts')
            ->with('success', 'Bank account created successfully');
    }
}
