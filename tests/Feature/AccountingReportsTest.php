<?php

use App\Models\AccountingAuditLog;
use App\Models\AccountMapping;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->accountingUser = User::create([
        'name' => 'Accounting Tester',
        'username' => 'accounting-tester',
        'password' => 'password',
        'role' => 'accountant',
        'status' => 'active',
    ]);
    $this->actingAs($this->accountingUser);
});

function accountingTestAccount(string $code, string $name, string $type, bool $contra = false): ChartOfAccount
{
    return ChartOfAccount::create([
        'account_code' => $code,
        'account_name' => $name,
        'account_type' => $type,
        'is_contra' => $contra,
        'is_active' => true,
    ]);
}

function accountingTestEntry(array $data, array $lines): JournalEntry
{
    $entry = JournalEntry::create($data);
    foreach ($lines as $line) {
        $entry->lines()->create($line);
    }

    return $entry;
}

test('financial reports share posted-date balances and account normal sides', function () {
    $cash = accountingTestAccount('1010', 'Cash', 'Asset');
    $equity = accountingTestAccount('3010', 'Owner Equity', 'Equity');
    $retainedEarnings = accountingTestAccount('3020', 'Retained Earnings', 'Equity');
    $revenue = accountingTestAccount('4010', 'Service Revenue', 'Revenue');
    $contraRevenue = accountingTestAccount('4020', 'Sales Returns', 'Revenue', true);
    $expense = accountingTestAccount('5010', 'Wages', 'Expense');
    $contraExpense = accountingTestAccount('5020', 'Payroll Deductions', 'Expense', true);
    $zeroBalance = accountingTestAccount('6010', 'Unused Account', 'Asset');

    accountingTestEntry([
        'journal_number' => 'JE-2025-00001',
        'transaction_date' => '2025-12-31',
        'description' => 'Accumulated results',
        'status' => 'Posted',
    ], [
        ['account_id' => $cash->id, 'debit' => 500, 'credit' => 0],
        ['account_id' => $retainedEarnings->id, 'debit' => 0, 'credit' => 500],
    ]);
    accountingTestEntry([
        'journal_number' => 'JE-2026-00001',
        'transaction_date' => '2026-01-05',
        'description' => 'Opening capital',
        'status' => 'Posted',
    ], [
        ['account_id' => $cash->id, 'debit' => 1000, 'credit' => 0],
        ['account_id' => $equity->id, 'debit' => 0, 'credit' => 1000],
    ]);
    accountingTestEntry([
        'journal_number' => 'JE-2026-00002',
        'transaction_date' => '2026-01-10',
        'description' => 'Transport service and payroll',
        'status' => 'Posted',
    ], [
        ['account_id' => $cash->id, 'debit' => 160, 'credit' => 0],
        ['account_id' => $expense->id, 'debit' => 50, 'credit' => 0],
        ['account_id' => $contraRevenue->id, 'debit' => 20, 'credit' => 0],
        ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 220],
        ['account_id' => $contraExpense->id, 'debit' => 0, 'credit' => 10],
    ]);
    accountingTestEntry([
        'journal_number' => 'JE-2026-00003',
        'transaction_date' => '2026-01-11',
        'description' => 'Outside selected date',
        'status' => 'Posted',
    ], [
        ['account_id' => $cash->id, 'debit' => 100, 'credit' => 0],
        ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 100],
    ]);
    accountingTestEntry([
        'journal_number' => 'JE-2026-00004',
        'transaction_date' => '2026-01-10',
        'description' => 'Draft excluded',
        'status' => 'Draft',
    ], [
        ['account_id' => $cash->id, 'debit' => 500, 'credit' => 0],
        ['account_id' => $equity->id, 'debit' => 0, 'credit' => 500],
    ]);
    accountingTestEntry([
        'journal_number' => 'JE-2026-00005',
        'transaction_date' => '2026-01-10',
        'description' => 'Voided excluded',
        'status' => 'Void',
    ], [
        ['account_id' => $cash->id, 'debit' => 700, 'credit' => 0],
        ['account_id' => $equity->id, 'debit' => 0, 'credit' => 700],
    ]);

    $matchingLedgerLines = \App\Models\JournalEntryLine::where('account_id', $cash->id)
        ->whereHas('journalEntry', fn ($query) => $query
            ->where('status', 'Posted')
            ->whereDate('transaction_date', '>=', '2026-01-10')
            ->whereDate('transaction_date', '<=', '2026-01-10'))
        ->count();
    expect($matchingLedgerLines)->toBe(1);

    $this->get(route('accounting.general-ledger.index', [
        'account_id' => $cash->id,
        'date_from' => '2026-01-10',
        'date_to' => '2026-01-10',
    ]))
        ->assertOk()
        ->assertSee('body * { visibility: hidden !important; }', false)
        ->assertSee('GENERAL LEDGER')
        ->assertSee('Opening balance')
        ->assertSee('1,500.00')
        ->assertSee('1,660.00')
        ->assertDontSee('Outside selected date');

    $this->get(route('accounting.trial-balance.index', ['as_of' => '2026-01-10']))
        ->assertOk()
        ->assertSee('TRIAL BALANCE')
        ->assertSee('Unused Account')
        ->assertSee('BALANCED');
    $this->get(route('accounting.general-ledger.summary'))
        ->assertRedirect(route('accounting.trial-balance.index'));

    $this->get(route('accounting.income-statement.index', [
        'date_from' => '2026-01-01',
        'date_to' => '2026-01-10',
    ]))
        ->assertOk()
        ->assertSee('INCOME STATEMENT')
        ->assertSee('Sales Returns')
        ->assertSee('Net income')
        ->assertSee('160.00')
        ->assertDontSee('Outside selected date');

    $this->get(route('accounting.balance-sheet.index', ['as_of' => '2026-01-10']))
        ->assertOk()
        ->assertSee('BALANCE SHEET')
        ->assertSee('Total assets')
        ->assertSee('Current-period earnings')
        ->assertSee('1,660.00')
        ->assertSee('BALANCED');

    $csv = $this->get(route('accounting.general-ledger.csv', [
        'account_id' => $cash->id,
        'date_from' => '2026-01-10',
        'date_to' => '2026-01-10',
    ]))->assertOk();
    expect($csv->streamedContent())
        ->toContain('Opening balance')
        ->toContain('JE-2026-00002')
        ->not->toContain('JE-2026-00003');

    accountingTestEntry([
        'journal_number' => 'JE-2026-00006',
        'transaction_date' => '2026-01-12',
        'description' => 'Posted reversal',
        'status' => 'Posted',
    ], [
        ['account_id' => $cash->id, 'debit' => 0, 'credit' => 160],
        ['account_id' => $expense->id, 'debit' => 0, 'credit' => 50],
        ['account_id' => $contraRevenue->id, 'debit' => 0, 'credit' => 20],
        ['account_id' => $revenue->id, 'debit' => 220, 'credit' => 0],
        ['account_id' => $contraExpense->id, 'debit' => 10, 'credit' => 0],
    ]);
    $this->get(route('accounting.income-statement.index', [
        'date_from' => '2026-01-01',
        'date_to' => '2026-01-12',
    ]))
        ->assertOk()
        ->assertSee('100.00');
});

