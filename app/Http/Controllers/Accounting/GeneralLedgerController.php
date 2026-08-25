<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;

class GeneralLedgerController extends Controller
{
    public function index(Request $request)
    {
        $accounts = ChartOfAccount::active()->orderBy('account_code')->get();

        $selectedAccount = null;
        $transactions = collect();
        $runningBalance = 0;

        if ($request->filled('account_id')) {
            $selectedAccount = ChartOfAccount::find($request->input('account_id'));

            if ($selectedAccount) {
                $query = JournalEntryLine::where('account_id', $selectedAccount->id)
                    ->whereHas('journalEntry', fn ($q) => $q->where('status', 'Posted'))
                    ->with('journalEntry');

                if ($request->filled('date_from')) {
                    $query->whereHas('journalEntry', fn ($q) => $q->where('transaction_date', '>=', $request->input('date_from')));
                }

                if ($request->filled('date_to')) {
                    $query->whereHas('journalEntry', fn ($q) => $q->where('transaction_date', '<=', $request->input('date_to')));
                }

                $transactions = $query->orderBy('journal_entries.transaction_date')
                    ->orderBy('journal_entries.id')
                    ->get();

                $runningBalance = 0;
                foreach ($transactions as &$t) {
                    $runningBalance += (float) $t->debit - (float) $t->credit;
                    $t->running_balance = $runningBalance;
                }
                unset($t);

                $transactions = $transactions->values();
            }
        }

        return view('accounting.general-ledger.index', compact('accounts', 'selectedAccount', 'transactions', 'runningBalance'));
    }

    public function summary(Request $request)
    {
        $query = ChartOfAccount::active()->withSum([
            'journalEntryLines as total_debit' => function ($q) {
                $q->whereHas('journalEntry', fn ($q) => $q->where('status', 'Posted'));
            },
        ], 'debit')->withSum([
            'journalEntryLines as total_credit' => function ($q) {
                $q->whereHas('journalEntry', fn ($q) => $q->where('status', 'Posted'));
            },
        ], 'credit');

        $accounts = $query->orderBy('account_code')->get()->map(function ($account) {
            $account->balance = (float) $account->total_debit - (float) $account->total_credit;
            return $account;
        });

        $totalDebit = $accounts->sum('total_debit');
        $totalCredit = $accounts->sum('total_credit');

        return view('accounting.general-ledger.summary', compact('accounts', 'totalDebit', 'totalCredit'));
    }
}
