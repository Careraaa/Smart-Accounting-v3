<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GeneralLedgerController extends Controller
{
    public function index(Request $request)
    {
        $filters = $this->validatedFilters($request);
        $accounts = ChartOfAccount::active()->orderBy('account_code')->get();
        $selectedAccount = isset($filters['account_id'])
            ? ChartOfAccount::active()->findOrFail($filters['account_id'])
            : null;
        $ledger = $selectedAccount ? $this->ledger($selectedAccount, $filters) : null;

        return view('accounting.general-ledger.index', compact(
            'accounts',
            'selectedAccount',
            'ledger',
            'filters'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $filters = $this->validatedFilters($request);
        abort_unless(isset($filters['account_id']), 422, 'Select an account before exporting the ledger.');

        $account = ChartOfAccount::active()->findOrFail($filters['account_id']);
        $ledger = $this->ledger($account, $filters);
        $filename = 'general-ledger-'.preg_replace('/[^A-Za-z0-9._-]/', '_', $account->account_code).'.csv';

        return response()->streamDownload(function () use ($account, $ledger) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Account', $this->csvSafe($account->account_code.' - '.$account->account_name)]);
            fputcsv($output, ['Period from', $ledger['dateFrom'] ?? 'Beginning']);
            fputcsv($output, ['Period to', $ledger['dateTo'] ?? 'Latest']);
            fputcsv($output, ['Normal balance side', $account->normal_balance_side]);
            fputcsv($output, ['Opening balance', number_format($ledger['openingBalance'], 2, '.', '')]);
            fputcsv($output, []);
            fputcsv($output, ['Date', 'Reference', 'Journal number', 'Description', 'Debit', 'Credit', 'Running balance (signed normal-side balance)']);

            foreach ($ledger['transactions'] as $line) {
                fputcsv($output, [
                    $line->journalEntry->transaction_date->toDateString(),
                    $this->csvSafe($this->referenceFor($line)),
                    $this->csvSafe($line->journalEntry->journal_number),
                    $this->csvSafe($line->description ?: $line->journalEntry->description),
                    number_format((float) $line->debit, 2, '.', ''),
                    number_format((float) $line->credit, 2, '.', ''),
                    number_format($line->running_balance, 2, '.', ''),
                ]);
            }

            fputcsv($output, []);
            fputcsv($output, ['Period debit total', number_format($ledger['periodDebit'], 2, '.', '')]);
            fputcsv($output, ['Period credit total', number_format($ledger['periodCredit'], 2, '.', '')]);
            fputcsv($output, ['Closing balance (signed normal-side balance)', number_format($ledger['closingBalance'], 2, '.', '')]);
            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function summary()
    {
        return redirect()->route('accounting.trial-balance.index');
    }

    private function validatedFilters(Request $request): array
    {
        return $request->validate([
            'account_id' => ['nullable', 'integer', 'exists:chart_of_accounts,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);
    }

    private function ledger(ChartOfAccount $account, array $filters): array
    {
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;

        $openingBalance = 0.0;
        if ($dateFrom !== null) {
            $opening = JournalEntryLine::query()
                ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
                ->where('journal_entry_lines.account_id', $account->id)
                ->where('journal_entries.status', 'Posted')
                ->whereDate('journal_entries.transaction_date', '<', $dateFrom)
                ->selectRaw('COALESCE(SUM(journal_entry_lines.debit), 0) as debit_total')
                ->selectRaw('COALESCE(SUM(journal_entry_lines.credit), 0) as credit_total')
                ->first();
            $openingBalance = $account->normalBalance((float) $opening->debit_total, (float) $opening->credit_total);
        }

        $query = JournalEntryLine::query()
            ->join('journal_entries', 'journal_entries.id', '=', 'journal_entry_lines.journal_entry_id')
            ->where('journal_entry_lines.account_id', $account->id)
            ->where('journal_entries.status', 'Posted')
            ->select('journal_entry_lines.*')
            ->with('journalEntry');

        if ($dateFrom !== null) {
            $query->whereDate('journal_entries.transaction_date', '>=', $dateFrom);
        }
        if ($dateTo !== null) {
            $query->whereDate('journal_entries.transaction_date', '<=', $dateTo);
        }

        $transactions = $query
            ->orderBy('journal_entries.transaction_date')
            ->orderBy('journal_entries.id')
            ->orderBy('journal_entry_lines.id')
            ->get();

        $periodDebit = (float) $transactions->sum(fn ($line) => (float) $line->debit);
        $periodCredit = (float) $transactions->sum(fn ($line) => (float) $line->credit);
        $runningBalance = $openingBalance;

        foreach ($transactions as $line) {
            $runningBalance += $account->normalBalance((float) $line->debit, (float) $line->credit);
            $line->running_balance = $runningBalance;
            $line->ledger_reference = $this->referenceFor($line);
        }

        return [
            'transactions' => $transactions,
            'openingBalance' => $openingBalance,
            'periodDebit' => $periodDebit,
            'periodCredit' => $periodCredit,
            'closingBalance' => $runningBalance,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ];
    }

    private function referenceFor(JournalEntryLine $line): string
    {
        $entry = $line->journalEntry;
        if ($entry->reference_type === null && $entry->reference_id === null) {
            return $entry->journal_number;
        }

        return trim(($entry->reference_type ?? 'Reference').($entry->reference_id !== null ? ' #'.$entry->reference_id : ''));
    }

    private function csvSafe(string $value): string
    {
        return preg_match('/^[\s]*[=+\-@]/', $value) ? "'".$value : $value;
    }
}
