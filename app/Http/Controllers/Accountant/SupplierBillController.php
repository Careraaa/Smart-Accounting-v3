<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\SupplierBill;
use App\Models\Supplier;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\GLAccount;
use Illuminate\Http\Request;

class SupplierBillController extends Controller
{
    /**
     * Display list of supplier bills (Accounts Payable)
     */
    public function index(Request $request)
    {
        $query = SupplierBill::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Date range
        if ($request->filled('from_date')) {
            $query->where('bill_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('bill_date', '<=', $request->to_date);
        }

        $bills = $query->with('supplier')
            ->orderBy('due_date', 'asc')
            ->paginate(20);

        $suppliers = Supplier::active()->get();

        return view('accountant.bills.index', compact('bills', 'suppliers'));
    }

    /**
     * Show bill detail
     */
    public function show(SupplierBill $bill)
    {
        $bill->load(['supplier', 'createdBy', 'journalEntry']);

        return view('accountant.bills.show', compact('bill'));
    }

    /**
     * Create new supplier bill
     */
    public function create()
    {
        $suppliers = Supplier::active()->get();
        $expenseAccounts = GLAccount::expenses()->active()->get();

        return view('accountant.bills.create', compact('suppliers', 'expenseAccounts'));
    }

    /**
     * Store supplier bill
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'bill_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:bill_date',
            'bill_amount' => 'required|numeric|min:0.01',
            'bill_description' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'expense_category_id' => 'required|exists:gl_accounts,id',
        ]);

        $billNumber = 'BILL-' . now()->format('YmdHis');

        $bill = SupplierBill::create([
            'bill_number' => $billNumber,
            'supplier_id' => $validated['supplier_id'],
            'bill_date' => $validated['bill_date'],
            'due_date' => $validated['due_date'],
            'bill_amount' => $validated['bill_amount'],
            'amount_remaining' => $validated['bill_amount'],
            'bill_description' => $validated['bill_description'],
            'reference_number' => $validated['reference_number'],
            'status' => 'Received',
            'payment_status' => 'Pending',
            'created_by' => auth()->id(),
        ]);

        // Optionally create journal entry automatically
        if ($request->has('post_journal_entry')) {
            $this->postBillToGL($bill, $validated['expense_category_id']);
        }

        return redirect()->route('accountant.bills.show', $bill)
            ->with('success', 'Supplier bill created successfully');
    }

    /**
     * Post bill to GL as Accounts Payable
     */
    public function postBill(Request $request, SupplierBill $bill)
    {
        $validated = $request->validate([
            'expense_account_id' => 'required|exists:gl_accounts,id',
        ]);

        if ($bill->posted_journal_entry_id) {
            return redirect()->back()->with('error', 'Bill is already posted');
        }

        $this->postBillToGL($bill, $validated['expense_account_id']);

        return redirect()->back()->with('success', 'Bill posted to GL');
    }

    /**
     * Helper function to post bill to GL
     */
    private function postBillToGL(SupplierBill $bill, $expenseAccountId)
    {
        // Create journal entry
        $jeNumber = 'JE-' . now()->format('YmdHis');

        $entry = JournalEntry::create([
            'je_number' => $jeNumber,
            'je_date' => $bill->bill_date,
            'posting_date' => now()->toDateString(),
            'description' => 'Bill from ' . $bill->supplier->supplier_name . ' - ' . $bill->bill_number,
            'status' => 'Pending Approval',
            'je_type' => 'Automated Expense',
            'created_by' => auth()->id(),
            'reference_number' => $bill->bill_number,
            'reference_type' => 'SupplierBill',
            'total_debit' => $bill->bill_amount,
            'total_credit' => $bill->bill_amount,
            'is_balanced' => true,
        ]);

        // Debit expense account
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'gl_account_id' => $expenseAccountId,
            'debit_amount' => $bill->bill_amount,
            'credit_amount' => 0,
            'line_description' => $bill->bill_description,
            'line_number' => 1,
        ]);

        // Credit AP account
        $apAccount = GLAccount::where('account_code', '2001')->first(); // Accounts Payable
        JournalEntryLine::create([
            'journal_entry_id' => $entry->id,
            'gl_account_id' => $apAccount->id,
            'debit_amount' => 0,
            'credit_amount' => $bill->bill_amount,
            'line_description' => 'Payable to ' . $bill->supplier->supplier_name,
            'line_number' => 2,
        ]);

        // Auto-approve and post
        $entry->approve(auth()->user());
        $entry->post(auth()->user());

        // Link bill to journal entry
        $bill->update([
            'posted_journal_entry_id' => $entry->id,
            'status' => 'Approved',
        ]);
    }

    /**
     * Approve and process payment
     */
    public function processPayment(Request $request, SupplierBill $bill)
    {
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:0.01|max:' . $bill->amount_remaining,
            'payment_date' => 'required|date',
            'payment_reference' => 'nullable|string',
        ]);

        $bill->amount_paid += $validated['payment_amount'];
        $bill->amount_remaining -= $validated['payment_amount'];

        if ($bill->amount_remaining <= 0) {
            $bill->payment_status = 'Paid';
            $bill->status = 'Paid';
        } else {
            $bill->payment_status = 'Partially Paid';
        }

        $bill->save();

        // TODO: Create payment journal entry here

        return redirect()->back()->with('success', 'Payment processed successfully');
    }

    /**
     * Edit supplier bill
     */
    public function edit(SupplierBill $bill)
    {
        $suppliers = Supplier::active()->get();
        $expenseAccounts = GLAccount::expenses()->active()->get();

        return view('accountant.bills.edit', compact('bill', 'suppliers', 'expenseAccounts'));
    }

    /**
     * Update supplier bill
     */
    public function update(Request $request, SupplierBill $bill)
    {
        // Only allow editing if not yet posted
        if ($bill->posted_journal_entry_id) {
            return redirect()->back()->with('error', 'Cannot edit a posted bill');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'bill_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:bill_date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        $bill->update([
            'supplier_id' => $validated['supplier_id'],
            'bill_date' => $validated['bill_date'],
            'due_date' => $validated['due_date'],
            'amount' => $validated['amount'],
            'outstanding_balance' => $validated['amount'],
            'description' => $validated['description'],
        ]);

        return redirect()->route('accountant.bills.show', $bill)
            ->with('success', 'Bill updated successfully');
    }
}
