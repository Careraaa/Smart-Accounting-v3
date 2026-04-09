<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\GLAccount;
use App\Models\JournalEntry;
use App\Models\TrialBalance;
use App\Models\SupplierBill;
use App\Models\CashReceipt;
use Illuminate\Http\Request;
use \Carbon\Carbon;

class FinancialReportController extends Controller
{
    /**
     * Dashboard with key metrics
     */
    public function dashboard(Request $request)
    {
        $asOfDate = $request->as_of_date ? Carbon::parse($request->as_of_date) : now();

        // Calculate totals
        $totalRevenue = $this->calculateAccountTotal('Revenue', $asOfDate);
        $totalExpenses = $this->calculateAccountTotal('Expense', $asOfDate);
        $totalAssets = $this->calculateAccountTotal('Asset', $asOfDate);
        $totalLiabilities = $this->calculateAccountTotal('Liability', $asOfDate);

        $netIncome = $totalRevenue - $totalExpenses;

        // Recent transactions
        $recentEntries = JournalEntry::where('status', 'Posted')
            ->where('posting_date', '<=', $asOfDate)
            ->latest()
            ->take(10)
            ->get();

        return view('accountant.financial-reports.dashboard', compact(
            'totalRevenue',
            'totalExpenses',
            'totalAssets',
            'totalLiabilities',
            'netIncome',
            'recentEntries',
            'asOfDate'
        ));
    }

    /**
     * Generate Income Statement
     */
    public function incomeStatement(Request $request)
    {
        $asOfDate = $request->as_of_date ? Carbon::parse($request->as_of_date) : now();
        $fromDate = $request->from_date ? Carbon::parse($request->from_date) : $asOfDate->copy()->firstOfYear();

        // Get revenue accounts
        $revenues = GLAccount::revenue()->active()->get();
        $expenses = GLAccount::expenses()->active()->get();

        $totalRevenue = 0;
        $totalExpenses = 0;
        $revenueDetails = [];
        $expenseDetails = [];

        foreach ($revenues as $account) {
            $amount = $this->calculateAccountAmount($account, $asOfDate, $fromDate);
            if ($amount != 0) {
                $revenueDetails[] = [
                    'account' => $account,
                    'amount' => $amount,
                ];
                $totalRevenue += $amount;
            }
        }

        foreach ($expenses as $account) {
            $amount = $this->calculateAccountAmount($account, $asOfDate, $fromDate);
            if ($amount != 0) {
                $expenseDetails[] = [
                    'account' => $account,
                    'amount' => $amount,
                ];
                $totalExpenses += $amount;
            }
        }

        $netIncome = $totalRevenue - $totalExpenses;
        $netIncomePercent = $totalRevenue != 0 ? ($netIncome / $totalRevenue) * 100 : 0;

        return view('accountant.financial-reports.income-statement', compact(
            'revenueDetails',
            'expenseDetails',
            'totalRevenue',
            'totalExpenses',
            'netIncome',
            'netIncomePercent',
            'asOfDate',
            'fromDate'
        ));
    }

    /**
     * Generate Balance Sheet
     */
    public function balanceSheet(Request $request)
    {
        $asOfDate = $request->as_of_date ? Carbon::parse($request->as_of_date) : now();

        // Assets
        $assets = GLAccount::assets()->active()->get();
        $totalAssets = 0;
        $assetDetails = [];

        foreach ($assets as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if ($balance != 0) {
                $assetDetails[] = [
                    'account' => $account,
                    'balance' => $balance,
                ];
                $totalAssets += $balance;
            }
        }

        // Liabilities
        $liabilities = GLAccount::liabilities()->active()->get();
        $totalLiabilities = 0;
        $liabilityDetails = [];

        foreach ($liabilities as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if ($balance != 0) {
                $liabilityDetails[] = [
                    'account' => $account,
                    'balance' => $balance,
                ];
                $totalLiabilities += $balance;
            }
        }

        // Equity (simplified)
        $equity = GLAccount::whereIn('account_type', ['Equity', 'Earnings'])->active()->get();
        $totalEquity = 0;
        $equityDetails = [];

        foreach ($equity as $account) {
            $balance = $this->calculateAccountBalance($account, $asOfDate);
            if ($balance != 0) {
                $equityDetails[] = [
                    'account' => $account,
                    'balance' => $balance,
                ];
                $totalEquity += $balance;
            }
        }

        return view('accountant.financial-reports.balance-sheet', compact(
            'assetDetails',
            'liabilityDetails',
            'equityDetails',
            'totalAssets',
            'totalLiabilities',
            'totalEquity',
            'asOfDate'
        ));
    }