test('accounts with journal entries can be deactivated', function () {
    $account = accountingTestAccount('1010', 'Cash', 'Asset');
    accountingTestEntry([
        'journal_number' => 'JE-2026-00020',
        'transaction_date' => '2026-02-01',
        'description' => 'Existing journal history',
        'status' => 'Posted',
    ], [
        ['account_id' => $account->id, 'debit' => 100, 'credit' => 0],
        ['account_id' => accountingTestAccount('3010', 'Owner Equity', 'Equity')->id, 'debit' => 0, 'credit' => 100],
    ]);

    $this->patch(route('accounting.chart-of-accounts.toggle-status', $account))
        ->assertRedirect(route('accounting.chart-of-accounts.index'))
        ->assertSessionHas('success', 'Account 1010 deactivated successfully.');

    expect($account->fresh()->is_active)->toBeFalse();
});

test('inactive account history remains reportable and inactive accounts cannot be reused', function () {
    $cash = accountingTestAccount('1010', 'Inactive Cash', 'Asset');
    $expense = accountingTestAccount('5010', 'Inactive Wages', 'Expense');
    $revenue = accountingTestAccount('4010', 'Inactive Service Revenue', 'Revenue');
    $equity = accountingTestAccount('3010', 'Owner Equity', 'Equity');
    $cash->update(['is_active' => false]);
    $expense->update(['is_active' => false]);
    $revenue->update(['is_active' => false]);

    accountingTestEntry([
        'journal_number' => 'JE-2026-00030',
        'transaction_date' => '2026-03-01',
        'description' => 'Activity in inactive accounts',
        'status' => 'Posted',
    ], [
        ['account_id' => $cash->id, 'debit' => 100, 'credit' => 0],
        ['account_id' => $expense->id, 'debit' => 30, 'credit' => 0],
        ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 100],
        ['account_id' => $equity->id, 'debit' => 0, 'credit' => 30],
    ]);

    $this->get(route('accounting.trial-balance.index', ['as_of' => '2026-03-01']))
        ->assertOk()
        ->assertSee('Inactive Cash')
        ->assertSee('Inactive Wages')
        ->assertSee('Inactive Service Revenue')
        ->assertSee('BALANCED');
    $this->get(route('accounting.income-statement.index', [
        'date_from' => '2026-03-01',
        'date_to' => '2026-03-01',
    ]))
        ->assertOk()
        ->assertSee('Inactive Wages')
        ->assertSee('Inactive Service Revenue')
        ->assertSee('70.00');
    $this->get(route('accounting.balance-sheet.index', ['as_of' => '2026-03-01']))
        ->assertOk()
        ->assertSee('Inactive Cash')
        ->assertSee('BALANCED');
    $this->get(route('accounting.general-ledger.index', ['account_id' => $cash->id]))
        ->assertOk()
        ->assertSee('Inactive Cash')
        ->assertSee('100.00');

    $this->post(route('accounting.journal-entries.store'), [
        'transaction_date' => '2026-03-02',
        'description' => 'Attempt to reuse inactive account',
        'lines' => [
            ['account_id' => $cash->id, 'debit' => 10, 'credit' => 0],
            ['account_id' => $equity->id, 'debit' => 0, 'credit' => 10],
        ],
    ])->assertSessionHasErrors('lines.0.account_id');

    $draft = accountingTestEntry([
        'journal_number' => 'JE-2026-00031',
        'transaction_date' => '2026-03-02',
        'description' => 'Draft with newly inactive account',
        'status' => 'Draft',
    ], [
        ['account_id' => $cash->id, 'debit' => 10, 'credit' => 0],
        ['account_id' => $equity->id, 'debit' => 0, 'credit' => 10],
    ]);
    $this->from(route('accounting.journal-entries.show', $draft))
        ->post(route('accounting.journal-entries.post', $draft))
        ->assertRedirect(route('accounting.journal-entries.show', $draft))
        ->assertSessionHas('error', 'Cannot post a journal entry that uses an inactive account.');
    expect($draft->fresh()->status)->toBe('Draft');
});

