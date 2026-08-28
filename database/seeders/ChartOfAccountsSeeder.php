<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;
use App\Models\AccountMapping;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // Assets
            ['account_code' => '1010', 'account_name' => 'Cash', 'account_type' => 'Asset', 'description' => 'Cash on hand and in bank'],
            ['account_code' => '1020', 'account_name' => 'Accounts Receivable', 'account_type' => 'Asset', 'description' => 'Amounts owed by customers'],
            ['account_code' => '1030', 'account_name' => 'Employee Receivable', 'account_type' => 'Asset', 'description' => 'Amounts owed by employees (cash advances, loans)'],

            // Liabilities
            ['account_code' => '2010', 'account_name' => 'Salaries Payable', 'account_type' => 'Liability', 'description' => 'Net pay owed to employees'],
            ['account_code' => '2020', 'account_name' => 'SSS Payable', 'account_type' => 'Liability', 'description' => 'SSS contributions payable'],
            ['account_code' => '2030', 'account_name' => 'PhilHealth Payable', 'account_type' => 'Liability', 'description' => 'PhilHealth contributions payable'],
            ['account_code' => '2040', 'account_name' => 'Pag-IBIG Payable', 'account_type' => 'Liability', 'description' => 'Pag-IBIG contributions payable'],
            ['account_code' => '2050', 'account_name' => 'Withholding Tax Payable', 'account_type' => 'Liability', 'description' => 'Withholding tax deducted from employees'],
            ['account_code' => '2060', 'account_name' => '13th Month Payable', 'account_type' => 'Liability', 'description' => '13th month pay owed to employees'],

            // Equity
            ['account_code' => '3010', 'account_name' => 'Owner\'s Equity', 'account_type' => 'Equity', 'description' => 'Owner\'s investment in the business'],
            ['account_code' => '3020', 'account_name' => 'Retained Earnings', 'account_type' => 'Equity', 'description' => 'Accumulated earnings retained in the business'],

            // Revenue
            ['account_code' => '4010', 'account_name' => 'Transportation Revenue', 'account_type' => 'Revenue', 'description' => 'Revenue from transportation services'],

            // Expenses
            ['account_code' => '5010', 'account_name' => 'Salaries and Wages Expense', 'account_type' => 'Expense', 'description' => 'Gross salaries and wages expense'],
            ['account_code' => '5020', 'account_name' => 'Overtime Expense', 'account_type' => 'Expense', 'description' => 'Overtime pay expense'],
            ['account_code' => '5030', 'account_name' => 'Employee Benefits Expense', 'account_type' => 'Expense', 'description' => 'Employee benefits expense'],
            ['account_code' => '5040', 'account_name' => '13th Month Pay Expense', 'account_type' => 'Expense', 'description' => '13th month pay expense'],
            ['account_code' => '5050', 'account_name' => 'Government Contributions Expense', 'account_type' => 'Expense', 'description' => 'Employer share of government contributions'],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::updateOrCreate(
                ['account_code' => $account['account_code']],
                $account
            );
        }

        $mappings = [
            'payroll_expense'          => '5010',
            'sss_payable'              => '2020',
            'philhealth_payable'       => '2030',
            'pagibig_payable'          => '2040',
            'withholding_tax_payable'  => '2050',
            'salaries_payable'         => '2010',
            'thirteenth_month_expense' => '5040',
            'thirteenth_month_payable' => '2060',
        ];

        foreach ($mappings as $key => $code) {
            $account = ChartOfAccount::where('account_code', $code)->first();
            if ($account) {
                AccountMapping::updateOrCreate(
                    ['mapping_key' => $key],
                    [
                        'label'      => $key,
                        'account_id' => $account->id,
                    ]
                );
            }
        }
    }
}