    /**
     * Generate Trial Balance Report
     */
    public function trialBalance(Request $request)
    {
        $asOfDate = $request->as_of_date ? Carbon::parse($request->as_of_date) : now();

        // Get all accounts
        $accounts = GLAccount::active()->get();
        $trialBalanceDetails = [];
        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($accounts as $account) {
            // Get all posted journal entry lines for this account up to the as_of_date
            $lines = $account->journalEntryLines()
                ->whereHas('journalEntry', function ($q) use ($asOfDate) {
                    $q->where('status', 'Posted')
                      ->where('posting_date', '<=', $asOfDate);
                })
                ->get();

            $debit = $lines->sum('debit_amount');
            $credit = $lines->sum('credit_amount');

            // Calculate balance
            if (in_array($account->account_type, ['Asset', 'Expense'])) {
                $balance = $account->opening_balance + $debit - $credit;
                $finalDebit = $balance >= 0 ? $balance : 0;
                $finalCredit = $balance < 0 ? abs($balance) : 0;
            } else {
                $balance = $account->opening_balance + $credit - $debit;
                $finalDebit = $balance < 0 ? abs($balance) : 0;
                $finalCredit = $balance >= 0 ? $balance : 0;
            }

            if ($finalDebit != 0 || $finalCredit != 0) {
                $trialBalanceDetails[] = [
                    'account' => $account,
                    'debit' => $finalDebit,
                    'credit' => $finalCredit,
                ];
                $totalDebits += $finalDebit;
                $totalCredits += $finalCredit;
            }
        }

        // Sort by account code
        usort($trialBalanceDetails, function ($a, $b) {
            return $a['account']->code <=> $b['account']->code;
        });

        $isBalanced = abs($totalDebits - $totalCredits) < 0.01;

        return view('accountant.financial-reports.trial-balance', compact(
            'trialBalanceDetails',
            'totalDebits',
            'totalCredits',
            'isBalanced',
            'asOfDate'
        ));
    }

    /**
     * Generate Cash Flow Statement
     */
    public function cashFlowStatement(Request $request)
    {
        $asOfDate = $request->as_of_date ? Carbon::parse($request->as_of_date) : now();
        $fromDate = $request->from_date ? Carbon::parse($request->from_date) : $asOfDate->copy()->firstOfYear();

        // Operating Activities
        $operatingAccounts = GLAccount::whereIn('account_type', ['Revenue', 'Expense'])->active()->get();
        $operatingCashFlow = 0;

        foreach ($operatingAccounts as $account) {
            $amount = $this->calculateAccountAmount($account, $asOfDate, $fromDate);
            $operatingCashFlow += $amount;
        }

        // Investing Activities (from Asset accounts - simplified)
        $assetAccounts = GLAccount::assets()->active()->get();
        $investingCashFlow = 0;

        foreach ($assetAccounts as $account) {
            $amount = $this->calculateAccountAmount($account, $asOfDate, $fromDate);
            if ($amount < 0) {
                $investingCashFlow += $amount;
            }
        }

        // Financing Activities (from Liability accounts - simplified)
        $liabilityAccounts = GLAccount::liabilities()->active()->get();
        $financingCashFlow = 0;

        foreach ($liabilityAccounts as $account) {
            $amount = $this->calculateAccountAmount($account, $asOfDate, $fromDate);
            if ($amount > 0) {
                $financingCashFlow += $amount;
            }
        }

        $netCashFlow = $operatingCashFlow + $investingCashFlow + $financingCashFlow;

        return view('accountant.financial-reports.cash-flow-statement', compact(
            'operatingCashFlow',
            'investingCashFlow',
            'financingCashFlow',
            'netCashFlow',
            'asOfDate',
            'fromDate'
        ));
    }