test('account mappings require active accounts of the correct type and preserve mapped account classification', function () {
    $expense = accountingTestAccount('5010', 'Payroll Expense', 'Expense');
    $asset = accountingTestAccount('1010', 'Cash', 'Asset');
    $contraExpense = accountingTestAccount('5061', 'Payroll Deductions', 'Expense', true);

    $this->post(route('accounting.account-mappings.store'), [
        'mappings' => [
            ['key' => 'payroll_expense', 'account_id' => $expense->id],
        ],
    ])->assertRedirect(route('accounting.account-mappings.index'));
    expect(AccountMapping::getAccountIdForKey('payroll_expense'))->toBe($expense->id);

    $this->from(route('accounting.account-mappings.index'))
        ->post(route('accounting.account-mappings.store'), [
            'mappings' => [
                ['key' => 'payroll_expense', 'account_id' => $asset->id],
            ],
        ])
        ->assertRedirect(route('accounting.account-mappings.index'))
        ->assertSessionHasErrors('mappings.0.account_id');
    expect(AccountMapping::getAccountIdForKey('payroll_expense'))->toBe($expense->id);

    $this->from(route('accounting.account-mappings.index'))
        ->post(route('accounting.account-mappings.store'), [
            'mappings' => [
                ['key' => 'payroll_deductions', 'account_id' => $expense->id],
            ],
        ])
        ->assertRedirect(route('accounting.account-mappings.index'))
        ->assertSessionHasErrors('mappings.0.account_id');

    $this->post(route('accounting.account-mappings.store'), [
        'mappings' => [
            ['key' => 'payroll_deductions', 'account_id' => $contraExpense->id],
        ],
    ])->assertRedirect(route('accounting.account-mappings.index'));
    expect(AccountMapping::getAccountIdForKey('payroll_deductions'))->toBe($contraExpense->id);

    $this->from(route('accounting.chart-of-accounts.edit', $expense))
        ->put(route('accounting.chart-of-accounts.update', $expense), [
            'account_code' => $expense->account_code,
            'account_name' => $expense->account_name,
            'account_type' => 'Revenue',
            'description' => $expense->description,
        ])
        ->assertRedirect(route('accounting.chart-of-accounts.edit', $expense))
        ->assertSessionHas('error');
    expect($expense->fresh()->account_type)->toBe('Expense');

    $expense->update(['is_active' => false]);
    $this->from(route('accounting.account-mappings.index'))
        ->post(route('accounting.account-mappings.store'), [
            'mappings' => [
                ['key' => 'payroll_expense', 'account_id' => $expense->id],
            ],
        ])
        ->assertRedirect(route('accounting.account-mappings.index'))
        ->assertSessionHasErrors('mappings.0.account_id');
});

