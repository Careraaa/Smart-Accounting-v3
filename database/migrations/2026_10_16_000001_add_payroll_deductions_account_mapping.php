<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $account = DB::table('chart_of_accounts')
            ->where('account_code', '5060')
            ->first();

        if ($account && $account->account_name !== 'Payroll Deductions') {
            throw new RuntimeException('Account code 5060 is already assigned to another account.');
        }

        if (!$account) {
            DB::table('chart_of_accounts')->insert([
                'account_code' => '5060',
                'account_name' => 'Payroll Deductions',
                'account_type' => 'Expense',
                'description' => 'Employee payroll deductions recognized separately from salaries and wages expense',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $accountId = DB::table('chart_of_accounts')
            ->where('account_code', '5060')
            ->value('id');

        DB::table('account_mappings')->insertOrIgnore([
            'mapping_key' => 'payroll_deductions',
            'label' => 'Payroll Deductions',
            'account_id' => $accountId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $employeeReceivableId = DB::table('chart_of_accounts')
            ->where('account_code', '1030')
            ->value('id');

        if ($employeeReceivableId) {
            DB::table('account_mappings')->insertOrIgnore([
                'mapping_key' => 'employee_receivable',
                'label' => 'Employee Receivable',
                'account_id' => $employeeReceivableId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        $payrollDeductionsAccountId = DB::table('chart_of_accounts')
            ->where('account_code', '5060')
            ->value('id');

        if ($payrollDeductionsAccountId) {
            DB::table('account_mappings')
                ->where('mapping_key', 'payroll_deductions')
                ->where('account_id', $payrollDeductionsAccountId)
                ->delete();
        }

        $employeeReceivableId = DB::table('chart_of_accounts')
            ->where('account_code', '1030')
            ->value('id');

        if ($employeeReceivableId) {
            DB::table('account_mappings')
                ->where('mapping_key', 'employee_receivable')
                ->where('account_id', $employeeReceivableId)
                ->delete();
        }
    }
};
