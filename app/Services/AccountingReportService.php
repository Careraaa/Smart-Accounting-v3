<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryLine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AccountingReportService
{
    public function postedTotals(?string $dateFrom = null, ?string $dateTo = null): Collection
    {
        $query = JournalEntryLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entries.status', 'Posted')
            ->select(
                'journal_entry_lines.account_id',
                DB::raw('SUM(journal_entry_lines.debit) as debit_total'),
                DB::raw('SUM(journal_entry_lines.credit) as credit_total')
            )
            ->groupBy('journal_entry_lines.account_id');

        if ($dateFrom !== null) {
            $query->whereDate('journal_entries.transaction_date', '>=', $dateFrom);
        }

        if ($dateTo !== null) {
            $query->whereDate('journal_entries.transaction_date', '<=', $dateTo);
        }

        return $query->get()->keyBy('account_id');
    }

    public function balancesAsOf(?string $dateTo = null): Collection
    {
        $totals = $this->postedTotals(dateTo: $dateTo);

        return ChartOfAccount::active()
            ->orderBy('account_code')
            ->get()
            ->map(function (ChartOfAccount $account) use ($totals) {
                $total = $totals->get($account->id);
                $account->total_debit = (float) ($total->debit_total ?? 0);
                $account->total_credit = (float) ($total->credit_total ?? 0);
                $account->balance = $account->normalBalance($account->total_debit, $account->total_credit);

                return $account;
            });
    }

    public function incomeStatement(string $dateFrom, string $dateTo): array
    {
        $accounts = ChartOfAccount::active()->orderBy('account_code')->get();
        $periodTotals = $this->postedTotals($dateFrom, $dateTo);

        $accounts->each(function (ChartOfAccount $account) use ($periodTotals) {
            $total = $periodTotals->get($account->id);
            $account->period_debit = (float) ($total->debit_total ?? 0);
            $account->period_credit = (float) ($total->credit_total ?? 0);
            $account->period_balance = $account->normalBalance($account->period_debit, $account->period_credit);
            $account->statement_amount = $account->is_contra
                ? -$account->period_balance
                : $account->period_balance;
        });

        $hasPeriodActivity = fn (ChartOfAccount $account) => $account->period_debit != 0 || $account->period_credit != 0;
        $revenue = $accounts->where('account_type', 'Revenue')->filter($hasPeriodActivity);
        $expenses = $accounts->where('account_type', 'Expense')->filter($hasPeriodActivity);
        $revenues = $revenue->where('is_contra', false)->values();
        $contraRevenues = $revenue->where('is_contra', true)->values();
        $expenseAccounts = $expenses->where('is_contra', false)->values();
        $contraExpenses = $expenses->where('is_contra', true)->values();
        $grossRevenue = (float) $revenues->sum('statement_amount');
        $contraRevenue = (float) $contraRevenues->sum('statement_amount');
        $netRevenue = $grossRevenue + $contraRevenue;
        $grossExpenses = (float) $expenseAccounts->sum('statement_amount');
        $contraExpense = (float) $contraExpenses->sum('statement_amount');
        $netExpenses = $grossExpenses + $contraExpense;

        return [
            'revenues' => $revenues,
            'contraRevenues' => $contraRevenues,
            'expenses' => $expenseAccounts,
            'contraExpenses' => $contraExpenses,
            'grossRevenue' => $grossRevenue,
            'contraRevenue' => $contraRevenue,
            'netRevenue' => $netRevenue,
            'grossExpenses' => $grossExpenses,
            'contraExpense' => $contraExpense,
            'netExpenses' => $netExpenses,
            'netIncome' => $netRevenue - $netExpenses,
        ];
    }
}