test('journal drafts enforce currency precision and can be edited before posting', function () {
    $cash = accountingTestAccount('1010', 'Cash', 'Asset');
    $equity = accountingTestAccount('3010', 'Owner Equity', 'Equity');

    $this->from(route('accounting.journal-entries.create'))
        ->post(route('accounting.journal-entries.store'), [
            'transaction_date' => '2026-03-03',
            'description' => 'Invalid precision',
            'lines' => [
                ['account_id' => $cash->id, 'debit' => '10.005', 'credit' => '0'],
                ['account_id' => $equity->id, 'debit' => '0', 'credit' => '10.005'],
            ],
        ])
        ->assertRedirect(route('accounting.journal-entries.create'))
        ->assertSessionHasErrors('lines.0.debit');
    expect(JournalEntry::count())->toBe(0);

    $this->post(route('accounting.journal-entries.store'), [
        'transaction_date' => '2026-03-03',
        'description' => 'Initial draft',
        'lines' => [
            ['account_id' => $cash->id, 'debit' => '10.00', 'credit' => '0'],
            ['account_id' => $equity->id, 'debit' => '0', 'credit' => '10.00'],
        ],
    ])->assertRedirect();
    $entry = JournalEntry::latest('id')->firstOrFail();
    expect($entry->status)->toBe('Draft');

    $this->get(route('accounting.journal-entries.edit', $entry))->assertOk();
    $this->put(route('accounting.journal-entries.update', $entry), [
        'transaction_date' => '2026-03-04',
        'description' => 'Updated draft',
        'lines' => [
            ['account_id' => $cash->id, 'debit' => '15.00', 'credit' => '0'],
            ['account_id' => $equity->id, 'debit' => '0', 'credit' => '15.00'],
        ],
    ])->assertRedirect(route('accounting.journal-entries.show', $entry));

    expect($entry->fresh()->description)->toBe('Updated draft')
        ->and((float) $entry->fresh()->lines()->sum('debit'))->toBe(15.0);

    $this->post(route('accounting.journal-entries.post', $entry))->assertRedirect();
    expect($entry->fresh()->status)->toBe('Posted');
});

