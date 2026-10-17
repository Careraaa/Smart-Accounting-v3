<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Services\AccountingReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    public function __construct(private readonly AccountingReportService $reports) {}

    public function trialBalance(Request $request)
    {
        $filters = $request->validate([
            'as_of' => ['nullable', 'date'],
        ]);
        $asOf = $filters['as_of'] ?? now()->toDateString();
        $accounts = $this->reports->balancesAsOf($asOf)->map(function ($account) {
            $netDebit = $account->total_debit - $account->total_credit;
            $account->ending_debit = max($netDebit, 0);
            $account->ending_credit = max(-$netDebit, 0);

            return $account;
        });
        $totalDebit = (float) $accounts->sum('total_debit');
        $totalCredit = (float) $accounts->sum('total_credit');
        $endingDebit = (float) $accounts->sum('ending_debit');
        $endingCredit = (float) $accounts->sum('ending_credit');
        $difference = $endingDebit - $endingCredit;

        return view('accounting.reports.trial-balance', compact(
            'accounts',
            'asOf',
            'totalDebit',
            'totalCredit',
            'endingDebit',
            'endingCredit',
            'difference'
        ));
    }

    public function incomeStatement(Request $request)
    {
        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);
        $dateTo = $filters['date_to'] ?? now()->toDateString();
        $dateFrom = $filters['date_from'] ?? Carbon::parse($dateTo)->startOfMonth()->toDateString();
        $statement = $this->reports->incomeStatement($dateFrom, $dateTo);

        return view('accounting.reports.income-statement', array_merge($statement, compact('dateFrom', 'dateTo')));
    }

    public function balanceSheet(Request $request)
    {
        $filters = $request->validate([
            'as_of' => ['nullable', 'date'],
        ]);
        $asOf = $filters['as_of'] ?? now()->toDateString();
        $accounts = $this->reports->balancesAsOf($asOf);
        $assets = $accounts->where('account_type', 'Asset')->values();
        $liabilities = $accounts->where('account_type', 'Liability')->values();
        $equityAccounts = $accounts->where('account_type', 'Equity')->values();
        $totalAssets = (float) $assets->sum('balance');
        $totalLiabilities = (float) $liabilities->sum('balance');
        $totalEquityBeforeEarnings = (float) $equityAccounts->sum('balance');
        $currentPeriodStart = Carbon::parse($asOf)->startOfYear()->toDateString();
        $currentEarnings = $this->reports->incomeStatement($currentPeriodStart, $asOf)['netIncome'];
        $totalEquity = $totalEquityBeforeEarnings + $currentEarnings;
        $equationDifference = $totalAssets - $totalLiabilities - $totalEquity;

        return view('accounting.reports.balance-sheet', compact(
            'assets',
            'liabilities',
            'equityAccounts',
            'asOf',
            'currentPeriodStart',
            'currentEarnings',
            'totalAssets',
            'totalLiabilities',
            'totalEquityBeforeEarnings',
            'totalEquity',
            'equationDifference'
        ));
    }
}