    /**
     * Helper function to calculate account balance
     */
    private function calculateAccountBalance($account, $asOfDate)
    {
        $lines = $account->journalEntryLines()
            ->whereHas('journalEntry', function ($q) use ($asOfDate) {
                $q->where('status', 'Posted')
                  ->where('posting_date', '<=', $asOfDate);
            })
            ->get();

        $totalDebit = $lines->sum('debit_amount');
        $totalCredit = $lines->sum('credit_amount');

        // Assets & Expenses increase with debit
        if (in_array($account->account_type, ['Asset', 'Expense'])) {
            return $account->opening_balance + $totalDebit - $totalCredit;
        }

        // Liabilities, Revenue & Equity increase with credit
        return $account->opening_balance + $totalCredit - $totalDebit;
    }

    /**
     * Helper function to calculate account total (for amounts in a period)
     */
    private function calculateAccountAmount($account, $asOfDate, $fromDate)
    {
        $lines = $account->journalEntryLines()
            ->whereHas('journalEntry', function ($q) use ($asOfDate, $fromDate) {
                $q->where('status', 'Posted')
                  ->whereBetween('posting_date', [$fromDate, $asOfDate]);
            })
            ->get();

        if (in_array($account->account_type, ['Revenue', 'Expense'])) {
            $totalDebit = $lines->sum('debit_amount');
            $totalCredit = $lines->sum('credit_amount');

            // For revenue, credit is positive
            if ($account->account_type === 'Revenue') {
                return $totalCredit - $totalDebit;
            }

            // For expenses, debit is positive
            return $totalDebit - $totalCredit;
        }

        return 0;
    }

    /**
     * Helper function to calculate total for account type
     */
    private function calculateAccountTotal($type, $asOfDate)
    {
        if ($type === 'Revenue') {
            $accounts = GLAccount::revenue()->active()->get();
            $total = 0;
            foreach ($accounts as $account) {
                $credit = $account->journalEntryLines()
                    ->whereHas('journalEntry', function ($q) use ($asOfDate) {
                        $q->where('status', 'Posted')
                          ->where('posting_date', '<=', $asOfDate);
                    })
                    ->sum('credit_amount');
                $debit = $account->journalEntryLines()
                    ->whereHas('journalEntry', function ($q) use ($asOfDate) {
                        $q->where('status', 'Posted')
                          ->where('posting_date', '<=', $asOfDate);
                    })
                    ->sum('debit_amount');
                $total += $credit - $debit;
            }
            return $total;
        }

        if ($type === 'Expense') {
            $accounts = GLAccount::expenses()->active()->get();
            $total = 0;
            foreach ($accounts as $account) {
                $debit = $account->journalEntryLines()
                    ->whereHas('journalEntry', function ($q) use ($asOfDate) {
                        $q->where('status', 'Posted')
                          ->where('posting_date', '<=', $asOfDate);
                    })
                    ->sum('debit_amount');
                $credit = $account->journalEntryLines()
                    ->whereHas('journalEntry', function ($q) use ($asOfDate) {
                        $q->where('status', 'Posted')
                          ->where('posting_date', '<=', $asOfDate);
                    })
                    ->sum('credit_amount');
                $total += $debit - $credit;
            }
            return $total;
        }

        // For assets and liabilities
        $query = $type === 'Asset' ? GLAccount::assets() : GLAccount::liabilities();
        $accounts = $query->active()->get();
        $total = 0;
        foreach ($accounts as $account) {
            $total += $this->calculateAccountBalance($account, $asOfDate);
        }
        return $total;
    }
}