test('posting requires a balanced entry and duplicate post or void does not duplicate accounting history', function () {
    $cash = accountingTestAccount('1010', 'Cash', 'Asset');
    $equity = accountingTestAccount('3010', 'Owner Equity', 'Equity');
    $entry = accountingTestEntry([
        'journal_number' => 'JE-2026-00010',
        'transaction_date' => '2026-02-01',
        'description' => 'Test posting',
        'status' => 'Draft',
        'created_by' => $this->accountingUser->id,
    ], [
        ['account_id' => $cash->id, 'debit' => 100, 'credit' => 0],
        ['account_id' => $equity->id, 'debit' => 0, 'credit' => 100],
    ]);

    $this->post(route('accounting.journal-entries.post', $entry))->assertRedirect();
    expect($entry->fresh()->status)->toBe('Posted');
    expect(AccountingAuditLog::where('record_id', $entry->id)->where('action', 'posted')->count())->toBe(1);

    $this->from(route('accounting.journal-entries.show', $entry))
        ->post(route('accounting.journal-entries.post', $entry))
        ->assertRedirect(route('accounting.journal-entries.show', $entry))
        ->assertSessionHas('error');
    expect(AccountingAuditLog::where('record_id', $entry->id)->where('action', 'posted')->count())->toBe(1);

    $this->patch(route('accounting.journal-entries.void', $entry))->assertRedirect();
    expect($entry->fresh()->status)->toBe('Void');
    expect(AccountingAuditLog::where('record_id', $entry->id)->count())->toBe(2);
    $this->from(route('accounting.journal-entries.show', $entry))
        ->patch(route('accounting.journal-entries.void', $entry))
        ->assertRedirect(route('accounting.journal-entries.show', $entry))
        ->assertSessionHas('error');
    expect(AccountingAuditLog::where('record_id', $entry->id)->count())->toBe(2);

    $this->get(route('accounting.trial-balance.index', ['as_of' => '2026-02-01']))
        ->assertOk()
        ->assertDontSee('100.00');

    $unbalanced = accountingTestEntry([
        'journal_number' => 'JE-2026-00011',
        'transaction_date' => '2026-02-02',
        'description' => 'Invalid imbalance',
        'status' => 'Draft',
        'created_by' => $this->accountingUser->id,
    ], [
        ['account_id' => $cash->id, 'debit' => 100, 'credit' => 0],
        ['account_id' => $equity->id, 'debit' => 0, 'credit' => 90],
    ]);
    $this->from(route('accounting.journal-entries.show', $unbalanced))
        ->post(route('accounting.journal-entries.post', $unbalanced))
        ->assertRedirect(route('accounting.journal-entries.show', $unbalanced))
        ->assertSessionHas('error');
    expect($unbalanced->fresh()->status)->toBe('Draft');
    expect(AccountingAuditLog::where('record_id', $unbalanced->id)->count())->toBe(0);
});

test('accounting reports remain behind role permissions and filters reject invalid values', function () {
    $this->get(route('accounting.trial-balance.index', ['as_of' => 'not-a-date']))->assertSessionHasErrors('as_of');
    $this->get(route('accounting.general-ledger.index', ['account_id' => 999999]))->assertSessionHasErrors('account_id');
    $this->get(route('accounting.general-ledger.index', [
        'account_id' => accountingTestAccount('1010', 'Cash', 'Asset')->id,
        'date_to' => '2026-01-10',
    ]))->assertOk();
    $this->get(route('accounting.general-ledger.index', [
        'account_id' => 1,
        'date_from' => '2026-02-01',
        'date_to' => '2026-01-01',
    ]))->assertSessionHasErrors('date_to');

    $employee = User::create([
        'name' => 'Employee Tester',
        'username' => 'employee-tester',
        'password' => 'password',
        'role' => 'employee',
        'status' => 'active',
    ]);

    $this->actingAs($employee)
        ->get(route('accounting.trial-balance.index'))
        ->assertForbidden();

    $this->actingAs($this->accountingUser)
        ->get(route('accounting.chart-of-accounts.index'))
        ->assertOk();
    $this->get(route('accounting.journal-entries.index'))->assertOk();
    $this->get(route('accounting.account-mappings.index'))->assertOk();
});
