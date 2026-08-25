<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\AccountingAuditLog;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = ChartOfAccount::orderBy('account_code')->get();

        $stats = [
            'total'   => $accounts->count(),
            'active'  => $accounts->where('is_active', true)->count(),
            'assets'  => $accounts->where('account_type', 'Asset')->count(),
            'liabilities' => $accounts->where('account_type', 'Liability')->count(),
        ];

        return view('accounting.chart-of-accounts.index', compact('accounts', 'stats'));
    }

    public function create()
    {
        return view('accounting.chart-of-accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_code' => 'required|string|max:20|unique:chart_of_accounts,account_code',
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'description'  => 'nullable|string|max:1000',
        ]);

        $validated['created_by'] = auth()->id();

        $account = ChartOfAccount::create($validated);

        AccountingAuditLog::log(
            'created',
            'Chart of Accounts',
            $account->id,
            "Created account {$account->account_code} – {$account->account_name}",
            null,
            $account->toArray()
        );

        return redirect()->route('accounting.chart-of-accounts.index')
            ->with('success', "Account {$account->account_code} created successfully.");
    }

    public function show(ChartOfAccount $chartOfAccount)
    {
        $journalLines = $chartOfAccount->journalEntryLines()
            ->with('journalEntry')
            ->whereHas('journalEntry', fn ($q) => $q->where('status', 'Posted'))
            ->orderByDesc('journal_entries.transaction_date')
            ->get();

        return view('accounting.chart-of-accounts.show', compact('chartOfAccount', 'journalLines'));
    }

    public function edit(ChartOfAccount $chartOfAccount)
    {
        return view('accounting.chart-of-accounts.edit', compact('chartOfAccount'));
    }

    public function update(Request $request, ChartOfAccount $chartOfAccount)
    {
        $validated = $request->validate([
            'account_code' => 'required|string|max:20|unique:chart_of_accounts,account_code,' . $chartOfAccount->id,
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'description'  => 'nullable|string|max:1000',
        ]);

        $oldValues = $chartOfAccount->toArray();
        $chartOfAccount->update($validated);

        AccountingAuditLog::log(
            'updated',
            'Chart of Accounts',
            $chartOfAccount->id,
            "Updated account {$chartOfAccount->account_code}",
            $oldValues,
            $chartOfAccount->toArray()
        );

        return redirect()->route('accounting.chart-of-accounts.index')
            ->with('success', "Account {$chartOfAccount->account_code} updated successfully.");
    }

    public function toggleStatus(ChartOfAccount $chartOfAccount)
    {
        if ($chartOfAccount->journalEntryLines()->exists()) {
            return redirect()->back()
                ->with('error', "Cannot deactivate account {$chartOfAccount->account_code} — it has existing journal entries. Mark it inactive instead.");
        }

        $oldStatus = $chartOfAccount->is_active;
        $chartOfAccount->update(['is_active' => !$chartOfAccount->is_active]);

        AccountingAuditLog::log(
            $chartOfAccount->is_active ? 'activated' : 'deactivated',
            'Chart of Accounts',
            $chartOfAccount->id,
            ($chartOfAccount->is_active ? 'Activated' : 'Deactivated') . " account {$chartOfAccount->account_code}",
            ['is_active' => $oldStatus],
            ['is_active' => $chartOfAccount->is_active]
        );

        $status = $chartOfAccount->is_active ? 'activated' : 'deactivated';
        return redirect()->route('accounting.chart-of-accounts.index')
            ->with('success', "Account {$chartOfAccount->account_code} {$status} successfully.");
    }
}
