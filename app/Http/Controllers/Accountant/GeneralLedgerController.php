<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\GLAccount;
use Illuminate\Http\Request;

class GeneralLedgerController extends Controller
{
    /**
     * Display list of GL accounts
     */
    public function index(Request $request)
    {
        $query = GLAccount::query();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('account_type', $request->type);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('account_category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('account_code', 'like', "%{$search}%")
                  ->orWhere('account_name', 'like', "%{$search}%");
            });
        }

        $accounts = $query->orderBy('account_code')->paginate(20);

        return view('accountant.gl.index', compact('accounts'));
    }

    /**
     * Show GL account detail
     */
    public function show(GLAccount $account)
    {
        // Load relationships
        $account->load([
            'journalEntryLines.journalEntry',
            'trialBalances',
            'bankAccounts',
        ]);

        // Calculate balance
        $currentBalance = $account->calculateBalance();

        return view('accountant.gl.show', compact('account', 'currentBalance'));
    }

    /**
     * Show GL account ledger (debit/credit entries)
     */
    public function ledger(GLAccount $account, Request $request)
    {
        $from = $request->from_date ? \Carbon\Carbon::parse($request->from_date) : now()->subMonths(3);
        $to = $request->to_date ? \Carbon\Carbon::parse($request->to_date) : now();

        $entries = $account->journalEntryLines()
            ->with(['journalEntry' => function ($q) {
                $q->where('status', 'Posted');
            }])
            ->whereHas('journalEntry', function ($q) use ($from, $to) {
                $q->where('status', 'Posted')
                  ->whereBetween('posting_date', [$from, $to]);
            })
            ->orderBy('created_at')
            ->paginate(50);

        return view('accountant.gl.ledger', compact('account', 'entries', 'from', 'to'));
    }

    /**
     * Create new GL account
     */
    public function create()
    {
        $parentAccounts = GLAccount::where('is_header', true)->orderBy('account_code')->get();
        $types = ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'];
        
        return view('accountant.gl.create', compact('parentAccounts', 'types'));
    }

    /**
     * Store new GL account
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_code' => 'required|string|unique:gl_accounts|max:10',
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'account_category' => 'required|string',
            'is_header' => 'nullable|boolean',
            'opening_balance' => 'nullable|numeric',
            'parent_account_id' => 'nullable|exists:gl_accounts,id',
            'description' => 'nullable|string',
        ]);

        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;
        $validated['current_balance'] = $validated['opening_balance'];
        $validated['is_header'] = $request->has('is_header');

        GLAccount::create($validated);

        return redirect()->route('accountant.gl.index')
            ->with('success', 'GL Account created successfully');
    }

    /**
     * Edit GL account
     */
    public function edit(GLAccount $account)
    {
        $parentAccounts = GLAccount::where('is_header', true)
            ->where('id', '!=', $account->id)
            ->orderBy('account_code')
            ->get();
        $types = ['Asset', 'Liability', 'Equity', 'Revenue',  'Expense'];

        return view('accountant.gl.edit', compact('account', 'parentAccounts', 'types'));
    }

    /**
     * Update GL account
     */
    public function update(Request $request, GLAccount $account)
    {
        $validated = $request->validate([
            'account_name' => 'required|string|max:255',
            'account_category' => 'required|string',
            'is_header' => 'nullable|boolean',
            'parent_account_id' => 'nullable|exists:gl_accounts,id',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
        ]);

        $validated['is_header'] = $request->has('is_header');
        $validated['is_active'] = $request->has('is_active');

        $account->update($validated);

        return redirect()->route('accountant.gl.show', $account)
            ->with('success', 'GL Account updated successfully');
    }

    /**
     * Get trial balance for period
     */
}
